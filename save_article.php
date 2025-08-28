<?php
require 'db.php';
 
$title = trim($_POST['title'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0);
$excerpt = trim($_POST['excerpt'] ?? '');
$image = trim($_POST['image'] ?? '');
$author = trim($_POST['author'] ?? '');
$content = trim($_POST['content'] ?? '');
 
if(!$title || !$slug || !$category_id || !$content){
    die('Missing required fields. <a href="admin_create.php">Back</a>');
}
 
// insert
$ins = $mysqli->prepare("INSERT INTO articles (category_id,title,slug,excerpt,content,author,image) VALUES (?,?,?,?,?,?,?)");
$ins->bind_param('issssss',$category_id,$title,$slug,$excerpt,$content,$author,$image);
if($ins->execute()){
    // redirect to article page using JS redirection as requested
    $newslug = $mysqli->real_escape_string($slug);
    echo "<script>window.location='article.php?slug=' + encodeURIComponent(" . json_encode($slug) . ");</script>";
}else{
    echo "DB error: " . $mysqli->error;
}
