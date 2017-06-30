<?php
require_once '../grplib-php/init.php';

if($_SERVER['REQUEST_METHOD'] != 'POST') {
include_once '404.php'; }

		if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 403));  exit(); }

$search_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$_SESSION['pid'].'" LIMIT 1');
if($search_profile->num_rows == 0) {
http_response_code(404); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 404));  exit(); }

	$update_profile = $mysql->query('UPDATE grape.profiles SET profiles.favorite_screenshot = NULL WHERE profiles.pid = "'.$_SESSION['pid'].'"');
        if(!$update_profile) {
http_response_code(500);
header('Content-Type: application/json');
print json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500)); } else { 
header('Content-Type: application/json'); print 
json_encode(array('success' => 1)); }
