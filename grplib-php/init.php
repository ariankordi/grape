<?php
// hi howdy ho if you rehost this please let me know don't share the source or whatever and have fun looking at gauley drm!!

/*if(time() > 1709506800 && !str_contains($_SERVER["REQUEST_URI"], "/v1/")){
    //header("Location: /error/gone.php");
    exit("<script>window.location.href='/error/gone.php';</script>");
}*/
$langdata = "";
date_default_timezone_set("America/New_York");
//$_SERVER["REMOTE_ADDR"] = $_SERVER["HTTP_CF_CONNECTING_IP"];


global $pid;
require_once dirname(__FILE__,2).'/config.php';
$dev_server = CONFIG_SRV_TYPE == 0;

$version = '0.9.0-real-chicken-finger-lickin-good'; // wtf
//define('LOCATION', 'http'.($grp_config_recommend_ssl || (isset($_SERVER['https']) && $_SERVER['https'] == 'on') ? 's' : '').'://'.$_SERVER['HTTP_HOST']);

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

require_once dirname(__FILE__).'/err_display.php';
set_error_handler('grp_err', E_ERROR);
function connectSQL($server, $user, $pw, $name) {
    try {
        $mysql = @mysqli_connect($server, $user, $pw, $name);
    } catch(e) {
        header("Location: /error/500.html");
        exit();
    }

if(!$mysql){
header("Location: /error/500.html");
exit();
}
$mysql->set_charset('utf8mb4');
$mysql->query('SET time_zone = "-4:00"');
date_default_timezone_set('America/New_York');
return $mysql;
}

if(extension_loaded('mbstring')){
mb_internal_encoding('UTF-8');
}

function initAll() {
$mysql = connectSQL(CONFIG_DB_SERVER, CONFIG_DB_USER, CONFIG_DB_PASS, CONFIG_DB_NAME);
return $mysql;
}

