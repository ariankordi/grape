<?php
require_once '../grplib-php/init.php';
if(empty($_SESSION['pid'])) {
require_once 'lib/htm.php';
plainErr(403, '403 Forbidden');
	exit();
} else {
    header('Location: '.$grp_config_default_redir_prot.''.$_SERVER['HTTP_HOST'] .'/users/'.$_SESSION['user_id'].'', true, 302);	
	exit();
}

