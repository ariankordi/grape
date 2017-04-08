<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

if($_SERVER['REQUEST_METHOD'] != 'POST' && !isset($_GET['user_id'])) {
$pagetitle = 'Error'; print printHeader('old'); print printMenu('old'); print notFound('d', false); printFooter('old'); grpfinish($mysql); exit();
}
	        if(!isset($_POST['user_id']))
        { $error_message[] = 'You must enter a login ID.'; $error_code[] = '1022543'; }
        if(!isset($_POST['password']))
        { $error_message[] = "You must enter a password."; $error_code[] = '1022616'; }
	
	    if(!empty($error_code) || !empty($error_message) )
    {
		// HTML response.
		
$bodyID = 'help';
print printHeader('old');

print '<h1 id="page-title">Grape::Account</h1>
<h2 style="    height: 50px; line-height: 50px; padding-left: 20px; background: -webkit-gradient(linear, left top, left bottom, from(#e6e6e6), to(#cccccc)) 0 0; font-size: 16px; box-shadow: inset 0 4px 4px rgba(0, 0, 0, 0.1), inset 0 -4px 4px rgba(0, 0, 0, 0.1); text-shadow: 0 2px 2px white;">Authenticate</h2>

      <div id="guide" class="help-content"><div class="num3">
  <h2>Errors</h2>
  <p>Error Code: '.substr($error_code[0], 0, 3).'-'.substr($error_code[0], 3, 4).'</p><p>'.$error_message[0].'</p>
</div></div>';
		exit();
    }
        else
        {
            //Check user.
            $login = $mysql->query('SELECT * FROM people WHERE user_id = "'.$mysql->real_escape_string($_POST['user_id']).'" AND user_pass = "'.password_hash($_POST['password'],PASSWORD_BCRYPT,['salt'=>(isset($grp_config_server_salt) ? $grp_config_server_salt : 'zvHy85=EZLaw8?5ct!Ov9YEiP(Gi)itI')]).'"');
        if(!$login)
        {
            //MySQL error; HTML response.
$bodyID = 'help';
print printHeader('old');

print '<h1 id="page-title">Grape::Account</h1>
<h2 style="    height: 50px; line-height: 50px; padding-left: 20px; background: -webkit-gradient(linear, left top, left bottom, from(#e6e6e6), to(#cccccc)) 0 0; font-size: 16px; box-shadow: inset 0 4px 4px rgba(0, 0, 0, 0.1), inset 0 -4px 4px rgba(0, 0, 0, 0.1); text-shadow: 0 2px 2px white;">Authenticate</h2>

      <div id="guide" class="help-content"><div class="num3">
  <h2>Errors</h2>
  <p>Error Code: 160-' . $mysql->errno . '</p><p>A database error has occurred. 
  Please try again later, or report the 
  error code to the webmaster.</p>
</div></div>';
		exit();
		} else {
                if($login->num_rows == 0)
                {
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
		exit();
} else {
			
            $result2 = $mysql->query('SELECT * FROM people WHERE user_id = "'.$mysql->real_escape_string($_POST['user_id']).'" AND user_pass = "'.password_hash($_POST['password'],PASSWORD_BCRYPT,['salt'=>'zvHy85=EZLaw8?5ct!Ov9YEiP(Gi)itI']).'"')->fetch_assoc();	
		if($result2['ban_status'] >= 4) {
$bodyID = 'help';
print printHeader('old');

print '<h1 id="page-title">Grape::Account</h1>
<h2 style="    height: 50px; line-height: 50px; padding-left: 20px; background: -webkit-gradient(linear, left top, left bottom, from(#e6e6e6), to(#cccccc)) 0 0; font-size: 16px; box-shadow: inset 0 4px 4px rgba(0, 0, 0, 0.1), inset 0 -4px 4px rgba(0, 0, 0, 0.1); text-shadow: 0 2px 2px white;">Authenticate</h2>

      <div id="guide" class="help-content">
<div class="num3">
  <h2>Errors</h2>
  <p>Error Code: 102-2802</p><p>Account has been banned.<br>Please contact the administrator if you need any help.</p>
</div></div>';
		exit();
		}
                    $_SESSION['signed_in'] = true;
					
                    while($row = $login->fetch_assoc())
                    {
		                $_SESSION['pid']    = $row['pid'];
                        $_SESSION['user_id']    = $row['user_id'];
                        $_SESSION['password']    = $row['user_pass'];
					    $_SESSION['device_id'] = $row['device_id'];
					    $_SESSION['platform_id'] = $row['platform_id'];	
					
				}

/*		if(!empty($_SESSION['pid'])) {
$search_relationships_own = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.source = "'.$_SESSION['pid'].'" AND relationships.is_me2me = "1"');

if($search_relationships_own->num_rows == 0) {
$create_relationships_own = $mysql->query('INSERT INTO relationships (source, target, is_me2me) VALUES ("'.$_SESSION['pid'].'", "'.$_SESSION['pid'].'", "1")'); }

		}*/
# Please handle this in the future

		
# Set identity token
require_once '../grplib-php/crypto.php';
setcookie('grp_identity', base64_encode(encrypt_identity($grp_config_pubkey, gen_identity($grp_config_server_env, $row['pid'], $row['user_id'], $row['user_pass']))), time() + 604800, '/');
                    }
                     
header('Location: '.$grp_config_default_redir_prot.'' . $_SERVER['HTTP_HOST'] .'/'.(!empty($_GET['location']) ? urldecode($_GET['location']) : 'communities'), true, 302);
                }
            }
	

?>