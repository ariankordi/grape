<?php
require_once '../grplib-php/init.php';
if($_SERVER['REQUEST_METHOD'] != 'POST') {
include_once '404alli.php'; }

$search_community = $mysql->query('SELECT * FROM communities WHERE communities.community_id = "'.$mysql->real_escape_string($_POST['community_id']).'" LIMIT 1');

if($search_community->num_rows == 0) { http_response_code(404); header('Content-Type: application/json; charset=utf-8');
print json_encode(array('success' => 0, 'errors' => [], 'code' => 404)); grpfinish($mysql); exit(); }

if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403)); grpfinish($mysql); exit(); }
require_once '../grplib-php/community-helper.php';
require_once '../grplib-php/post-helper.php';

$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
$community = $search_community->fetch_assoc();

if(!postPermission($user, $community)) {
http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 400)); grpfinish($mysql); exit(); }
$is_post_valid = postValid($user, 'upload');
$fastpost = ($mysql->query('SELECT posts.pid, posts.created_at FROM posts WHERE posts.pid = "'.$user['pid'].'" AND posts.created_at > NOW() - '.(isset($grp_config_max_postbuffertime) ? $grp_config_max_postbuffertime : '10').' ORDER BY posts.created_at DESC LIMIT 5')->num_rows != 0 ? true : false);
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
elseif($is_post_valid == 'invalid-screenshot') {
$error_message[] = 'The screenshot you have specified is not valid.';
$error_code[] = 1515005; }
}
if(!empty($error_code)) {
http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [array(
'message' => $error_message[0],
'error_code' => $error_code[0]
)], 'code' => 400)); grpfinish($mysql); exit();
}

if(isset($_POST['screenshot']) && strlen($_POST['screenshot']) > 1) {
$ch_imgu = curl_init();
curl_setopt($ch_imgu, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
curl_setopt($ch_imgu, CURLOPT_POST, TRUE);
curl_setopt($ch_imgu, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch_imgu, CURLOPT_HTTPHEADER, array( 'Authorization: Client-ID ef7417cca96d79f' ));
curl_setopt($ch_imgu, CURLOPT_POSTFIELDS, array( 'image' => $_POST['screenshot'] ));
$reply_imgu = curl_exec($ch_imgu);
curl_close($ch_imgu);

$reply_imgu2 = json_decode($reply_imgu, true);
if($reply_imgu2['success'] == false) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8');
print json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1511000 + $reply_imgu2['status'])], 'code' => 500));
exit();
} else {
$result_imgu = 'https://i.imgur.com/'.$reply_imgu2['data']['id'].'.png'; }
}

require_once '../grplib-php/olv-url-enc.php';
$gen_olive_url = genURL();

if(empty($_POST['feeling_id']) || strval($_POST['feeling_id']) >= 6) { $_POST['feeling_id'] = 0; } 

$createpost = $mysql->query('INSERT INTO posts(id, pid, _post_type, feeling_id, platform_id, body, url, screenshot, community_id, is_spoiler) VALUES (
"'.$gen_olive_url.'", 
"'.$_SESSION['pid'].'",
"'.(!empty($_POST['_post_type']) ? $mysql->real_escape_string($_POST['_post_type']) : 'body').'",
"'.(!empty($_POST['feeling_id']) && is_numeric($_POST['feeling_id']) ? $mysql->real_escape_string($_POST['feeling_id']) : 0).'",
"2",
"'.$mysql->real_escape_string($_POST['body']).'",
"'.(!empty($_POST['url']) ? $mysql->real_escape_string($_POST['url']) : null).'",
"'.(!empty($_POST['screenshot']) ? $result_imgu : null).'",
"'.$mysql->real_escape_string($_POST['community_id']).'",
"'.(!empty($_POST['is_spoiler']) ? $mysql->real_escape_string($_POST['is_spoiler']) : 0).'"
)');

if(!$createpost) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8');
print json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
} else {
# Success, print post.
$search_post_created = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$gen_olive_url.'" LIMIT 1')->fetch_assoc();
require_once 'lib/htm.php';
require_once 'lib/htmCommunity.php';
print printPost($search_post_created);
}
# Finished, clear sys resources!
grpfinish($mysql);