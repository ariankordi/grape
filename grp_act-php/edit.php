<?php
$grpmode = 1; require_once '../grplib-php/init.php';
require_once '../grp_act-php/lib/htm.php';

if(empty($_SESSION['pid'])) {
defaultRedir(false, true); exit();
}

$get_my_user = prepared('SELECT user_id, screen_name, password, email, nnas_info FROM people WHERE people.pid = ?', [$_SESSION['pid']])->fetch_assoc();
if($_SERVER['REQUEST_METHOD'] == 'POST') {
require_once '../grplib-php/account-helper.php';

$check_form = acteditCheck();
if(is_array($check_form)) {
printErr($check_form[0], $check_form[1], '/act/edit');   exit();
	}
$has_mii = !empty($_POST['nn_user_id']) && $_POST['nn_user_id'] != (json_decode($get_my_user['nnas_info'],true)['user_id'] ?? null);
if($has_mii) {
$get_mii = getNNASmii($_POST['nn_user_id']);
if(!$get_mii) {
printErr(1022402, 'The Nintendo Network ID that has been submitted either doesn\'t exist or isn\'t on Miiverse.', '/act/edit'); exit();
		}
	}
// Put previous attribs in a table some day before this
$arr_var_prepare = [$_POST['screen_name']];
if(!empty($_POST['password'])) {
$arr_var_prepare[] = passgen($_POST['password']);
	}
if($has_mii) {
$arr_var_prepare[] = json_encode($get_mii);
$arr_var_prepare[] = $get_mii['mii_image'];
}
if(!empty($_POST['email'])) {
$arr_var_prepare[] = $_POST['email'];
}
$arr_var_prepare[] = $_SESSION['pid'];
$update_user = prepared('UPDATE people SET screen_name = ?'.(!empty($_POST['password']) ? ', password = ?' : '').($has_mii ? ', nnas_info = ?, mii_hash = ?' : '').(!empty($_POST['email']) ? ', email = ?' : '').' WHERE people.pid = ?', $arr_var_prepare);
defaultRedir(true, false);
exit();
}

if(!empty($get_my_user['nnas_info'])) {
$nnas_userid = htmlspecialchars(json_decode($get_my_user['nnas_info'],true)['user_id']);
} else {
$nnas_userid = '';
}
printHeader();
print '<div class="page-header">
        <h3>'.sprintf(loc('grp.act.account_edit'), htmlspecialchars($get_my_user['user_id'])).'</h3>
    </div>
    <form id="act-create" method="POST" action="/act/edit" class="form-horizontal">
<fieldset>


<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">'.loc('grp.act.user.id').'</label>  
  <div class="col-md-4">
  <input id="textinput" name="user_id" type="text" value="'.htmlspecialchars($get_my_user['user_id']).'" placeholder="'.loc('grp.act.login.id').'" class="form-control input-md" disabled>
  <span class="help-block">'.loc('grp.act.userid_help').'</span>  
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">'.loc('grp.act.login.passwd').'</label>  
  <div class="col-md-4">
  <input id="textinput" name="password" type="password" placeholder="'.loc('grp.act.login.passwd').'" class="form-control input-md">
    
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">'.loc('grp.act.login.passwd_confirm').'</label>  
  <div class="col-md-4">
  <input id="textinput" name="password2" type="password" placeholder="'.loc('grp.act.login.passwd').'" class="form-control input-md">
    
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">'.loc('grp.act.email_addr').'</label>  
  <div class="col-md-4">
  <input id="textinput" name="email" type="email" value="'.htmlspecialchars($get_my_user['email']).'" placeholder="'.loc('grp.act.email').'" class="form-control input-md">
  <span class="help-block">'.loc('grp.act.email_help').'</span>  
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">'.loc('grp.act.nnid').'</label>  
  <div class="col-md-4">
  <input id="textinput" name="nn_user_id" type="text" value="'.$nnas_userid.'" placeholder="NNID" class="form-control input-md">
  <span class="help-block">'.loc('grp.act.nnid_help').'</span>  
  </div>
</div>
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">'.loc('grp.act.screenname').'</label>  
  <div class="col-md-4">
  <input id="textinput" name="screen_name" type="text" value="'.htmlspecialchars($get_my_user['screen_name']).'" placeholder="'.loc('grp.act.screenmii_name').'" class="form-control input-md">
  <span class="help-block">'.loc('grp.act.screenname_help').'</span>  
  </div>
</div>





<div class="form-group">
  
  <div class="col-md-4">
  <a href="/my_menu" class="btn btn-primary btn-primary">'.loc('grp.act.back').'</a>
<button class="btn btn-primary">'.loc('grp.act.submit').'
</button>
  </div>
</div>

</fieldset>
</form>';
printFooter();
