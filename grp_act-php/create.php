<?php
$grpmode = 1; require_once '../grplib-php/init.php';
require_once '../grp_act-php/lib/htm.php'; $bodyClass = 'min-height:400px';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
require_once '../grplib-php/account-helper.php';
// act create

// reCAPTCHA
	if(!empty($grp_config_recaptcha_pkey)) {
	$recaptcha = true;
	$recaptcha_success = check_reCAPTCHA($grp_config_recaptcha_pkey);
	} else {
	$recaptcha = false;
	}

$check_form = actformCheck(false);
if($check_form != true) {
printErr($check_form[0], $check_form[1], '/act/create');  grpfinish($mysql); exit();
	}
// Checks finished


grpfinish($mysql); exit();
}

printHeader();
print '<div class="page-header">
        <h3>Create Account</h3>
    </div>
    <form id="act-create" method="POST" action="/act/create" class="form-horizontal">
<fieldset>





<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">User ID</label>  
  <div class="col-md-4">
  <input id="textinput" name="user_id" type="text" placeholder="Login ID" class="form-control input-md" required="">
  <span class="help-block">This ID will be used to log in and access your profile</span>  
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Password</label>  
  <div class="col-md-4">
  <input id="textinput" name="password" type="text" placeholder="Password" class="form-control input-md" required="">
    
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Confirm Password</label>  
  <div class="col-md-4">
  <input id="textinput" name="password2" type="text" placeholder="Password" class="form-control input-md" required="">
    
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">E-mail Address</label>  
  <div class="col-md-4">
  <input id="textinput" name="textinput" type="text" placeholder="E-mail" class="form-control input-md" required="">
  <span class="help-block">Will be used for confirmation</span>  
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Nintendo Network ID</label>  
  <div class="col-md-4">
  <input id="textinput" name="nn_user_id" type="text" placeholder="NNID" class="form-control input-md">
  <span class="help-block">Used for grabbing Mii and other info from</span>  
  </div>
</div>
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Screen name</label>  
  <div class="col-md-4">
  <input id="textinput" name="screen_name" type="text" placeholder="Screen/Mii name" class="form-control input-md">
  <span class="help-block">Name you will appear as; Will be NNID\'s Mii name if blank</span>  
  </div>
</div>
';

/*
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">(NSS) Invite ID</label>  
  <div class="col-md-4">
  <input id="textinput" name="device_id" type="text" placeholder="DeviceID" class="form-control input-md" required="">
  <span class="help-block">Invite ID retrieved from an administrator; required for this server</span>  
  </div>
</div>
*/

print '
<!-- Button and reCAPTCHA -->
<div class="form-group">
  
  <div class="col-md-4">';
  if(!empty($grp_config_recaptcha_pkey)) { print '
<!-- Hopefully this works -->
     <script src="https://www.google.com/recaptcha/api.js" async defer></script>
     <script>
       function onSubmit(token) {
         document.getElementById("act-create").submit();
       }
     </script>
<button class="g-recaptcha btn btn-primary" data-sitekey="'.$grp_config_recaptcha_pubkey.'" data-callback="onSubmit">Submit
</button>';
  } else {
  print '<button type="submit" value="submit" class="btn btn-primary">Submit</button>';
  }  print '
  </div>
</div>

</fieldset>
</form>';
printFooter();
