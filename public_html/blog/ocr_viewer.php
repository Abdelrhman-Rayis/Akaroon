<?php
/**
 * ocr_viewer.php — Styled markdown reader for OCR text files
 *
 * GET params:
 *   src    - GCS URL to .md file (validated to start with https://storage.googleapis.com/akaroon-media/)
 *   title  - Document title (for display + download filename)
 *   author - Document author (for display)
 */

// Validate + fetch the .md file
$src = $_GET['src'] ?? '';
$title = $_GET['title'] ?? 'Document';
$author = $_GET['author'] ?? 'Unknown';

$error = null;
$doc_content = '';
$frontmatter = [];

if (!$src) {
    $error = 'لم يتم تحديد الملف (src parameter missing)';
} elseif (strpos($src, 'https://storage.googleapis.com/akaroon-media/') !== 0) {
    $error = 'رابط غير صحيح (invalid URL — must be from akaroon-media GCS bucket)';
} else {
    // Fetch the .md file
    $md_content = @file_get_contents($src);
    if ($md_content === false) {
        $error = 'فشل في تحميل الملف (file not found or network error)';
    } else {
        // Parse YAML frontmatter
        if (strpos($md_content, '---') === 0) {
            $parts = explode('---', $md_content, 3);
            if (count($parts) >= 3) {
                $yaml_raw = $parts[1];
                $doc_content = trim($parts[2]);

                // Simple YAML parsing (key: value lines only)
                foreach (explode("\n", $yaml_raw) as $line) {
                    if (strpos($line, ':') !== false) {
                        list($k, $v) = explode(':', $line, 2);
                        $frontmatter[trim($k)] = trim($v, ' "\'');
                    }
                }
            } else {
                $doc_content = $md_content;
            }
        } else {
            $doc_content = $md_content;
        }
    }
}

