<?php
require_once '../grplib-php/init.php';
if($_SERVER['REQUEST_METHOD'] != 'POST') {
	include_once '404.php';
     exit();
}


$search_user = prepared('SELECT pid FROM people WHERE people.user_id = ?', [$_GET['user_id'] ?? null]);
require_once '../grplib-php/user-helper.php';

if($search_user->num_rows == 0) { jsonErr(404); }

if(empty($_SESSION['pid'])) {
jsonErr(403); }

$user = $search_user->fetch_assoc();
require_once '../grplib-php/user-helper.php';

if($_SESSION['pid'] == $user['pid']) {
jsonErr(400); 
}
$block = findBlock($_SESSION['pid'], $user['pid']);

if(isset($_GET['un'])) {
	if(!$block) {
	jsonErr(400);	
	}

$delete_blacklist = prepared('DELETE FROM blacklist WHERE blacklist.source = ? AND blacklist.target = ?', [$_SESSION['pid'], $user['pid']]);
} else {
	if($block) {
if(empty($grp_config_allow_blacklist)) {
jsonErr(403); exit();
}	
	jsonErr(400);
	}

$create_blacklist = nice_ins('blacklist', ['source'=>$_SESSION['pid'], 'target'=>$user['pid'], 'type'=>0]);
}