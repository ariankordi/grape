<?php
require_once '../grplib-php/init.php';

if(empty($_SESSION["pid"])){
    die("no");
}
if(!empty($_GET['theme'])) {

if($_GET['theme'] == 'blueberry' || $_GET['theme'] == 'grape' || $_GET['theme'] == 'cherry' || $_GET['theme'] == 'orange' || $_GET['theme'] == 'olive') {
$sessionId = substr(bin2hex(base64_decode($_SERVER['HTTP_X_NINTENDO_SERVICETOKEN'])), 0, 64);
$stmt = $mysql->prepare("UPDATE `console_auth` SET `theme` = ? WHERE `long_id` = ?;");
$stmt->bind_param("ss", $_GET["theme"], $sessionId);
$stmt->execute();
if($stmt->error){
    header("refresh:5;url=/");
    exit("failed to set theme.");
}
setcookie( "grp_theme", htmlspecialchars($_GET['theme']), strtotime('+10 days') );
}

else {
setcookie( "grp_theme", 'olive', strtotime('+10 days') );
}
    header("Location: ".(!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/communities'));
}
else {
include_once '404.php';
exit(); }

