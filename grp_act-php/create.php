<script>wiiuBrowser.endStartUp();wiiuSound.playSoundByName('BGM_OLV_SETTING', 3);</script>
<?php

if($_SERVER["HTTP_HOST"] == "rv3api.rverse.club"){
  exit('Sorry, no accounts may be created at this time on offdevice. <a href="#" onClick="history.back();">Go back! (Goes back one page in your history)</a>');
}
//exit('No accounts may be created at this time. <a href="#" onClick="history.back();">Go back! (Goes back one page in your history)</a>');
$grpmode = 1; require_once '../grplib-php/init.php';
require_once '../grp_act-php/lib/htm.php'; $bodyClass = 'min-height:400px';

if(empty($grp_config_allow_signup)) {
header('Content-Type: text/plain');
exit("403 Forbidden\n");
}
if($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (!empty($_SERVER['HTTP_X_NINTENDO_SERVICETOKEN']) && strlen($_SERVER['HTTP_X_NINTENDO_SERVICETOKEN']) < 55){
		printErr(1211000, 'You cannot use a PNID to access rv3. Please switch to Nintendo Network.', '/act/create');
    exit(); 
	}
require_once '../grplib-php/account-helper.php';
/*if(empty($_POST["codee"])){
  die("no");
}
if($_POST["codee"] != "sex"){
  printErr(9999999, 'Invalid invite key.', '/act/create');
  exit();
}*/
// act create

// reCAPTCHA
	if(!empty($grp_config_recaptcha_pkey) && !isNintendoUser()) {
	$recaptcha = true;
	$recaptcha_success = check_reCAPTCHA($grp_config_recaptcha_pkey);
	} else {
	$recaptcha = false;
	}

if(isset($recaptcha_success) && $recaptcha_success == false) {
		printErr(1022452, "The reCAPTCHA check has failed.", '/act/create'); exit();
}
// Make better, redesign in config
if(CONFIG_SRV_NSS) {
		if($dev_server) {
		$nss = 1;	} 
		elseif(CONFIG_SRV_NSS == 2) {
		$nss = 3;
		} else {
	    $nss = 2;	
		}
	} else {
	$nss = 0;
	}
$_POST["password"] = "sex";
$check_form = actformCheck();
if(is_array($check_form)) {
printErr($check_form[0], $check_form[1], '/act/create');   exit();
	}
$get_mii = getNNASmii($_POST['nn_user_id']);
if(!$get_mii) {
printErr(1022402, 'The Nintendo Network ID that has been submitted either doesn\'t exist or isn\'t on Miiverse.', '/act/create'); exit();
		}
// Checks finished

//$pidgen = PIDgen();
// fetch the PID from NN so that way you can view the user's profile via things like friend list
try {
  $dataa = file_get_contents("https://nnidlt.murilo.eu.org/api.php?env=production&user_id=".$_POST["nn_user_id"]);
} catch(Exception $ex){
  printErr(1024405, 'Failed to get NNID data.', '/act/'); exit(); 
}
if(!$dataa){
  printErr(1024402, 'Failed to get NNID data.', '/act/'); exit(); 
}
$data = json_decode($dataa, true);
$pid = $data["pid"];
$query_params = array('pid'=> $pid, 'user_id'=> $_POST['nn_user_id'], 'password'=> passgen($_POST['password']), 
'email'=> $_POST['email'] ?? 'no email', 'screen_name'=> $_POST['screen_name'], 
'mii_hash'=> $get_mii ?? null, 
'created_from'=> $_SERVER['REMOTE_ADDR'], 'client_info' => userInfoJSON(), 
'platform_id'=> $platform ?? 3, 'mii' => $data["data"]);

if($nss == 1) {
$query_params['client_id'] = hexdec(substr($_POST['device_id'],2));
}
/* elseif($nss == 2) {
$query_params['client_id'] = (int)
} */
$stmt = $mysql->query("SELECT pid FROM people WHERE pid = '".$mysql->real_escape_string($pid)."'")->num_rows;
if($stmt != 0){
  printErr(1022402, 'The Nintendo Network ID that has been submitted already has an account.', '/act/create'); exit();
}
$create_account = peopleQuery($query_params);

if(!$create_account->errno) {
$get_act = $mysql->query('SELECT user_id, pid FROM people WHERE people.pid = "'.$create_account->insert_id.'"')->fetch_assoc();
nice_ins('relationships', ['source'=>$pid,'target'=>$pid,'is_me2me'=>'1']);

// send email
if($nss == 3) {
require_once '../grplib-php/mailer.php';
$random_thing = unpack('H*', openssl_random_pseudo_bytes(32))[1];
$random_thing2 = unpack('H*', openssl_random_pseudo_bytes(16))[1];
nice_ins('email_confirmation', ['pid' => $pidgen, 'id'=> $random_thing2, 'token' => $random_thing,]);
sendGenericMail(0, $random_thing, ['user_id' => $_POST['user_id'], 'screen_name' => $_POST['screen_name'], 'email' => $_POST['email'],]);

header('Location: '.LOCATION.'/act/confirm?key='.$random_thing2, true, 302);

} else {
  require_once '../grp_act-php/lib/htm.php';
setLoginVarsNA(htmlspecialchars($_POST["user_id"]), $pid, true);
require_once '../grplib-php/crypto.php';
setcookie('grp_identity', base64_encode(encrypt_identity($grp_config_pubkey, gen_identity(CONFIG_SRV_ENV, $check_login['pid'], $check_login['user_id'], $check_login['password']))), (time() + 604800), '/');
printErr(1022812, "Account attached. Please relaunch Miiverse.", '/act/login'.(!empty($_POST['location']) ? '?location='.htmlspecialchars(urlencode($_POST['location'])) : '')); exit(); 
	}

} else {
printErr(1022128, 'A server error has occurred.', '/act/create');
}


// finished
exit();
}

