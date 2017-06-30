<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';
if(empty($_GET['pid']) || !is_numeric($_GET['pid'])) {
generalError(404, 'The user could not be found.');  exit();
}
$get_user = prepared('SELECT user_id FROM people WHERE people.pid = ?', [$_GET['pid']]);
if($get_user->num_rows == 0) {
generalError(404, 'The user could not be found.');  exit();
}
else {
# Redir to profile
header('Location: '.LOCATION.'/users/'.htmlspecialchars($get_user->fetch_assoc()['user_id']), true, 302);
}

