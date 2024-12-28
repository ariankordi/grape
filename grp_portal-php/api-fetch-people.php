<?php
header('Content-Type: application/xml; charset=utf-8');
//header('Content-Length: 14'); - do we actually need this? let's find out!!
//header('Connection: close');
exit('
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <has_error>0</has_error>
  <version>1</version>
  <expire>2023-10-05 19:33:05</expire>
  <request_name>people</request_name>
  <people></people>
</result>
');
if(!isset($_SERVER["HTTP_X_NINTENDO_SERVICETOKEN"])){
    http_response_code(404);
    exit();
}
if(!isset($_GET["language_id"]) OR !isset($_GET["limit"]) OR !isset($_GET["search_key"])){
    http_response_code(404);
    exit();
}
require_once '../grplib-php/init.php';
if(empty($_SESSION["pid"])){
    http_response_code(404);
    exit();
}
$stmt = $mysql->prepare("SELECT * FROM `posts` WHERE `community_id` = 1 ORDER BY `id` DESC LIMIT ".$mysql->real_escape_string($_GET["limit"])); // sorry for the shitty code php is being Retarded
$stmt->execute();
$res2 = $stmt2->get_result();
$res = $stmt->get_result();
if($res->num_rows == 0){
    $xml = new SimpleXMLElement('<result/>');
    $xml->addChild('has_error', 1);
    $xml->addChild('version', 1);
    print($xml->asXML());
    exit();
}
$xml = new SimpleXMLElement('<result/>');
$xml->addChild('has_error', 0);
$xml->addChild('version', 1);
$xml->addChild('request_name', 'people');
$topic = $xml->addChild('topic');
$topic->addChild('community_id', 1);
$posts = $xml->addChild('posts');
$i = 0;
while($row = $res->fetch_assoc() && $row2 = $res2->fetch_assoc()){
    $empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$row['id'].'"');
    $stmt = $mysql->prepare("SELECT * FROM `people` WHERE `pid` = ?");
    $stmt->bind_param("s", $row["pid"]);
    $stmt->execute();
    $ress = $stmt->get_result();
    if($ress->num_rows == 0){
        goto skipxml;
    }
    $roww = $ress->fetch_assoc();
    if(empty($roww["mii"])){
        goto skipxml;
    }
    if(empty($row["body"])){
        goto skipxml;
    }
    $mii = getMii($roww, $row["feeling_id"]);
    $in = "post".$i;
    // $$(var name) basically sets the name of a variable
    // to the value of the variable you inputted
    // at the $$ thing
    $$in = $posts->addChild('post');
    //$$in->addChild('app_data', ''); ??? might be mii data will figure out later
    $$in->addChild('body', htmlspecialchars($row["body"]));
    $$in->addChild('community_id', 1);
    $$in->addChild('country_id', 49); // we don't have this
    $$in->addChild('created_at', $row["created_at"]);
    $$in->addChild('feeling_id', $row["feeling_id"]);
    $$in->addChild('id', $row["id"]);
    $$in->addChild('is_autopost', 0); // was this ever actually used
    $$in->addChild('is_community_private_autopost', 0);
    $$in->addChild('is_spoiler', $row["is_spoiler"]);
    $$in->addChild('is_app_jumpable', 0);
    $$in->addChild('empathy_count', $empathies->num_rows);
    $$in->addChild('language_id', 1);
    $$in->addChild('mii', $roww["mii"]);
    $$in->addChild('mii_face_url', $mii["output"]);
    $$in->addChild('number', 0); // okay what the fuck
    $$in->addChild('pid', $row["pid"]);
    $$in->addChild('platform_id', 1); // no 3ds yet so,,,
    $$in->addChild('region_id', 4); // i'll figure out regions.. eventually.
    $$in->addChild('reply_count', 99); // cba to do this rn
    $$in->addChild('screen_name', $roww["screen_name"]);
    $$in->addChild('title_id', '0005000010102000');
    skipxml:
}
print($xml->asXML());
exit();
/*
for ($i = 1; $i <= 8; ++$i) {
    $track = $xml->addChild('track');
    $track->addChild('path', "song$i.mp3");
    $track->addChild('title', "Track $i - Track Title");
}

print($xml->asXML());
*/
?>