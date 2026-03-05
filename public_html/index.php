<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>مكتبة عكارون البحثية</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="css/akaroon-theme.css">
</head>
<body>

<!-- ── Navbar ──────────────────────────────────────── -->
<!-- ── WordPress blog navigation bar ────────────────────── -->
<div class="ak-blog-bar">
  <span class="ak-blog-bar-label">🌐 الموقع:</span>
  <a href="https://www.akaroon.com/blog">الرئيسية</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="https://akaroon.com/blog/?page_id=9">المدونة</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="https://akaroon.com/blog/?page_id=103">التصنيفات</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="https://akaroon.com/blog/?page_id=7">عن الموقع</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="https://akaroon.com/blog/?page_id=207">معرض الصور</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="https://akaroon.com/blog/?page_id=8">تواصل معنا</a>
</div>

<nav class="ak-navbar">
  <a href="/" class="ak-logo">عكارون</a>
  <ul class="ak-nav-links">
    <li><a href="files/التأصيل/search.php">التأصيل</a></li>
    <li><a href="files/التعليم/search.php">التعليم</a></li>
    <li><a href="files/الدولة/search.php">الدولة</a></li>
    <li><a href="files/السياسة/search.php">السياسة</a></li>
    <li><a href="files/الفلسفة/search.php">الفلسفة</a></li>
    <li><a href="files/المجتمع/search.php">المجتمع</a></li>
    <li><a href="files/منظمات/search.php">منظمات</a></li>
  </ul>
</nav>

<!-- ── Hero ────────────────────────────────────────── -->
<section class="ak-hero">
  <h1>مكتبة عكارون البحثية</h1>
  <p>منصة بحثية شاملة في العلوم الإنسانية والاجتماعية والإسلامية</p>
  <form action="blog/ibrahimfinalsearch.php" method="get">
    <div class="ak-search-box">
      <button type="submit" name="search_btn">بحث</button>
      <input type="text" name="search" placeholder="ابحث بالعنوان أو المؤلف أو الكلمات المفتاحية..." autocomplete="off" required>
    </div>
  </form>
</section>

<!-- ── Categories grid ──────────────────────────────── -->
<section style="padding: 3rem 0; background: var(--cream);">
  <div class="container">
    <h3 class="text-center mb-4" style="color:var(--brown); font-weight:700;">تصفح حسب التصنيف</h3>
    <div class="row g-4">

      <div class="col-md-4 col-sm-6">
        <a href="files/التأصيل/search.php" class="ak-cat-card">
          <span class="ak-cat-icon">📖</span>
          <div class="ak-cat-name">التأصيل</div>
          <div class="ak-cat-desc">التأصيل الإسلامي والفقه</div>
        </a>
      </div>

      <div class="col-md-4 col-sm-6">
        <a href="files/التعليم/search.php" class="ak-cat-card">
          <span class="ak-cat-icon">🎓</span>
          <div class="ak-cat-name">التعليم</div>
          <div class="ak-cat-desc">علوم التعليم والتربية</div>
        </a>
      </div>

      <div class="col-md-4 col-sm-6">
        <a href="files/الدولة/search.php" class="ak-cat-card">
          <span class="ak-cat-icon">🏛️</span>
          <div class="ak-cat-name">الدولة</div>
          <div class="ak-cat-desc">الدولة والحكم والإدارة</div>
        </a>
      </div>

      <div class="col-md-4 col-sm-6">
        <a href="files/السياسة/search.php" class="ak-cat-card">
          <span class="ak-cat-icon">🌐</span>
          <div class="ak-cat-name">السياسة</div>
          <div class="ak-cat-desc">العلوم السياسية والعلاقات الدولية</div>
        </a>
      </div>

      <div class="col-md-4 col-sm-6">
        <a href="files/الفلسفة/search.php" class="ak-cat-card">
          <span class="ak-cat-icon">💡</span>
          <div class="ak-cat-name">الفلسفة</div>
          <div class="ak-cat-desc">الفلسفة والفكر والمنطق</div>
        </a>
      </div>

      <div class="col-md-4 col-sm-6">
        <a href="files/المجتمع/search.php" class="ak-cat-card">
          <span class="ak-cat-icon">🏘️</span>
          <div class="ak-cat-name">المجتمع</div>
          <div class="ak-cat-desc">الاجتماع والثقافة والتراث</div>
        </a>
      </div>

      <div class="col-md-4 col-sm-6 mx-auto">
        <a href="files/منظمات/search.php" class="ak-cat-card">
          <span class="ak-cat-icon">🏢</span>
          <div class="ak-cat-name">منظمات</div>
          <div class="ak-cat-desc">المنظمات والهيئات الدولية</div>
        </a>
      </div>

    </div>
  </div>
</section>

<!-- ── Footer ────────────────────────────────────────── -->
<footer class="ak-footer">
  <p>© عكارون — جميع الحقوق محفوظة | <a href="blog/ibrahimfinalsearch.php">البحث الشامل</a></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
