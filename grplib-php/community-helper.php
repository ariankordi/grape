<?php
require_once('init.php');

function miitooCan($pid, $post, $type) {
global $mysql;
$getpost = $mysql->query('SELECT * FROM '.($type == 'replies' ? 'replies' : 'posts').' WHERE id = "'.$post.'"')->fetch_assoc();
$getuser = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$pid.'" LIMIT 1')->fetch_assoc();
if($getpost['pid'] == $pid) { return false; }
elseif(strval($getuser['status'] >= 3) || strval($getuser['empathy_restriction'] >= 1)) { return false; }
else { return true; }
}

function postPermission($user, $community) {
if(strval($user['status'] >= 4)) { return false; }
elseif(strval($community['min_perm'] > $user['privilege']) && !empty($community['allowed_pids']) && !in_array($user['pid'], split(', ', $community['allowed_pids']))) { return false; }
elseif(strval($community['min_perm'] > $user['privilege']) && empty($community['allowed_pids'])) { return false; }
else { return true; }
}