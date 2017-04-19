<?php
//Signup post
require_once '../grplib-php/init.php';
	
if($_SERVER['REQUEST_METHOD'] != 'POST' && !isset($_GET['user_id'])) {
include_once '404.php';
grpfinish($mysql); exit();
}
else {
require_once 'lib/htm.php';
	    if(empty($_POST['user_id'])) {
            $error_message[] = 'You must enter a login ID.';
		    $error_code[] = '1022543'; }
        if(empty($_POST['password'])) {
			$error_message[] = "You must enter a password.";
            $error_code[] = '1022616'; }

	    if(!empty($error_code) || !empty($error_message) ) {
actError(array('code'=>$error_code[0],'message'=>$error_message[0]), 'Authenticate', '/act/login');  grpfinish($mysql); exit(); }
else {
require_once '../grplib-php/account-helper.php';
$act_template_subheader = 'Authenticate';
$act_back_location = '/act/login';
$check_login = actLoginCheck($_POST['user_id'], $_POST['password']);

      if($check_login == 'none' || $check_login == 'fail') {
actError(array('code'=>'1022611','message'=>'Invalid account ID and password combination.
  Try again.'), $act_template_subheader, $act_back_location); grpfinish($mysql); exit(); }

      if($check_login == 'ban') {
actError(array('code'=>'1022802','message'=>'Account has been banned.<br>Please contact the administrator if you need any help.'), $act_template_subheader, $act_back_location); grpfinish($mysql); exit(); }

      $_SESSION['signed_in'] = true;       
	  $_SESSION['pid'] = $check_login['pid'];
      $_SESSION['user_id'] = $check_login['user_id'];

# Set identity token
require_once '../grplib-php/crypto.php';
setcookie('grp_identity', base64_encode(encrypt_identity($grp_config_pubkey, gen_identity($grp_config_server_env, $check_login['pid'], $check_login['user_id'], $check_login['user_pass']))), time() + 604800, '/');
                    }
                     
header('Location: '.$grp_config_default_redir_prot.'' . $_SERVER['HTTP_HOST'] .'/my_menu', true, 302);
        }