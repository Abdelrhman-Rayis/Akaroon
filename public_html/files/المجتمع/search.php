<?php
include('database_connection.php');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>المجتمع — مكتبة عكارون</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="../../css/akaroon-theme.css">
</head>
<body>

<!-- ── WordPress blog navigation bar ────────────────────── -->
<div class="ak-blog-bar">
  <span class="ak-blog-bar-label">🌐 الموقع:</span>
  <a href="/blog">الرئيسية</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="/blog/?page_id=9">المدونة</a>
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
  <a href="../../" class="ak-logo">عكارون</a>
  <ul class="ak-nav-links">
    <li><a href="../التأصيل/search.php">التأصيل</a></li>
    <li><a href="../التعليم/search.php">التعليم</a></li>
    <li><a href="../الدولة/search.php">الدولة</a></li>
    <li><a href="../السياسة/search.php">السياسة</a></li>
    <li><a href="../الفلسفة/search.php">الفلسفة</a></li>
    <li><a href="../المجتمع/search.php">المجتمع</a></li>
    <li><a href="../منظمات/search.php">منظمات</a></li>
    <li><a href="../../blog/ibrahimfinalsearch.php">🔍 بحث شامل</a></li>
  </ul>
</nav>

<div class="ak-page-header">
  <h2>المجتمع</h2>
  <p>الاجتماع والثقافة والتراث</p>
</div>

<div class="container mt-4">
  <div class="row">

    <!-- Sidebar filters -->
    <div class="col-md-3">
      <div class="ak-sidebar">

        <!-- Text search -->
        <h5>🔍 بحث نصي</h5>
        <input type="text" id="search_text" class="ak-sidebar-search" placeholder="ابحث هنا...">
        <button class="ak-sidebar-btn" id="search_go">بحث</button>

        <!-- Author filter -->
        <?php
        $q = $connect->prepare("SELECT DISTINCT(The_number_of_the_Author) FROM soc ORDER BY The_number_of_the_Author");
        $q->execute();
        $authors = $q->fetchAll();
        if (!empty($authors)):
        ?>
        <h5 class="mt-3">✍️ المؤلف</h5>
        <div class="ak-filter-scroll">
          <?php foreach ($authors as $row):
            $val = htmlspecialchars($row['The_number_of_the_Author'], ENT_QUOTES, 'UTF-8');
          ?>
          <label class="ak-filter-item">
            <input type="checkbox" class="common_selector brand" value="<?= $val ?>">
            <span><?= $val ?></span>
          </label>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Field of research filter -->
        <?php
        $q = $connect->prepare("SELECT DISTINCT(Field_of_research) FROM soc ORDER BY Field_of_research");
        $q->execute();
        $fields = $q->fetchAll();
        if (!empty($fields)):
        ?>
        <h5 class="mt-3">🔬 مجال البحث</h5>
        <div class="ak-filter-scroll">
          <?php foreach ($fields as $row):
            $val = htmlspecialchars($row['Field_of_research'], ENT_QUOTES, 'UTF-8');
          ?>
          <label class="ak-filter-item">
            <input type="checkbox" class="common_selector ram" value="<?= $val ?>">
            <span><?= $val ?></span>
          </label>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Place of issue filter -->
        <?php
        $q = $connect->prepare("SELECT DISTINCT(Place_of_issue) FROM soc ORDER BY Place_of_issue");
        $q->execute();
        $places = $q->fetchAll();
        if (!empty($places)):
        ?>
        <h5 class="mt-3">📍 مكان الإصدار</h5>
        <div class="ak-filter-scroll">
          <?php foreach ($places as $row):
            $val = htmlspecialchars($row['Place_of_issue'], ENT_QUOTES, 'UTF-8');
          ?>
          <label class="ak-filter-item">
            <input type="checkbox" class="common_selector storage" value="<?= $val ?>">
            <span><?= $val ?></span>
          </label>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

      </div><!-- /ak-sidebar -->
    </div>

    <!-- Results area -->
    <div class="col-md-9">
      <div class="filter_data">
        <div class="ak-loading">
          <div class="ak-loading-spinner"></div>
          <p>جارٍ التحميل...</p>
        </div>
      </div>
    </div>

  </div>
</div>

<footer class="ak-footer">
  <p>© عكارون — جميع الحقوق محفوظة | <a href="../../">الصفحة الرئيسية</a></p>
</footer>

<script src="js/jquery-1.10.2.min.js"></script>
<script>
$(document).ready(function(){

  filter_data();

  function filter_data() {
    $('.filter_data').html('<div class="ak-loading"><div class="ak-loading-spinner"></div><p>جارٍ التحميل...</p></div>');
    var action       = 'fetch_data';
    var brand        = get_filter('brand');
    var ram          = get_filter('ram');
    var storage      = get_filter('storage');
    var search_text  = $('#search_text').val();
    $.ajax({
      url: 'fetch_data.php',
      method: 'POST',
      data: { action: action, brand: brand, ram: ram, storage: storage, search_text: search_text },
      success: function(data) {
        $('.filter_data').html('<div class="row g-4">' + data + '</div>');
      }
    });
  }

  function get_filter(class_name) {
    var filter = [];
    $('.' + class_name + ':checked').each(function(){
      filter.push($(this).val());
    });
    return filter;
  }

  $('.common_selector').click(function(){ filter_data(); });
  $('#search_go').click(function(){ filter_data(); });
  $('#search_text').keypress(function(e){
    if (e.which === 13) filter_data();
  });

});
</script>

</body>
</html>
