<?php
require_once '../grplib-php/init.php';

if(empty($_SESSION['pid'])) {
notLoggedIn();  exit(); }

if($_SERVER['REQUEST_METHOD'] != 'POST') {
require_once 'lib/htm.php';

$pagetitle = 'Profile Settings';
require_once 'lib/htm.php';
printHeader(false);
printMenu();
require_once '../grplib-php/user-helper.php';
$profile = getProfile($me);

print $GLOBALS['div_body_head'];
print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header><div class="body-content">
  <div class="settings-list-content">
    <ul class="settings-list">
      <li class="setting-profile-comment scroll">
        <p class="settings-label">Profile Comment</p>
        <form method="post" action="/settings/profile" id="profile-text-form">
          <textarea id="profile-text" name="profile_comment" maxlength="1200" class="scroll-focus" placeholder="Write about yourself here.">'.htmlspecialchars($profile['comment']).'</textarea>	  
            
        </form>
      </li>
      <li class="setting-profile-post scroll'.(empty($profile['favorite_screenshot']) ? ' none-profile-post' : '').'">
	  ';
if(empty($profile['favorite_screenshot'])) {
print '        <p class="settings-label">Favorite Post<span class="note">You can set one of your own screenshot posts as your favorite via the settings button of that post.</span></p>
      </li>'; }
else {
print '<p class="settings-label">Favorite Post<span class="note">You can set one of your own screenshot posts as your favorite via the settings button of that post.</span></p>';
    $get_screenshot_post = $mysql->query('SELECT screenshot FROM posts WHERE posts.id = "'.$profile['favorite_screenshot'].'" LIMIT 1');
	if($get_screenshot_post->num_rows != 0) {
	$screenshot_post = $get_screenshot_post->fetch_assoc();
print '<a id="profile-post" href="#" class="scroll-focus"><img src="'.htmlspecialchars($screenshot_post['screenshot']).'"><span class="remove-button-label">Remove</span></a>
'; }
}

switch($profile['game_experience']) {  
case 0:
$game_experience_text = 'Beginner'; break;
case '1':
$game_experience_text = 'Intermediate'; break;
case '2':
$game_experience_text = 'Expert'; break;
}

switch($profile['gender']) {
case 0:
$sex_text = 'Not Set'; break;
case '1':
$sex_text = 'Male'; break;
case '2':
$sex_text = 'Female'; break;
case '3':
$sex_text = 'N/A'; break;
}

switch($profile['relationship_visibility']) {
case '1':
$relationship_visibility_text = 'Everyone'; break;
case '2':
$relationship_visibility_text = 'Wii U Friends Only'; break;
case '3':
$relationship_visibility_text = 'Private'; break;
}

if(empty($profile['country'])) {
$profile['country'] = 'Not Set'; }

print '

<li data-name="game_skill" class="scroll">
  <p class="settings-label">How would you describe your experience with games?</p>
  <a class="settings-button scroll-focus" href="#" data-modal-open=".settings-page[data-name=\'game_skill\']">'.$game_experience_text.'
  </a>
  
</li>


<li data-name="country" class="scroll">
  <p class="settings-label">What is your region?</p>
  <a class="settings-button scroll-focus" href="#" data-modal-open=".settings-page[data-name=\'country\']">'.htmlspecialchars($profile['country']).'
  </a>
  
</li>


<li data-name="gender" class="scroll">
  <p class="settings-label">What is your gender? (optional)</p>
  <a class="settings-button scroll-focus" href="#" data-modal-open=".settings-page[data-name=\'gender\']">'.$sex_text.'
  </a>
  
</li>


<li data-name="relationship_visibility" class="scroll">
  <p class="settings-label">Who should be able to see your friend list, followers, and followed users?</p>
  <a class="settings-button scroll-focus" href="#" data-modal-open=".settings-page[data-name=\'relationship_visibility\']">'.$relationship_visibility_text.'
  </a>
  
</li>

';


// Modals
print '   
      
    </ul>
  </div>
</div><div class="settings-page window-page none" data-modal-types="select-settings" data-action="/settings/profile" data-name="game_skill" style="position: absolute; left: 0px; top: 0px;">
  <div class="window">
    <h1 class="window-title">Profile Settings</h1>
    <div class="window-body"><div class="window-body-inner message">
        How would you describe your experience with games?
    </div></div>
    <div class="window-bottom-buttons scroll">
      <button class="checkbox-button post-button scroll-focus'.($profile['game_experience'] == 0 ? ' selected' : '').'" value="0" data-sound="SE_WAVE_TOGGLE_CHECK">Beginner</button>
      <button class="checkbox-button post-button scroll-focus'.($profile['game_experience'] == '1' ? ' selected' : '').'" value="1" data-sound="SE_WAVE_TOGGLE_CHECK">Intermediate</button>
      <button class="checkbox-button post-button scroll-focus'.($profile['game_experience'] == '2' ? ' selected' : '').'" value="2" data-sound="SE_WAVE_TOGGLE_CHECK">Expert</button>
    </div>
  </div>

</div><div class="settings-page window-page none" data-modal-types="select-settings" data-action="/settings/profile" data-name="relationship_visibility" style="position: absolute; left: 0px; top: 0px;">
  <div class="window">
    <h1 class="window-title">Profile Settings</h1>
    <div class="window-body"><div class="window-body-inner message">
        Who should be able to see your friend list, followers, and followed users?
    </div></div>
    <div class="window-bottom-buttons scroll">
      <button class="checkbox-button post-button scroll-focus'.($profile['relationship_visibility'] == '1' ? ' selected' : '').'" value="1" data-sound="SE_WAVE_TOGGLE_CHECK">Everyone</button>
      <button class="checkbox-button post-button scroll-focus'.($profile['relationship_visibility'] == '2' ? ' selected' : '').'" value="2" data-sound="SE_WAVE_TOGGLE_CHECK">Wii U Friends Only</button>
      <button class="checkbox-button post-button scroll-focus'.($profile['relationship_visibility'] == '3' ? ' selected' : '').'" value="3" data-sound="SE_WAVE_TOGGLE_CHECK">Private</button>
    </div>
  </div>
</div>

<div class="settings-page window-page none" data-modal-types="select-settings" data-action="/settings/profile" data-name="gender" style="position: absolute; left: 0px; top: 0px;">
  <div class="window">
    <h1 class="window-title">Profile Settings</h1>
    <div class="window-body"><div class="window-body-inner message">What is your gender?</div></div>
    <div class="window-bottom-buttons scroll">
      <button class="checkbox-button post-button scroll-focus'.($profile['gender'] == '1' ? ' selected' : '').'" value="1" data-sound="SE_WAVE_TOGGLE_CHECK">Male</button>
      <button class="checkbox-button post-button scroll-focus'.($profile['gender'] == '2' ? ' selected' : '').'" value="2" data-sound="SE_WAVE_TOGGLE_CHECK">Female</button>
      <button class="checkbox-button post-button scroll-focus'.($profile['gender'] == '3' || $profile['gender'] == null ? ' selected' : '').'" value="3" data-sound="SE_WAVE_TOGGLE_CHECK">Not applicable</button>
    </div>
  </div>
</div>

<div class="settings-page window-page none" data-modal-types="select-settings" data-action="/settings/profile" data-name="country" style="position: absolute; left: 0px; top: 0px;">
  <div class="window">
    <h1 class="window-title">Profile Settings</h1>
    <div class="window-body"><div class="window-body-inner message">What is your region?</div></div>
    <div class="window-bottom-buttons scroll">
      <button class="checkbox-button post-button scroll-focus'.($profile['country'] == 'North America' ? ' selected' : '').'" value="North America" data-sound="SE_WAVE_TOGGLE_CHECK">North America</button>
      <button class="checkbox-button post-button scroll-focus'.($profile['country'] == 'Europe' ? ' selected' : '').'" value="Europe" data-sound="SE_WAVE_TOGGLE_CHECK">Europe</button>
      <button class="checkbox-button post-button scroll-focus'.($profile['country'] == 'Japan' ? ' selected' : '').'" value="Japan" data-sound="SE_WAVE_TOGGLE_CHECK">Japan</button>
    </div>
  </div>
</div>';
print $GLOBALS['div_body_head_end'];	
printFooter();
	
}
# Method is POST. Update the profile.
else {

function invoke400() {
http_response_code(400); header('Content-Type: application/json'); print json_encode(array('success' => 0, 'errors' => [], 'code' => 400));  exit();
}
        if(isset($_POST['country']) && strlen($_POST['country']) > 50)
        { invoke400(); }
        if(isset($_POST['profile_comment']) && strlen($_POST['profile_comment']) > 1200)
        { invoke400(); }
		if(isset($_POST['gender']) && strlen($_POST['gender']) > 1)
        { invoke400(); }
		if(isset($_POST['game_skill']) && strval($_POST['game_skill']) > 2)
        { invoke400(); }
		if(isset($_POST['gender']) && strval($_POST['gender']) > 3)
        { invoke400(); }
		if(isset($_POST['relationship_visibility']) && strval($_POST['relationship_visibility']) > 3)
        { invoke400(); }

if(isset($_POST['game_skill'])) { $updates[] = 'game_experience = "'.$mysql->real_escape_string($_POST['game_skill']).'"'; }	
if(isset($_POST['profile_comment'])) { $updates[] = 'comment = "'.$mysql->real_escape_string($_POST['profile_comment']).'"'; }
if(isset($_POST['country'])) { $updates[] = 'country = "'.$mysql->real_escape_string($_POST['country']).'"'; }
if(isset($_POST['gender'])) { $updates[] = 'gender = "'.$mysql->real_escape_string($_POST['gender']).'"'; }
if(isset($_POST['relationship_visibility'])) { $updates[] = 'relationship_visibility = "'.$mysql->real_escape_string($_POST['relationship_visibility']).'"'; }
	
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