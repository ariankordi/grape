<?php
require 'config.php';

define('VERSION', '0.6.7');

function connectSQL($server, $user, $pw, $name) {
$mysql = new mysqli($server, $user, $pw, $name);
$mysql->set_charset('utf8mb4');

if($mysql->connect_errno){
http_response_code(502); die(); } 
$mysql->query('SET time_zone = "-4:00"');
date_default_timezone_set('America/New_York');
return $mysql;
}
function initAll() {
global $grp_config_database_server; global $grp_config_database_user; global $grp_config_database_pw; global $grp_config_database_name;
$mysql = connectSQL($grp_config_database_server, $grp_config_database_user, $grp_config_database_pw, $grp_config_database_name);
return $mysql;
}

if(!is_callable('humanTiming')) {
function humanTiming($time) {
if(time() - $time >= 345600) {
return date("m/d/Y g:i A",$time);
}

    $time = time() - $time; // to get the time since that moment
if(strval($time) < 1) { 
$time = 1; }
if($time <= 59) {
return 'Less than a minute ago';
}
    $tokens = array (
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'').' ago';
    }


  } }
function grpfinish($mysql) {
$mysql->close();
}
$mysql = initAll();

# Start session if not already started
session_name('grp');
if(session_status() == PHP_SESSION_NONE) {
session_set_cookie_params(72000);
ini_set('session.gc_maxlifetime', 72000);
session_start();
if(!empty($_COOKIE['grp_identity']) && empty($_SESSION['pid']) && $_SERVER['REQUEST_URI'] != '/act/logout' && $_SERVER['REQUEST_URI'] != '/guest_menu' && $_SERVER['REQUEST_URI'] != '/my_menu') {
if(isset($grp_config_privkey) && isset($grp_config_pubkey)) {
require_once 'crypto.php';
$identity_auth = initToken(decrypt_identity($grp_config_privkey, base64_decode($_COOKIE['grp_identity'])));
if($identity_auth == false) { }
else {
	$_SESSION['signed_in'] = true;
		                $_SESSION['pid']    = $identity_auth['pid'];
                        $_SESSION['user_id']    = $identity_auth['user_id'];
                        $_SESSION['password']    = $identity_auth['user_pass'];
					    $_SESSION['device_id'] = $identity_auth['device_id'];
					    $_SESSION['platform_id'] = $identity_auth['platform_id'];	
					
				}



		if(!empty($_SESSION['pid'])) {
$search_relationships_own = $mysql->query('SELECT * FROM grape.relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.source = "'.$_SESSION['pid'].'" AND relationships.is_me2me = "1"');
if($search_relationships_own->num_rows == 0) {
$mysql->query('INSERT INTO grape.relationships (source, target, is_me2me) VALUES ("'.$_SESSION['pid'].'", "'.$_SESSION['pid'].'", "1")'); }

		}

}	}


	
}
