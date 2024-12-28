<?php
// Set headers
header('Server: nginx');
header('Content-Type: application/xml');
header('X-Dispatch: Olive::Web::API::V1::Post-search_by_topic');
header('Connection: Keep-alive');
//header('Content-Length: 336526');
//exit(file_get_contents("thing.txt"));


function isBase64Encoded($s) // can't believe that this isn't a function in php
    {
        if ((bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s) === false) {
            return false;
        }
        $decoded = base64_decode($s, true);
        if ($decoded === false) {
            return false;
        }
        $encoding = mb_detect_encoding($decoded);
        if (! in_array($encoding, ['UTF-8', 'ASCII'], true)) {
            return false;
        }
        return $decoded !== false && base64_encode($decoded) === $s;
    }

// Default checks
if(!isset($_SERVER["HTTP_X_NINTENDO_SERVICETOKEN"])){
    http_response_code(404);
    exit();
}
if(!isset($_GET["language_id"]) OR !isset($_GET["limit"])){
    http_response_code(404);
    exit();
}
require_once '../grplib-php/init.php';
if(empty($_SESSION["pid"])){
    http_response_code(404);
    exit();
}

// Parse parampack
$parampack = explode("\\", base64_decode($_SERVER["HTTP_X_NINTENDO_PARAMPACK"]));

