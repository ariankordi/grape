<?php
header('Content-Type: text/html; charset=utf-8');
header('Connection: close');
header('X-Dispatch: Olive::Web::API::V1::Post-create');

function paintingProcess($tgadata) {
    require_once 'tga.php';
    $painting_name = rand(100000,999999);
    $painting_path1 = "img/ugc/".$_SESSION["pid"]."-".$painting_name.'.tga';
    $painting_path2 = "img/ugc/".$_SESSION["pid"]."-".$painting_name.'.png';
    $painting_url = "https://rvqcportal.rverse.club/img/ugc/".$_SESSION["pid"]."-".$painting_name.'.png';
    $painting = base64_decode($tgadata);
    $painting = zlib_decode($painting);
    if($painting){
        $ge = file_put_contents($painting_path1, $painting);
        if(!file_get_contents($painting_path1)){
            http_response_code(500);
            exit("failed to save painting");
        }
        $im = imagecreatefromtga2($painting_path1);
        imagepng($im, $painting_path2);
        unlink($painting_path1);
    }
    return $painting_url;
}

if(!isset($_SERVER["HTTP_X_NINTENDO_SERVICETOKEN"])){
    exit('{"success": 0}');
}
require_once '../grplib-php/init.php';
require_once '../grplib-php/olv-url-enc.php';
$gen_olive_url = genURL();
if(empty($_SESSION["pid"])){
    http_response_code(404);
    exit();
}
if(empty(trim($_POST["body"])) AND empty($_POST["painting"])){
    http_response_code(404);
    exit();
}
if(!empty($_POST["body"]) && strlen($_POST["body"]) > 100){
    http_response_code(404);
    exit();
}
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
            http_response_code(403);
            $xml = new SimpleXMLElement('<result/>');
            $xml->addChild('has_error', 1);
            $xml->addChild('error', "no_title");
            $xml->addChild('version', 1);
            print($xml->asXML());
            exit();
        }
    }
}

$dayta = $res->fetch_assoc();
$title_id = $dayta["olive_title_id"];
$community_id = $dayta["olive_community_id"];
if($community_id != 13){
    http_response_code(403);
    exit();
}
$useappdata = $dayta["require_app_data"];
$cansend = $dayta["can_in_game_post"];
if($cansend !== 1){
    http_response_code(403);
    $xml = new SimpleXMLElement('<result/>');
    $xml->addChild('has_error', 1);
    $xml->addChild('error', "no_title");
    $xml->addChild('version', 1);
    print($xml->asXML());
    exit();
}
if($useappdata == 1 AND empty($_POST["app_data"])){
    http_response_code(500);
    exit("can you like not");
}
if($useappdata == 1){
    $sql = "SELECT id FROM app_data WHERE pid = '".$mysql->real_escape_string($_SESSION["pid"])."' AND title_id = '".$mysql->real_escape_string($title_id)."'";
    $apppdata = $mysql->prepare($sql);
if(!$apppdata){
    exit("bad sql query");
}
//$apppdata->bind_param("ii", $_SESSION["pid"], $parampack[2]);
$apppdata->execute();
if($apppdata->error){
    http_response_code(500);
    exit($apppdata->error);
}
$appdata2 = $apppdata->get_result();
if($appdata2->num_rows == 0){
    $appdata = $mysql->query('INSERT INTO app_data(pid, app_data, title_id) VALUES (
        "'.$_SESSION["pid"].'",
        "'.$_POST["app_data"].'",
        "'.$title_id.'"
    )');
} else {
    $appdata = $mysql->query('UPDATE `app_data` SET `app_data` = "'.$mysql->real_escape_string($_POST["app_data"]).'" WHERE `pid` = "'.$mysql->real_escape_string($_SESSION["pid"]).'" AND `title_id` = "'.$mysql->real_escape_string($parampack[2]).'";');
    if(!$appdata){
        http_response_code(500);
        exit('fail2');
    }
if(!$appdata){
    http_response_code(500);
    exit('{"success": 0}');
}
}
}

