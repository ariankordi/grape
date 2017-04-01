<?php
include 'lib/sql-connect.php';

		if(empty($_SESSION['pid'])) {
header('Content-Type: application/json; charset=utf-8');
print '{"success":1,"admin_message":{"unread_count":0},"mission":{"unread_count":0},"news":{"unread_count":0},"message":{"unread_count":0}}';
        }
else {
header('Content-Type: application/json; charset=utf-8');
$get_notifications = mysqli_num_rows(mysqli_query($link, 'SELECT * FROM grape.news WHERE news.to_pid = "'.$_SESSION['pid'].'" AND news.has_read = "0" AND news.merged IS NULL ORDER BY news.news_id LIMIT 64
')) + mysqli_num_rows(mysqli_query($link, 'SELECT * FROM grape.friend_requests WHERE friend_requests.recipient = "'.$_SESSION['pid'].'" AND friend_requests.has_read = "0" AND friend_requests.finished = "0"'));

$get_messages = mysqli_num_rows(mysqli_query($link, 'select a.*, bm.conversation_id, bm.pid, bm.has_read from (select pid, has_read, conversation_id from messages where has_read != "1" and pid != "'.$_SESSION['pid'].'") bm inner join conversations a on bm.conversation_id = a.conversation_id WHERE bm.pid != "'.$_SESSION['pid'].'" AND a.sender = "'.$_SESSION['pid'].'" OR a.recipient = "'.$_SESSION['pid'].'" and bm.has_read = "0" OR bm.has_read IS NULL'));

print '{"success":1,"admin_message":{"unread_count":0},"mission":{"unread_count":0},"news":{"unread_count":'.$get_notifications.'},"message":{"unread_count":'.$get_messages.'}}';	
	
}

?>