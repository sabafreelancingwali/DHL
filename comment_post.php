<?php
require 'db.php';
header('Content-Type: application/json');
 
$article_id = intval($_POST['article_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$comment = trim($_POST['comment'] ?? '');
 
if(!$article_id || !$name || !$comment){
    echo json_encode(['success'=>false,'message'=>'Missing data']);
    exit;
}
 
$ins = $mysqli->prepare("INSERT INTO comments (article_id,name,comment) VALUES (?,?,?)");
$ins->bind_param('iss',$article_id,$name,$comment);
if($ins->execute()){
    echo json_encode(['success'=>true]);
}else{
    echo json_encode(['success'=>false,'message'=>'DB error']);
}
 
