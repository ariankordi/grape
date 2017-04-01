<?php
//Sign out form.
include 'lib/sql-connect.php';

if($_SESSION['signed_in'] == true)
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
echo '{}';
setcookie("grp_identity", false, time() - 4, '/');
						
    header('Location: '.$grp_config_default_redir_prot.'' . $_SERVER['HTTP_HOST'] .'/guest_menu', true, 302);
}
else
{
		$act_template_subheader = 'Sign Out';
		$act_back_location = '/act/login';
		$act_content = '<div class="num3">
  <h2>Errors</h2>
  <p>Error Code: 102-2597</p><p>You are not logged in.</p>
</div>';
      include 'lib/act_template.php';
}

?>