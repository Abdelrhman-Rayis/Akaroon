<?php
// Read GET params only — no DB query needed here.
// All searching is done via parallel AJAX to search_fetch.php.
$mode        = $_GET['mode']   ?? 'semantic';
$search_term = substr(trim($_GET['search'] ?? ''), 0, 200);
$has_search  = isset($_GET['search_btn']);
$cssV        = @filemtime(__DIR__ . '/../css/akaroon-theme.css') ?: 1;
// Sanitised values for HTML output
$mode_safe   = htmlspecialchars($mode,        ENT_QUOTES, 'UTF-8');
$term_safe   = htmlspecialchars($search_term, ENT_QUOTES, 'UTF-8');
?>
<?php /* JS needs the raw search params — expose as JSON-safe strings */ ?>
<?php $js_term = json_encode($search_term); $js_mode = json_encode($mode); ?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>البحث — مكتبة عكارون</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="../css/akaroon-theme.css?v=<?= $cssV ?>">
</head>
<body>

<!-- ── Navigation ─────────────────────────────────────────── -->
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
  <a href="../" class="ak-logo"><span class="ak-logo-name">Akaroon || عكارون</span><span class="ak-logo-desc">by Ibrahim Ahmed Omer</span></a>
  <ul class="ak-nav-links">
    <li><a href="../files/التأصيل/search.php">التأصيل</a></li>
    <li><a href="../files/التعليم/search.php">التعليم</a></li>
    <li><a href="../files/الدولة/search.php">الدولة</a></li>
    <li><a href="../files/السياسة/search.php">السياسة</a></li>
    <li><a href="../files/الفلسفة/search.php">الفلسفة</a></li>
    <li><a href="../files/المجتمع/search.php">المجتمع</a></li>
    <li><a href="../files/منظمات/search.php">منظمات</a></li>
  </ul>
</nav>

<!-- ── Hero (shown when no active search) ─────────────────── -->
<?php if (!$has_search): ?>
<section class="ak-hero">
  <h1>مكتبة عكارون البحثية</h1>
  <p>ابحث في آلاف الأوراق البحثية والكتب عبر جميع التصنيفات</p>
  <form action="" method="get">
    <div class="ak-search-box">
      <div class="ak-search-box-row">
        <button type="submit" name="search_btn">بحث</button>
        <input type="text" name="search" placeholder="ابحث بالعنوان أو المؤلف أو الكلمات المفتاحية..." autocomplete="off" required>
      </div>
      <div class="ak-search-toggle-row">
        <div class="ak-mode-seg">
          <button type="button" class="ak-mode-btn <?= $mode === 'normal'   ? 'ak-mode-active' : '' ?>" data-mode="normal">🔤 عادي</button>
          <button type="button" class="ak-mode-btn <?= $mode === 'semantic' ? 'ak-mode-active' : '' ?>" data-mode="semantic">🧠 دلالي</button>
          <button type="button" class="ak-mode-btn <?= $mode === 'deep'     ? 'ak-mode-active' : '' ?>" data-mode="deep">🔬 عميق</button>
        </div>
        <input type="hidden" name="mode" id="mode_val" value="<?= $mode_safe ?>">
        <span class="ak-info-icon" tabindex="0" data-tip="🔤 عادي: بحث مباشر بالنص • 🧠 دلالي: يوسّع بالجذور والمرادفات واللهجة السودانية • 🔬 عميق: يبحث داخل نص الوثيقة كاملاً عبر OCR">i</span>
      </div>
    </div>
  </form>
</section>

<div class="container py-5 text-center" style="color:var(--muted);">
  <span style="font-size:3.5rem;">📚</span>
  <p class="mt-3" style="font-size:1.05rem;">اكتب كلمة البحث أعلاه للعثور على الأوراق والكتب</p>
  <a href="../" class="mt-2 d-inline-block" style="color:var(--gold);">أو تصفح التصنيفات ←</a>
</div>

<?php else: ?>

<!-- ── Results header ─────────────────────────────────────── -->
<div class="ak-results-header">
  <form action="" method="get">
    <div class="ak-search-box">
      <div class="ak-search-box-row">
        <button type="submit" name="search_btn">بحث</button>
        <input type="text" name="search" value="<?= $term_safe ?>" autocomplete="off">
      </div>
      <div class="ak-search-toggle-row">
        <div class="ak-mode-seg">
          <button type="button" class="ak-mode-btn <?= $mode === 'normal'   ? 'ak-mode-active' : '' ?>" data-mode="normal">🔤 عادي</button>
          <button type="button" class="ak-mode-btn <?= $mode === 'semantic' ? 'ak-mode-active' : '' ?>" data-mode="semantic">🧠 دلالي</button>
          <button type="button" class="ak-mode-btn <?= $mode === 'deep'     ? 'ak-mode-active' : '' ?>" data-mode="deep">🔬 عميق</button>
        </div>
        <input type="hidden" name="mode" id="mode_val" value="<?= $mode_safe ?>">
        <span class="ak-info-icon" tabindex="0" data-tip="🔤 عادي: بحث مباشر • 🧠 دلالي: يوسّع بالجذور والمرادفات • 🔬 عميق: يبحث داخل نص الوثيقة كاملاً">i</span>
      </div>
    </div>
  </form>
  <div id="ak-result-count" class="ak-result-count">
    <span id="ak-count-text" class="ak-count-loading">
      <span class="ak-spinner-inline"></span> جارٍ البحث…
    </span>
  </div>
