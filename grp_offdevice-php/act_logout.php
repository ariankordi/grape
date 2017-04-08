<?php
require_once '../grplib-php/init.php';

if(!empty($_SESSION['pid']))
{
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
unset($_COOKIE['grp_identity']);
setcookie("grp_identity", false, time() - 4, '/');
						
    header('Location: '.$grp_config_default_redir_prot.'' . $_SERVER['HTTP_HOST'] .'/communities', true, 302);
}
else
{
require_once 'lib/htm.php';
$bodyID = 'help';
print printHeader('old');

print '<h1 id="page-title">Grape::Account</h1>
<h2 style="    height: 50px; line-height: 50px; padding-left: 20px; background: -webkit-gradient(linear, left top, left bottom, from(#e6e6e6), to(#cccccc)) 0 0; font-size: 16px; box-shadow: inset 0 4px 4px rgba(0, 0, 0, 0.1), inset 0 -4px 4px rgba(0, 0, 0, 0.1); text-shadow: 0 2px 2px white;">Sign Out</h2>

      <div id="guide" class="help-content"><div class="num3">
  <h2>Errors</h2>
  <p>Error Code: 102-2597</p><p>You are not logged in.</p>
</div></div>';
		exit();
}

?>