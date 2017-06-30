<?php
include '../grplib-php/init.php';

if(empty($_SESSION['pid'])) {
require_once 'lib/htm.php';
noLogin(); exit();
} else {
	$getmyuser = prepared('SELECT user_id FROM people WHERE people.pid = ? LIMIT 1', [$_SESSION['pid']])->fetch_assoc();
    header('Location: '.LOCATION.'/users/'.htmlspecialchars($getmyuser['user_id']), true, 302);
	exit();
}