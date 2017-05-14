<?php

function getUpdates($pid) {
global $mysql;
return array(
'news' => $mysql->query('SELECT * FROM news WHERE news.to_pid = "'.$pid.'" AND news.has_read = "0" AND news.merged IS NULL ORDER BY news.news_id LIMIT 64')->num_rows,
'friend_requests' => $mysql->query('SELECT * FROM friend_requests WHERE friend_requests.recipient = "'.$pid.'" AND friend_requests.has_read = "0" AND friend_requests.finished = "0"')->num_rows,
'messages' => $mysql->query('select a.*, bm.conversation_id, bm.pid, bm.has_read from (select pid, has_read, conversation_id from messages where has_read != "1" and pid != "'.$pid.'") bm inner join conversations a on bm.conversation_id = a.conversation_id WHERE bm.pid != "'.$pid.'" AND a.sender = "'.$pid.'" OR a.recipient = "'.$pid.'" and bm.has_read = "0" OR bm.has_read IS NULL')->num_rows,
);
}

function getProfile($user) {
global $mysql;
$search_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1');
if($search_profile->num_rows == 0) {
$createprofile = $mysql->query('INSERT INTO profiles(pid, platform_id) VALUES("'.$user['pid'].'", "'.$user['platform_id'].'")');
return $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1')->fetch_assoc();
} else {
return $search_profile->fetch_assoc(); }
}

function userIDtoPID($user_id) {
global $mysql;
$query = $mysql->query('SELECT pid FROM people WHERE people.user_id = "'.$user_id.'" LIMIT 1');
if(!$query || $query->num_rows == 0) { return false; }
return $query->fetch_assoc()['pid'];
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

function getFriendRelationship($source, $target) {
global $mysql;
$search_relationship = $mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$source.'" AND friend_relationships.target = "'.$target.'" OR friend_relationships.source = "'.$target.'" AND friend_relationships.target = "'.$source.'"');
if(!$search_relationship || $search_relationship->num_rows == 0) { return false; }
$relationship = $search_relationship->fetch_assoc();
$search_conversation = $mysql->query('SELECT * FROM conversations WHERE conversations.sender = "'.$source.'" AND conversations.recipient = "'.$target.'" OR conversations.sender = "'.$target.'" AND conversations.recipient = "'.$source.'"');
if($search_conversation->num_rows != 0) { $conversation = $search_conversation->fetch_assoc(); }

return array(
'relationship_id' => $relationship['relationship_id'],
'updated' => $relationship['updated'],
'conversation_id' => ($search_conversation->num_rows != 0 ? $conversation['conversation_id'] : false),
'conversation_createdat' => ($search_conversation->num_rows != 0 ? $conversation['created_at'] : false)
);
}