<?php
require_once '../grplib-php/init.php';
require_once '../grp_act-php/lib/htm.php';

if(empty($_SESSION['pid'])) {
    header("Location: /act/login");
} else {
    $stmt = $mysql->prepare("SELECT `user_id` FROM `people` WHERE `pid` = ?");
    $stmt->bind_param("i", $_SESSION["pid"]);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows == 0){
        exit("fail!!!");
    }
    $row = $res->fetch_assoc();
    try {
        $dataa = file_get_contents("https://nnidlt.murilo.eu.org/api.php?env=production&user_id=".$row["user_id"]);
    } catch(Exception $ex){
        printErr(1024405, 'Failed to update Mii data.', '/act/'); exit(); 
    }
    if(!$dataa){
        printErr(1024402, 'Failed to update Mii data.', '/act/'); exit(); 
    }
    $data = json_decode($dataa, true);
    $mii = $data["data"]; // LOL!
    $stmt = $mysql->prepare("UPDATE `people` SET `mii` = ? WHERE `pid` = ?");
    $stmt->bind_param("si", $mii, $_SESSION["pid"]);
    $stmt->execute();
    if($stmt->error){
        printErr(1024500, 'Failed to update Mii data.', '/act/'); exit(); 
    } else {
        if($_SERVER["HTTP_HOST"] == "rvqcportal.rverse.club"){
            exit("Success! Please relaunch Miiverse.");
        }
        printErr(1024200, 'Mii data updated successfully.', '/act/'); exit(); 
    }
}
?>