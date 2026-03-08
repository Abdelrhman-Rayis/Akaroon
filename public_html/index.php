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
  <a href="/">الرئيسية</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="/blog/">المدونة</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="/blog/?page_id=103">التصنيفات</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="/blog/?page_id=7">عن الموقع</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="/blog/?page_id=207">معرض الصور</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="/blog/?page_id=8">تواصل معنا</a>
</div>

<nav class="ak-navbar">
  <a href="/" class="ak-logo">
    <span class="ak-logo-name">Akaroon || عكارون</span>
    <span class="ak-logo-desc">by Ibrahim Ahmed Omer</span>
  </a>
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
      <div class="ak-search-box-row">
        <button type="submit" name="search_btn">بحث</button>
        <input type="text" name="search" placeholder="ابحث بالعنوان أو المؤلف أو الكلمات المفتاحية..." autocomplete="off" required>
      </div>
      <div class="ak-search-toggle-row">
        <label class="ak-switch" for="semantic_toggle_cb">
          <input type="checkbox" class="ak-switch-input" id="semantic_toggle_cb" checked>
          <span class="ak-switch-track"><span class="ak-switch-thumb"></span></span>
          <span class="ak-switch-label">🧠 دلالي</span>
        </label>
        <input type="hidden" name="semantic" id="semantic_val" value="1">
      </div>
    </div>
  </form>
</section>

<!-- ── Professor's Letter ───────────────────────────── -->
<section class="ak-letter-section">
  <div class="container">
    <div class="row g-0 align-items-start">

      <!-- Author sidebar (right in RTL) -->
      <div class="col-md-3">
        <div class="ak-letter-author">
          <div class="ak-letter-monogram">
            <img src="img/professor.jpg" alt="إبراهيم أحمد عمر">
          </div>
          <div>
            <div class="ak-letter-author-from">رسالة من أخوكم</div>
            <div class="ak-letter-author-name">إبراهيم أحمد عمر</div>
            <div class="ak-letter-author-role">مؤسس مكتبة عكارون</div>
          </div>
        </div>
      </div>

      <!-- Letter content (left in RTL) -->
      <div class="col-md-9">
        <div class="ak-letter-content">
          <span class="ak-letter-deco-quote">❝</span>

          <p class="ak-letter-bismillah">بسم الله الرحمن الرحيم</p>
          <p class="ak-letter-subtitle">رجاء ودعاء</p>

          <!-- Always-visible preview -->
          <p class="ak-letter-preview">الأخ الكريم / الأخت الكريمة السلام عليكم ورحمة الله</p>
          <p class="ak-letter-preview">نحن أبناء وبنات الثلثين الأخيرين من القرن العشرين والخمس الأول من القرن الحادي والعشرين عشنا أحداثاً وطنية وأخرى إقليمية وعالمية كثيرة وكبيرة...</p>

          <!-- Collapsible body -->
          <div class="collapse" id="professorLetter">
            <div class="ak-letter-collapse-body">
              <p>نحن أبناء وبنات الثلثين الأخيرين من القرن العشرين والخمس الأول من القرن الحادي والعشرين عشنا أحداثاً وطنية وأخرى إقليمية وعالمية كثيرة وكبيرة. وعمّرنا ساحات الوطن بأعمالنا في مجالاته المتعددة. تطوعنا في منظمات العمل الإجتماعي الوطنية؛ أنشأنا الأحزاب السياسية الوطنية وملأنا دورها حركة ونشاطاً؛ تدربنا وحملنا السلاح وقاتلنا دفاعاً عن الوطن؛ انتظمنا في اتحادات العمل النقابي الفئوي والطلابي توعيةً بالحقوق والواجبات؛ وخدمنا جماهير شعبنا من مواقع الخدمة المدنية.</p>
              <p>قرأنا ودرسنا وحفظنا في خلاوي ومدارس ومعاهد وجامعات الوطن وجامعات خارج الوطن؛ فكرنا وألّفنا ونشرنا في دور النشر النظري والأكاديمي والإعلامي؛ ابتهجنا في صالات العمل الثقافي والفني؛ ولعبنا وشجعنا في ميادين الرياضة. كذلك عشنا إقليمياً وعالمياً أحداثاً ضخمة.</p>
              <p>ألم نعش أهم فترة في مسيرة الاتحاد السوفيتي "العظيم" ثم انهياره بتلك الطريقة المذهلة؟ وشهدنا بناء حائط برلين العنيد ثم رأيناه ينهار بضربات الألماني الوحدوي الذي بدأ يبرأ من جرح النازية اللئيم ثم يقوم على ساقيه من جديد. وفي الفترة المذكورة ارتفعت رايات القومية العربية ورايات عدم الإنحياز ثم طوتها السنوات الأخيرة من نفس الفترة. وتأسست وطغت إسرائيل. وخرج الإستعمار الذي كان جاثماً على إفريقيا بفعل الحركات التحررية؛ وقامت منظمة الوحدة الإفريقية ثم الإتحاد الإفريقي. وقام الإتحاد الأوربي وبدا مثالاً في الخروج من ربقة الدولة القطرية...</p>
            </div>
          </div>

          <hr class="ak-letter-divider">

          <!-- Actions -->
          <div class="ak-letter-actions">
            <button class="ak-letter-toggle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#professorLetter"
                    aria-expanded="false"
                    aria-controls="professorLetter">
              اقرأ المزيد
              <span class="ak-letter-arrow">▾</span>
            </button>
            <a href="/blog/" class="ak-letter-readmore">
              اقرأ الرسالة كاملة على المدونة ←
            </a>
          </div>

        </div>
      </div>

    </div>
  </div>
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
<script>
document.getElementById('semantic_toggle_cb').addEventListener('change', function() {
  var on = this.checked;
  document.getElementById('semantic_val').value = on ? '1' : '0';
  document.querySelector('.ak-switch-label').textContent = on ? '🧠 دلالي' : '🔤 عادي';
});
</script>
</body>
</html>
