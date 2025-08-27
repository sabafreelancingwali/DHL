<?php
require 'db.php';
$cats = $mysqli->query("SELECT id,name FROM categories")->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Create Article — DHL Admin</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{font-family:Inter,Arial;background:#f4f6fb;padding:20px}
  .card{max-width:900px;margin:0 auto;background:#fff;padding:18px;border-radius:12px;box-shadow:0 12px 30px rgba(20,20,30,0.06)}
  input,textarea,select{width:100%;padding:10px;margin:8px 0;border-radius:8px;border:1px solid #e3e6ef}
  label{font-weight:700}
</style>
</head>
<body>
<div class="card">
  <h2>Create Article</h2>
  <form action="save_article.php" method="post">
    <label>Title</label>
    <input name="title" required>
    <label>Slug (unique, e.g. my-article)</label>
    <input name="slug" required>
    <label>Category</label>
    <select name="category_id" required>
      <?php foreach($cats as $c): ?>
        <option value="<?=htmlspecialchars($c['id'])?>"><?=htmlspecialchars($c['name'])?></option>
      <?php endforeach; ?>
    </select>
    <label>Excerpt</label>
    <input name="excerpt">
    <label>Image URL (absolute)</label>
    <input name="image">
    <label>Author</label>
    <input name="author">
    <label>Content (plain text)</label>
    <textarea name="content" rows="8" required></textarea>
    <div style="display:flex;gap:10px">
      <button type="submit">Save</button>
      <button type="button" onclick="window.location='index.php'">Cancel</button>
    </div>
  </form>
</div>
</body>
</html>
