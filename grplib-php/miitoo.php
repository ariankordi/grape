<?php
require_once '../grplib-php/user-helper.php';

function miitooAdd($type) {
global $mysql;
global $search_post;
# Method is POST.
require_once '../grplib-php/community-helper.php';

        if($search_post->num_rows == 0) { jsonErr(404);  exit(); }

$post = $search_post->fetch_assoc();
if(!empty($_SESSION['pid']) && canUserView($_SESSION['pid'], $post['pid'])) {
require '404.php'; exit(); }
        if($post['is_hidden'] == 1) { jsonErr(404);  exit(); }
		
		if(empty($_SESSION['pid'])) {
		jsonErr(403);  exit(); }

if(!empty($_SESSION['pid'])) {
if(!miitooCan($_SESSION['pid'], $post['id'], $type == 'replies' ? 'replies' : 'posts')) {
 		jsonErr(400); exit(); } }
$searchmyempathy = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'"');
if($searchmyempathy->num_rows != 0) {
jsonErr(400);
# Oops, you're supposed to remove the empathy
}  else {
        $empathycreate = $mysql->query('INSERT INTO empathies(id, pid, created_from)
                VALUES ("'.$post['id'].'", "'.$_SESSION['pid'].'", "'.$_SERVER['REMOTE_ADDR'].'")');
require_once '../grplib-php/user-helper.php';
sendNews($_SESSION['pid'], $post['pid'], ($type == 'replies' ? 3 : 2), $post['id']);

        if(!$empathycreate)
        { json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500)); } else {
header('Content-Type: application/json'); print 
json_encode(array('success' => 1)); }	
  }
}
function miitooDelete($type) {
global $mysql;
global $search_post;
        if($search_post->num_rows == 0) { jsonErr(404); exit(); }

$post = $search_post->fetch_assoc();

        if($post['is_hidden'] == 1) { jsonErr(404);  exit(); }
		
		if(empty($_SESSION['pid'])) {
		jsonErr(403);  exit(); }
$searchmyempathy = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$post['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'"');
if($searchmyempathy->num_rows == 0) { jsonErr(400);  exit(); }
		
$removemyempathy = $mysql->query('DELETE FROM empathies WHERE empathies.id = "'.$post['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'"');
if(!$removemyempathy) {
http_response_code(500);
header('Content-Type: application/json'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
} else {
header('Content-Type: application/json'); print 
json_encode(array('success' => 1));
	}
}