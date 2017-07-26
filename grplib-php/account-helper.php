<?php

function actLoginCheck($user_id, $password) {
global $mysql;
$search_user = $mysql->query('SELECT * FROM people WHERE people.user_id = "'.$user_id.'" LIMIT 1');
      if(!$search_user ||$search_user->num_rows == 0) {
return 'none'; }
$user = $search_user->fetch_assoc();
		if($user['ban_status'] >= 4) {
return 'ban'; }

$parts = explode('$', $user['password']);
if(crypt($password, sprintf('$%s$%s$%s$', $parts[1], $parts[2], $parts[3])) != $user['password']) {
if(!password_verify($_POST['password'], $user['password'])) {
return 'fail'; } }

return $user;
}

function passgen($pass) {
// return crypt($pass, sprintf('$5$rounds=%d$%s$', 10000, substr(str_replace('+','.',base64_encode(md5(mt_rand(), true))),0,16)));
return password_hash($pass, PASSWORD_BCRYPT);
}

function setLoginVars($user, $login) {
if($login == true) {
      $_SESSION['signed_in'] = true;       
	  $_SESSION['pid'] = $user['pid'];
      $_SESSION['user_id'] = $user['user_id'];
} else {
      $_SESSION['signed_in'] = false;       
	  $_SESSION['pid'] = null;
      $_SESSION['user_id'] = null;
	}
}

function findPendingEmailConfirm($user) {
global $mysql;
$search = $mysql->query('SELECT id, state FROM email_confirmation WHERE email_confirmation.finished = 0 AND email_confirmation.pid = "'.$user['pid'].'" LIMIT 1');
if($search && $search->num_rows != 0) {
return $search->fetch_assoc();
	}
return false;
}

function check_reCAPTCHA($secret) {
if(empty($_POST['g-recaptcha-response'])) {
return false;
	}
            $ch = curl_init();
			curl_setopt_array($ch, [CURLOPT_URL=>'https://www.google.com/recaptcha/api/siteverify', CURLOPT_POST=>true, CURLOPT_HEADER=>true, 
			CURLOPT_HTTPHEADER=>['Content-Type: application/x-www-form-urlencoded'], 
			CURLOPT_POSTFIELDS=>'secret='.$secret.'&response='.urlencode($_POST['g-recaptcha-response']).'&remoteip='.urlencode($_SERVER['REMOTE_ADDR']), 
			CURLOPT_RETURNTRANSFER=>true]);
            $response = curl_exec($ch);
            $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            curl_close($ch);
            if(json_decode($body, true)['success'] != true) {
			return false;
			}
return true;
}

function PIDgen() {
global $mysql;
$rPrPid = $mysql->query('SELECT pid FROM people ORDER BY people.pid LIMIT 1');
$rPid2 = ($rPrPid->num_rows != 0 ? 1799999999 - $rPrPid->fetch_assoc()['pid'] : 1799999999);
return $rPid2 != 1799999999 ? 1799999998 - $rPid2 : 1799999999;
}

function getNNASmii($user_id) {
global $grp_config_olvkey; global $grp_config_olvkey_pass;
        $ch = curl_init();
	curl_setopt_array($ch, array(
        CURLOPT_URL => 'https://3ds-us.olv.nintendo.net/users/'.$user_id.'/blacklist.confirm',
        CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_SSLCERT => $grp_config_olvkey, CURLOPT_SSLCERTPASSWD => $grp_config_olvkey_pass,
		CURLOPT_HEADER => true,
        CURLOPT_HTTPHEADER => ['X-Nintendo-ParamPack: XFxc'],
		CURLOPT_RETURNTRANSFER => true));
	$response = curl_exec($ch);
	$body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
$dom = new DOMDocument;
$dom->loadHTML($body);
$xpath = new DOMXPath($dom);
$results = $xpath->query("//*[@class='id-name']");

if ($results->length > 0) {
    $user_id = $results->item(0)->nodeValue;
	$screen_name = $xpath->query("//*[@class='nick-name']")->item(0)->nodeValue;
	$mii_image = $xpath->query("//*[@class='user-icon']")->item(0)->getAttribute('src');
} else {
	return false;
	}

return array(
'user_id'=>$user_id,
'screen_name'=>$screen_name,
'mii_image'=>str_replace('_normal_face.png','',str_replace('http://mii-images.cdn.nintendo.net/','',$mii_image))
	);
}

