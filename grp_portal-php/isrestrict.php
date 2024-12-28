<?php
// warning\/readonly
require_once '../grplib-php/init.php';
if(!isset($_SESSION["pid"])){
    die();
}
$mee = $mysql->query('SELECT * FROM bans WHERE reciever = "'.$_SESSION["pid"].'" LIMIT 1');
if($mee->num_rows !== 0){
    header("Location: /warning/readonly");
    exit();
} else {
$parampack = explode("\\", base64_decode($_SERVER["HTTP_X_NINTENDO_PARAMPACK"]));
$stmt = $mysql->prepare("SELECT * FROM `titles` WHERE `olive_title_id_usa` = ?");
$stmt->bind_param("s", $parampack[2]);
$stmt->execute();
$res = $stmt->get_result();
if($res->num_rows == 0){
    $stmt = $mysql->prepare("SELECT * FROM `titles` WHERE `olive_title_id_eur` = ?");
    $stmt->bind_param("s", $parampack[2]);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows == 0){
        $stmt = $mysql->prepare("SELECT * FROM `titles` WHERE `olive_title_id_jpn` = ?");
        $stmt->bind_param("s", $parampack[2]);
        $stmt->execute();
        $res = $stmt->get_result();
        if($res->num_rows == 0){
            header("Location: /communities");
            exit();
        }
    }
}
$row = $res->fetch_assoc();
header("Location: /titles/".$row["olive_title_id"]."/".$row["olive_community_id"]);
    exit();
}
?>