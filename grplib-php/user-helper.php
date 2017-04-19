<?php

function getUpdates($pid) {
global $mysql;
return array(
'news' => $mysql->query('SELECT * FROM news WHERE news.to_pid = "'.$pid.'" AND news.has_read = "0" AND news.merged IS NULL ORDER BY news.news_id LIMIT 64')->num_rows,
'friend_requests' => $mysql->query('SELECT * FROM friend_requests WHERE friend_requests.recipient = "'.$pid.'" AND friend_requests.has_read = "0" AND friend_requests.finished = "0"')->num_rows,
'messages' => $mysql->query('select a.*, bm.conversation_id, bm.pid, bm.has_read from (select pid, has_read, conversation_id from messages where has_read != "1" and pid != "'.$pid.'") bm inner join conversations a on bm.conversation_id = a.conversation_id WHERE bm.pid != "'.$pid.'" AND a.sender = "'.$pid.'" OR a.recipient = "'.$pid.'" and bm.has_read = "0" OR bm.has_read IS NULL')->num_rows,
);
}

function infoFromPID($pid) {
global $mysql;
return $mysql->query('SELECT user_id, screen_name FROM people WHERE people.pid = "'.$pid.'" LIMIT 1')->fetch_assoc();
}

function getNewsNotify() {
global $mysql;
if($mysql->query('SELECT * FROM news WHERE news.to_pid = "'.$_SESSION['pid'].'" AND news.has_read = "0" AND news.merged IS NULL LIMIT 1')->num_rows == 1) { return true; } else { return false; }
}

function getMessageNotify() {
global $mysql;
if($mysql->query('SELECT * FROM friend_requests WHERE friend_requests.recipient = "'.$_SESSION['pid'].'" AND friend_requests.finished = "0" AND friend_requests.has_read = "0" LIMIT 1')->num_rows == 1) { return true; } else { return false; }
}