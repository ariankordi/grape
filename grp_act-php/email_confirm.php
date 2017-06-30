<?php
require_once '../grplib-php/init.php';
require_once '../grp_act-php/lib/htm.php';
require_once '../grplib-php/account-helper.php';

if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['email']) && !empty($_POST['key'])) {
	$search_token = prepared('SELECT * FROM email_confirmation WHERE email_confirmation.id = ? AND email_confirmation.finished = 0 LIMIT 1', [$_POST['key']]);
	if(!$search_token || $search_token->num_rows == 0) {
	require_once '../grp_act-php/404.php'; exit();
	}
$token = $search_token->fetch_assoc();
$token_user = $mysql->query('SELECT pid, user_id, screen_name, email FROM people WHERE people.pid = "'.$token['pid'].'" LIMIT 1')->fetch_assoc();

	if(!emailCheck($_POST['email'])) {
	printErr(1022575, 'The e-mail address you have entered is not valid.', '/act/confirm?key='.htmlspecialchars($_POST['key']));
	}
prepared('UPDATE people SET email = ? WHERE people.pid = ?', [$_POST['email'], $token_user['pid']]);
require_once '../grplib-php/mailer.php';
sendGenericMail(0, $token['token'], array('user_id'=>$token_user['user_id'],'screen_name'=>$token_user['screen_name'],'email'=>$_POST['email']));

header('Location: '.LOCATION.'/act/confirm?key='.htmlspecialchars($_POST['key']));
}
elseif(!empty($_GET['token'])) {
// strip trailing slash from url
$_GET['token'] = preg_replace('/\\/$/', '', $_GET['token']);

	$search_token = prepared('SELECT * FROM email_confirmation WHERE email_confirmation.token = ? AND email_confirmation.finished = 0 LIMIT 1', [$_GET['token']]);
	if(!$search_token || $search_token->num_rows == 0) {
	require_once '../grp_act-php/404.php'; exit();
	}
$token = $search_token->fetch_assoc();
$token_user = $mysql->query('SELECT pid, user_id FROM people WHERE people.pid = "'.$token['pid'].'" LIMIT 1')->fetch_assoc();

$mysql->query('UPDATE email_confirmation SET finished = 1 WHERE email_confirmation.id = "'.$token['id'].'"');
setLoginVars($token_user, true);

defaultRedir(true, false);
exit();
}
elseif(!empty($_GET['key'])) {
	$search_token = prepared('SELECT * FROM email_confirmation WHERE email_confirmation.id = ? AND email_confirmation.finished = 0 LIMIT 1', [$_GET['key']]);
	if(!$search_token || $search_token->num_rows == 0) {
	require_once '../grp_act-php/404.php'; exit();
	}
$token = $search_token->fetch_assoc();
$token_user = $mysql->query('SELECT email FROM people WHERE people.pid = "'.$token['pid'].'" LIMIT 1')->fetch_assoc();
printHeader();
print '<div class="page-header">
        <h3>Confirm E-mail</h3>
    </div>
    <div class="col-sm-6"><p>Your e-mail address ('.htmlspecialchars($token_user['email']).') needs to be confirmed before your account can be used.<br>You were sent an e-mail by '.htmlspecialchars($grp_mail_param['addr']).' at '.date(loc('default', 'grp.datetime'), strtotime($token['created_at'])).'. The link in the e-mail will activate your account.<br>If the e-mail does not appear in your inbox, check your Junk or Spam folder.<br><br>If the e-mail needs to be re-sent or re-sent to a different e-mail address, submit the form below.</p><p>
</p><form action="/act/confirm" method="post" class="form-horizontal">       
		    
			  <br>
			         <div class="row">
			  <input type="hidden" name="key" value="'.htmlspecialchars($_GET['key']).'">
			  <input type="email" class="form-control" name="email" placeholder="E-mail Address" required="" autofocus="">
                     </div><div class="row">   		  
			 <div class="form-actions">
			<span><br>
		<button class="btn btn-primary btn-block" name="Submit" value="Submit" type="Submit">Submit</button>  
</span></div>
		</div>			
		</form>
    <p></p></div>';
printFooter();
}
else {
require_once '../grp_act-php/404.php'; exit();
}