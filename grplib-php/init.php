<?php

require_once dirname(__FILE__,2).'/config.php';
$dev_server = CONFIG_SRV_TYPE == 0;

$version = '0.8.8';
define('LOCATION', 'http'.($grp_config_recommend_ssl || (isset($_SERVER['https']) && $_SERVER['https'] == 'on') ? 's' : '').'://'.$_SERVER['HTTP_HOST']);

require_once dirname(__FILE__).'/err_display.php';
set_error_handler('grp_err', E_ERROR);
function connectSQL($server, $user, $pw, $name) {
$mysql = new mysqli($server, $user, $pw, $name);
$mysql->set_charset('utf8mb4');

if($mysql->connect_errno){
http_response_code(502); die(); }
$mysql->query('SET time_zone = "-4:00"');
date_default_timezone_set('America/New_York');
return $mysql;
}

mb_internal_encoding('UTF-8');

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

function localeSet($custom) {
require_once dirname(__FILE__,2).'/l10n/langs.php';
if(!empty($custom)) {
$lang = $custom;
		}
elseif(!empty($_GET['locale_lang'])&&in_array($_GET['locale_lang'], ALLOWED_LANGS)) {
$lang = $_GET['locale_lang'];
		}
elseif(!empty($_COOKIE['lang'])&&in_array($_COOKIE['lang'], ALLOWED_LANGS)) {
$lang = $_COOKIE['lang'];
		}
else {
$browser_lang = explode(",",($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''))[0];
if(!empty($browser_lang)&&in_array($browser_lang, ALLOWED_LANGS)) {
$lang = $browser_lang;
	}	}
if(!isset($lang)) {
$lang = DEFAULT_LOCALE_LANG;
		}
if(empty($_COOKIE['lang']) || $_COOKIE['lang'] != $lang) {
setcookie('lang', $lang, (time() + 664800), '/');
	}
// Set locale constant
define('LOCALE', $lang);
//
$lang_enc = str_replace('-', '_', LOCALE).'.UTF-8';
   setlocale(LC_ALL, $lang_enc);
// Change later to a default
   bindtextdomain('default', dirname(__FILE__,2).'/l10n/');
   textdomain('default');
}

function loc() {
$args = func_get_args();
switch(func_num_args()) {
case 1:
return htmlspecialchars(gettext($args[0])); break;
case 2:
return htmlspecialchars(dgettext($args[0], $args[1])); break;
case 3:
return htmlspecialchars(ngettext($args[0], $args[1], $args[2])); break;
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
        return sprintf(loc('grp.datetime.'.$text.'_ago', 'grp.datetime.'.$text.'s_ago', $numberOfUnits), $numberOfUnits);
    } }
function getMii($user, $feeling_id) {
$hash_loc = 'https://mii-secure.cdn.nintendo.net/%s_%s_face.png';
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
	'miitoo' => loc('miitoo', 'olv.portal.miitoo.' . $feeling),
	'miitoo_delete' => loc('miitoo', 'olv.portal.miitoo.' . $feeling . '.delete'),
	'official' => !empty($user['official_user']),
	);
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

$mysql = initAll();

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
if(isset($_SESSION['pid'])) {
if(prepared('SELECT pid FROM people WHERE people.pid = ? AND people.user_id = ?', array($_SESSION['pid'], $_SESSION['user_id']))->num_rows == 0) { $_SESSION['pid'] = false; }
$me = prepared('SELECT pid, user_id, screen_name, mii_hash, nnas_info, face, email, official_user, organization, platform_id, privilege, image_perm, status, ban_status FROM people WHERE people.pid = ? LIMIT 1', array($_SESSION['pid']))->fetch_assoc();
}

if(CONFIG_SRV_NSS == true && empty($_SESSION['pid'])) {
header('X-Robots-Tag: none');
	if($_SERVER['SCRIPT_NAME'] != '/act.php') {
header('Location: '.LOCATION.'/act/login?location='.htmlspecialchars(urlencode($_SERVER['REQUEST_URI'])), true, 302);
exit(); } }

if(!empty($_SESSION['pid'])) {
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
}
