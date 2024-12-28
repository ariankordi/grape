<?php
if(!isset($_SERVER["HTTP_X_NINTENDO_SERVICETOKEN"])){
    http_response_code(403);
    exit();
}
    header("Content-Type: application/json");
    $activate = true;
    require_once '../../grplib-php/init.php';
    $stmt = $mysql->prepare("UPDATE `people` SET `done_setup` = 1 WHERE `pid` = ?");
    $stmt->bind_param("i", $_SESSION["pid"]);
    $stmt->execute();
    if($stmt->error){
        http_response_code(500);
        exit();
    }
    header("Location: /titles/show");
    exit('');
?>