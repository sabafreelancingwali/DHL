<?php
require 'db.php';
$slug = $_GET['slug'] ?? '';
if(!$slug){ header('Location: index.php'); exit; }
 
// fetch article
$stmt = $mysqli->prepare("SELECT a.*, c.name AS category FROM articles a JOIN categories c ON a.category_id=c.id WHERE a.slug = ?");
$stmt->bind_param('s',$slug);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();
if(!$article){ echo "Article not found"; exit; }
 
// increment views (async-ish)
$inc = $mysqli->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
$inc->bind_param('i',$article['id']);
$inc->execute();
 
// fetch comments
$com = $mysqli->prepare("SELECT * FROM comments WHERE article_id = ? ORDER BY created_at DESC");
$com->bind_param('i',$article['id']);
$com->execute();
$comments = $com->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?=htmlspecialchars($article['title'])?> — DHL News</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{font-family:Inter,Arial;margin:0;background:#f5f7fb;color:#111}
  .wrap{max-width:860px;margin:26px auto;padding:0 16px}
  .hero{background:#fff;padding:18px;border-radius:12px;box-shadow:0 10px 30px rgba(20,20,30,0.06)}
  .hero img{width:100%;height:420px;object-fit:cover;border-radius:8px;margin-top:12px}
  .meta{color:#777;font-size:13px;margin-top:6px}
  .content{background:#fff;padding:18px;border-radius:12px;margin-top:12px;line-height:1.7}
  .comments{margin-top:14px}
  .comment-item{background:#fff;padding:12px;border-radius:10px;margin-bottom:8px}
  .cform textarea{width:100%;min-height:80px;padding:10px;border-radius:8px;border:1px solid #e3e6ef}
  .cform input{padding:8px;border-radius:8px;border:1px solid #e3e6ef;width:48%}
  @media(max-width:600px){ .hero img{height:220px} .cform input{width:100%;margin-bottom:8px} }
</style>
</head>
<body>
<div class="wrap">
  <div style="display:flex;justify-content:space-between;align-items:flex-start">
    <div><a href="index.php">← Home</a></div>
    <div style="color:#666"><?=htmlspecialchars($article['category'])?> • Views: <?=htmlspecialchars($article['views'])?></div>
  </div>
 
  <div class="hero">
    <h1><?=htmlspecialchars($article['title'])?></h1>
    <div class="meta"><?=htmlspecialchars($article['author']?:'Staff')?> • <?=htmlspecialchars(date('F j, Y, g:i A',strtotime($article['published_at'])))?></div>
    <?php if($article['image']): ?>
      <img src="<?=htmlspecialchars($article['image'])?>" alt="">
    <?php endif; ?>
  </div>
 
  <article class="content">
    <?=nl2br(htmlspecialchars($article['content']))?>
  </article>
 
  <section class="comments">
    <h3>Comments (<?=count($comments)?>)</h3>
    <div style="margin-bottom:12px" class="cform">
      <input id="cname" placeholder="Your name"><input id="cemail" placeholder="Email (optional)">
      <textarea id="cmsg" placeholder="Write your comment..."></textarea>
      <div style="margin-top:8px">
        <button onclick="postComment()">Post Comment</button>
        <small style="color:#777;margin-left:8px">Be kind and civil.</small>
      </div>
    </div>
 
    <div id="comments-list">
      <?php foreach($comments as $cm): ?>
        <div class="comment-item">
          <div style="font-weight:700"><?=htmlspecialchars($cm['name'])?> <small style="color:#888;font-weight:400">• <?=htmlspecialchars(date('M j, Y H:i',strtotime($cm['created_at'])))?></small></div>
          <div style="margin-top:6px"><?=nl2br(htmlspecialchars($cm['comment']))?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>
 
<script>
  const articleId = <?=json_encode((int)$article['id'])?>;
  function postComment(){
    const name = document.getElementById('cname').value.trim();
    const comment = document.getElementById('cmsg').value.trim();
    if(!name || !comment){ alert('Please type name and comment'); return; }
 
    const form = new FormData();
    form.append('article_id', articleId);
    form.append('name', name);
    form.append('comment', comment);
 
    fetch('comment_post.php', { method: 'POST', body: form })
      .then(r=>r.json()).then(j=>{
        if(j.success){
          // prepend new comment (simple immediate feedback)
          const list = document.getElementById('comments-list');
          const div = document.createElement('div'); div.className = 'comment-item';
          div.innerHTML = '<div style="font-weight:700">'+escapeHtml(name)+' <small style="color:#888;font-weight:400">• Just now</small></div><div style="margin-top:6px">'+escapeHtml(comment).replace(/\n/g, '<br>')+'</div>';
          list.prepend(div);
          document.getElementById('cname').value=''; document.getElementById('cmsg').value='';
        } else alert(j.message || 'Error');
      }).catch(()=>alert('Network error'));
  }
 
  function escapeHtml(text){ return text.replace(/[&<>"']/g, function(m){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; }); }
</script>
</body>
</html>
