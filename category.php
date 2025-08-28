<?php
require 'db.php';
$slug = $_GET['slug'] ?? '';
if(!$slug) { header('Location: index.php'); exit; }
 
// fetch category
$cStmt = $mysqli->prepare("SELECT id,name FROM categories WHERE slug = ?");
$cStmt->bind_param('s',$slug);
$cStmt->execute();
$cat = $cStmt->get_result()->fetch_assoc();
if(!$cat){ echo "Category not found"; exit; }
 
// fetch articles
$aStmt = $mysqli->prepare("SELECT id,title,excerpt,slug,image,published_at,author FROM articles WHERE category_id = ? ORDER BY published_at DESC");
$aStmt->bind_param('i',$cat['id']);
$aStmt->execute();
$arts = $aStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?=htmlspecialchars($cat['name'])?> — DHL News</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{font-family:Inter,Arial;background:#f6f7fb;margin:0;color:#111}
  .wrap{max-width:900px;margin:28px auto;padding:0 16px}
  header{display:flex;align-items:center;gap:12px;margin-bottom:18px}
  .back{cursor:pointer;color:#555}
  .list-item{background:#fff;padding:14px;border-radius:10px;margin-bottom:12px;box-shadow:0 8px 20px rgba(20,20,30,0.05);display:flex;gap:12px}
  .list-item img{width:180px;height:110px;object-fit:cover;border-radius:8px}
  .meta{color:#777;font-size:13px}
  @media(max-width:650px){ .list-item{flex-direction:column} .list-item img{width:100%;height:180px} }
</style>
</head>
<body>
<div class="wrap">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <div>
      <div class="back" onclick="history.back()">← Back</div>
      <h1><?=htmlspecialchars($cat['name'])?></h1>
    </div>
    <div><button onclick="window.location='index.php'">Home</button></div>
  </div>
 
  <?php if(count($arts)): foreach($arts as $a): ?>
    <div class="list-item" onclick="goArticle('<?=htmlspecialchars($a['slug'])?>')" style="cursor:pointer">
      <img src="<?=htmlspecialchars($a['image']?:'https://via.placeholder.com/300x180')?>" alt="">
      <div>
        <div style="font-weight:800"><?=htmlspecialchars($a['title'])?></div>
        <div class="meta"><?=htmlspecialchars($a['author']?:'Staff')?> • <?=htmlspecialchars(date('M j, Y',strtotime($a['published_at'])))?></div>
        <p style="margin-top:8px;color:#555"><?=htmlspecialchars($a['excerpt'])?></p>
      </div>
    </div>
  <?php endforeach; else: ?>
    <div style="padding:14px;background:#fff;border-radius:10px">No articles yet in this category.</div>
  <?php endif; ?>
</div>
 
<script>
  function goArticle(slug){ window.location = 'article.php?slug=' + encodeURIComponent(slug); }
</script>
</body>
</html>