</div>

<!-- ── Results stream (populated by JS) ───────────────────── -->
<section class="ak-results-section">
  <div class="container">
    <div id="ak-results-stream"></div>
    <div id="ak-all-empty" class="ak-empty" style="display:none;">
      <span class="ak-empty-icon">🔍</span>
      <p id="ak-empty-msg">لا توجد نتائج</p>
      <p style="font-size:0.9rem;color:var(--muted);">جرب كلمات مختلفة أو <a href="../" style="color:var(--gold);">تصفح التصنيفات</a></p>
    </div>
  </div>
</section>

<?php endif; ?>

<footer class="ak-footer">
  <p>© عكارون — جميع الحقوق محفوظة | <a href="../">الصفحة الرئيسية</a></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Mode segmented control ────────────────────────────────────
(function () {
  var modeVal = document.getElementById('mode_val');
  document.querySelectorAll('.ak-mode-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.ak-mode-btn').forEach(function (b) { b.classList.remove('ak-mode-active'); });
      this.classList.add('ak-mode-active');
      if (modeVal) modeVal.value = this.getAttribute('data-mode');
    });
  });
})();

// ── Info tooltip ──────────────────────────────────────────────
(function () {
  var popup = null;
  function showTip(icon) {
    hideTip();
    popup = document.createElement('div');
    popup.className = 'ak-tooltip-popup';
    popup.textContent = icon.getAttribute('data-tip');
    document.body.appendChild(popup);
    var r    = icon.getBoundingClientRect();
    var top  = r.bottom + window.scrollY + 8;
    var left = r.left + window.scrollX + r.width / 2 - popup.offsetWidth / 2;
    left = Math.max(8, Math.min(left, window.innerWidth - popup.offsetWidth - 8));
    popup.style.top  = top + 'px';
    popup.style.left = left + 'px';
  }
  function hideTip() { if (popup) { popup.remove(); popup = null; } }
  document.querySelectorAll('.ak-info-icon').forEach(function (icon) {
    icon.addEventListener('mouseenter', function () { showTip(icon); });
    icon.addEventListener('mouseleave', hideTip);
    icon.addEventListener('focus',      function () { showTip(icon); });
    icon.addEventListener('blur',       hideTip);
  });
})();

