<?php
require_once '../grplib-php/init.php';
if($_SERVER['REQUEST_METHOD'] != 'POST') {
include_once '404.php';  exit(); }
function errorout($message){
    http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [array(
        'message' => $message,
        'error_code' => 4204200
        )], 'code' => 400));  exit();
}

$search_community = prepared('SELECT * FROM communities WHERE communities.community_id = ? AND communities.type = "5" OR (communities.hidden != "1" OR communities.hidden IS NULL) LIMIT 1', [$_POST['community_id'] ?? null]);
$search_communityy = $mysql->prepare('SELECT * FROM titles WHERE olive_community_id = "'.$mysql->real_escape_string($_POST["community_id"]).'" LIMIT 1');
$search_communityy->execute();
if($search_communityy->error){
    die("oops");
}
if($search_community->num_rows == 0) { http_response_code(404); header('Content-Type: application/json');
print json_encode(array('success' => 0, 'errors' => [], 'code' => 404));  exit(); }
$ree = $search_communityy->get_result();
$row0 = $ree->fetch_assoc();

if(empty($_SESSION["pid"])) {
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
/*
if($_POST["community_id"] == 2 && $_SESSION["pid"] !== 1799999999 OR 1738406060 OR 1738406070){
    exit('{"success":0,"errors":[{"error_code":4206969,"message":"This is the announcements community..."}]}');
}
*/
$posttype = "body";
$body = $_POST["body"];
if(!empty($_POST["url"])){
    if($me["privilege"] < 2){
        http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [array(
            'message' => "no",
            'error_code' => 9999999
            )], 'code' => 400));  exit();
    }
}
if(!empty($_POST["painting"])){
    if($_POST["painting"] == "Qk1SEwAAAAAAAJIAAAB8AAAAQAEAAHgAAAABAAEAAAAAAMASAAASCwAAEgsAAAIAAAACAAAAAAD/AAD/AAD/AAAAAAAA/0JHUnOPwvUoUbgeFR6F6wEzMzMTZmZmJmZmZgaZmZkJPQrXAyhcjzIAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAAAAAAAP///wD/////////////////////+P/////8f/////////////////////////////////////////////j//////H/////////////////////////////////////////////4//////x/////////////////////////////////////////////+P/////8f////////////////////////////////////////4A///h//////H////////////////////////////////////////4AH//4f/////x////////////////////////////////////////+Hx///H/////8f////////////////////////////////////////j8H//x//////H////////////////////////////////////////4wB//8f/////x////////////////////////////////////////+AAP//D//h//8f////////////////////////////////////////wPh//4//4f//H////////////////////////////////////////8H4f/+P/+H//x/////////////////////////////////////////h/D//j/////8f////////////////////////////////////////4f4f/4//////H/////////////////////////////////////////D/D/+P/////x/////////////////////////////////////////w/w//j/////8f////////////////////////////////////////+H+H/4//////H/////////////////////////////////////////w/g/+P/////x/////////////////////////////////////////8P+H/j//A//8f/////////////////////////////////////////h/h/4//wP//H/////////////////////////////////////////8f8P+P/4B//x//////////////////////////////////////////D/D/j/8Mf/8f/////////////////////////////////////////4f4f4/+HD//H/////////////////////////////////////////+D+H+P/h4//x//////////////////////////////////////////4fwfj/w+H/8f/wB//////////////////////////////////////+H+H4/8Ph//H/wAP//////////////////////////////////////w/w+H+H4P/x/wPA//////////////////////////////////////+H+Hx/h+D/8fwH4H//////////////////////////////////////x/w8f4Pgf/HwP/wf/////////////////////////////////////8P8HH8D4H/x4H/8D//////////////////////////////////////h/wx/A8A/8YP//4H/////////////////////////////////////8f8EfgPEP/EH///B//////////////////////////////////////D/gH4jxj/wH///8H/////////////////////////////////////4f8B+I8Y/8D////g//////////////////////////////////////D/gfCHGH/D/8B/8D/////////////////////////////////////4f8Hwhxx/w/+AP/gf/////////////////////////////////////D/h8cccf///Bg/+D/////////////////////////////////////4f//HHHH///g8H/wf/////////////////////////////////////D//hxhw///w/gf+D/////////////////////////////////////wf/48YeP//4P+D/4f/////////////////////////////////////D/+PCPj//4P/wf/A/////////////////////////////////////w//Dwj4//8H//B/4H////////////////////////////////////+D/x+A+P/+D//4H/h/////////////////////////////////////w/8fgPj//D///g/4D////////////////////////////////////+D/GAAAf/B///+D8AP////////////////////////////////////w/xAAAH/g////wPDD////////////////////////////////////+H8ADAB/w/////BB4f////////////////////////////////////w/Aggwf4f////4A/H////////////////////////////////////+HweA/D4P/////gfh/////////////////////////////////////w4Pgfw8H/////8Hwf///////////////////////////////////+AAAAB+EH//////wAP//////////////////////////////////+AAAAAAAAAf/////8AH//////////////////////////////////gAB+P//gAAAAH////////////////////////////////////////4B///////H4AB////////////////////////////////////////+P//////////4f////////////////////////////////////////j//////////+H////////////////////////////////////////4///////////j////////////////////////////////////////+P//////////4/////////////////////////////////////////j//////////8P////////////////////////////////////////4///////////D////////////////////////////////////////+P//////////x/////////////////////////////////////////j//////////8f////////////////////////////////////////4////wD/////H////////////////////////////////////////+P///4AP////x/////////////////////////////////////////h///wPAH///8f////////////////////////////////////////4f//4H8Af///H//////////////////////////////////////8ADH//wP/+AP//x//////////////////////////////////////8AAB//wH//4B//8YB////////////////////////////////////+D/Af/wP///8B//AAAP///////////////////////////////////j/8H/4H////gH/wHgA/////////////////////////////////8AA//x/8P/////gP8f/8H///////AAAAAAAAP////////////////+AAP/8f+H/////+A/H//x///////wAAAAAAAD////////////////+D////H+D8P///H8Bx//4f//////////////w/////////////////h////x/B/D///x/wEf/8AH///////////////////////////////4////8fB/x///8f/gH//AAP//////////////////////////////+H////HA/8f///H/8B///+B/////////////////////////////8AA////xg//H///x//4f///8H////////////////////////////8AAP///8Q//x///8f//H////h////////////////////////////8D//////Af/4f///H//x////8f///////////////////////////+D//////wP/+P///x//8f////H////////////////////////////D//////8H//j///8f//H////h////////////////////////////h///////H//4////H//x////w////////////////////////////4///////x///////x//8D///gf///////////////////////////+P/////8Af//////////AH//4D////////////////////////////j/////AAH//////////xAP/+AD////////H//////////////////4/////ADj//////////8eB///Af///////w//////////////////+P///wA/4///////////H8H///D///////+H//////////////////h///wA/8P//////////x/gf//w//////g/g//////////////////8P//AP//D//////////8f+B//+P/////4P+P//////////////////D/+AP//w/gAAAAAAAAAH/4H//j/////+H/j//////////////////4P4A///8AAAAAAAAAAAB//gf/4////j/w/////////////////////BgB////gAf///////////+D/+P///4f+P////////////////////4AH///////////////////4H/D////D/h/4D//////////////////Af////////////////////Afg/x//w/AP+A////////////////////////////////////////+Awf8f/+MAB/h/////////////////////////////////////////wAP/D//gA4f8f/////////////////////////////////////////wP/4//8D/D///////////////////////////////////////////+D/+P//A/4///////////////////////////////////////////////j//4f+H//////////////////////////////////////////////4f//H/x///////////////////////////////////////////////H//w/////////////////////////////////////////////////w//8H////////////////////////////////////////////////+P//x/////////////////////////////////////////////////h////////////////////////////////////////////////////8P////////////////////////////////////////////////////D////////////////////////////////////////////////////4////////////////////////////////////////////////////+P////////////////////////////////////////////////////j////////////////////////////////////////////////////4//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////8="){
        http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [array(
            'message' => "Logged attempt",
            'error_code' => 4204200
            )], 'code' => 400));  exit();
    }
    //errorout($_POST['painting']." <-- this is the music to when i'm walking to class");
    $painting = base64_decode($_POST['painting']);
    $num = rand(100000,999999);
    $painting_name = $_SESSION["pid"].'-'.$num.'.tga';
    if(file_put_contents('img/drawings/'.$painting_name, $painting)){
        $pathhh = "https://rvqcportal.rverse.club/img/drawings/".$painting_name;
        $posttype = "artwork";
        $body = $pathhh;
    } else {
        http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [array(
            'message' => "Failed to process drawing.",
            'error_code' => 1210211
            )], 'code' => 400));  exit();
    }
}
if(!empty($_POST["screenshot"])){
    if($row0["olive_community_id"] != '6' && $row0["can_screenshot"] !== 1 && $_SESSION["pid"] != "1738262487"){
        http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [array(
            'message' => "no screenshots >:(",
            'error_code' => 1210211
            )], 'code' => 400));  exit();
    }
    $parampack = explode("\\", base64_decode($_SERVER["HTTP_X_NINTENDO_PARAMPACK"]));
    if($row0["olive_community_id"] == 6 && $_SESSION["pid"] != "1738262487"){
        $stmt = $mysql->prepare("SELECT * FROM `titles` WHERE `olive_title_id_usa` = ? AND can_screenshot = 1");
$stmt->bind_param("s", $parampack[2]);
$stmt->execute();
$res = $stmt->get_result();
if($res->num_rows == 0){
    $stmt = $mysql->prepare("SELECT * FROM `titles` WHERE `olive_title_id_eur` = ? AND can_screenshot = 1");
    $stmt->bind_param("s", $parampack[2]);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows == 0){
        $stmt = $mysql->prepare("SELECT * FROM `titles` WHERE `olive_title_id_jpn` = ? AND can_screenshot = 1");
        $stmt->bind_param("s", $parampack[2]);
        $stmt->execute();
        $res = $stmt->get_result();
        if($res->num_rows == 0){
            http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [array(
                'message' => "Sorry, using screenshots from this title is not supported in the Activity Feed.",
                'error_code' => 1210211
                )], 'code' => 400));  exit();
        }
        }
    }
} else {
    if($parampack[2] !== $row0["olive_title_id_usa"] && $parampack[2] !== $row0["olive_title_id_eur"] && $parampack[2] !== $row0["olive_title_id_jpn"] && $_SESSION["pid"] != "1738262487"){
        if(!pleasebanme($_SESSION["pid"], "unauthorized portal access (detection via user fault :mocking: )")){
            http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [array(
                'message' => "Well well well. Congratulations. You made it into portal via your web browser! What a great accomplishment. Here is your award: A ban. Merry Christmas! Or not, if it's not christmas. - Well, it would have been, if the ban didn't fail. But it's been logged, so enjoy that.",
                'error_code' => 1210211
                )], 'code' => 400));  exit();
        }
        http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [array(
            'message' => "Well well well. Congratulations. You made it into portal via your web browser! What a great accomplishment. Here is your award: A ban. Merry Christmas! Or not, if it's not christmas.",
            'error_code' => 1210211
            )], 'code' => 400)); exit();
    }
    $screenie = base64_decode($_POST['screenshot']);
    if(!$screenie){
        http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [array(
            'message' => "oops",
            'error_code' => 9999999
            )], 'code' => 400));  exit();
    }
    $screenie_name = rand(100000,999999).'.png';
    file_put_contents('img/ugc/'.$_SESSION["pid"]."-".$screenie_name, $screenie);
    $_POST["screenshot"] = "https://rvqcportal.rverse.club/img/ugc/".$_SESSION["pid"]."-".$screenie_name;
}
}
$can = 0;
if($row0["can_in_game_post"] == 1){
    if(strlen($_POST["body"]) > 100){
        $can = 0;
    } else {
        $can = 1;
    }
}
$createpost = $mysql->query('INSERT INTO posts(id, pid, _post_type, feeling_id, platform_id, body, url, screenshot, community_id, is_spoiler, created_from, can_show_ingame) VALUES (
"'.$gen_olive_url.'", 
"'.$_SESSION["pid"].'",
"'.(!empty($posttype) ? $mysql->real_escape_string($posttype) : 'body').'",
"'.(!empty($_POST['feeling_id']) && is_numeric($_POST['feeling_id']) ? $mysql->real_escape_string($_POST['feeling_id']) : 0).'",
"1",
"'.$mysql->real_escape_string($body).'",
"'.(!empty($_POST['url']) ? $mysql->real_escape_string($_POST['url']) : null).'",
"'.(!empty($_POST['screenshot']) ? $mysql->real_escape_string($_POST['screenshot']) : null).'",
"'.$mysql->real_escape_string($_POST['community_id']).'",
"'.(!empty($_POST['is_spoiler']) ? $mysql->real_escape_string($_POST['is_spoiler']) : 0).'",
"'.$mysql->real_escape_string($_SERVER['REMOTE_ADDR']).'",
"'.$can.'"
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
