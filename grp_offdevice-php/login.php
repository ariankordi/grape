<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

if($_SERVER['REQUEST_METHOD'] != 'POST' && !isset($_GET['user_id'])) {
$pagetitle = 'Error'; print printHeader('old'); print printMenu('old'); print notFound('d', false); printFooter('old'); grpfinish($mysql); exit();
}
	    if(empty($_POST['user_id']))
        { $error_message[] = 'You must enter a login ID.'; $error_code[] = '1022543'; }
        if(empty($_POST['password']))
        { $error_message[] = "You must enter a password."; $error_code[] = '1022616'; }
	
	    if(!empty($error_code) || !empty($error_message) ) {	
$bodyID = 'help';
print printHeader('old');
print '<h1 id="page-title">Grape::Account</h1>
<h2 style="    height: 50px; line-height: 50px; padding-left: 20px; background: -webkit-gradient(linear, left top, left bottom, from(#e6e6e6), to(#cccccc)) 0 0; font-size: 16px; box-shadow: inset 0 4px 4px rgba(0, 0, 0, 0.1), inset 0 -4px 4px rgba(0, 0, 0, 0.1); text-shadow: 0 2px 2px white;">Authenticate</h2>
      <div id="guide" class="help-content"><div class="num3">
  <h2>Errors</h2>
  <p>Error Code: '.substr($error_code[0], 0, 3).'-'.substr($error_code[0], 3, 4).'</p><p>'.$error_message[0].'</p>
</div></div>';
	grpfinish($mysql);	exit(); } else {
require_once '../grplib-php/account-helper.php';
$check_login = actLoginCheck($_POST['user_id'], $_POST['password']);

  if($check_login == 'none' || $check_login == 'fail') {
$bodyID = 'help';
print printHeader('old');

print '<h1 id="page-title">Grape::Account</h1>
<h2 style="    height: 50px; line-height: 50px; padding-left: 20px; background: -webkit-gradient(linear, left top, left bottom, from(#e6e6e6), to(#cccccc)) 0 0; font-size: 16px; box-shadow: inset 0 4px 4px rgba(0, 0, 0, 0.1), inset 0 -4px 4px rgba(0, 0, 0, 0.1); text-shadow: 0 2px 2px white;">Authenticate</h2>

      <div id="guide" class="help-content">
<div class="num3">
  <h2>Errors</h2>
  <p>Error Code: 102-2611</p><p>Invalid account ID and password combination.
  Try again.</p>
</div></div>';
		grpfinish($mysql); exit();
   }

      if($check_login == 'ban') {
$bodyID = 'help';
print printHeader('old');

print '<h1 id="page-title">Grape::Account</h1>
<h2 style="    height: 50px; line-height: 50px; padding-left: 20px; background: -webkit-gradient(linear, left top, left bottom, from(#e6e6e6), to(#cccccc)) 0 0; font-size: 16px; box-shadow: inset 0 4px 4px rgba(0, 0, 0, 0.1), inset 0 -4px 4px rgba(0, 0, 0, 0.1); text-shadow: 0 2px 2px white;">Authenticate</h2>

      <div id="guide" class="help-content">
<div class="num3">
  <h2>Errors</h2>
  <p>Error Code: 102-2802</p><p>Account has been banned.<br>Please contact the administrator if you need any help.</p>
</div></div>'; grpfinish($mysql); exit(); }

      $_SESSION['signed_in'] = true;       
	  $_SESSION['pid'] = $check_login['pid'];
      $_SESSION['user_id'] = $check_login['user_id'];
		
# Set identity token
require_once '../grplib-php/crypto.php';
setcookie('grp_identity', base64_encode(encrypt_identity($grp_config_pubkey, gen_identity($grp_config_server_env, $check_login['pid'], $check_login['user_id'], $check_login['user_pass']))), time() + 604800, '/');
                     
header('Location: '.$grp_config_default_redir_prot.'' . $_SERVER['HTTP_HOST'] .''.(!empty($_POST['location']) ? urldecode($_POST['location']) : '/communities'), true, 302);
            }
	

?>