// ── Parallel AJAX search ──────────────────────────────────────
<?php if ($has_search): ?>
(function () {
  var SEARCH_TERM  = <?= $js_term ?>;
  var SEARCH_MODE  = <?= $js_mode ?>;

  var CATEGORIES   = [
    { key: 'edu',   label: 'التعليم',  folder: 'التعليم'  },
    { key: 'soc',   label: 'المجتمع',  folder: 'المجتمع'  },
    { key: 'tas',   label: 'التأصيل',  folder: 'التأصيل'  },
    { key: 'pol',   label: 'السياسة',  folder: 'السياسة'  },
    { key: 'org',   label: 'منظمات',   folder: 'منظمات'   },
    { key: 'state', label: 'الدولة',   folder: 'الدولة'   },
    { key: 'philo', label: 'الفلسفة',  folder: 'الفلسفة'  },
  ];

  var totalFound    = 0;
  var doneCount     = 0;
  var synonymsShown = false;
  var countTextEl   = document.getElementById('ak-count-text');
  var streamEl      = document.getElementById('ak-results-stream');
  var emptyEl       = document.getElementById('ak-all-empty');
  var emptyMsgEl    = document.getElementById('ak-empty-msg');

  function escHtml(s) {
    return String(s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function updateCounter() {
    if (doneCount < CATEGORIES.length) {
      countTextEl.innerHTML =
        '<span class="ak-spinner-inline"></span> جارٍ البحث… <span class="ak-cat-progress-text">(' + doneCount + '/' + CATEGORIES.length + ')</span>';
    } else {
      if (totalFound > 0) {
        countTextEl.className = '';
        countTextEl.innerHTML =
          'تم العثور على <strong>' + totalFound + '</strong> نتيجة' +
          (SEARCH_TERM ? ' لـ "<strong>' + escHtml(SEARCH_TERM) + '</strong>"' : '');
      } else {
        countTextEl.className = '';
        countTextEl.innerHTML = 'لا توجد نتائج لـ "<strong>' + escHtml(SEARCH_TERM) + '</strong>"';
        emptyEl.style.display = '';
        emptyMsgEl.textContent = 'لا توجد نتائج لـ "' + SEARCH_TERM + '"';
      }
    }
  }

  function appendSynonyms(expanded) {
    if (synonymsShown || !expanded || expanded.length <= 1) return;
    synonymsShown = true;
    var hint = document.createElement('div');
    hint.className = 'ak-synonyms-hint';
    hint.innerHTML = '🔗 بحث موسّع يشمل المترادفات: ' +
      expanded.slice(1).map(function(s) {
        return '<span class="ak-syn-tag">' + escHtml(s) + '</span>';
      }).join(' ');
    countTextEl.parentNode.appendChild(hint);
  }

  // ── Step 1: Create skeleton sections immediately ──────────
  var sectionMap = {};  // key → DOM node
  CATEGORIES.forEach(function (cat) {
    var el = document.createElement('div');
    el.className = 'ak-cat-results-section';
    el.setAttribute('data-cat', cat.key);
    el.setAttribute('data-rel', '0');
    el.innerHTML =
      '<div class="ak-cat-results-header">' +
        '<span class="ak-skeleton-label"></span> ' +
        '<span class="ak-skeleton-badge"></span>' +
      '</div>' +
      '<div class="row g-4">' +
        '<div class="col-lg-3 col-md-4 col-sm-6"><div class="ak-skeleton-card"></div></div>' +
        '<div class="col-lg-3 col-md-4 col-sm-6"><div class="ak-skeleton-card"></div></div>' +
        '<div class="col-lg-3 col-md-4 col-sm-6"><div class="ak-skeleton-card"></div></div>' +
        '<div class="col-lg-3 col-md-4 col-sm-6"><div class="ak-skeleton-card"></div></div>' +
      '</div>';
    streamEl.appendChild(el);
    sectionMap[cat.key] = el;
  });

  // ── Step 2: Replace skeleton with real cards as each AJAX returns ──
  function fillSection(cat, data) {
    var el = sectionMap[cat.key];
    if (data.count === 0) {
      el.classList.add('ak-cat-hidden');
      return;
    }
    var searchUrl = '/files/' + encodeURIComponent(cat.folder) + '/search.php' +
                    '?search=' + encodeURIComponent(SEARCH_TERM) +
                    '&search_btn=1&mode=' + encodeURIComponent(SEARCH_MODE);
    var showAllLink = (data.total_in_cat > 12)
      ? '<a href="' + searchUrl + '" class="ak-cat-more-link">' +
          'عرض الكل (' + data.total_in_cat + ') ←</a>'
      : '';

    // Combined relevance: max_rel weighs quality, total_in_cat weighs quantity
    var relScore = (data.max_rel || 0) * 1000 + (data.total_in_cat || 0);
    el.setAttribute('data-rel', String(relScore));
    el.style.opacity = '0.4';
    el.innerHTML =
      '<div class="ak-cat-results-header">' +
        '<h5>' + escHtml(cat.label) + '</h5>' +
        '<span class="ak-cat-count-badge">' + data.count + ' نتيجة</span>' +
        showAllLink +
      '</div>' +
      '<div class="row g-4">' + data.html + '</div>';

    requestAnimationFrame(function () {
      el.style.opacity = '1';
    });
  }

  // ── Step 3: Re-sort all sections by relevance once all 7 are done ──
  function sortByRelevance() {
    var sections = Array.from(streamEl.querySelectorAll('.ak-cat-results-section:not(.ak-cat-hidden)'));
    sections.sort(function (a, b) {
      return parseInt(b.getAttribute('data-rel') || '0') - parseInt(a.getAttribute('data-rel') || '0');
    });
    // Remove hidden (0-result) skeletons entirely
    streamEl.querySelectorAll('.ak-cat-hidden').forEach(function (el) { el.remove(); });
    // Re-append visible sections in relevance order
    sections.forEach(function (s) { streamEl.appendChild(s); });
  }

  // ── Step 4: Fire all 7 requests in parallel ─────────────────
  CATEGORIES.forEach(function (cat) {
    var body = new URLSearchParams({
      search:   SEARCH_TERM,
      mode:     SEARCH_MODE,
      category: cat.key,
    });

    fetch('search_fetch.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body:    body.toString(),
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      doneCount++;
      if (data.count > 0) {
        totalFound += data.count;
        appendSynonyms(data.expanded);
      }
      fillSection(cat, data);
      updateCounter();
      if (doneCount === CATEGORIES.length) sortByRelevance();
    })
    .catch(function () {
      doneCount++;
      sectionMap[cat.key].classList.add('ak-cat-hidden');
      updateCounter();
      if (doneCount === CATEGORIES.length) sortByRelevance();
    });
  });

  // Show initial "searching" state
  updateCounter();
})();
<?php endif; ?>
</script>
</body>
</html>