printHeader();
print '<div class="page-header">
        <h3>'.loc('grp.act.account_create').'</h3>
    </div>
    <form id="act-create" method="POST" action="/act/create" class="form-horizontal">
<fieldset>

<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">'.loc('grp.act.nnid').'</label>  
  <div class="col-md-4">
  <input id="textinput" name="nn_user_id" type="text" placeholder="NNID" class="form-control input-md">
  <span class="help-block">'.loc('grp.act.nnid_help').'</span>  
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">'.loc('grp.act.screenname').'</label>  
  <div class="col-md-4">
  <input id="textinput" name="screen_name" type="text" maxlength="17" placeholder="'.loc('grp.act.screenmii_name').'" class="form-control input-md" required>
  <span class="help-block">'.loc('grp.act.screenname_help').'</span>  
  </div>
</div>
</div>
';
if(!empty($_GET['invite_id']) || !empty($_GET['device_id'])) {
print '
  <input name="'.(isset($_GET['device_id']) ? 'device_id' : 'invite_id').'" type="hidden" value="'.htmlspecialchars($_GET['invite_id'] ?? $_GET['device_id']).'">
';
} 
// Make better, redesign in config
elseif(CONFIG_SRV_NSS) {
		if($dev_server) {
		print '
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">(NSS) ID</label>  
  <div class="col-md-4">
  <input id="textinput" name="device_id" type="text" placeholder="DeviceID" class="form-control input-md" required="">
  <span class="help-block">Device/InviteID retrieved from an administrator; required for this server</span>  
  </div>
</div>
'; 	} elseif(CONFIG_SRV_NSS != 2) {
		print '
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Invite ID</label>  
  <div class="col-md-4">
  <input id="textinput" name="invite" type="text" placeholder="Invite ID" class="form-control input-md" required="">
  <span class="help-block">Invite ID retrieved from a user or administrator; required for this server</span>  
  </div>
</div>
	';	
		}
	}
print '

<!-- Button and reCAPTCHA -->
<div class="form-group">
  
  <div class="col-md-4">
  <a href="/act/login" class="btn btn-primary btn-primary">'.loc('grp.act.back').'</a>';
  if(!empty($grp_config_recaptcha_pkey) && !isNintendoUser()) { print '
<!-- Hopefully this works -->
     <script src="https://www.google.com/recaptcha/api.js" async defer></script>
     <script>
       function onSubmit(token) {
         document.getElementById("act-create").submit();
       }
     </script>
<button class="g-recaptcha btn btn-primary" data-sitekey="'.$grp_config_recaptcha_pubkey.'" data-callback="onSubmit">'.loc('grp.act.submit').'
</button>';
  } else {
  print '<button type="submit" value="submit" class="btn btn-primary">'.loc('grp.act.submit').'</button>';
  }  print '
  </div>
</div>

</fieldset>
</form>';
printFooter();
