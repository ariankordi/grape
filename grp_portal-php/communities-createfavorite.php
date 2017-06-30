<?php
require_once '../grplib-php/init.php';
if($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_GET['olive_community_id'])) {
	include_once '404.php';
     exit();
}

$search_community = $mysql->query('SELECT * FROM communities WHERE communities.olive_community_id = "'.$mysql->real_escape_string($_GET['olive_community_id']).'" LIMIT 1');

if($search_community->num_rows == 0) { http_response_code(404); header('Content-Type: application/json');
print json_encode(array('success' => 0, 'errors' => [], 'code' => 404));  exit(); }

if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403));  exit(); }
$community = $search_community->fetch_assoc();

if(isset($_GET['delete'])) {
$search_my_user_favorites_for_this = $mysql->query('SELECT * FROM favorites WHERE favorites.pid = "'.$_SESSION['pid'].'" AND favorites.community_id = "'.$community['community_id'].'"');
if($search_my_user_favorites_for_this->num_rows == 0) { http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 400));  exit(); }

$result_create_favorite = $mysql->query('DELETE FROM favorites WHERE favorites.pid = "'.$_SESSION['pid'].'" AND favorites.community_id = "'.$community['community_id'].'"');
        if(!$result_create_favorite) {
http_response_code(500);
header('Content-Type: application/json');
print json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500)); }
        else {
header('Content-Type: application/json'); print 
json_encode(array('success' => 1)); }
}

else {
$search_my_user_favorites_for_this = $mysql->query('SELECT * FROM favorites WHERE favorites.pid = "'.$_SESSION['pid'].'" AND favorites.community_id = "'.$community['community_id'].'"');
if($search_my_user_favorites_for_this->num_rows != 0) { http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 400));  exit(); }
	
else {
$result_create_favorite = $mysql->query('INSERT INTO favorites (pid, community_id) VALUES ("'.$_SESSION['pid'].'", "'.$community['community_id'].'")');
        if(!$result_create_favorite) {
http_response_code(500);
header('Content-Type: application/json');
print json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500)); }
        else {
header('Content-Type: application/json'); print 
json_encode(array('success' => 1)); }
}
}

