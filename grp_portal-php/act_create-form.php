<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';
$has_header_js = 'no';
$act_template_subheader = 'Create Account';
$act_back_location = '/guest_menu';
$act_content = '
<div class="num6">
  <h2>Account Form</h2>
  <p>You can create an account here.</p>
'.(isset($grp_config_server_nsslog) && $grp_config_server_nsslog == true ? '<p>NSS is enabled on this server; a key is required to create an account at this time.<br>Please contact the webmaster if you need a key.</p>' : '').'
</div>
<form action="/people" method="post" id="act_form">Login ID: <input class="textbox" name="user_id" minlength="3" maxlength="20" type="text"><br>Password: <input type="password" name="user_pass" maxlength="255" class="textbox"><br>Confirm Password: <input type="password" name="user_pass_check" maxlength="255" class="textbox"><br>E-mail Address (optional): <input type="email" name="user_email" minlength="6" maxlength="255" class="textbox"><br>
NNID to get Mii from (required for Mii to appear): <input type="text" name="mii_hash" minlength="6" maxlength="16" class="textbox"><br>
Screen name/Mii name: <input type="text" name="screen_name" maxlength="25" class="textbox"><br>'.(isset($grp_config_server_nsslog) && $grp_config_server_nsslog == true ? '<br>
NSS key ID: <input type="text" name="device_id" maxlength="12" class="textbox"><br>' : '').'
 		<input type="submit" value="Create" class="btn_001">

 	 
</form>
';
actTemplate($act_template_subheader, $act_back_location, $act_content);
	