<?php
require_once '../grplib-php/init.php';
if($_SERVER['REQUEST_METHOD'] != 'POST') {
include_once '404.php';  exit(); }

$search_community = prepared('SELECT * FROM communities WHERE communities.community_id = ? AND (communities.hidden != "1" OR communities.hidden IS NULL) LIMIT 1', [$_POST['community_id'] ?? null]);

if($search_community->num_rows == 0) { http_response_code(404); header('Content-Type: application/json');
print json_encode(array('success' => 0, 'errors' => [], 'code' => 404));  exit(); }

if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403));  exit(); }
require_once '../grplib-php/community-helper.php';
require_once '../grplib-php/post-helper.php';

$community = $search_community->fetch_assoc();

if(!postPermission($me, $community)) {
http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 400));  exit(); }
$is_post_valid = postValid($me, 'url');
$fastpost = ($mysql->query('SELECT posts.pid, posts.created_at FROM posts WHERE posts.pid = "'.$me['pid'].'" AND posts.created_at > NOW() - '.(isset($grp_config_max_postbuffertime) ? $grp_config_max_postbuffertime : '10').' ORDER BY posts.created_at DESC LIMIT 5')->num_rows != 0 ? true : false);
if($is_post_valid != 'ok' || $fastpost == true) {
if($fastpost == true) {
$error_message[] = 'Multiple posts cannot be made in such a short period of time. Please try posting again later.';
$error_code[] = 1515918; }
if($is_post_valid == 'blank') {
$error_message[] = 'The content you have entered is blank.
Please enter content into your post.';
$error_code[] = 1515001; }
elseif($is_post_valid == 'max') {
$error_message[] = 'You have exceeded the amount of characters that you can send.';
$error_code[] = 1515002; }
elseif($is_post_valid == 'min') {
$error_message[] = 'The URL you have specified is too short.';
$error_code[] = 1515004; }
elseif($is_post_valid == 'nohttp') {
$error_message[] = 'The URL you have specified is not of HTTPS.';
$error_code[] = 1515003; }
elseif($is_post_valid == 'nossl') {
$error_message[] = 'The URL you have specified is not of HTTP or HTTPS.';
$error_code[] = 1515003; }
elseif($is_post_valid == 'invalid') {
$error_message[] = 'The URL you have specified is not valid.';
$error_code[] = 1515005; }
elseif($is_post_valid == 'invalid_screenshot') {
$error_message[] = 'The screenshot you have specified is not valid.';
$error_code[] = 1515005; }
}
if(!empty($error_code)) {
http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [array(
'message' => $error_message[0],
'error_code' => $error_code[0]
)], 'code' => 400));  exit();
}

require_once '../grplib-php/olv-url-enc.php';
$gen_olive_url = genURL();

if(empty($_POST['feeling_id']) || strval($_POST['feeling_id']) >= 6) { $_POST['feeling_id'] = 0; } 

$createpost = $mysql->query('INSERT INTO posts(id, pid, _post_type, feeling_id, platform_id, body, url, screenshot, community_id, is_spoiler, created_from) VALUES (
"'.$gen_olive_url.'", 
"'.$_SESSION['pid'].'",
"'.(!empty($_POST['_post_type']) ? $mysql->real_escape_string($_POST['_post_type']) : 'body').'",
"'.(!empty($_POST['feeling_id']) && is_numeric($_POST['feeling_id']) ? $mysql->real_escape_string($_POST['feeling_id']) : 0).'",
"1",
"'.$mysql->real_escape_string($_POST['body']).'",
"'.(!empty($_POST['url']) ? $mysql->real_escape_string($_POST['url']) : null).'",
"'.(!empty($_POST['screenshot']) ? $mysql->real_escape_string($_POST['screenshot']) : null).'",
"'.$mysql->real_escape_string($_POST['community_id']).'",
"'.(!empty($_POST['is_spoiler']) ? $mysql->real_escape_string($_POST['is_spoiler']) : 0).'",
"'.$mysql->real_escape_string($_SERVER['REMOTE_ADDR']).'"
)');

if(!$createpost) {
http_response_code(500);
header('Content-Type: application/json');
print json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
} else {
# Success, print post.
require_once 'lib/htmCommunity.php';
require_once '../grplib-php/community-helper.php';
$search_post_created = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$gen_olive_url.'" LIMIT 1')->fetch_assoc();
printPost($search_post_created, false, false, false);
}
# Finished, clear sys resources!
