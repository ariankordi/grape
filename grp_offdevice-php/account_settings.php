<?php
require_once '../grplib-php/init.php';

if(empty($_SESSION['pid'])) {
noLogin(); grpfinish($mysql); exit(); }

if($_SERVER['REQUEST_METHOD'] != 'POST') {
require_once 'lib/htm.php';

$pagetitle = 'Account Settings';
printHeader('old'); printMenu('old');
# Start of main-body
print '<div id="main-body">
<h2 class="headline">Account Settings</h2>
';
$me_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
require_once '../grplib-php/user-helper.php';
$profile = getProfile($me_user);

print '<form id="profile-settings-form" class="setting-form" method="post" action="/settings/account">
  
  <ul class="settings-list">
 
    <li>
      <p class="settings-label"><label for="empathy_notice_opt_out">Do you want to receive notifications about Yeahs?</label></p>
      <div class="select-content">
        <div class="select-button">
          <select name="notify.empathy_notice_opt_out" id="empathy_notice_opt_out">
            <option value="0"'.($profile['relationship_visibility'] == '1' ? ' selected' : '').'>Receive</option>
            <option value="1"'.($profile['relationship_visibility'] == '2' ? ' selected' : '').'>Don\'t Receive</option>
          </select>
        </div>
      </div>
    </li>  
    
';
if($dev_server) {
print '
      <li class="scroll">
        <p class="settings-label">grape version '.VERSION.' (offdevice)
</p>
      </li>
      <li class="scroll">
        <p class="settings-label">Updated '.humanTiming(filemtime('../grplib-php/init.php')).'</p>
      </li>
';
	  }
print '	    </ul>
  <div class="form-buttons">
    <input type="submit" class="black-button apply-button" value="Save Settings">
  </div>
</form>';

# End of main-body
print '
</div>';
printFooter('old');
}
else {
// Post settings
function invoke400() {
http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 400)); grpfinish($mysql); exit();
}
        if(isset($_POST['notify_empathy_notice_opt_out']) && !is_numeric($_POST['notify_empathy_notice_opt_out']) && strval($_POST['notify_empathy_notice_opt_out']) > 1)
        { invoke400(); }

if(isset($_POST['notify_empathy_notice_opt_out'])) { $updates[] = 'empathy_optout = "'.$mysql->real_escape_string($_POST['notify_empathy_notice_opt_out']).'"'; }
	
	$sql_update = 'UPDATE profiles SET '.(implode(', ', $updates)).' WHERE profiles.pid = "'.$_SESSION['pid'].'"';
$update_profile = $mysql->query($sql_update);
if(!$update_profile) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8');
print json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500)); } else {
header('Content-Type: application/json; charset=utf-8');
print json_encode(array('success' => 1));
}

}