$posttype = "body";
if(!empty($_POST["url"])){
    if($me["privilege"] < 2){
        http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [array(
            'message' => "no",
            'error_code' => 9999999
            )], 'code' => 400));  exit();
    }
}
if(isset($_POST["painting"])){
    /*
    http_response_code(500);
    exit("no paintings");
    */
    $painting = $_POST["painting"];
    $_POST["clean"] = trim(preg_replace('/\0/', '', preg_replace('/\r?\n|\r/', '', $painting)));
    $body = paintingProcess($_POST["clean"]);
    if(!$body){
        http_response_code(500);
        exit("decode fail");
    }
    $posttype = "artwork";
}
if(!isset($_POST["feeling_id"])){
    http_response_code(500);
    exit("what are you doing lad");
}
if(isset($_POST["screenshot"])){
    $painting_name = rand(100000,999999).'.jpg';
    $painting_path = "/home/ubuntu/botv2/grapeqc/grp_portal-php/img/ugc/".$_SESSION["pid"]."-".$painting_name;
    $screenshot = "https://rvqcportal.rverse.club/img/ugc/".$_SESSION["pid"]."-".$painting_name;
    $screeenshot = substr($_POST["screenshot"], 0, -1);
    $screeenshot = base64_decode($screeenshot);
    $ge = file_put_contents($painting_path, $screeenshot);
    if(!file_get_contents($painting_path)){
        http_response_code(500);
        exit("failed to save screenshot");
    }
}
//$rawData = file_get_contents("php://input");
$searchKeys = "";
/*
if(!is_array($searchKeys)){
    http_response_code(500);
    exit("NOARRAY");
}
*/
if(!empty($searchKeys[0])){
    $search_key1 = $searchKeys[0];
} else {
    $search_key1 = "none";
}
if(!empty($searchKeys[1])){
    $search_key2 = $searchKeys[1];
} else {
    $search_key2 = "none";
}
if(!empty($searchKeys[2])){
    $search_key3 = $searchKeys[2];
} else {
    $search_key3 = "none";
}
if(!empty($searchKeys[3])){
    $search_key4 = $searchKeys[3];
} else {
    $search_key4 = "none";
}
if(!empty($searchKeys[4])){
    $search_key5 = $searchKeys[4];
} else {
    $search_key5 = "none";
}
$createpost = $mysql->query('INSERT INTO posts(id, pid, _post_type, feeling_id, platform_id, body, painting_encoded, url, screenshot, community_id, is_spoiler, is_autopost, created_from, topic_tag, search_key, search_key2, search_key3, search_key4, search_key5, is_ingame, can_show_ingame) VALUES (
    "'.$gen_olive_url.'", 
    "'.$_SESSION["pid"].'",
    "'.$posttype.'",
    "'.(!empty($_POST['feeling_id']) && is_numeric($_POST['feeling_id']) ? $mysql->real_escape_string($_POST['feeling_id']) : 0).'",
    "1",
    "'.(!empty($_POST['body']) ? $mysql->real_escape_string($_POST['body']) : $mysql->real_escape_string($body)).'",
    "'.(!empty($_POST["clean"]) ? $_POST["clean"] : null).'",
    null,
    "'.(!empty($_POST['screenshot']) ? $mysql->real_escape_string($screenshot) : '').'",
    "'.$mysql->real_escape_string($community_id).'",
    "'.(!empty($_POST['is_spoiler']) ? $mysql->real_escape_string($_POST['is_spoiler']) : 0).'",
    "'.(!empty($_POST['is_autopost']) ? $mysql->real_escape_string($_POST['is_autopost']) : 0).'",
    "'.$mysql->real_escape_string($_SERVER['REMOTE_ADDR']).'",
    "'.(!empty($_POST['topic_tag']) ? $mysql->real_escape_string($_POST['topic_tag']) : null).'",
    "'.$mysql->real_escape_string($search_key1).'",
    "'.$mysql->real_escape_string($search_key2).'",
    "'.$mysql->real_escape_string($search_key3).'",
    "'.$mysql->real_escape_string($search_key4).'",
    "'.$mysql->real_escape_string($search_key5).'",
    "1",
    "1"
)');
if(!$createpost){
    http_response_code(500);
    exit($createpost->error);
} else {
    http_response_code(200);
    header('Content-Length: 14');
    exit('{"success": 1}');
}
?>