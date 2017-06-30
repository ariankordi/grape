<?php
require_once '../grplib-php/init.php';

if(empty($_SESSION['pid'])) {
notLoggedIn();  exit(); }

if($_SERVER['REQUEST_METHOD'] != 'POST') {
// View settings
require_once 'lib/htm.php';

// 'Miiverse Settings'
$pagetitle = 'Account Settings';
printHeader(false); printMenu();
$me_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
require_once '../grplib-php/user-helper.php';
$profile = getProfile($me_user);

print $GLOBALS['div_body_head'];
print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header><div class="body-content">
  <div class="settings-list-content">
    <ul class="settings-list">
';

switch($profile['empathy_optout']) {
case 0:
$empathyoptout_text = 'Receive'; break;
case '1':
$empathyoptout_text = 'Don\'t Receive'; break;
}
switch($profile['allow_request']) {
case 0:
$allowrequest_text = 'Don\'t Allow'; break;
case '1':
$allowrequest_text = 'Allow'; break;
}

print '
<li data-name="allow_request" class="scroll">
  <p class="settings-label">Do you want to allow friend requests?</p>
  <a class="settings-button scroll-focus" href="#" data-modal-open=".settings-page[data-name=\'allow_request\']">'.$allowrequest_text.'
  </a>
  
</li>
';
/*
<li data-name="allow_reply" class="scroll">
  <p class="settings-label">Who can comment on your posts?</p>
  <a class="settings-button scroll-focus" href="#" data-modal-open=".settings-page[data-name=\'allow_reply\']">Everyone
  </a>
  
</li>









*/
print '
<li data-name="notify.empathy_notice_opt_out" class="scroll">
  <p class="settings-label">Do you want to receive notifications about Yeahs?</p>
  <a class="settings-button scroll-focus" href="#" data-modal-open=".settings-page[data-name=\'notify.empathy_notice_opt_out\']">'.$empathyoptout_text.'
  </a>
  
</li>

';
if($dev_server) {
print '
      <li class="scroll">
        <p class="settings-label">grape version '.$version.' (portal)
</p>
      </li>
      <li class="scroll">
        <p class="settings-label">Updated '.humanTiming(filemtime('../grplib-php/init.php')).'</p>
      </li>
';
	  }
print '
    </ul>
  </div>
</div>


<div class="settings-page window-page none" data-modal-types="select-settings" data-action="/settings/account" data-name="allow_request">
  <div class="window">
    <h1 class="window-title">Account Settings</h1>
    <div class="window-body"><div class="window-body-inner message">
        Do you want to allow friend requests?
    </div></div>
    <div class="window-bottom-buttons scroll">
      <button class="checkbox-button post-button scroll-focus" value="0" data-sound="SE_WAVE_TOGGLE_CHECK">Don\'t Allow</button>
      <button class="checkbox-button post-button scroll-focus selected" value="1" data-sound="SE_WAVE_TOGGLE_CHECK">Allow</button>
    </div>
  </div>
</div>
';
/*
<div class="settings-page window-page none" data-modal-types="select-settings" data-action="/settings/account" data-name="allow_reply">
  <div class="window">
    <h1 class="window-title">Miiverse Settings</h1>
    <div class="window-body"><div class="window-body-inner message">
        Who can comment on your posts?
    </div></div>
    <div class="window-bottom-buttons scroll">
      <button class="checkbox-button post-button scroll-focus selected" value="1" data-sound="SE_WAVE_TOGGLE_CHECK">Everyone</button>
      <button class="checkbox-button post-button scroll-focus" value="2" data-sound="SE_WAVE_TOGGLE_CHECK">Wii U Friends Only</button>
    </div>
  </div>
</div>








*/
print '

<div class="settings-page window-page none" data-modal-types="select-settings" data-action="/settings/account" data-name="notify.empathy_notice_opt_out">
  <div class="window">
    <h1 class="window-title">Account Settings</h1>
    <div class="window-body"><div class="window-body-inner message">
        Do you want to receive notifications about Yeahs?
    </div></div>
    <div class="window-bottom-buttons scroll">
      <button class="checkbox-button post-button scroll-focus'.($profile['empathy_optout'] == 0 ? ' selected' : '').'" value="0" data-sound="SE_WAVE_TOGGLE_CHECK">Receive</button>
      <button class="checkbox-button post-button scroll-focus'.($profile['empathy_optout'] == '1' ? ' selected' : '').'" value="1" data-sound="SE_WAVE_TOGGLE_CHECK">Don\'t Receive</button>
    </div>
  </div>
</div>
';

print $GLOBALS['div_body_head_end'];
printFooter();
} else {
// Post settings
function invoke400() {
http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 400));  exit();
}
        if(isset($_POST['notify_empathy_notice_opt_out']) && !is_numeric($_POST['notify_empathy_notice_opt_out']) && strval($_POST['notify_empathy_notice_opt_out']) > 1)
        { invoke400(); }
	    if(isset($_POST['allow_request']) && !is_numeric($_POST['allow_request']) && strval($_POST['allow_request']) > 1)
        { invoke400(); }

if(isset($_POST['notify_empathy_notice_opt_out'])) { $updates[] = 'empathy_optout = "'.$mysql->real_escape_string($_POST['notify_empathy_notice_opt_out']).'"'; }
if(isset($_POST['allow_request'])) { $updates[] = 'allow_request = "'.$mysql->real_escape_string($_POST['allow_request']).'"'; }
	
	$sql_update = 'UPDATE profiles SET '.(implode(', ', $updates)).' WHERE profiles.pid = "'.$_SESSION['pid'].'"';
$update_profile = $mysql->query($sql_update);
if(!$update_profile) {
http_response_code(500);
header('Content-Type: application/json');
print json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500)); } else {
header('Content-Type: application/json');
print json_encode(array('success' => 1));
}

}