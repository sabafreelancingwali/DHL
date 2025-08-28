<?php
require 'db.php';
 
// fetch featured (latest 3)
$featStmt = $mysqli->prepare("SELECT a.id,a.title,a.excerpt,a.slug,a.image,c.name AS category FROM articles a JOIN categories c ON a.category_id=c.id ORDER BY a.published_at DESC LIMIT 3");
$featStmt->execute();
$featured = $featStmt->get_result()->fetch_all(MYSQLI_ASSOC);
 
// fetch recent per category (3 each)
$catStmt = $mysqli->prepare("SELECT id,name,slug FROM categories");
$catStmt->execute();
$categories = $catStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>DHL News — Home</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    /* Internal CSS - homepage: modern, realistic look */
    :root{--accent:#d62828;--muted:#6b6b6b;--bg:#f6f7fb;--card:#ffffff}
    *{box-sizing:border-box;font-family:Inter,Segoe UI,Roboto,Arial,sans-serif}
    body{margin:0;background:linear-gradient(180deg,#f8f9fc,#f6f7fb);color:#111}
    header{background:var(--card);padding:18px 24px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 6px 18px rgba(20,20,30,0.06);position:sticky;top:0;z-index:10}
    .brand{display:flex;gap:12px;align-items:center;cursor:pointer}
    .logo{width:44px;height:44px;background:var(--accent);color:#fff;border-radius:6px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px}
    nav a{margin-left:14px;text-decoration:none;color:#333;font-weight:600}
    .container{max-width:1100px;margin:26px auto;padding:0 16px}
    .featured{display:grid;grid-template-columns:2fr 1fr;gap:18px}
    .card{background:var(--card);border-radius:12px;overflow:hidden;box-shadow:0 8px 30px rgba(20,20,30,0.06)}
    .hero{position:relative;height:340px;background:#222;color:#fff;display:flex;flex-direction:column;justify-content:flex-end;padding:20px}
    .hero img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0.62}
    .hero .meta{position:relative;z-index:2}
    .grid-cats{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-top:22px}
    .small-card{display:flex;gap:12px;padding:14px;align-items:center}
    .small-card img{width:110px;height:72px;object-fit:cover;border-radius:8px}
    .cats-row{display:flex;gap:12px;flex-wrap:wrap;margin-top:18px}
    .cat-pill{background:#fff;padding:10px 12px;border-radius:999px;box-shadow:0 6px 18px rgba(20,20,30,0.04);cursor:pointer}
    .searchbar{display:flex;align-items:center;gap:8px;border-radius:10px;padding:6px 10px;background:#fff;width:320px;box-shadow:0 6px 18px rgba(20,20,30,0.04)}
    .searchbar input{border:0;outline:0;font-size:14px}
    footer{max-width:1100px;margin:40px auto;padding:20px;text-align:center;color:var(--muted)}
    @media(max-width:800px){
      .featured{grid-template-columns:1fr;gap:12px}
      .hero{height:220px}
      .searchbar{width:100%}
    }
  </style>
</head>
<body>
<header>
  <div class="brand" onclick="goHome()">
    <div class="logo">DHL</div>
    <div>
      <div style="font-weight:800;font-size:18px">DHL News</div>
      <div style="font-size:12px;color:var(--muted)">Real stories. Fast.</div>
    </div>
  </div>
 
  <div style="display:flex;gap:12px;align-items:center">
    <div class="searchbar">
      <input id="q" placeholder="Search news..." onkeydown="if(event.key==='Enter') doSearch()">
      <button onclick="doSearch()" style="border:0;background:none;cursor:pointer;font-weight:700">Go</button>
    </div>
    <nav>
      <?php foreach($categories as $c): ?>
        <a href="#" onclick="goCategory('<?=htmlspecialchars($c['slug'])?>')"><?=htmlspecialchars($c['name'])?></a>
      <?php endforeach; ?>
    </nav>
  </div>
</header>
 
<main class="container">
  <section class="featured">
    <div class="card hero">
      <?php if(isset($featured[0])): ?>
        <?php $f = $featured[0]; ?>
        <?php if($f['image']): ?>
          <img src="<?=htmlspecialchars($f['image'])?>" alt="">
        <?php endif; ?>
        <div class="meta">
          <div style="font-size:13px;color:#fff;opacity:0.95"><?=htmlspecialchars($f['category'])?></div>
          <h1 style="margin:6px 0 10px;font-size:28px;line-height:1.05;cursor:pointer" onclick="goArticle('<?=htmlspecialchars($f['slug'])?>')"><?=htmlspecialchars($f['title'])?></h1>
          <p style="max-width:70%;color:#fff;opacity:0.95"><?=htmlspecialchars($f['excerpt'])?></p>
        </div>
      <?php else: ?>
        <div style="padding:18px;color:#fff">No featured articles yet.</div>
      <?php endif; ?>
    </div>
 
    <div style="display:flex;flex-direction:column;gap:12px">
      <?php for($i=1;$i<count($featured);$i++): $s = $featured[$i]; ?>
        <div class="card small-card" style="cursor:pointer" onclick="goArticle('<?=htmlspecialchars($s['slug'])?>')">
          <img src="<?=htmlspecialchars($s['image']?:'https://via.placeholder.com/200x120')?>" alt="">
          <div>
            <div style="font-weight:700"><?=htmlspecialchars($s['title'])?></div>
            <div style="font-size:13px;color:var(--muted)"><?=htmlspecialchars($s['excerpt'])?></div>
            <div style="font-size:12px;color:#888;margin-top:6px"><?=htmlspecialchars($s['category'])?></div>
          </div>
        </div>
      <?php endfor; ?>
    </div>
  </section>
 
  <section class="grid-cats">
    <?php foreach($categories as $c): ?>
      <div class="card" style="padding:14px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
          <strong><?=htmlspecialchars($c['name'])?></strong>
          <a href="#" onclick="goCategory('<?=htmlspecialchars($c['slug'])?>')">View all →</a>
        </div>
        <?php
          $stmt = $mysqli->prepare("SELECT id,title,excerpt,slug,image FROM articles WHERE category_id = ? ORDER BY published_at DESC LIMIT 3");
          $stmt->bind_param('i',$c['id']);
          $stmt->execute();
          $arts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        ?>
        <?php if(count($arts)): foreach($arts as $a): ?>
          <div style="display:flex;gap:12px;padding:8px 0;border-top:1px solid #f0f0f0">
            <img src="<?=htmlspecialchars($a['image']?:'https://via.placeholder.com/150')?>" style="width:110px;height:66px;object-fit:cover;border-radius:8px;cursor:pointer" onclick="goArticle('<?=htmlspecialchars($a['slug'])?>')">
            <div style="flex:1">
              <div style="font-weight:700;cursor:pointer" onclick="goArticle('<?=htmlspecialchars($a['slug'])?>')"><?=htmlspecialchars($a['title'])?></div>
              <div style="color:var(--muted);font-size:13px"><?=htmlspecialchars($a['excerpt'])?></div>
            </div>
          </div>
        <?php endforeach; else: ?>
          <div style="color:var(--muted);padding:8px 0">No articles yet in this category.</div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </section>
 
</main>
 
<footer><small>&copy; <?=date('Y')?> DHL News — Built with ❤️</small></footer>
 
<script>
  function goHome(){ window.location = 'index.php'; }
  function goCategory(slug){ // JS redirection to category page
    window.location = 'category.php?slug=' + encodeURIComponent(slug);
  }
  function goArticle(slug){
    window.location = 'article.php?slug=' + encodeURIComponent(slug);
  }
  function doSearch(){
    const q = document.getElementById('q').value.trim();
    if(!q) return;
    window.location = 'search.php?q=' + encodeURIComponent(q);
  }
</script>
</body>
</html>
