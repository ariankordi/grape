<?php
require_once '../grplib-php/init.php';
require_once '../grplib-php/user-helper.php';
header('Content-Type: application/json');
if(empty($_SESSION['pid'])) {
print json_encode(array(
'success' => 1,
'admin_message' => array('unread_count' => 0),
'mission' => array('unread_count' => 0),
'news' => array('unread_count' => 0),
'message' => array('unread_count' => 0),
));
} else {
$updates = getUpdates($_SESSION['pid']);
print json_encode(array(
'success' => 1,
'admin_message' => array('unread_count' => 0),
'mission' => array('unread_count' => 0),
'news' => array('unread_count' => $updates['news'] + $updates['friend_requests']),
'message' => array('unread_count' => $updates['messages']),
));
}