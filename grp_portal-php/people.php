<?php
require_once '../grplib-php/init.php';
	
if($_SERVER['REQUEST_METHOD'] != 'POST') {
include_once '404.php';
grpfinish($mysql); exit(); 
}
require_once 'lib/htm.php';
       if(preg_match('/\s/',$_POST['user_id'])) {
            $error_message[] = "The login ID you entered contains characters that cannot be used.\n Please try again, using only letters, numbers, dashes\n or underscores.";
            $error_code[] = '1022572'; }
        if(preg_match('/[^a-z_\-0-9]/i', $_POST['user_id'])) {
            $error_message[] = "The login ID you entered contains characters that cannot be used.\n Please try again, using only letters, numbers, dashes\n or underscores.";
            $error_code[] = '1022572'; }
        if(strlen($_POST['user_id']) > 20) {
            $error_message[] = "The login ID you entered contains too many characters.\n Try again, using an ID that is 20 characters or less.";
			$error_code[] = '1022576'; }
		if($_POST['user_id'] == '') {
            $error_message[] = 'You did not enter a login ID.';
		    $error_code[] = '1022543'; }
		if(strlen($_POST['user_id']) < 6) {
            $error_message[] = "The login ID you entered contains less\nthan the minimum characters required.\n Try again, using an ID that is 6-20 characters.";
			$error_code[] = '1022576'; }
	    if(strlen($_POST['screen_name']) > 16) {
            $error_message[] = "The screen you entered contains too many characters.\n Try again, using an name that is 16 characters or less.";
			$error_code[] = '1512508'; }
		if(empty($_POST['screen_name'])) {
            $error_message[] = "You did not enter a screen name.";
		    $error_code[] = '1022543'; }

        if($_POST['user_pass'] != $_POST['user_pass_check']) {
            $error_message[] = "The passwords you have entered do not match.";
			$error_code[] = '1022616'; } 
			if(($_POST['user_pass']) == '' || strlen($_POST['user_pass']) < 1) {
			$error_message[] = "The password field cannot be blank.";
            $error_code[] = '1022616'; }
	        if(!empty($_POST['mii'])) {
			$error_message[] = "Your Mii data is invalid.\n Either enter valid Mii data, or no Mii data at all.";
            $error_code[] = '1512507'; }
        if(!empty($_POST['mii']) && strlen($_POST['mii']) > 130) {
            $error_message[] = "Your Mii data is invalid.\n Either enter valid Mii data, or no Mii data at all.";
            $error_code[] = '1512507'; }   
		# Check for NSS key existence
	    if($grp_config_server_type == 'dev' && isset($grp_config_server_nsslog) && $grp_config_server_nsslog == true && isset($grp_config_server_nss_keys)) {
		if(!in_array((!empty($_POST['device_id']) ? $_POST['device_id'] : 'NG00000000'), $grp_config_server_nss_keys)) {
		    $error_message[] = "Sorry, but the NSS key entered is not registered in the server.
			Please contact the webmaster if you need a key!";
            $error_code[] = '1022452';	
		} }

	if($_POST['mii_hash']) {
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://3ds-us.olv.nintendo.net/users/'.$_POST['mii_hash'].'/blacklist.confirm');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSLCERT, $grp_config_olvkey);
curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $grp_config_olvkey_pass);
curl_setopt($ch, CURLOPT_HEADER, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Nintendo-ParamPack: XFxc']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$res=$dom->loadHTML($response);
$xpath = new DomXPath($dom);
$imgs = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' user-icon ')]");
if ($imgs->length != 0) {
    $mii_hash_act = str_replace('_normal_face.png','',str_replace('http://mii-images.cdn.nintendo.net/','',$imgs[0]->getAttribute('src')));
    $mii_hash_success = true;
	}