function nice_ins($table, $values) {
global $mysql;
$stmt = $mysql->prepare('INSERT INTO '.$table.'('.(implode(', ', array_keys($values))).')
VALUES('.rtrim(str_repeat('?, ', count($values)), ', ').')');
$params = '';
foreach($values as &$param) {
$params .= is_int($param) ? 'i' : 's';
        }
$funcparam = array_merge(array($params), array_values($values));
foreach($funcparam as $key => $value) $tmp[$key] = &$funcparam[$key];
call_user_func_array([$stmt, 'bind_param'], $tmp);

$stmt->execute();
if($stmt->errno) {
	return false;
	} else {
	return $stmt->get_result();	
	}
}
function prepared($txt, $values) {
global $mysql;
$stmt = $mysql->prepare($txt);
$params = '';
foreach($values as &$param) {
$params .= is_int($param) ? 'i' : 's';
	}
$funcparam = array_merge(array($params), $values);
foreach($funcparam as $key => $value) $tmp[$key] = &$funcparam[$key];
call_user_func_array([$stmt, 'bind_param'], $tmp);

$stmt->execute();
if($stmt->errno) {
	return false;
	} else {
	return $stmt->get_result();	
	}
}

$mysql = initAll();
function localeSet($custom) {
    global $langdata;
    global $mysql;
    require_once dirname(__FILE__,2).'/l10n/langs.php';
    $serviceToken = bin2hex(base64_decode($_SERVER['HTTP_X_NINTENDO_SERVICETOKEN']));
	$sessionId = substr($serviceToken, 0, 64);
	$stmt = $mysql->prepare("SELECT `language` FROM `console_auth` WHERE `long_id` = ?");
	$stmt->bind_param("s", $sessionId);
	$stmt->execute();
	$res = $stmt->get_result();
	if($res->num_rows == 0){
	  if($_SERVER['REQUEST_URI'] != "/act/login" AND $_SERVER['REQUEST_URI'] != "/act/create"){
		//exit("<script>wiiuErrorViewer.openByCodeAndMessage(1270010, 'You are not whitelisted. Sorry.');wiiuBrowser.closeApplication();</script>");
		header("Location: /act/login");
		exit("You need to login.<br><a href='/act/login'>Click here if you're not redirected.</a>");
	  }
	} else {
	  $row = $res->fetch_assoc();
    }
    $_SESSION["lang"] = $row["language"];
if(!empty($custom)) {
$lang = $custom;
		}
elseif(!empty($_GET['locale_lang'])&&in_array($_GET['locale_lang'], ALLOWED_LANGS)) {
$lang = $_GET['locale_lang'];
	$stmt = $mysql->prepare("UPDATE `console_auth` SET `language` = ? WHERE `long_id` = ?");
	$stmt->bind_param("ss", $lang, $sessionId);
	$stmt->execute();
    if($stmt->error){
        header("Location: /error/500.html");
        exit();
    }
    $_SESSION["lang"] = $lang;
		}
elseif(!empty($_SESSION['lang'])&&in_array($_SESSION['lang'], ALLOWED_LANGS)) {
$lang = $_SESSION['lang'];
		}
else {
$browser_lang = explode(",",($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''))[0];
if(!empty($browser_lang)&&in_array($browser_lang, ALLOWED_LANGS)) {
$lang = $browser_lang;
	}	}
if(!isset($lang)) {
$lang = DEFAULT_LOCALE_LANG;
		}
if(empty($_SESSION['lang']) || $_SESSION['lang'] != $lang) {
$_SESSION["lang"] = $lang;
	}
// Set locale constant
define('LOCALE', $lang);
$langdata = json_decode(file_get_contents(dirname(__FILE__,2).'/l10n/'.str_replace('-', '_', $lang).'/lang.json'), true);
//exit(print_r($langdata));
}
//exit(file_get_contents(dirname(__FILE__,2).'/l10n/'.str_replace('-', '_', "en_US").'/lang.json'));
function loc($stringName, ...$args) {
    global $langdata;
    $value = "";
    if (isset($langdata[$stringName])) {
        $value = $langdata[$stringName];
    } else {
        return $stringName;
    }
    
    // If there are arguments, format the string with them
    if (!empty($args)) {
        return vsprintf($value, $args);
    } else {
        return $value;
    }
}

function setTextDomain($domain) {
   bindtextdomain($domain, '../l10n/');
   textdomain($domain);
}

function humanTiming($time) {
if(time() - $time >= 345600) {
return date(loc('grp.datetime'),$time); }
    $time = time() - $time; // to get the time since that moment
if(strval($time) < 1) $time = 1; if($time <= 59) {
return loc('grp.datetime.within_1_minute'); }
    $tokens = array(86400 => 'day', 3600 => 'hour', 60 => 'minute');
    foreach($tokens as $unit => $text) {
        if($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        if($numberOfUnits > 1){
            return loc('grp.datetime.'.$text.'_ago.other', $numberOfUnits);
        } else {
            return loc('grp.datetime.'.$text.'_ago', $numberOfUnits);
        }
    } }
function getMii($user, $feeling_id) {
if($_SERVER["HTTP_X_NINTENDO_SERVICETOKEN"]){
$hash_loc = 'http://mii-images.account.nintendo.net/%s_%s_face.png';
} else {
$hash_loc = '//mii-images.account.nintendo.net/%s_%s_face.png';
}
switch($feeling_id ?? 0) {
case 1:
$feeling = 'happy'; break;
case 2:
$feeling = 'like'; break;
case 3:
$feeling = 'surprised'; break;
case 4:
$feeling = 'frustrated'; break;
case 5:
$feeling = 'puzzled'; break;
default :
$feeling = 'normal';
break;
}
		if(!empty($user['mii_hash'])) {
		$face = sprintf($hash_loc, $user['mii_hash'], $feeling);
		} elseif(!empty($user['face'])) {
		$face = htmlspecialchars($user['face']);
		} else {
		$face = '/img/mii/img_unknown_MiiIcon.png';
		}
	return array(
	'output' => $face,
	'feeling' => $feeling,
	'miitoo' => loc('olv.portal.miitoo.' . $feeling),
	'miitoo_delete' => loc('olv.portal.miitoo.' . $feeling . '.delete'),
	'official' => !empty($user['official_user']),
	);
}
function sendHook($reason){
    $webhookurl = "https://discord.com/api/webhooks/1158937171386974308/56xN9q-XSHQz3Slfe6Z6D6RUEjzi0X39zex93FI9_UlO8geOsiE6YK9fhsN2gd9WcXhi";

$timestamp = date("c", strtotime("now"));

$json_data = json_encode([
    // Message
    "content" => "<@856360433761779762>",
    
    // Username
    "username" => "Captain Hook",

    // Text-to-speech
    "tts" => false,

    // File upload
    // "file" => "",

    // Embeds Array
    "embeds" => [
        [
            // Embed Title
            "title" => "mace",

            // Embed Type
            "type" => "rich",

            // Embed Description
            "description" => $reason,

            // URL of title link
            //"url" => "https://gist.github.com/Mo45/cb0813cb8a6ebcd6524f6a36d4f8862c",

            // Timestamp of embed must be formatted as ISO8601
            "timestamp" => $timestamp,

            // Embed left border color in HEX
            "color" => hexdec( "3366ff" ),

            // Footer
            "footer" => [
                "text" => "hhgreg"
                //"icon_url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=375"
            ],

            // Image to send
            //"image" => [
            //    "url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=600"
            //],

            // Thumbnail
            //"thumbnail" => [
            //    "url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=400"
            //],

            // Author
            //"author" => [
            //    "name" => "krasin.space",
            //    "url" => "https://krasin.space/"
            //],

            // Additional Fields array
            "fields" => [
                // Field 1
                [
                    "name" => "banned by plsbanme?",
                    "value" => "yes",
                    "inline" => true
                ],
            ]
        ]
    ]

], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );


$ch = curl_init( $webhookurl );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec( $ch );
// If you need to debug, or find out why you can't send message uncomment line below, and execute script.
// echo $response;
curl_close( $ch );

}

function sendHook2($reason){
    $webhookurl = "https://discord.com/api/webhooks/1158937171386974308/56xN9q-XSHQz3Slfe6Z6D6RUEjzi0X39zex93FI9_UlO8geOsiE6YK9fhsN2gd9WcXhi";

$timestamp = date("c", strtotime("now"));

$json_data = json_encode([
    // Message
    "content" => "<@856360433761779762>",
    
    // Username
    "username" => "Captain Hook",

    // Text-to-speech
    "tts" => false,

    // File upload
    // "file" => "",

    // Embeds Array
    "embeds" => [
        [
            // Embed Title
            "title" => "uh oh",

            // Embed Type
            "type" => "rich",

            // Embed Description
            "description" => $reason,

            // URL of title link
            //"url" => "https://gist.github.com/Mo45/cb0813cb8a6ebcd6524f6a36d4f8862c",

            // Timestamp of embed must be formatted as ISO8601
            "timestamp" => $timestamp,

            // Embed left border color in HEX
            "color" => hexdec( "3366ff" ),

            // Footer
            "footer" => [
                "text" => "hhgreg"
                //"icon_url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=375"
            ],

            // Image to send
            //"image" => [
            //    "url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=600"
            //],

            // Thumbnail
            //"thumbnail" => [
            //    "url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=400"
            //],

            // Author
            //"author" => [
            //    "name" => "krasin.space",
            //    "url" => "https://krasin.space/"
            //],

            // Additional Fields array
            "fields" => [
                // Field 1
                [
                    "name" => "Suspicious activity detected",
                    "value" => "IP: ".$_SERVER["HTTP_X_FORWARDED_FOR"].", URL: ".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"],
                    "inline" => true
                ],
            ]
        ]
    ]

], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );


$ch = curl_init( $webhookurl );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec( $ch );
// If you need to debug, or find out why you can't send message uncomment line below, and execute script.
// echo $response;
curl_close( $ch );

}
function json500() {
global $mysql; header('Content-Type: application/json', true, 500);
print json_encode(array('success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
}
function jsonErr($code) {
$error = (func_num_args() == 2 ? func_get_arg(2) :'');
header('Content-Type: application/json', true, $code); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => $code)); exit();
}
function jsonSuccess() {
header('Content-Type: application/json'); print
json_encode(array('success' => 1));
}
function isNintendoUser() {
if(!empty($_SERVER['HTTP_USER_AGENT']) && preg_match('/\bmiiverse\b/', $_SERVER['HTTP_USER_AGENT'])) {
	return true;
	} else {
	return false;
	}
}

// Start session if not already started
session_name('grp');
if(session_status() == PHP_SESSION_NONE) {
session_set_cookie_params(72000);
ini_set('session.gc_maxlifetime', 72000);
session_start();
// Locale
localeSet(null);
// <Locale
if(!empty($_COOKIE['grp_identity']) && empty($_SESSION['pid']) && $_SERVER['REQUEST_URI'] != '/act/logout' && isset($grp_config_privkey, $grp_config_pubkey)) {
if(isset($grp_config_privkey) && isset($grp_config_pubkey)) {
require_once 'crypto.php';
$identity_auth = initToken(decrypt_identity($grp_config_privkey, base64_decode($_COOKIE['grp_identity'])));
if($identity_auth) {
require_once 'account-helper.php';
setLoginVars($identity_auth, true); }
	} 	}
}
/*
if($_SESSION["pid"] != "1"){
    exit("<script>wiiuErrorViewer.openByCodeAndMessage(4206969, 'Currently undergoing maintenance.');wiiuBrowser.closeApplication();cave.error_callFreeErrorViewer(20102, 'Currently undergoing maintenance.');</script>Grape is undergoing maintenance.");
}
*/


if(CONFIG_SRV_NSS == true && empty($_SESSION['pid'])) {
header('X-Robots-Tag: none');
	if($_SERVER['SCRIPT_NAME'] != '/act.php') {
header('Location: '.LOCATION.'/act/login?location='.htmlspecialchars(urlencode($_SERVER['REQUEST_URI'])), true, 302);
exit(); } }

if(!empty($_SESSION['pid'])) {
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
}

function pleasebanme($pid, $type){
	global $mysql;
	$mee = $mysql->query('SELECT * FROM bans WHERE reciever = "'.$mysql->real_escape_string($pid).'" LIMIT 1')->num_rows;
    if($mee !== 0){
        return false;
    }
    $sex = $mysql->query("SELECT * FROM people WHERE pid = '".$pid."'")->fetch_assoc();
	$mee = $mysql->query("UPDATE `people` SET `status` = 2 WHERE `pid` = '".$pid."'");
	if(!$mee){
		return false;
	}
    if($type == "inactivity"){
        $mee = $mysql->query("INSERT INTO `bans` (`operator`, `reciever`, `operation_id`, `operation`, `created_at`, `expires_at`, `finished`, `comment`) VALUES ('".$pid."', '".$pid."', NULL, '1', current_timestamp(), '".$mysql->real_escape_string(strval("2090-12-31T11:59"))."', '0', 'inactivity');");
    } else {
        $mee = $mysql->query("INSERT INTO `bans` (`operator`, `reciever`, `operation_id`, `operation`, `created_at`, `expires_at`, `finished`, `comment`) VALUES ('".$pid."', '".$pid."', NULL, '1', current_timestamp(), '".$mysql->real_escape_string(strval("2090-12-31T11:59"))."', '0', 'plsbanme');");
    }
	if(!$mee){
		return false;
	}
	sendHook(var_dump($_SERVER));
	sendHook("username: ".$sex["user_id"]);
	sendHook("type: ".$type);
	return true;
}

if(strpos($_SERVER['HTTP_USER_AGENT'], 'Discordbot') !== false){
    goto skipcheck;
}
if(strpos($_SERVER['HTTP_USER_AGENT'], 'miiverse') !== false){
    goto skipcheck;
}
if(strpos($_SERVER['HTTP_USER_AGENT'], 'Miiverse') !== false){
    goto skipcheck;
}
if(!isset($_SESSION["banerooni"]) || $_SESSION["banerooni"] == false){
    if($_SESSION["ip"] != $_SERVER["REMOTE_ADDR"]){
        $ip = $_SERVER["REMOTE_ADDR"];
    $apii = @file_get_contents('http://proxycheck.io/v2/'.$_SERVER['REMOTE_ADDR'].'?key=69807b-6wo140-932655-88670i&vpn=1&asn=1');
    $apidecoded = json_decode($apii, true);
    //exit(print_r($apidecoded));
    if(isset($apidecoded[$ip]['proxy']) && $apidecoded[$ip]['proxy'] == "yes" && $apidecoded[$ip]['type'] == "VPN" || $apidecoded[$ip]['type'] == "Compromised Server"){
        $_SESSION["banerooni"] = true;
        exit("No VPN access. <img src='https://media.discordapp.net/attachments/1090619998541713478/1176908703388147722/1174887913876631592.png'/>");
    }
    $_SESSION["banerooni"] = false;
    $_SESSION["ip"] = $_SERVER["REMOTE_ADDR"];
    }
}

if($_SESSION["banerooni"]){
    exit("No VPN access. <img src='https://media.discordapp.net/attachments/1090619998541713478/1176908703388147722/1174887913876631592.png'/>");
}
skipcheck:

if(isset($_SERVER["HTTP_X_NINTENDO_SERVICETOKEN"])){
	global $mysql;
	$serviceToken = bin2hex(base64_decode($_SERVER['HTTP_X_NINTENDO_SERVICETOKEN']));
	$sessionId = substr($serviceToken, 0, 64);
	$stmt = $mysql->prepare("SELECT * FROM `console_auth` WHERE `long_id` = ?");
	$stmt->bind_param("s", $sessionId);
	$stmt->execute();
	$res = $stmt->get_result();
	if($res->num_rows == 0){
	  if($_SERVER['REQUEST_URI'] != "/act/login" AND $_SERVER['REQUEST_URI'] != "/act/create"){
		//exit("<script>wiiuErrorViewer.openByCodeAndMessage(1270010, 'You are not whitelisted. Sorry.');wiiuBrowser.closeApplication();</script>");
		header("Location: /act/login");
		exit("You need to login.<br><a href='/act/login'>Click here if you're not redirected.</a>");
	  }
	} else {
	  $row = $res->fetch_assoc();
	  if($row["pid"] == "1738294576"){
		exit("Absolutely not.");
	  }
	  $_SESSION["signed_in"] = true;
	  $_SESSION["user_id"] = $row["user_id"];
	  $_SESSION["pid"] = $row["pid"];
	  $pid = $row["pid"];
      $_SESSION["lang"] = $row["language"];
	  $_COOKIE["grp_theme"] = $row["theme"]; // themes are exclusive to the wii u:tm: experience
	}
  } else {
	if($_SERVER['HTTP_HOST'] != "rv3api.rverse.club"){
		if(!isset($_SESSION["num"])){
			$_SESSION["num"] = 1;
		} else {
			$_SESSION["num"] = $_SESSION["num"] + 1;
		}
		if($_SESSION["num"] > 3){
			if($_SESSION["num"] == 3){
				sendHook2('Type: '.$_SERVER["HTTP_HOST"]);
			}
			exit("Please don't mess with this site, your activity has been logged.");
		}
		exit("Please don't mess with this site, activity is logged.");
	} else {
		if(!isset($_SESSION["pid"])){
			if($_SERVER['REQUEST_URI'] != "/act/login" AND $_SERVER['REQUEST_URI'] != "/act/create"){
				header("Location: /act/login");
			}
		}
	}
}
if($_SESSION["pid"] == "1738294576" || $_SESSION["pid"] == 1738294576){
	exit("Absolutely not.");
}
if($_SESSION["pid"] == "1739044112" || $_SESSION["pid"] == 1739044112){
	session_destroy();
}
$me = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION["pid"].'"')->fetch_assoc();
/*if($me["status"] > 1){
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		//http_response_code(403);
        header("Location: /warning/readonly");
		exit();
	}
}*/ 
$mee = $mysql->query('SELECT * FROM bans WHERE reciever = "'.$mysql->real_escape_string($_SESSION["pid"]).'" LIMIT 1')->num_rows;
if($mee !== 0 && $_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER["REQUEST_URI"] !== "/warning/readonly"){ // 
    //header("Location: /warning/readonly");
    exit("<script>window.location.href = '/warning/readonly';</script>");
}
if(!empty($_SESSION["pid"]) && $me["done_setup"] != 1 && !$activate && $_SERVER['HTTP_HOST'] == "rvqcportal.rverse.club"){
    header("Location: /welcome/");
    exit();
  }
if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_SESSION["pid"])){
    $stmt = $mysql->prepare("UPDATE `people` SET `la` = current_timestamp() WHERE `pid` = ?");
    $stmt->bind_param("s", $_SESSION["pid"]);
    $stmt->execute();
    if($stmt->error){
        http_response_code(500);
        exit("Couldn't update your last active time.");
    }
}

if($maintenance && $_SERVER["REQUEST_URI"] != "/error/maintenance.php" && $_SESSION["pid"] != $maintenance_exception_pid){
    header("Location: /error/maintenance.php");
    exit();
}
if($maintenance && $_SESSION["pid"] == $maintenance_exception_pid){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}