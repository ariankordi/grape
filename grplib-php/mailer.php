<?php

function sendGenericMail($msg, $params, $recipient) {
include_once('Mail.php');
$recipients = $recipient['email'];
global $grp_mail_param;
$headers = array(
'From' => (CONFIG_SRV_NAME ?? 'grape').' <'.$grp_mail_param['addr'].'>',
'To' => htmlspecialchars($recipient['screen_name']).' ('.htmlspecialchars($recipient['user_id']).') <'.$recipient['email'].'>',
'MIME-Version'=>'1.0',
'Content-Type'=>'text/html; charset=UTF-8',
);
if(empty(CONFIG_SRV_NAME)) {
$grp_config_server_name = $_SERVER['HTTP_HOST'];
	} else {
	$grp_config_server_name = CONFIG_SRV_NAME;
	}
switch($msg) {
case 0:
// email confirmation
$title = 'Confirm address';
$loc = LOCATION.'/act/confirm?token='.$params;
$content = <<< EOF
	Hello!<br>
	<p>Someone (hopefully you) at {$_SERVER['REMOTE_ADDR']} has attempted to create an account on a grape server at "{$grp_config_server_name}" with user ID {$recipient['user_id']}.<br>
	<br>
	If this was you, the account can be activated here:<br><br>
	<a href="{$loc}/">{$loc}</a><br><br>
	Otherwise, don't respond to this message.<br>
	<br>
	Thank you!</p>
EOF;
break;
case 1:
// temp pass
$title = 'Temporary password';
$content = <<< EOF
	Hello!<br>
	<p>Someone (hopefully you) at {$_SERVER['REMOTE_ADDR']} has requested a temporary on a grape server at "{$grp_config_server_name}" with user ID {$recipient['user_id']}.<br>
	<br>
	If this was you, your temporary password is:<br>
	<b>{$params}</b><br>
	<br>
	This password can be used for up to 24 hours to log in and change your password.
	<br>
	Thank you!</p>
EOF;
break;
}
$headers['Subject'] = 'grape - '.$title;
$body = '
<!DOCTYPE html>
<html>
<head>
	<title>grape</title>
</head>
<body>
'.$content.'
</body>
</html>
'; // Define SMTP Parameters
/* The following option enables SMTP debugging and will print the SMTP conversation to the page, it will only help with authentication issues, if PEAR::Mail is not installed you won't get this far. */

$mode = 'smtp';
$mail_object = Mail::factory($mode, $grp_mail_param); // Print the parameters you are using to the page
// Send the message
$mail_object->send($recipients, $headers, $body);
}