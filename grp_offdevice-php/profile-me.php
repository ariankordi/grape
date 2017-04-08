<?php
include '../grplib-php/init.php';

if(empty($_SESSION['pid'])) {
require_once 'lib/htm.php';
noLogin();
	grpfinish($mysql); exit();
} else {
	$getmyuser = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
    header('Location: '.$grp_config_default_redir_prot.'' . $_SERVER['HTTP_HOST'] .'/users/'.htmlspecialchars($getmyuser['user_id']).'', true, 302);
	exit();
}

?>