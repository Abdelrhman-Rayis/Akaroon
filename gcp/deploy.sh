#!/bin/bash
# ─────────────────────────────────────────────────────────────────────────────
# Akaroon — One-time Google Cloud setup & deploy script
# Run this ONCE from your Mac to set up everything on GCP.
#
# Prerequisites:
#   1. gcloud CLI installed: https://cloud.google.com/sdk/docs/install
#   2. Logged in: gcloud auth login
#   3. Project set: gcloud config set project YOUR_PROJECT_ID
#   4. Your SQL dump available (e.g. localhost.sql or a fresh export)
# ─────────────────────────────────────────────────────────────────────────────
set -e

# ── Configuration — edit these ───────────────────────────────────────────────
PROJECT_ID=$(gcloud config get-value project)
REGION="us-central1"
SERVICE_NAME="akaroon"
SQL_INSTANCE="akaroon-mysql"
DB_USER="akaroon_user"
DB_PASSWORD="$(openssl rand -base64 20)"   # auto-generated strong password
SQL_DUMP_FILE="../localhost.sql"            # path to your SQL dump

# WordPress URLs — will be updated to the Cloud Run URL after deploy
WP_URL="https://PLACEHOLDER"              # replaced automatically below

echo "─────────────────────────────────────────────────────────────"
echo " Akaroon GCP Deployment"
echo " Project: $PROJECT_ID  |  Region: $REGION"
echo "─────────────────────────────────────────────────────────────"

# ── Step 1: Enable required APIs ─────────────────────────────────────────────
echo "[1/8] Enabling required GCP APIs..."
gcloud services enable \
    run.googleapis.com \
    sqladmin.googleapis.com \
    cloudbuild.googleapis.com \
    containerregistry.googleapis.com \
    --project=$PROJECT_ID

# ── Step 2: Create Cloud SQL instance (MySQL 8.0, smallest tier) ─────────────
echo "[2/8] Creating Cloud SQL instance '$SQL_INSTANCE'..."
gcloud sql instances create $SQL_INSTANCE \
    --database-version=MYSQL_8_0 \
    --tier=db-f1-micro \
    --region=$REGION \
    --storage-size=10GB \
    --storage-auto-increase \
    --no-backup \
    --project=$PROJECT_ID || echo "  (instance may already exist, continuing...)"

CLOUD_SQL_CONNECTION=$(gcloud sql instances describe $SQL_INSTANCE \
    --project=$PROJECT_ID \
    --format='value(connectionName)')
echo "  Cloud SQL connection: $CLOUD_SQL_CONNECTION"

# ── Step 3: Create databases ──────────────────────────────────────────────────
echo "[3/8] Creating databases..."
gcloud sql databases create akaroon_akaroondb --instance=$SQL_INSTANCE --project=$PROJECT_ID || true
gcloud sql databases create akaroon_wpblog    --instance=$SQL_INSTANCE --project=$PROJECT_ID || true
gcloud sql databases create akaroon_wplibrary --instance=$SQL_INSTANCE --project=$PROJECT_ID || true

# ── Step 4: Create database user ──────────────────────────────────────────────
echo "[4/8] Creating database user '$DB_USER'..."
gcloud sql users create $DB_USER \
    --instance=$SQL_INSTANCE \
    --password="$DB_PASSWORD" \
    --project=$PROJECT_ID || echo "  (user may already exist)"

echo ""
echo "  ⚠️  SAVE THESE CREDENTIALS:"
echo "  DB_USER=$DB_USER"
echo "  DB_PASSWORD=$DB_PASSWORD"
echo ""

# ── Step 5: Import database ───────────────────────────────────────────────────
echo "[5/8] Importing database..."
if [ -f "$SQL_DUMP_FILE" ]; then
    BUCKET_NAME="${PROJECT_ID}-akaroon-imports"
    gsutil mb -p $PROJECT_ID gs://$BUCKET_NAME 2>/dev/null || true
    gsutil cp "$SQL_DUMP_FILE" gs://$BUCKET_NAME/import.sql

    # Grant Cloud SQL service account access to the bucket
    SQL_SA=$(gcloud sql instances describe $SQL_INSTANCE \
        --project=$PROJECT_ID \
        --format='value(serviceAccountEmailAddress)')
    gsutil iam ch serviceAccount:${SQL_SA}:objectViewer gs://$BUCKET_NAME

    gcloud sql import sql $SQL_INSTANCE gs://$BUCKET_NAME/import.sql \
        --project=$PROJECT_ID \
        --quiet
    echo "  Database imported successfully."
else
    echo "  ⚠️  SQL dump not found at: $SQL_DUMP_FILE"
    echo "  Skipping import — run manually later with:"
    echo "  gsutil cp YOUR_DUMP.sql gs://BUCKET/import.sql"
    echo "  gcloud sql import sql $SQL_INSTANCE gs://BUCKET/import.sql"
fi

# ── Step 6: Build Docker image ────────────────────────────────────────────────
echo "[6/8] Building Docker image..."
cd "$(dirname "$0")/.."   # go to repo root
gcloud builds submit \
    --tag gcr.io/$PROJECT_ID/$SERVICE_NAME:latest \
    --project=$PROJECT_ID \
    .

# ── Step 7: Deploy to Cloud Run ───────────────────────────────────────────────
echo "[7/8] Deploying to Cloud Run..."
gcloud run deploy $SERVICE_NAME \
    --image=gcr.io/$PROJECT_ID/$SERVICE_NAME:latest \
    --region=$REGION \
    --platform=managed \
    --allow-unauthenticated \
    --add-cloudsql-instances=$CLOUD_SQL_CONNECTION \
    --set-env-vars="\
DB_HOST=mysql,\
DB_SOCKET=/cloudsql/$CLOUD_SQL_CONNECTION,\
DB_NAME=akaroon_akaroondb,\
DB_USER=$DB_USER,\
DB_PASSWORD=$DB_PASSWORD,\
WP_BLOG_DB_NAME=akaroon_wpblog,\
WP_LIBRARY_DB_NAME=akaroon_wplibrary,\
WP_URL=https://PLACEHOLDER" \
    --memory=512Mi \
    --cpu=1 \
    --min-instances=0 \
    --max-instances=3 \
    --project=$PROJECT_ID

# ── Step 8: Update WordPress URLs ────────────────────────────────────────────
echo "[8/8] Updating WordPress URLs..."
SERVICE_URL=$(gcloud run services describe $SERVICE_NAME \
    --region=$REGION \
    --project=$PROJECT_ID \
    --format='value(status.url)')
echo "  Service URL: $SERVICE_URL"

# Re-deploy with correct WP_URL
gcloud run services update $SERVICE_NAME \
    --region=$REGION \
    --update-env-vars="WP_URL=$SERVICE_URL" \
    --project=$PROJECT_ID

# Also update WordPress URLs in the database
echo "  Run this SQL in Cloud SQL to finalize WordPress URLs:"
echo ""
echo "  -- On akaroon_wpblog:"
echo "  UPDATE wp_options SET option_value = REPLACE(option_value, 'localhost:8082', '${SERVICE_URL}') WHERE option_name IN ('siteurl','home');"
echo "  UPDATE wp_posts   SET post_content = REPLACE(post_content, 'localhost:8082', '${SERVICE_URL}');"
echo ""

echo "─────────────────────────────────────────────────────────────"
echo " ✅  Deployment complete!"
echo " 🌐  Your site: $SERVICE_URL"
echo "─────────────────────────────────────────────────────────────"