curl_close($ch);
if(strlen($mii_hash_act) < 3) {
$error_message[] = "The NNID you have entered is not valid.\nEither the NNID doesn't exist, was deleted,\nor was unable to get Mii data from.";
$error_code[] = '1022595';
}
	}

	    if(!empty($error_code) || !empty($error_message)) {
		// HTML response.
actError(array('code'=>$error_code[0],'message'=>$error_message[0]), 'Create Account', '/act/create');
    } else {
if(strlen($_POST['mii_hash']) < 1) { $mii_hash_image = null; } else {
	$mii_hash_image = 'https://mii-secure.cdn.nintendo.net/'.$mii_hash_act.'_whole_body.png'; }
$rPrPid = $mysql->query('SELECT * FROM people ORDER BY people.pid LIMIT 1');
$rPid2 = ($rPrPid->num_rows != 0 ? 1799999999 - $rPrPid->fetch_assoc()['pid'] : 1799999999);
$pid_gen = ($rPid2 != 1799999999 ? 1799999998 - $rPid2 : 1799999999);

$password_gen = crypt($_POST['user_pass'], sprintf('$5$rounds=%d$%s$', 10000, substr(str_replace('+','.',base64_encode(md5(mt_rand(), true))),0,16)));

        $create_account = $mysql->query("INSERT INTO
                    people(pid, user_id, user_pass, user_email, screen_name, mii, mii_hash, mii_image, created_from, device_id, privilege, platform_id, comment)
                VALUES('" . $pid_gen . "',
				       '" . $mysql->real_escape_string($_POST['user_id']) . "',
                       '" . $password_gen . "',
                       '" . $mysql->real_escape_string($_POST['user_email']) . "',
                       '" . $mysql->real_escape_string($_POST['screen_name']) . "',
                       '" . (!empty($_POST['mii']) ? $mysql->real_escape_string($_POST['mii']) : NULL) . "',
                       '" . (!empty($mii_hash_act) ? $mii_hash_act : NULL) . "',
					   '" . $mii_hash_image . "',
                       '".$_SERVER['REMOTE_ADDR']."',
					   '0',
                        '0', 
						'1',
						'Account created via Grape::Account::Portal')");
                         
        if(!$create_account)
        {
            //MySQL error; HTML response.
		$act_template_subheader = 'Create Account';
		$act_back_location = '/act/create';
		if($mysql->errno == '1062') {
actError(array('code'=>'1022587','message'=>'Either the user ID you have entered already
  exists, or a database error has occurred. Please use another one or log in.'), $act_template_subheader, $act_back_location);
		} else {
actError(array('code'=>'160'.$mysql->errno,'message'=>'A database error has occurred. 
  Please try again later, or report the 
  error code to the webmaster.'), $act_template_subheader, $act_back_location);
		}
		} else {
			http_response_code(201);
        $act_template_subheader = 'Create Account';
		$act_back_location = '/act/create'; 
		if(isset($mii_hash_image)) {
		$act_created_mii_image_body = '<img align="left" src="' . $mii_hash_image . '">';
		$act_created_mii_image_face = '<img height="64" width="64" src="https://mii-secure.cdn.nintendo.net/' . $mii_hash_act . '_normal_face.png">';
		}
		else {
        $act_created_mii_image_body = null;
		$act_created_mii_image_face = 'No Mii hash.';
		}
		
		$act_content = '<div class="num4">
  <h2>Account Created</h2>
  <p>Your account has been succesfully created.<br></p>
</div>
<div id="act_detail">'.$act_created_mii_image_body.'PID:'.$pid_gen.'<br>
Login ID: '.htmlspecialchars($_POST['user_id']).'<br>
Mii: '.$act_created_mii_image_face.'<br>
Screen name: '.htmlspecialchars($_POST['screen_name']).'<br><br><p>You can now log in <a href="/act/login">here!</a></p>
 		

 	 
</div>    </div>';
      actTemplate($act_template_subheader, $act_back_location, $act_content);
        }
    } grpfinish($mysql);
    