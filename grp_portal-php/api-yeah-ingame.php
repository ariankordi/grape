<?php
http_response_code(403);
exit();
header('Content-Type: text/html; charset=utf-8');
header('Content-Length: 14');
header('Connection: close');
header('X-Dispatch: Olive::Web::API::V1::Post-create');
if(!isset($_SERVER["HTTP_X_NINTENDO_SERVICETOKEN"])){
    http_response_code(404);
    exit();
}
$plsnoprint = true;
require_once '../grplib-php/init.php';
require_once '../grplib-php/community-helper.php';
require_once '../grplib-php/miitoo.php';
if(!isset($_GET["id"])){
    http_response_code(404);
    exit();
}
if(empty($_SESSION["pid"])){
    http_response_code(404);
    exit();
}
$canmiitoo = miitooCan($_SESSION["pid"], $_GET['id'], 'posts'); 
$my_empathy_added = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$mysql->real_escape_string($_GET['id']).'" AND empathies.pid = "'.$_SESSION["pid"].'" LIMIT 1')->num_rows == 1;
$my_post = $mysql->query('SELECT * FROM posts WHERE pid = "'.$mysql->real_escape_string($_SESSION['id']).'" AND id = "'.$mysql->real_escape_string($_GET['id']).'" LIMIT 1')->num_rows == 1;
if($my_empathy_added == true){
    exit('{"success": 1}');
}
if($my_post){
    http_status_code(403);
    exit();
}
$search_post = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.(isset($_GET['id']) ? $mysql->real_escape_string($_GET['id']) : 'a').'"');
miitooAdd('posts', true);
exit('{"success": 1}');
?>