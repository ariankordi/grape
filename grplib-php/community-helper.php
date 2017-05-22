<?php
function miitooCan($pid, $post, $type) {
global $mysql;
$getpost = $mysql->query('SELECT pid FROM '.($type == 'replies' ? 'replies' : 'posts').' WHERE id = "'.$post.'"')->fetch_assoc();
$getuser = $mysql->query('SELECT status, privilege, empathy_restriction FROM people WHERE people.pid = "'.$pid.'" LIMIT 1')->fetch_assoc();
$search_restrictions = $mysql->query('SELECT type FROM restrictions WHERE restrictions.id = "'.$post.'" AND operation = 0 AND (restrictions.recipients LIKE "%'.$pid.'%" OR restrictions.recipients IS NULL) LIMIT 1');
if($getpost['pid'] == $pid) { return false; }
elseif(strval($getuser['status'] >= 3) || strval($getuser['empathy_restriction'] >= 1)) { return false; }
elseif($search_restrictions->num_rows != 0) { return false; }
else { return true; }
}

function getPost($id) {
global $mysql;
return $mysql->query('SELECT id, pid, _post_type, screenshot, feeling_id, body, created_at, community_id, is_spoiler, is_hidden, hidden_resp, url FROM posts WHERE posts.id = "'.$id.'"  AND posts.is_hidden != "1" UNION ALL SELECT id, pid, reply_to_id, screenshot, feeling_id, body, created_at, community_id, is_spoiler, is_hidden, hidden_resp, created_from from replies where replies.id = "'.$id.'"  AND replies.is_hidden != "1"');
}

function searchPopular($community, $time, $limit, $offset, $all) {
global $mysql;
if($time != 'a' && $time != 'n') {
$time = date('Y-m-d', strtotime($time));
$time2 = date('Y-m-d', strtotime($time) - 259199);
} else {
if($time == 'n') {
$time = date('Y-m-d', strtotime($time));
$time2 = date('Y-m-d', 0); }
if($time == 'a') {
$time = date('Y-m-d', strtotime($time));
$time2 = date('Y-m-d', 7258118400); }
}
$sql = 'SELECT '.($all ? 'posts.*, ' : '').'posts.created_at, COUNT(empathies.id) AS empathies FROM posts INNER JOIN empathies ON empathies.id = posts.id WHERE posts.community_id = "'.$community['community_id'].'" AND posts.created_at BETWEEN "'.$time2.'" AND "'.$time.'" GROUP BY posts.id

HAVING (SELECT AVG(empathies) FROM (SELECT COUNT(empathies.id) AS empathies FROM posts INNER JOIN empathies ON empathies.id = posts.id WHERE posts.community_id = "'.$community['community_id'].'" AND posts.created_at BETWEEN "'.$time2.'" AND "'.$time.'" GROUP BY empathies.id) AS empathies) <= empathies

ORDER BY empathies DESC, posts.created_at DESC LIMIT '.$limit.''.(!empty($offset) && is_numeric($offset) ? 'OFFSET '.$offset : '');
return $mysql->query($sql);
}

function postPermission($user, $community) {
if(strval($user['status'] >= 4)) { return false; }
elseif(strval($community['min_perm'] > $user['privilege']) && !empty($community['allowed_pids']) && !in_array($user['pid'], split(', ', $community['allowed_pids']))) { return false; }
elseif(strval($community['min_perm'] > $user['privilege']) && empty($community['allowed_pids'])) { return false; }
else { return true; }
}