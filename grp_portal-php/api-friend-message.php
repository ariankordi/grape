<?php
http_response_code(403);
exit();
header('Server: nginx');
header('Content-Type: text/html; charset=utf-8');
header('Content-Length: 14');
//header('X-Dispatch: Olive::Web::API::V1::Post-search_by_topic');
header('Connection: Keep-alive');

require_once '../grplib-php/init.php';

if(empty($_SESSION["pid"])) {
    http_response_code(401);
    exit();
}
if($_SERVER["REQUEST_METHOD"] !== "POST"){
    http_response_code(401);
    exit();
}
$pid = $_SESSION["pid"];
$topid = $_POST["message_to_pid"];
$search_conversation = $mysql->query('SELECT * FROM conversations WHERE recipient = "'.$mysql->real_escape_string($topid).'" AND sender = "'.$pid.'" LIMIT 1');
if(!$search_conversation){ http_response_code(403); exit("bad query"); }
if($search_conversation->num_rows == 0) { 
    $search_conversation = $mysql->query('SELECT * FROM conversations WHERE sender = "'.$mysql->real_escape_string($topid).'" AND recipient = "'.$pid.'" LIMIT 1');
if(!$search_conversation){ http_response_code(403); exit("bad query"); }
if($search_conversation->num_rows == 0) { http_response_code(403); exit("bad convo"); }
}
$me = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION["pid"].'"')->fetch_assoc();


require_once '../grplib-php/user-helper.php';
$relationship = getFriendRelationship($pid, $mysql->real_escape_string($topid));
if(!$relationship) { plainErr(403, '403 Forbidden');  exit(); }

require_once '../grplib-php/post-helper.php';
$is_post_valid = postValid($me, 'upload');
if($is_post_valid != 'ok') {
if($is_post_valid == 'blank') {
    http_response_code(500);
    die("blank");
$error_code[] = 1515001; }
elseif($is_post_valid == 'max') {
    http_response_code(500);
    die("max");
$error_code[] = 1515002; }
} if(empty($error_code)) {

require_once '../grplib-php/olv-url-enc.php';
$gen_olive_url = genURL();
$conversation_id = $search_conversation->fetch_assoc()['conversation_id'];


$stmt_message = $mysql->prepare('INSERT INTO messages(conversation_id, id, pid, feeling_id, platform_id, body, is_spoiler, has_read, created_from) VALUES(?, ?, ?, ?, "1", ?, ?, "0", ?)');
if(!$stmt_message){
    http_response_code(500);
    exit("fail");
}
$scrnst = (empty($_POST['screenshot']) ? '' : $_POST['screenshot']); $isspr = (empty($_POST['is_spoiler']) ? 0 : $_POST['is_spoiler']);
$body = $_POST["body"];
$feeling_id = $_POST["feeling_id"];
$pid = $_SESSION["pid"];
$omfg = 0;
$addr = $_SERVER["REMOTE_ADDR"];
$stmt_message->bind_param('isiisis', $conversation_id, $gen_olive_url, $pid, $feeling_id, $body, $omfg, $addr);
$stmt_message->execute();
if($stmt_message->error){
    http_response_code(500);
    exit('{"status": 0}');
}
if(!empty($relationship)) {
    $updateFM = $mysql->query('UPDATE friend_relationships SET updated = NOW() WHERE relationship_id = "'.$relationship['relationship_id'].'"');
}
exit('{"success": 1}');
} else {
    http_response_code(500);
    die($error_message);
}
?>