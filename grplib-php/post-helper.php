<?php

function imageCheck($img) {
$img = @getimagesizefromstring($img);
if(!$img || substr($img['mime'],0,5) != 'image' || $img['mime'] == 'image/gif') { return false; }
if($img[0] > 4320 || $img[1] > 4320) { return false; }
if(($img[0] / $img[1]) < 0.101) { return false; }
return true;
}

/*function imgurUpload($img) {

}*/

function postValid($user, $screenshot_type) {
if(empty($_POST['body']) || mb_strlen($_POST['body']) <= 0) { return 'blank'; }
#if(preg_match('/[^\x20-\xdf]/',(preg_replace('/[\x00-\x1F\x7F]/','',$_POST['body'])))) { return 'blank'; }
if(empty(preg_replace('/[\x00-\x1F\x7F]/','',$_POST['body']))) { return 'blank'; }
if(strval($user['privilege']) <= 3 && mb_strlen($_POST['body']) > 1000) { return 'max'; }
if(!empty($_POST['url'])) {
if(mb_strlen($_POST['url']) > 255) { return 'max'; } elseif(mb_substr($_POST['url'], 0, 4) != "http" && strlen($_POST['url']) >= 3) { return 'nohttp'; } elseif(mb_strlen($_POST['url']) < 11 && mb_strlen($_POST['url']) >= 3) { return 'min'; } elseif(filter_var($_POST['url'], FILTER_VALIDATE_URL) === FALSE) { return 'invalid'; } }
if(!empty($_POST['screenshot'])) {
if($screenshot_type == 'url') {
if(mb_strlen($_POST['screenshot']) > 255) { return 'max'; } 
elseif(mb_substr($_POST['screenshot'], 0, 4) != "http") { return 'nohttp'; } 
elseif(mb_strlen($_POST['screenshot']) < 11 && mb_strlen($_POST['screenshot']) >= 3) { return 'min'; } elseif(filter_var($_POST['screenshot'], FILTER_VALIDATE_URL) === FALSE) { return 'invalid'; } elseif(substr($_POST['screenshot'], 0, 5) != "https") { return 'nossl'; }
$ch1 = curl_init(); curl_setopt_array($ch1, [CURLOPT_RETURNTRANSFER=>1,CURLOPT_URL=>urldecode($_POST['screenshot']),CURLOPT_CONNECTTIMEOUT=>05,CURLOPT_FOLLOWLOCATION=>true,  CURLOPT_HEADER=>true,  /*CURLOPT_CUSTOMREQUEST=>'HEAD',CURLOPT_NOBODY=>true*/]); $resp = curl_exec($ch1); $body = substr($resp, curl_getinfo($ch1, CURLINFO_HEADER_SIZE));
if(!imageCheck($body)) { return 'invalid'; } 
}
elseif(!imageCheck(base64_decode($_POST['screenshot']))) {
return 'invalid_screenshot'; }
}
// End checks
return 'ok';
}


function commentCan($pid, $post) {
global $mysql;
require_once '../grplib-php/user-helper.php';
$search_restrictions = $mysql->query('SELECT operation FROM restrictions WHERE restrictions.id = "'.$post.'" AND operation = 1 AND (restrictions.recipients LIKE "%'.$pid.'%" OR restrictions.recipients IS NULL) LIMIT 1');
if($search_restrictions->num_rows != 0) {
	return false;
	}
if(canUserView($post, $pid)) {
	return false;
	}
return true;
}

function privatePostValid($user) {
if(mb_strlen($_POST['body']) <= 0) { return 'blank'; }
elseif(preg_replace('/[\x{200B}-\x{200D}]/u', '', $_POST['body']) == '') { return 'blank'; }
elseif(ctype_space(preg_replace('/[\x{200B}-\x{200D}]/u', '', $_POST['body']))) { return 'blank'; }
if(!empty($_POST['screenshot'])) {
if(filter_var($_POST['screenshot'], FILTER_VALIDATE_URL) === FALSE) { return 'invalid'; } elseif (substr($_POST['screenshot'], 0, 5) != "https" && strlen($_POST['screenshot']) >= 3) { return 'nossl'; }
  $ch1 = curl_init(); curl_setopt_array($ch1, [CURLOPT_RETURNTRANSFER=>1,  CURLOPT_URL=> urldecode($_POST['screenshot']),  CURLOPT_CONNECTTIMEOUT=>05, CURLOPT_BUFFERSIZE=>5000000, CURLOPT_USERAGENT=>'Nintendo Wii (http)',  CURLOPT_FOLLOWLOCATION=>true,  CURLOPT_HEADER=>true,  CURLOPT_CUSTOMREQUEST=>'HEAD',  CURLOPT_NOBODY=>true]);
  $content1 = curl_exec($ch1);
  $contentType1 = curl_getinfo($ch1, CURLINFO_CONTENT_TYPE);
if(substr($contentType1,0,5) != 'image' || $contentType1 == 'image/gif') { return 'invalid'; } }

return 'ok';
}