<?php
$grpmode = 1; require_once '../grplib-php/init.php';
require_once '../grp_act-php/lib/htm.php'; $bodyClass = 'min-height:400px';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
require_once '../grplib-php/account-helper.php';
	    if(empty($_POST['user_id'])) {
printErr(1022543, 'You must enter a login ID.', '/act/login'.(!empty($_POST['location']) ? '?location='.htmlspecialchars(urlencode($_POST['location'])) : '')); exit(); 
}        if(empty($_POST['password'])) {
printErr(1022616, 'You must enter a password.', '/act/login'.(!empty($_POST['location']) ? '?location='.htmlspecialchars(urlencode($_POST['location'])) : '')); exit(); 
} 
$check_login = actLoginCheck($mysql->real_escape_string($_POST['user_id']), $mysql->real_escape_string($_POST['password']));
if(!is_array($check_login)) {
if($check_login == 'none' || $check_login == 'fail') {
printErr(1022611, 'Invalid account ID and password combination.', '/act/login'.(!empty($_POST['location']) ? '?location='.htmlspecialchars(urlencode($_POST['location'])) : '')); exit(); 
} elseif($check_login == 'ban') {
printErr(1022812, "Account has been banned.\n\nPlease contact the admin if you need any help.", '/act/login'.(!empty($_POST['location']) ? '?location='.htmlspecialchars(urlencode($_POST['location'])) : '')); exit(); 
}
exit();
} else {
require_once '../grp_act-php/lib/htm.php';

if(!$dev_server && CONFIG_SRV_NSS == 2) {
$find_email_confirm = findPendingEmailConfirm($check_login);
if(is_array($find_email_confirm)) {
header('Location: '.LOCATION.'/act/confirm?key='.$find_email_confirm['id'], true, 302);
exit();
	}
}

setLoginVars($check_login, true);
require_once '../grplib-php/crypto.php';
setcookie('grp_identity', base64_encode(encrypt_identity($grp_config_pubkey, gen_identity(CONFIG_SRV_ENV, $check_login['pid'], $check_login['user_id'], $check_login['password']))), (time() + 604800), '/');

defaultRedir(true, false);
       }
exit();
}
printHeader();
print '<div class="page-header">
        <h3>'.loc('grp.act.authenticate').'</h3>
    </div>
    <div class="col-sm-6">';
	printf("<p>\n".loc('grp.act.login_account_signup')."<p>\n", '<a href="/act/create">', '</a>');
	print '
    <form action="/act/login" method="post" class="form-horizontal" id="login-form">       
		    
			  <br>
			         <div class="row">
			  <input type="text" class="form-control" name="user_id" placeholder="'.loc('grp.act.login.id').'" required autofocus>
                     </div><div class="row">
			  <input type="password" class="form-control" name="password" placeholder="'.loc('grp.act.login.passwd').'" required>  
                     </div><div class="row">   		  
			 <div class="form-actions">
			<span><br>
	';
	if(!empty($_GET['location'])) {
print '			  <input type="hidden" name="location" value="'.htmlspecialchars($_GET['location']).'">  ';
	}
	
if(!empty($grp_config_hosts['portal_host'])) {
print '<script>function changeHost(host) {
document.getElementById("login-form").action = "https://" + host + "/act/login";
}</script>';
print '<button class="btn btn-primary btn-block" onclick="changeHost(\'' . htmlspecialchars($grp_config_hosts['portal_host']) . '\')" name="Submit" value="'.loc('grp.act.login').'" type="Submit">'.loc('grp.act.login').' (Wii U)</button>
';
if(!empty($grp_config_hosts['offdevice_host'])) {
print '<button class="btn btn-primary btn-block" onclick="changeHost(\'' . htmlspecialchars($grp_config_hosts['offdevice_host']) . '\')" name="Submit" value="'.loc('grp.act.login').'" type="Submit">'.loc('grp.act.login').' (Off-Device)</button>
';
	}
} else {
print '	<button class="btn btn-primary btn-block" name="Submit" value="'.loc('grp.act.login').'" type="Submit">'.loc('grp.act.login').'</button>  '; }
print '
</span></div>
		</div>			
		</form></div>
';
printFooter();