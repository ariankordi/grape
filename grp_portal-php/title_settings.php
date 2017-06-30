<?php
require_once '../grplib-php/init.php';
if($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_GET['title_id'])) {
	include_once '404.php';
     exit();
}

$search_title = $mysql->query('SELECT olive_title_id FROM titles WHERE titles.olive_title_id = "'.$mysql->real_escape_string($_GET['title_id']).'" LIMIT 1');

if($search_title->num_rows == 0) { http_response_code(404); header('Content-Type: application/json');
print json_encode(array('success' => 0, 'errors' => [], 'code' => 404));  exit(); }

if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403));  exit(); }

if(!isset($_POST['viewable_post']) || !is_numeric($_POST['viewable_post']) || $_POST['viewable_post'] >= 3) {
http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 400));  exit(); }

$title = $search_title->fetch_assoc();

$search_settings = $mysql->query('SELECT * FROM settings_title WHERE settings_title.pid = "'.$_SESSION['pid'].'" AND settings_title.olive_title_id = "'.$title['olive_title_id'].'" LIMIT 1');

if($search_settings->num_rows != 0) {
$set = $mysql->query('UPDATE settings_title SET value = "'.$mysql->real_escape_string($_POST['viewable_post']).'" WHERE pid = "'.$_SESSION['pid'].'" AND olive_title_id = "'.$title['olive_title_id'].'"');
if(!$set) { json500();  exit(); }
jsonSuccess();
} else {
$set_create = $mysql->query('INSERT INTO settings_title (pid, value, olive_title_id) VALUES ("'.$_SESSION['pid'].'", "'.$mysql->real_escape_string($_POST['viewable_post']).'", "'.$title['olive_title_id'].'")');
if(!$set_create) { json500();  exit(); }
jsonSuccess();
}

