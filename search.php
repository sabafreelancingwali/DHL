<?php
require 'db.php';
$q = trim($_GET['q'] ?? '');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Search: <?=htmlspecialchars($q)?> — DHL News</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{font-family:Inter,Arial;background:#f6f8fb;margin:0;color:#111}
  .wrap{max-width:900px;margin:26px auto;padding:0 16px}
  .item{background:#fff;padding:12px;border-radius:10px;margin-bottom:10px;box-shadow:0 8px 20px rgba(20,20,30,0.04)}
</style>
</head>
<body>
<div class="wrap">
  <h2>Search results for "<?=htmlspecialchars($q)?>"</h2>
  <?php
    if($q){
      $like = "%$q%";
      $s = $mysqli->prepare("SELECT title,excerpt,slug FROM articles WHERE title LIKE ? OR content LIKE ? ORDER BY published_at DESC");
      $s->bind_param('ss',$like,$like);
      $s->execute();
      $res = $s->get_result()->fetch_all(MYSQLI_ASSOC);
      if(count($res)):
        foreach($res as $r): ?>
          <div class="item" onclick="window.location='article.php?slug=<?=htmlspecialchars($r['slug'])?>'" style="cursor:pointer">
            <div style="font-weight:800"><?=htmlspecialchars($r['title'])?></div>
            <div style="color:#666"><?=htmlspecialchars($r['excerpt'])?></div>
          </div>
        <?php endforeach;
      else: ?>
        <div style="padding:12px;background:#fff;border-radius:10px">No results found.</div>
      <?php endif;
    } else {
      echo "<div style='padding:12px;background:#fff;border-radius:10px'>Type something to search.</div>";
    }
  ?>
</div>
</body>
</html>
