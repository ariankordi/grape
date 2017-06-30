<?php
require_once '../grplib-php/init.php';

		if(empty($_SESSION['pid'])) {
	jsonErr(403); exit();
        }

if(empty($_POST['tutorial_name']) || $_POST['tutorial_name'] != 'my_news' || $_POST['tutorial_name'] == 'messages') {
jsonErr(400); exit();
}
		
if($_POST['tutorial_name'] == 'my_news') {
$query = prepared('SELECT pid FROM settings_tutorial WHERE pid = ? AND my_news = 1 LIMIT 1', [$_SESSION['pid']]);
} elseif($_POST['tutorial_name'] == 'messages') {
$query = prepared('SELECT pid FROM settings_tutorial WHERE pid = ? AND friend_messages = 1 LIMIT 1', [$_SESSION['pid']]);
}

if(!query || $query->num_rows != 0) {
jsonErr(400); exit();
}

	$query = prepared('INSERT INTO settings_tutorial (pid, '.($_POST['tutorial_name'] == 'my_news' ? 'my_news' : 'friend_messages').') VALUES (?, 1)', [$_SESSION['pid']]);

jsonSuccess();