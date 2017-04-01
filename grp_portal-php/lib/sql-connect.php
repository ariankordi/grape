<?php

include 'config.php';

//error_reporting("0");
$server   = $grp_config_database_server;
$username = $grp_config_database_user;
$password = $grp_config_database_pw;
$database = 'grape';
$link     = mysqli_connect($server, $username, $password);
mysqli_set_charset($link, "utf8mb4");

mysqli_query($link, 'SET time_zone = "-4:00"');
date_default_timezone_set('America/New_York');

if (!$link) {
header("HTTP/1.1 502 Bad Gateway");
header("Content-Type: text/plain");
	exit('MySQL Connection');
}
if (!mysqli_select_db($link, $database)) {
header("HTTP/1.1 502 Bad Gateway");
header("Content-Type: text/plain");
 	exit('MySQL Database Connection');
}

if(!isset($token_inited_connect)) {
	$token_inited_connect = true;
  function initToken($decryptedToken) {
if(!preg_match('#^(.+)\\\\h\\\\([0-9a-z]{40})$#i', $decryptedToken)) {
return false; }
global $grp_config_server_env;
if(!empty($grp_config_server_env) && GetTokenPart($decryptedToken, 's', '[A-Za-z]\d') != $grp_config_server_env) {
return false; }
if(strlen(GetTokenPart($decryptedToken, 'u', '\d+')) == 10) {
$account_sql = 'SELECT * FROM people WHERE people.pid = "'.GetTokenPart($decryptedToken, 'u', '\d+').'" AND people.user_id = "'.GetTokenPart($decryptedToken, 'a', '[0-9a-zA-Z\\-\\_\\.]{6,20}').'" AND people.ban_status != 5';
global $link;
$account_res = mysqli_query($link, $account_sql);
if(mysqli_num_rows($account_res) == 0) {
return false; }
else {
global $identity_login;
$identity_login = mysqli_fetch_assoc($account_res);
}

   }
else {
return false; }

	  
}
}


# Start session
session_name('grp');
# No session / session expired
if(session_status() == PHP_SESSION_NONE) {
session_set_cookie_params(72000);
ini_set('session.gc_maxlifetime', 72000);
session_start();
if($_SERVER['REQUEST_URI'] == '/act/logout') {
$_COOKIE['grp_identity'] = '';
unset($_COOKIE['grp_identity']);	
}
if(!empty($_COOKIE['grp_identity']) && empty($_SESSION['pid']) && $_SERVER['REQUEST_URI'] != '/act/logout' && $_SERVER['REQUEST_URI'] != '/guest_menu' && $_SERVER['REQUEST_URI'] != '/my_menu') {
if(isset($grp_config_privkey) && isset($grp_config_pubkey)) {
require_once 'crypto.php';
$identity_auth = initToken(decrypt_identity($grp_config_privkey, base64_decode($_COOKIE['grp_identity'])));
if($identity_auth = false) { }
else {
if(isset($identity_login)) {
	$_SESSION['signed_in'] = true;
		                $_SESSION['pid']    = $identity_login['pid'];
                        $_SESSION['user_id']    = $identity_login['user_id'];
                        $_SESSION['password']    = $identity_login['user_pass'];
					    $_SESSION['device_id'] = $identity_login['device_id'];
					    $_SESSION['platform_id'] = $identity_login['platform_id'];	
	                if(strval($identity_login['privilege']) >= 2) {
	                $_SESSION['is_special'] = '1'; }
                    else {
                    $_SESSION['is_special'] = '0'; }	
                        $_SESSION['screen_name']  = $identity_login['screen_name'];
						$_SESSION['organization'] = $identity_login['organization'];
						$_SESSION['user_status'] = $identity_login['status'];
						$_SESSION['empathy_restriction'] = $identity_login['empathy_restriction'];
						$_SESSION['user_privilege'] = $identity_login['privilege'];
						$_SESSION['mii_hash'] = $identity_login['mii_hash'];
						$_SESSION['user_face'] = $identity_login['user_face'];
						
			   if(strlen($identity_login['mii_hash']) > 4) {
			        $_SESSION['mii_normal_face'] = 'https://mii-secure.cdn.nintendo.net/' . $identity_login['mii_hash'] .'_normal_face.png'; }		
				else {
					if(strlen($identity_login['user_face']) > 4) {
					$_SESSION['mii_normal_face'] = '' . htmlspecialchars($identity_login['user_face']) .''; }
					else {
	                $_SESSION['mii_normal_face'] = '/img/mii/img_unknown_MiiIcon.png';
			        }
					
				}



		if(!empty($_SESSION['pid'])) {
$sql_search_relationships_own = 'SELECT * FROM grape.relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.source = "'.$_SESSION['pid'].'" AND relationships.is_me2me = "1"';
$result_search_relationships_own = mysqli_query($link, $sql_search_relationships_own);

if(mysqli_num_rows($result_search_relationships_own) == 0) {
$sql_create_relationships_own = 'INSERT INTO grape.relationships (source, target, is_me2me) VALUES ("'.$_SESSION['pid'].'", "'.$_SESSION['pid'].'", "1")';
$result_create_relationships_own = mysqli_query($link, $sql_create_relationships_own); }

		}

		
## Set identity token
#require_once 'crypto.php';
#$date_of_expiry1 = time() + 604800;
#setcookie('grp_identity', base64_encode(encrypt_identity($grp_config_pubkey, gen_identity($grp_config_server_env, $identity_login['pid'], $identity_login['user_id'], $identity_login['user_pass']))), $date_of_expiry1, '', $_SERVER['HTTP_HOST']);

}	}


	
} } }

include 'init.php';

?>
