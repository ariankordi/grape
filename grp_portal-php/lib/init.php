<?php
#if ($grp_config_server_type == 'prod' && false !== strpos($_SERVER['REQUEST_URI'], '.php')) {
#include '404.php';
#exit();
#}

if(isset($_COOKIE['gmas']) && $_COOKIE['gmas'] == '20') {
header('HTTP/1.1 504 Gateway Timeout');
exit();
}

if(!isset($_SESSION['pid']) && $grp_config_server_type == 'dev' && isset($grp_config_server_nsslog) && $grp_config_server_nsslog == true && substr($_SERVER['REQUEST_URI'], 0, 4) != '/act' && $_SERVER['REQUEST_URI'] != '/login' && $_SERVER['REQUEST_URI'] != '/people') {
header('Location: '.$grp_config_default_redir_prot.'' . $_SERVER['HTTP_HOST'] .'/act/login', true, 302);	
exit(); }

if(isset($_SESSION['pid'])) {
$sql_search_user_existence = 'SELECT * FROM grape.people WHERE people.pid = '.$_SESSION['pid'].'';	
$result_search_user_existence = mysqli_query($link, $sql_search_user_existence);

$get_user_init_script_start = mysqli_fetch_assoc($result_search_user_existence);
if($get_user_init_script_start['ban_status'] >= 4 || $get_user_init_script_start['user_id'] != $_SESSION['user_id']) {
	//Unset everything, except for DeviceID and device cert..
		                $_SESSION['pid']    = null;
                        $_SESSION['user_id']    = null;
                        $_SESSION['screen_name']  = null;
						$_SESSION['user_status'] = null;
						$_SESSION['is_special'] = null;
						$_SESSION['user_privilege'] = null;
				        $_SESSION['empathy_restriction'] = null;
				        $_SESSION['organization'] = null;
						$_SESSION['mii_hash'] = null;
                        $_SESSION['mii_normal_face'] = null;
						$_SESSION['user_face'] = null;
						$_SESSION['signed_in'] = false;
}
else {
if($_SERVER['REQUEST_URI'] != '/warning/deleted_account' && $get_user_init_script_start['ban_status'] >= 5) {
header('Location: '.$grp_config_default_redir_prot.'' . $_SERVER['HTTP_HOST'] .'/warning/deleted_account', true, 302);	
exit();	
} 
$search_ban_for_user = mysqli_query($link, 'SELECT * FROM bans WHERE bans.reciever = "'.$_SESSION['pid'].'" AND bans.expires_at > NOW() AND bans.finished = "0"');
if(mysqli_num_rows($search_ban_for_user) != 0) {
$row_current_peopleban = mysqli_fetch_assoc($search_ban_for_user);
if(empty($_COOKIE['readonly_displayed']) || $_COOKIE['readonly_displayed'] != '1') {
if(!isset($warning_page_grp)) {
header('Location: '.$grp_config_default_redir_prot.'' . $_SERVER['HTTP_HOST'] .'/warning/readonly?location='.urlencode($_SERVER['REQUEST_URI']).'', true, 302); } }
} } }

if(!is_callable('humanTiming')) {
function humanTiming ($time) {
$has_timing_def = true;
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


  }
}


?>
