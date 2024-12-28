<?php
// Set headers
header('Server: nginx');
header('Content-Type: application/xml');
require_once '../grplib-php/init.php';
require_once '../grplib-php/user-helper.php';
$person = $mysql->query("SELECT * FROM `people` WHERE `pid` = '".$mysql->real_escape_string($_GET["pid"])."'")->num_rows;
if($person == 0){
    http_response_code(404);
    exit('');
}
$updates = getUpdates($_GET["pid"]);
if($updates['news'] != 0 || $updates['friend_requests'] != 0 || $updates['messages'] != 0){
    http_response_code(200);
    exit('</result>');
} else {
    http_response_code(404);
    exit('');
}
?>