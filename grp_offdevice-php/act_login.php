<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';
$nnecnojs = true;
$bodyID = 'help';
$pagetitle = 'Grape::Account';
print printHeader('old');

print '<h1 id="page-title">Grape::Account</h1>
<h2 style="    height: 50px; line-height: 50px; padding-left: 20px; background: -webkit-gradient(linear, left top, left bottom, from(#e6e6e6), to(#cccccc)) 0 0; font-size: 16px; box-shadow: inset 0 4px 4px rgba(0, 0, 0, 0.1), inset 0 -4px 4px rgba(0, 0, 0, 0.1); text-shadow: 0 2px 2px white;">Authenticate</h2>

      <div id="guide" class="help-content">

<div class="num2">
  <h2>Sign In</h2>
  <p>You can sign in here after you have created an account.</p>
</div>
<form action="/login" method="post" id="act_form">Login ID: <input class="textbox" name="user_id" minlength="3" maxlength="20" type="text"><br>Password: <input type="password" name="password" maxlength="255" class="textbox"><br><br>
';
if(!empty($_GET['location'])) { print '<input type="hidden" name="location" value="'.htmlspecialchars($_GET['location']).'">'; } print '
<input type="submit" class="black-button" value="Login">

 	 
</form>    </div>
      ';

?>