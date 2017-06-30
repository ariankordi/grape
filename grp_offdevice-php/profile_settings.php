<?php
require_once '../grplib-php/init.php';

if(empty($_SESSION['pid'])) {
noLogin();  exit(); }

if($_SERVER['REQUEST_METHOD'] != 'POST') {
require_once 'lib/htm.php';

$pagetitle = 'Profile Settings';
printHeader('old'); printMenu('old');
# Start of main-body
print '<div id="main-body">
<h2 class="headline">Profile Settings</h2>
';
$me_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
require_once '../grplib-php/user-helper.php';
$profile = getProfile($me_user);

print '<form id="profile-settings-form" class="setting-form" method="post" action="/settings/profile">
  
  <ul class="settings-list">
    <li class="setting-profile-comment">
      <p class="settings-label">Profile Comment</p>
      <textarea id="profile-text" class="textarea" name="profile_comment" maxlength="1000" placeholder="Write about yourself here.">'.htmlspecialchars($profile['comment']).'</textarea>
      
    </li>
    <li class="setting-profile-post">
      <p class="settings-label">Favorite Post</p>
      <p class="note">You can set one of your own screenshot posts as your favorite from the settings button on that post.</p>
	  ';
	  if(!empty($profile['favorite_screenshot'])) {
    $get_screenshot_post = $mysql->query('SELECT screenshot FROM posts WHERE posts.id = "'.$profile['favorite_screenshot'].'" LIMIT 1');
	if($get_screenshot_post->num_rows != 0) {
	$screenshot_post = $get_screenshot_post->fetch_assoc();
print '      <div class="select-content">
        <button id="profile-post" type="button" class="submit"><img src="'.htmlspecialchars($screenshot_post['screenshot']).'"><span class="symbol">Remove</span></button>
	</div>'; } } print '
    </li>

    <li>
      <p class="settings-label"><label for="select_gender">What is your gender?</label></p>
      <div class="select-content">
        <div class="select-button">
          <select name="gender" id="select_gender">
            <option value="1"'.($profile['gender'] == '1' ? ' selected' : '').'>Male</option>
            <option value="2"'.($profile['gender'] == '2' ? ' selected' : '').'>Female</option>
            <option value="3"'.($profile['gender'] == '3' || $profile['gender'] == null ? ' selected' : '').'>Not applicable</option>
          </select>
        </div>
      </div>
    </li>
	
    <li>
      <p class="settings-label"><label for="select_country">What is your region?</label></p>
      <div class="select-content">
        <div class="select-button">
          <select name="country" id="select_country">
            <option value="North America"'.($profile['country'] == 'North America' ? ' selected' : '').'>North America</option>
            <option value="Europe"'.($profile['country'] == 'Europe' ? ' selected' : '').'>Europe</option>
            <option value="Japan"'.($profile['country'] == 'Japan' ? ' selected' : '').'>Japan</option>
          </select>
        </div>
      </div>
    </li>
    
    <li>
      <p class="settings-label"><label for="select_game_skill">How would you describe your experience with games?</label></p>
      <div class="select-content">
        <div class="select-button">
          <select name="game_skill" id="select_game_skill">
            <option value="0"'.($profile['game_experience'] == '0' ? ' selected' : '').'>Beginner</option>
            <option value="1"'.($profile['game_experience'] == '1' ? ' selected' : '').' selected>Intermediate</option>
            <option value="2"'.($profile['game_experience'] == '2' ? ' selected' : '').'>Expert</option>
          </select>
        </div>
      </div>
    </li>
    
    
    <li>
      <p class="settings-label"><label for="select_relationship_visibility">Who should be able to see your connections (friend list, followers and followed users)?</label></p>
      <div class="select-content">
        <div class="select-button">
          <select name="relationship_visibility" id="select_relationship_visibility">
            <option value="1"'.($profile['relationship_visibility'] == '1' ? ' selected' : '').'>Everyone</option>
            <option value="2"'.($profile['relationship_visibility'] == '2' ? ' selected' : '').'>Wii U Friends Only</option>
            <option value="3"'.($profile['relationship_visibility'] == '3' ? ' selected' : '').'>Keep Private</option>
          </select>
        </div>
      </div>
    </li>  
    
    
  </ul>
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
