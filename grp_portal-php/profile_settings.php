<?php
require_once '../grplib-php/init.php';

# User checks.
if(empty($_SESSION['pid'])) {
header('Location: http://' . $_SERVER['HTTP_HOST'] .'/guest_menu', true, 302); 
}
else {

$sql_profile_edit_user_profile = 'SELECT * FROM profiles WHERE profiles.pid = "' . $_SESSION['pid'] . '"';
$result_profile_edit_user_profile = mysqli_query($mysql, $sql_profile_edit_user_profile);
$row_profile_edit_user_profile = mysqli_fetch_assoc($result_profile_edit_user_profile); 
 
if(mysqli_num_rows($result_profile_edit_user_profile) == 0) {
header('Location: http://' . $_SERVER['HTTP_HOST'] .'/profiles', true, 302); }
else {
# If method isn't POST, display profile settings page.
if($_SERVER['REQUEST_METHOD'] != 'POST')
{
$pagetitle = 'Profile Settings';
require_once 'lib/htm.php';
printHeader(false);
printMenu();
print $GLOBALS['div_body_head'];
print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header><div class="body-content">
  <div class="settings-list-content">
    <ul class="settings-list">
      <li class="setting-profile-comment scroll">
        <p class="settings-label">Profile Comment</p>
        <form method="post" action="/settings/profile" id="profile-text-form">
          <textarea id="profile-text" name="profile_comment" maxlength="1200" class="scroll-focus" placeholder="Write about yourself here.">'.htmlspecialchars($row_profile_edit_user_profile['comment']).'</textarea>	  
            
        </form>
      </li>
      <li class="setting-profile-post scroll';
if(empty($row_userpage_user_profile['favorite_screenshot']) && strlen($row_profile_edit_user_profile['favorite_screenshot']) < 5) {
print ' none-profile-post'; }	  
	  
	  print '">
	  ';
if(empty($row_userpage_user_profile['favorite_screenshot']) && strlen($row_profile_edit_user_profile['favorite_screenshot']) < 5) {
print '        <p class="settings-label">Favorite Post<span class="note">You can set one of your own screenshot posts as your favorite via the settings button of that post.</span></p>
      </li>'; }
else {
print '<p class="settings-label">Favorite Post<span class="note">You can set one of your own screenshot posts as your favorite via the settings button of that post.</span></p>';
$result_posts_getfavoritepost = mysqli_query($mysql, 'SELECT * FROM posts WHERE posts.id = "'.mysqli_real_escape_string($mysql, $row_profile_edit_user_profile['favorite_screenshot']).'"');
print '<a id="profile-post" href="#" class="scroll-focus"><img src="'.htmlspecialchars(mysqli_fetch_assoc($result_posts_getfavoritepost)['screenshot']).'"><span class="remove-button-label">Remove</span></a>
'; }
	  
if($row_profile_edit_user_profile['game_experience'] == '0') {
$profile_edit_experience_title = 'Beginner'; 
$experience_select_0 = ' selected';
$experience_select_1 = '';
$experience_select_2 = ''; }
if($row_profile_edit_user_profile['game_experience'] == '1') {
$profile_edit_experience_title = 'Intermediate';
$experience_select_0 = '';
$experience_select_1 = ' selected';
$experience_select_2 = ''; }
if($row_profile_edit_user_profile['game_experience'] == '2') {
$profile_edit_experience_title = 'Expert';
$experience_select_0 = '';
$experience_select_1 = '';
$experience_select_2 = ' selected'; }
else {
$profile_edit_experience_title = 'Beginner'; {
$experience_select_0 = ' selected';
$experience_select_1 = '';
$experience_select_2 = ''; }
}

if($row_profile_edit_user_profile['gender']) {
if($row_profile_edit_user_profile['gender'] == '1') {
$profile_edit_sex_title = 'Male'; 
$sex_select_0 = ' selected';
$sex_select_1 = '';
$sex_select_2 = ''; }
if($row_profile_edit_user_profile['gender'] == '2') {
$profile_edit_sex_title = 'Female';
$sex_select_0 = '';
$sex_select_1 = ' selected';
$sex_select_2 = ''; }
if($row_profile_edit_user_profile['gender'] == '3') {
$profile_edit_sex_title = 'N/A';
$sex_select_0 = '';
$sex_select_1 = '';
$sex_select_2 = ' selected'; }
}
else {
$profile_edit_sex_title = 'Not Set';
$sex_select_0 = '';
$sex_select_1 = '';
$sex_select_2 = ' selected'; 
}

if(empty($row_profile_edit_user_profile['country'])) {
$row_profile_edit_user_profile['country'] = 'Not Set'; }

print '

<li data-name="game_skill" class="scroll">
  <p class="settings-label">How would you describe your experience with games?</p>
  <a class="settings-button scroll-focus" href="#" data-modal-open=".settings-page[data-name=';
  print "'game_skill']";
  print '">'.$profile_edit_experience_title.'
  </a>
  
</li>


<li data-name="country" class="scroll">
  <p class="settings-label">What is your region?</p>
  <a class="settings-button scroll-focus" href="#" data-modal-open=".settings-page[data-name=';
  print "'country']";
  print '">'.htmlspecialchars($row_profile_edit_user_profile['country']).'
  </a>
  
</li>


<li data-name="gender" class="scroll">
  <p class="settings-label">What is your gender? (optional)</p>
  <a class="settings-button scroll-focus" href="#" data-modal-open=".settings-page[data-name=';
  print "'gender']";
  print '">'.$profile_edit_sex_title.'
  </a>
  
</li>


<li data-name="relationship_visibility" class="scroll">
  <p class="settings-label">[Not implemented] Who should be able to see your friend list, followers, and followed users?</p>
  <a class="settings-button scroll-focus" href="#" data-modal-open=".settings-page[data-name=';
  print "'relationship_visibility']";
  print '">Everyone
  </a>
  
</li>

';
# This should always be at the end

if(isset($grp_config_server_type) && $grp_config_server_type == 'dev' && isset($grp_config_server_env)) {
print '<li class="scroll">
        <p class="settings-label">[DEBUG] Server type : dev</p>
      </li>
<li class="scroll">
        <p class="settings-label">[DEBUG] Server environment : '.$grp_config_server_env.'</p>
      </li>';
}




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
      <button class="checkbox-button post-button scroll-focus'.$experience_select_0.'" value="0" data-sound="SE_WAVE_TOGGLE_CHECK">Beginner</button>
      <button class="checkbox-button post-button scroll-focus'.$experience_select_1.'" value="1" data-sound="SE_WAVE_TOGGLE_CHECK">Intermediate</button>
      <button class="checkbox-button post-button scroll-focus'.$experience_select_2.'" value="2" data-sound="SE_WAVE_TOGGLE_CHECK">Expert</button>
    </div>
  </div>
</div><div class="settings-page window-page none" data-modal-types="select-settings" data-action="/settings/profile" data-name="relationship_visibility">
  <div class="window">
    <h1 class="window-title">Profile Settings</h1>
    <div class="window-body"><div class="window-body-inner message">
        Who should be able to see your friend list, followers, and followed users?
    </div></div>
    <div class="window-bottom-buttons scroll">
      <button class="checkbox-button post-button scroll-focus selected" value="1" data-sound="SE_WAVE_TOGGLE_CHECK">Everyone</button>
    </div>
  </div>
</div>

<div class="settings-page window-page none" data-modal-types="select-settings" data-action="/settings/profile" data-name="gender" style="position: absolute; left: 0px; top: 0px;">
  <div class="window">
    <h1 class="window-title">Profile Settings</h1>
    <div class="window-body"><div class="window-body-inner message">What is your gender?</div></div>
    <div class="window-bottom-buttons scroll">
      <button class="checkbox-button post-button scroll-focus'.$sex_select_0.'" value="1" data-sound="SE_WAVE_TOGGLE_CHECK">Male</button>
      <button class="checkbox-button post-button scroll-focus'.$sex_select_1.'" value="2" data-sound="SE_WAVE_TOGGLE_CHECK">Female</button>
      <button class="checkbox-button post-button scroll-focus'.$sex_select_2.'" value="3" data-sound="SE_WAVE_TOGGLE_CHECK">Not applicable</button>
    </div>
  </div>
</div>

<div class="settings-page window-page none" data-modal-types="select-settings" data-action="/settings/profile" data-name="country" style="position: absolute; left: 0px; top: 0px;">
  <div class="window">
    <h1 class="window-title">Profile Settings</h1>
    <div class="window-body"><div class="window-body-inner message">What is your region?</div></div>
    <div class="window-bottom-buttons scroll">
      <button class="checkbox-button post-button scroll-focus" value="North America" data-sound="SE_WAVE_TOGGLE_CHECK">North America</button>
      <button class="checkbox-button post-button scroll-focus" value="Europe" data-sound="SE_WAVE_TOGGLE_CHECK">Europe</button>
      <button class="checkbox-button post-button scroll-focus" value="Japan" data-sound="SE_WAVE_TOGGLE_CHECK">Japan</button>
    </div>
  </div>
</div>';
print $GLOBALS['div_body_head_end'];	
printFooter();
	
}
# Method is POST. Update the profile.
else {
        if(isset($_POST['country']) && strlen($_POST['country']) > 50)
        {
            $error_message[] = 'Your country name is too long.\nPlease enter a shorter one.';
			$error_code[] = '1056010';
        }	
        if(isset($_POST['profile_comment']) && strlen($_POST['profile_comment']) > 1200)
        {
            $error_message[] = 'Your profile comment is too long.\nPlease enter a shorter one.';
			$error_code[] = '1056010';
        }
		if(isset($_POST['game_skill']) && strlen($_POST['game_skill']) > 1)
        {
            $error_message[] = 'The data recieved was invalid.';
			$error_code[] = '1056010';
        }
		if(isset($_POST['gender']) && strlen($_POST['gender']) > 1)
        {
            $error_message[] = 'The data recieved was invalid.';
			$error_code[] = '1056010';
        }
		if(isset($_POST['game_skill']) && strval($_POST['game_skill']) > 2)
        {
            $error_message[] = 'The data recieved was invalid.';
			$error_code[] = '1056010';
        }
		if(isset($_POST['gender']) && strval($_POST['gender']) > 3)
        {
            $error_message[] = 'The data recieved was invalid.';
			$error_code[] = '1056010';
        }
    if(!empty($error_code) || !empty($error_message) ) /*Got errors?*/
    {
		// JSON response for errors.
			http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
			print '{"success":0,"errors":[{"message":"' . $error_message[0] . '","error_code":' . $error_code[0] . '}],"code":"400"}';
			print "\n";
    }
	
	else {
	// Update user's profile.	
	
	if(isset($_POST['game_skill'])) {
    $sql_update = 'UPDATE profiles SET game_experience="'.mysqli_real_escape_string($mysql, $_POST['game_skill']).'" WHERE pid="'.$_SESSION['pid'].'"';
    }
	
	if(isset($_POST['profile_comment'])) {
    $sql_update = 'UPDATE profiles SET comment="'.mysqli_real_escape_string($mysql, $_POST['profile_comment']).'" WHERE pid="'.$_SESSION['pid'].'"';
    }
	
	if(isset($_POST['country'])) {
    $sql_update = 'UPDATE profiles SET country="'.mysqli_real_escape_string($mysql, $_POST['country']).'" WHERE pid="'.$_SESSION['pid'].'"';
    }
	
	if(isset($_POST['gender'])) {
    $sql_update = 'UPDATE profiles SET gender="'.mysqli_real_escape_string($mysql, $_POST['gender']).'" WHERE pid="'.$_SESSION['pid'].'"';
    }
	
	if(isset($sql_update)) {
	    $result = mysqli_query($mysql, $sql_update);
        if(!$result)
        {
            //MySQL error; print jsON response.
			http_response_code(400);  
			header('Content-Type: application/json; charset=utf-8');
			
			// Enable in debug
			#print $sql_update;
			#print "\n\n";			
			
			print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($mysql) . '}],"code":"500"}';
			print "\n";
		}
		else { 
header('Content-Type: application/json; charset=utf-8');
print '{"success":1}'; 
}

}
else {
			http_response_code(400);  
			header('Content-Type: application/json; charset=utf-8');			
			
			print '{"success":0,"errors":[{"message":"Not implemented.","error_code":1515011}],"code":"501"}';
			print "\n";
}

	}
	
	}
	
}

}
