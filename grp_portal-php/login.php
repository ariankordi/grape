<?php
//Signup post
include 'lib/sql-connect.php';
	
if($_SERVER['REQUEST_METHOD'] != 'POST' && !isset($_GET['user_id'])) {
    $error_code_http = ( "404" );
	include 'lib/error-general.php';
}
else {
if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(strlen($_GET['user_id']) < 1)
        {
            $error_message[] = 'You must enter a login ID.';
		    $error_code[] = '1022543';
        }
         
        if(strlen($_GET['password']) < 1)
        {
			$error_message[] = "You must enter a password.";
            $error_code[] = '1022616';
        }
	    if(!empty($error_code) || !empty($error_message) ) /*Got milk?*/
    {
		// HTML response.
		$error_code_part1 = substr($error_code[0], 0, 3);
		$error_code_part2 = substr($error_code[0], 3, 4);
		
		$act_template_subheader = 'Authenticate';
		$act_back_location = '/act/login';
		$act_content = '<div class="num3">
  <h2>Errors</h2>
  <p>Error Code: ' . $error_code_part1 . '-' . $error_code_part2 . '</p><p>' . $error_message[0] . '</p>
</div>';
include 'lib/act_template.php';
    }
else {
            //the form has been posted without errors, so save it
            //notice the use of mysql_real_escape_string, keep everything safe!
            //also notice the sha1 function which hashes the password
            $sql = "SELECT * FROM people WHERE user_id = '" . mysqli_real_escape_string($link, $_POST['user_id']) . "'";
            $result = mysqli_query($link, $sql);
            $row = mysqli_fetch_assoc($result);
			if(!$result)
        {
header('Location: '.$_SERVER['HTTP_REFERER'].'', true, 302);
exit();
		}
            else
            {
                if($_GET['password'] != sha1($row['user_pass']))
                {
header('Location: '.$_SERVER['HTTP_REFERER'].'', true, 302);
exit();
                }
                else
                {
			
			$sql2 = "SELECT * FROM people WHERE user_id = '" . mysqli_real_escape_string($link, $_GET['user_id']) . "' AND user_pass = '" . password_hash($_POST['password'],PASSWORD_BCRYPT,['salt'=>'zvHy85=EZLaw8?5ct!Ov9YEiP(Gi)itI']) . "'";
            $result2 = mysqli_query($link, $sql2);		
		if(mysqli_fetch_assoc($result2)['ban_status'] >= 4) {
header('Location: '.$_SERVER['HTTP_REFERER'].'', true, 302);
		exit();
		}
                    //set the $_SESSION['signed_in'] variable to TRUE
                   #session_start();
                    $_SESSION['signed_in'] = true;
                     
                    //green


		                $_SESSION['pid']    = $row['pid'];
                        $_SESSION['user_id']    = $row['user_id'];
                        $_SESSION['password']    = $row['user_pass'];
					    $_SESSION['device_id'] = $row['device_id'];
					    $_SESSION['platform_id'] = $row['platform_id'];	
	                if(strval($row['privilege']) >= 2) {
	                $_SESSION['is_special'] = '1'; }
                    else {
                    $_SESSION['is_special'] = '0'; }	
                        $_SESSION['screen_name']  = $row['screen_name'];
						$_SESSION['organization'] = $row['organization'];
						$_SESSION['user_status'] = $row['status'];
						$_SESSION['empathy_restriction'] = $row['empathy_restriction'];
						$_SESSION['user_privilege'] = $row['privilege'];
						$_SESSION['mii_hash'] = $row['mii_hash'];
						$_SESSION['user_face'] = $row['user_face'];
						
			   if(strlen($row['mii_hash']) > 4) {
			        $_SESSION['mii_normal_face'] = 'https://mii-secure.cdn.nintendo.net/' . $row['mii_hash'] .'_normal_face.png'; }		
				else {
					if(strlen($row['user_face']) > 4) {
					$_SESSION['mii_normal_face'] = '' . htmlspecialchars($row['user_face']) .''; }
					else {
	                $_SESSION['mii_normal_face'] = '/img/mii/img_unknown_MiiIcon.png';
			        }
					
				}
		
# Set identity token
require_once 'lib/crypto.php';
$date_of_expiry1 = time() + 604800;
setcookie('grp_identity', base64_encode(encrypt_identity($grp_config_pubkey, gen_identity($grp_config_server_env, $row['pid'], $row['user_id'], $row['user_pass']))), $date_of_expiry1, '', $_SERVER['HTTP_HOST']);

                     
header('Location: '.$_SERVER['HTTP_REFERER'].'', true, 302);
                }
            }	
	
}


}	
else {
	        if(!isset($_POST['user_id']))
        {
            $error_message[] = 'You must enter a login ID.';
		    $error_code[] = '1022543';
        }
         
        if(!isset($_POST['password']))
        {
			$error_message[] = "You must enter a password.";
            $error_code[] = '1022616';
        }
         
	    if(!empty($error_code) || !empty($error_message) ) /*Got milk?*/
    {
		// HTML response.
		$error_code_part1 = substr($error_code[0], 0, 3);
		$error_code_part2 = substr($error_code[0], 3, 4);
		
		$act_template_subheader = 'Authenticate';
		$act_back_location = '/act/login';
		$act_content = '<div class="num3">
  <h2>Errors</h2>
  <p>Error Code: ' . $error_code_part1 . '-' . $error_code_part2 . '</p><p>' . $error_message[0] . '</p>
</div>';
include 'lib/act_template.php';
    }
        else
        {
            //the form has been posted without errors, so save it
            //notice the use of mysql_real_escape_string, keep everything safe!
            //also notice the sha1 function which hashes the password
            $sql = "SELECT * FROM people WHERE user_id = '" . mysqli_real_escape_string($link, $_POST['user_id']) . "' AND user_pass = '" . password_hash($_POST['password'],PASSWORD_BCRYPT,['salt'=>'zvHy85=EZLaw8?5ct!Ov9YEiP(Gi)itI']) . "'";
            $result = mysqli_query($link, $sql);
        if(!$result)
        {
            //MySQL error; HTML response.
		$act_template_subheader = 'Authenticate';
		$act_back_location = '/act/login';
		$act_content = '<div class="num3">
  <h2>Errors</h2>
  <p>Error Code: 160-' . mysqli_errno($link) . '</p><p>A database error has occurred. 
  Please try again later, or report the 
  error code to the webmaster.</p>
</div>';
      include 'lib/act_template.php';
		}
            else
            {
                if(mysqli_num_rows($result) == 0)
                {
		$act_template_subheader = 'Authenticate';
		$act_back_location = '/act/login';
		$act_content = '<div class="num3">
  <h2>Errors</h2>
  <p>Error Code: 102-2611</p><p>Invalid account ID and password combination.
  Try again.</p>
</div>';
      include 'lib/act_template.php';
                }
                else
                {
			
			$sql2 = "SELECT * FROM people WHERE user_id = '" . mysqli_real_escape_string($link, $_POST['user_id']) . "' AND user_pass = '" . password_hash($_POST['password'],PASSWORD_BCRYPT,['salt'=>'zvHy85=EZLaw8?5ct!Ov9YEiP(Gi)itI']) . "'";
            $result2 = mysqli_query($link, $sql2);		
		if(mysqli_fetch_assoc($result2)['ban_status'] >= 4) {
		$act_template_subheader = 'Authenticate';
		$act_back_location = '/act/login';
		$act_content = '<div class="num3">
  <h2>Errors</h2>
  <p>Error Code: 102-2802</p><p>Account has been banned.<br>Please contact the administrator if you need any help.</p>
</div>';
      include 'lib/act_template.php';
		exit();
		}
                    //set the $_SESSION['signed_in'] variable to TRUE
                   #session_start();
                    $_SESSION['signed_in'] = true;
                     
                    //green
                    while($row = mysqli_fetch_assoc($result))
                    {
		                $_SESSION['pid']    = $row['pid'];
                        $_SESSION['user_id']    = $row['user_id'];
                        $_SESSION['password']    = $row['user_pass'];
					    $_SESSION['device_id'] = $row['device_id'];
					    $_SESSION['platform_id'] = $row['platform_id'];	
	                if(strval($row['privilege']) >= 2) {
	                $_SESSION['is_special'] = '1'; }
                    else {
                    $_SESSION['is_special'] = '0'; }	
                        $_SESSION['screen_name']  = $row['screen_name'];
						$_SESSION['organization'] = $row['organization'];
						$_SESSION['user_status'] = $row['status'];
						$_SESSION['empathy_restriction'] = $row['empathy_restriction'];
						$_SESSION['user_privilege'] = $row['privilege'];
						$_SESSION['mii_hash'] = $row['mii_hash'];
						$_SESSION['user_face'] = $row['user_face'];
						
			   if(strlen($row['mii_hash']) > 4) {
			        $_SESSION['mii_normal_face'] = 'https://mii-secure.cdn.nintendo.net/' . $row['mii_hash'] .'_normal_face.png'; }		
				else {
					if(strlen($row['user_face']) > 4) {
					$_SESSION['mii_normal_face'] = '' . htmlspecialchars($row['user_face']) .''; }
					else {
	                $_SESSION['mii_normal_face'] = '/img/mii/img_unknown_MiiIcon.png';
			        }
					
				}

#if($row['user_avatar_type']='mii') {
#$_SESSION['avatar'] = 'https://mii-secure.cdn.nintendo.net/' . $row['user_avatar'] #. '_normal_face.png';
#} else {
#$_SESSION['avatar'] = $row['user_avatar'];
#}

		if(!empty($_SESSION['pid'])) {
$sql_search_relationships_own = 'SELECT * FROM grape.relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.source = "'.$_SESSION['pid'].'" AND relationships.is_me2me = "1"';
$result_search_relationships_own = mysqli_query($link, $sql_search_relationships_own);

if(mysqli_num_rows($result_search_relationships_own) == 0) {
$sql_create_relationships_own = 'INSERT INTO grape.relationships (source, target, is_me2me) VALUES ("'.$_SESSION['pid'].'", "'.$_SESSION['pid'].'", "1")';
$result_create_relationships_own = mysqli_query($link, $sql_create_relationships_own); }

		}

		
# Set identity token
require_once 'lib/crypto.php';
setcookie('grp_identity', base64_encode(encrypt_identity($grp_config_pubkey, gen_identity($grp_config_server_env, $row['pid'], $row['user_id'], $row['user_pass']))), time() + 604800, '/');
                    }
                     
header('Location: '.$grp_config_default_redir_prot.'' . $_SERVER['HTTP_HOST'] .'/my_menu', true, 302);
                }
            }
        }
}
		}
	

?>