function userInfoJSON() {
    $ch = curl_init();
    curl_setopt_array($ch, array(
	CURLOPT_URL => "https://ipinfo.io/{$_SERVER['REMOTE_ADDR']}/json",
	CURLOPT_BINARYTRANSFER=>true,CURLOPT_RETURNTRANSFER=>true));
    $ip_json = json_decode(curl_exec($ch));
    curl_close($ch);

return json_encode(array(
'ua' => base64_encode($_SERVER['HTTP_USER_AGENT']),
'https' => +(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'),
'nintendo' => +isNintendoUser(),
'ipinfo' => [$ip_json]
	));
}

function emailCheck($email) {
if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE || checkdnsrr(substr($email, strpos($email, '@') + 1)) === FALSE) {
	return false;
	} else {
	return true;
	}
}

function actformCheck() {
global $mysql;
	if(empty($_POST['user_id'])) {
		$error_message[] = "You did not enter a login ID.";
		$error_code[] = 1022543;
	}
	if(empty($_POST['password'])) {
		$error_message[] = "The password field cannot be blank.";
		$error_code[] = 1022616;
	}
	elseif(!preg_match('/^[A-Za-z0-9-._]{6,20}$/', $_POST['user_id'])) {
		$error_message[] = "Your login ID is too short, too long, or contains characters that cannot be used.";
		$error_code[] = 1022543;
	}
	elseif(empty($_POST['screen_name']) || empty(preg_replace('/[\x00-\x1F\x7F]/','',$_POST['screen_name']))) {
		$error_message[] = "You did not enter a screen name.";
		$error_code[] = 1022543; 
	}
    elseif(strlen($_POST['screen_name']) > 17) {
        $error_message[] = "Your screen name is too long.";
        $error_code[] = 1022543; 
    }
	elseif(empty($_POST['password2']) || $_POST['password'] != $_POST['password2']) {
        $error_message[] = "The passwords you have entered do not match.";
		$error_code[] = 1022616;
	}
	global $nss;
	if($nss == 0) {
	if(empty($_POST['email']) || !emailCheck($_POST['email'])) {
	    $error_message[] = "The e-mail address you have entered is not valid.";
		$error_code[] = 1022575;
		}
	}
	if($nss == 1) {
	$get_nss_keys = in_array(($_POST['device_id'] ?? null), $grp_config_nss_keys);
	    if(!$get_nss_keys) {
		$error_message[] = "The device ID you have entered is not registered on the server.";
		$error_code[] = 1022452;
		}
	}
	elseif($nss == 2) {
	/* Get an invite key */
	}
	$search_ouser = $mysql->query('SELECT pid FROM people WHERE people.user_id = "'.$mysql->real_escape_string($_POST['user_id'] ?? '').'" LIMIT 1');
	if(!$search_ouser || $search_ouser->num_rows != 0) {
		$error_message[] = "The login ID you have entered already exists.";
		$error_code[] = 1022587;	
	}
if(!empty($error_code)) {
return array($error_code[0], $error_message[0]);
	} else {
return true;
	}
}

function acteditCheck() {
global $mysql;
	if(empty($_POST['screen_name']) || empty(preg_replace('/[\x00-\x1F\x7F]/','',$_POST['screen_name']))) {
		$error_message[] = "You did not enter a screen name.";
		$error_code[] = 1022543; 
	}
    elseif(strlen($_POST['screen_name']) > 17) {
        $error_message[] = "Your screen name is too long.";
        $error_code[] = 1022543; 
    }
	elseif(!empty($_POST['password']) && (empty($_POST['password2']) || $_POST['password'] != $_POST['password2'])) {
        $error_message[] = "The passwords you have entered do not match.";
		$error_code[] = 1022616;
	}
if(!empty($error_code)) {
return array($error_code[0], $error_message[0]);
	} else {
return true;
	}
}

function peopleQuery($values) {
global $mysql;
$create_account_stmt = $mysql->prepare('INSERT INTO people('.(implode(', ', array_keys($values))).')
VALUES('.rtrim(str_repeat('?, ', count($values)), ', ').')');
$params = '';
foreach($values as &$param) {
$params .= is_int($param) ? 'i' : 's';
        }
$funcparam = array_merge(array($params), array_values($values));
foreach($funcparam as $key => $value) $tmp[$key] = &$funcparam[$key];
call_user_func_array([$create_account_stmt, 'bind_param'], $tmp); 

$create_account_stmt->execute();
return $create_account_stmt;
}
