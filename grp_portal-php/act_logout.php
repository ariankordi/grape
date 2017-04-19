<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

if(!empty($_SESSION['pid'])) {
	//Unset everything, except for DeviceID and device cert..
		                $_SESSION['pid'] = null;
setcookie("grp_identity", false, time() - 4, '/');
						
    header('Location: '.$grp_config_default_redir_prot.'' . $_SERVER['HTTP_HOST'] .''.(!empty($_GET['location']) ? urldecode($_GET['location']) : '/guest_menu').'', true, 302);
} else {
actError(array('code'=>'1022597','message'=>'You are not logged in.'), 'Log Out', '/act/logout');
}