// kind of ugly method to search for each title. usa, eur, and jpn. this makes all regions inclusive this way.
// how? well when it grabs the olive_title_id from the title_id of the game you're playing in the database,
// it can get the universal title id and community id displayed on the site,
// (where all posts are stored in one community instead of multiple) and there are no drops between regions.
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
            http_response_code(500);
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
// does the game require app_data for posts to work? 0 or a 1 integer value.
$useappdata = $dayta["require_app_data"];
// this value in the db is simply a 0 or a 1 integer value. it just determines if it allows the api to post to and deliver
// content from that community/title.
$cansend = $dayta["can_in_game_post"];
if($cansend !== 1){
    http_response_code(403); // important if the game decides not to parse the xml
    $xml = new SimpleXMLElement('<result/>');
    $xml->addChild('has_error', 1);
    $xml->addChild('error', "no_post");
    $xml->addChild('version', 1);
    print($xml->asXML());
    exit();
}
// actually select posts from the DB and order them by ID (newest)
/*
if(!empty($_GET["search_key"])){
    $search_key = $_GET["search_key"];
} else {
    $search_key = "none";
}
$search_key = "none";
$stmt = $mysql->prepare("SELECT * FROM `posts` WHERE `community_id` = ? AND can_show_ingame = 1 AND search_key = ? OR search_key2 = ? OR search_key3 = ? OR search_key4 = ? OR search_key5 = ? ORDER BY `tid` DESC LIMIT ".$mysql->real_escape_string($_GET["limit"]));
$stmt->bind_param("isssss", $community_id, $search_key, $search_key, $search_key, $search_key, $search_key);
*/
$stmt = $mysql->prepare("SELECT * FROM `posts` WHERE `community_id` = ? AND can_show_ingame = 1 ORDER BY `tid` DESC LIMIT ".$mysql->real_escape_string($_GET["limit"]));
$stmt->bind_param("i", $community_id);
$stmt->execute();
if($stmt->error){
        http_response_code(503);
        $xml = new SimpleXMLElement('<result/>');
        $xml->addChild('has_error', 1);
        $xml->addChild('error', "posts_err");
        $xml->addChild('version', 1);
        print($xml->asXML());
        exit();
}
$res = $stmt->get_result();
if($res->num_rows == 0){
    $xml = new SimpleXMLElement('<result/>');
    $xml->addChild('has_error', 0);
    $xml->addChild('version', 1);
    $xml->addChild('request_name', 'posts');
    $topic = $xml->addChild('topic');
    $topic->addChild('community_id', $community_id);
    $posts = $xml->addChild('posts');
    $$in = $posts->addChild('post');
    $$in->addChild('body', "There are no posts to display.");
    $$in->addChild('community_id', 1);
    $$in->addChild('created_at', "2017-08-30 01:40:59");
    $$in->addChild('feeling_id', 0);
    $$in->addChild('id', 1);
    $$in->addChild('is_autopost', 0);
    $$in->addChild('is_community_private_autopost', 0);
    $$in->addChild('is_spoiler', 0);
    $$in->addChild('is_app_jumpable', 0);
    $$in->addChild('empathy_count', 0);
    $$in->addChild('language_id', 1);
    $$in->addChild('number', 0);
    $$in->addChild('pid', 1);
    $$in->addChild('platform_id', 1);
    $$in->addChild('region_id', $parampack[8]);
    $$in->addChild('reply_count', 0);
    $$in->addChild('screen_name', "No posts");
    $$in->addChild('title_id', $title_id);
    $topic_tag = $$in->addChild('topic_tag');
    $topic_tag->addChild('name', "Topic");
    $topic_tag->addChild('title_id', $parampack[2]);
    $$in->addChild('mii', "AwEAQBs8xqsHR9PC3NyN94XEaBemLwAAVllEAGEAdgBpAGQAIABKAG8AYQBxAFBQAGBlAB9pRBgGJEUSgRJThg4AACkjUmlQRABhAHYAaQBkACAASgBvAGEAcQAAAOhf");
    $toxml = str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>', $xml->asXML());
    $dom = new \DOMDocument('1.0');
    $dom->preserveWhiteSpace = true;
    $dom->formatOutput = true;
    $dom->loadXML($toxml);
    $youremom = $dom->saveXML();
    header('Content-Length: '.strlen($youremom));
    exit($youremom);
}
$xml = new SimpleXMLElement('<result/>');
$xml->addChild('has_error', 0);
$xml->addChild('version', 1);
$xml->addChild('request_name', 'posts');
$topic = $xml->addChild('topic');
$topic->addChild('community_id', $community_id);
$posts = $xml->addChild('posts');
$i = 0;
$sent = array(); // array to tell which posts have already been sent
while($row = $res->fetch_assoc()){
    // fetch empathies and app data (if enabled)
    $empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$row['id'].'"');
    $replies = $mysql->query('SELECT * FROM replies WHERE replies.id = "'.$row['id'].'"');
    $app_data = "";
    if($useappdata == 1){
        $appdata = $mysql->prepare('SELECT app_data FROM app_data WHERE pid = "'.$mysql->real_escape_string($row['pid']).'" AND `title_id` = "'.$mysql->real_escape_string($title_id).'"');
        if($appdata->error){
            die("noooooo");
        }
        $appdata->execute();
        $aep = $appdata->get_result();
        if($aep->num_rows !== 0){
            $app_data = $aep->fetch_assoc();
        } else {
            goto skipxml;
        }
    }
    // get user info
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
    if($row["_post_type"] == "artwork" && !empty($row["painting_encoded"]) && $community_id == "4"){
        goto skipxml;
    }
    // if we've already sent their post, ignore it
    if(in_array($title_id.$row["pid"], $sent)){
        goto skipxml;
    }
    array_push($sent, $title_id.$row["pid"]);
    $mii = getMii($roww, $row["feeling_id"]);
    $in = "post".$i;
    // $$(var name) basically sets the name of a variable
    // to the value of the variable you inputted
    // at the $$ thing
    $$in = $posts->addChild('post');
    $$in->addChild('body', ($row["_post_type"] !== "artwork" ? htmlspecialchars($row["body"]) : " " ));
    $$in->addChild('community_id', 1);
    $$in->addChild('created_at', $row["created_at"]);
    if($row["_post_type"] == "artwork"){
        $path = $row["body"];
        $painting = $$in->addChild('painting');
        $painting->addChild('format', 'tga');
        $painting->addChild('content', $row["painting_encoded"]);
        $painting->addChild('size', strlen($row["painting_encoded"]));
        $painting->addChild('url', $row["body"]);
        /*
        <painting>
        <format>tga</format>
        <content>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</content>
        <size>2034</size>
        <url>https://pretendo-cdn.b-cdn.net/paintings/1226022166/du0nJeMzXaUZ7JoupyBuY.png</url>
      </painting>
        $painting = $$in->addChild('painting');
        $painting->addChild('format', 'tga');
        $painting->addChild('content', "eZZZZ");
        $painting->addChild('size', strlen($g));
        $painting->addChild('url', $row["body"]);
        */
    }
    $$in->addChild('feeling_id', $row["feeling_id"]);
    $$in->addChild('id', $row["id"]);
    $$in->addChild('is_autopost', $row["is_autopost"]);
    $$in->addChild('is_community_private_autopost', 0);
    $$in->addChild('is_spoiler', $row["is_spoiler"]);
    $$in->addChild('is_app_jumpable', 0);
    $$in->addChild('empathy_count', $empathies->num_rows);
    $$in->addChild('language_id', 1);
    $$in->addChild('number', 0);
    $$in->addChild('pid', $row["pid"]);
    $$in->addChild('platform_id', 1);
    $$in->addChild('region_id', $parampack[8]);
    $$in->addChild('reply_count', $replies->num_rows);
    $$in->addChild('screen_name', $roww["screen_name"]);
    $$in->addChild('title_id', $title_id);
    if(!empty($app_data["app_data"])){
        $$in->addChild('app_data', $app_data["app_data"]);
    }
    if(!empty($_GET["with_mii"]) && $_GET["with_mii"] == 1){
        $$in->addChild('mii', $roww["mii"]);
        $$in->addChild('mii_face_url', $mii["output"]);
    }
    $$in->addChild('country_id', $parampack[12]);
    if(!empty($row["topic_tag"])){
        $topic_tag = $$in->addChild('topic_tag');
        $topic_tag->addChild('name', $row["topic_tag"]);
        $topic_tag->addChild('title_id', $title_id);
    }
    //$$in->addChild('app_data', ''); this is data sent from a game. please save. crucial.
        /*
    $painting = $$in->addChild('painting');
    $painting->addChild('format', 'tga');
    $painting->addChild('content', '');
    $painting->addChild('size', 153618);
    $painting->addChild('url', 'http://botu');
    taken from wwp xml generator
    */
    skipxml:
}
// prettyify xml so the api will want to work with it
$toxml = str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>', $xml->asXML());
$dom = new \DOMDocument('1.0');
$dom->preserveWhiteSpace = true;
$dom->formatOutput = true;
$dom->loadXML($toxml);
$youremom = $dom->saveXML();
// set content-length or wii u will freak out
header('Content-Length: '.strlen($youremom));
exit($youremom);
?>