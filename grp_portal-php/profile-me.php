<?php
require_once '../grplib-php/init.php';
if(empty($_SESSION['pid'])) {
    header('Content-Type: text/plain');	
    print '403 Forbidden'."\n".'';
	exit();
} else {
    header('Location: '.$grp_config_default_redir_prot.'' . $_SERVER['HTTP_HOST'] .'/users/'.$_SESSION['user_id'].'', true, 302);	
	exit();
}

