<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

$has_header_js = 'no';
$act_template_subheader = 'Authenticate';
$act_back_location = '/guest_menu';
$act_content = '
<div class="num2">
  <h2>Sign In</h2>
  <p>'.(isset($grp_config_server_nsslog) && $grp_config_server_nsslog == true ? 'You are required to log in on this server ('.(isset($grp_config_server_env) ? $grp_config_server_env : 'X1').').
  <br>Create an account if you do not have one <a href="/act/create">here</a>.': 'You can sign in here after you have created an account.').'</p>
</div>
<form action="/login" method="post" id="act_form">Login ID: <input class="textbox" name="user_id" minlength="3" maxlength="20" type="text"><br>Password: <input type="password" name="password" maxlength="255" class="textbox"><br>
 		<input type="submit" value="Login" class="btn_001">

 	 
</form>    </div>
';
actTemplate($act_template_subheader, $act_back_location, $act_content);
	