// Extract metadata from frontmatter or params
$meta_title = $frontmatter['title'] ?? $title;
$meta_author = $frontmatter['author'] ?? $author;
$meta_year = $frontmatter['year'] ?? '';
$meta_category = $frontmatter['category'] ?? '';
$meta_keywords = $frontmatter['keywords'] ?? '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($meta_title, ENT_QUOTES, 'UTF-8') ?> — مكتبة عكارون</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/marked@14.0.0/marked.min.js"></link>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: #f9f8f5;
            color: #2d1b00;
            line-height: 1.8;
        }

        .ak-ocr-viewer {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        .ak-viewer-header {
            background: linear-gradient(135deg, #0B3D38 0%, #1a5a54 100%);
            color: #fff;
            padding: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .ak-viewer-header a {
            color: #fff;
            text-decoration: none;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .ak-viewer-header a:hover {
            opacity: 1;
        }

        .ak-viewer-title {
            flex: 1;
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Metadata */
        .ak-viewer-meta {
            background: #fff;
            border-bottom: 1px solid #e0d5c7;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .ak-meta-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #2d1b00;
        }

        .ak-meta-row {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            font-size: 0.95rem;
            color: #555;
            margin-top: 0.5rem;
        }

        .ak-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Toolbar */
        .ak-viewer-toolbar {
            background: #fff;
            border-bottom: 1px solid #e0d5c7;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .ak-toolbar-section {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ak-toolbar-section:not(:last-child) {
            border-right: 1px solid #e0d5c7;
            padding-right: 1rem;
        }

        .ak-toolbar-btn {
            background: #fff;
            border: 1px solid #d0c4b6;
            color: #0B3D38;
            padding: 0.5rem 0.8rem;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Tajawal', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .ak-toolbar-btn:hover {
            background: #0B3D38;
            color: #fff;
            border-color: #0B3D38;
        }

        .ak-toolbar-btn-small {
            padding: 0.4rem 0.6rem;
            font-size: 0.85rem;
        }

        .ak-font-label {
            font-size: 0.9rem;
            color: #555;
            font-weight: 500;
        }

        /* Content area */
        .ak-viewer-content {
            flex: 1;
            padding: 2rem 1.5rem;
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
            background: #fff;
        }

        article {
            color: #2d1b00;
            font-size: 1rem;
        }

        article h1,
        article h2,
        article h3,
        article h4,
        article h5,
        article h6 {
            font-weight: 700;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: #0B3D38;
        }

        article h1 { font-size: 1.8rem; }
        article h2 { font-size: 1.5rem; }
        article h3 { font-size: 1.3rem; }

        article p {
            margin-bottom: 1rem;
            line-height: 1.9;
        }

        article ul,
        article ol {
            margin: 1rem 0 1rem 2rem;
            padding: 0;
        }

        article li {
            margin-bottom: 0.5rem;
        }

        article blockquote {
            border-right: 4px solid #0B3D38;
            padding: 0.5rem 0 0.5rem 1rem;
            margin: 1rem 0;
            background: #f5f3f0;
            color: #555;
        }

        article code {
            background: #f5f3f0;
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }

        article pre {
            background: #f5f3f0;
            padding: 1rem;
            border-radius: 4px;
            overflow-x: auto;
            margin: 1rem 0;
        }

        article pre code {
            background: none;
            padding: 0;
        }

        article table {
            border-collapse: collapse;
            width: 100%;
            margin: 1rem 0;
        }

        article table th,
        article table td {
            border: 1px solid #d0c4b6;
            padding: 0.75rem;
            text-align: right;
        }

        article table th {
            background: #f5f3f0;
            font-weight: 700;
        }

        article mark {
            background: #ffeb3b;
            padding: 0.1rem 0.2rem;
        }

        /* Error state */
        .ak-error-box {
            background: #fce4ec;
            border: 1px solid #f48fb1;
            border-radius: 4px;
            padding: 2rem;
            text-align: center;
            color: #c2185b;
            margin: 2rem auto;
            max-width: 600px;
        }

        .ak-error-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .ak-error-text {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        /* Print styles */
        @media print {
            .ak-viewer-header,
            .ak-viewer-toolbar {
                display: none;
            }

            .ak-viewer-content {
                max-width: 100%;
                padding: 1rem;
                background: #fff;
            }

            article {
                color: #000;
            }

            article h1,
            article h2,
            article h3,
            article h4,
            article h5,
            article h6 {
                color: #000;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .ak-viewer-toolbar {
                padding: 0.75rem 1rem;
            }

            .ak-toolbar-section:not(:last-child) {
                border-right: none;
                border-bottom: 1px solid #e0d5c7;
                padding-right: 0;
                padding-bottom: 0.75rem;
                width: 100%;
            }

            .ak-viewer-content {
                padding: 1.5rem 1rem;
            }

            .ak-meta-row {
                gap: 1rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
<div class="ak-ocr-viewer">
    <!-- Header -->
    <div class="ak-viewer-header">
        <a href="javascript:window.history.back();" title="العودة">←</a>
        <div class="ak-viewer-title">مكتبة عكارون البحثية</div>
    </div>

    <?php if ($error): ?>
        <div class="ak-error-box">
            <div class="ak-error-icon">⚠️</div>
            <div class="ak-error-text"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <button class="ak-toolbar-btn" onclick="window.history.back();">العودة إلى البحث</button>
        </div>
    <?php else: ?>
        <!-- Metadata -->
        <div class="ak-viewer-meta">
            <div class="ak-meta-title"><?= htmlspecialchars($meta_title, ENT_QUOTES, 'UTF-8') ?></div>
            <div class="ak-meta-row">
                <?php if ($meta_author): ?>
                    <div class="ak-meta-item">✍️ <span><?= htmlspecialchars($meta_author, ENT_QUOTES, 'UTF-8') ?></span></div>
                <?php endif; ?>
                <?php if ($meta_year): ?>
                    <div class="ak-meta-item">📅 <span><?= htmlspecialchars($meta_year, ENT_QUOTES, 'UTF-8') ?></span></div>
                <?php endif; ?>
                <?php if ($meta_category): ?>
                    <div class="ak-meta-item">🏷️ <span><?= htmlspecialchars($meta_category, ENT_QUOTES, 'UTF-8') ?></span></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="ak-viewer-toolbar">
            <div class="ak-toolbar-section">
                <span class="ak-font-label">حجم الخط:</span>
                <button class="ak-toolbar-btn ak-toolbar-btn-small" onclick="decreaseFont()">A−</button>
                <button class="ak-toolbar-btn ak-toolbar-btn-small" onclick="increaseFont()">A+</button>
            </div>
            <div class="ak-toolbar-section">
                <button class="ak-toolbar-btn" onclick="printDocument();">🖨️ طباعة</button>
            </div>
            <div class="ak-toolbar-section">
                <a href="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>" download class="ak-toolbar-btn">⬇ تحميل النص</a>
            </div>
        </div>

        <!-- Content -->
        <div class="ak-viewer-content">
            <article id="ak-article"></article>
        </div>

        <!-- Hidden markdown source -->
        <script type="text/plain" id="md-source"><?= htmlspecialchars($doc_content, ENT_QUOTES, 'UTF-8') ?></script>

        <script src="https://cdn.jsdelivr.net/npm/marked@14.0.0/marked.min.js"></script>
        <script>
            // Render markdown on page load
            document.addEventListener('DOMContentLoaded', async function() {
                const mdText = document.getElementById('md-source').textContent;
                const html = await marked.parse(mdText);
                document.getElementById('ak-article').innerHTML = html;
            });

            // Font size control
            let currentSize = 1;
            const article = document.getElementById('ak-article');

            function increaseFont() {
                currentSize = Math.min(currentSize + 0.1, 1.6);
                article.style.fontSize = (currentSize) + 'rem';
            }

            function decreaseFont() {
                currentSize = Math.max(currentSize - 0.1, 0.8);
                article.style.fontSize = (currentSize) + 'rem';
            }

            // Print
            function printDocument() {
                window.print();
            }
        </script>
    <?php endif; ?>
</div>
</body>
</html>
