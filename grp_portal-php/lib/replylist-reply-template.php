<?php


#Include example:


#$template_creator_pid = $row_reply_user['pid'];
#$template_creator_user_id = $row_reply_user['user_id'];
#$template_creator_screen_name = $row_reply_user['screen_name'];
#$template_creator_mii_hash = $row_reply_user['mii_hash'];
#$template_creator_user_face = $row_reply_user['user_face'];
#$template_creator_official_user = $row_reply_user['official-user'];
#$template_reply_id = $row_reply['id'];
#$template_reply_to_id = $row_reply['reply_to_id'];
#$template_reply_pid = $row_reply['pid'];
#$template_reply_body = $row_reply['body'];
#$template_reply_screenshot = $row_reply_created['screenshot'];
#$template_reply_is_hidden = $row_reply['is_hidden'];
#$template_reply_hidden_resp = $row_reply['hidden_resp'];
#$template_reply_created_at = $row_reply['created_at'];
#$template_reply_spoiler = $row_reply['is_spoiler'];
#$template_reply_feeling_id = $row_reply['feeling_id'];

#$template_result_reply_empathies = $result_reply_empathies;
#include 'lib/replylist-reply-template.php';

if($template_reply_is_hidden == 1 && $template_reply_hidden_resp == '1') {
print null;	} else {

$sql_reply_post = 'SELECT * FROM posts WHERE posts.id = "' . $template_reply_to_id . '"';
$result_reply_post = mysqli_query($mysql, $sql_reply_post);
$row_reply_post = mysqli_fetch_assoc($result_reply_post);

// If the post's creator PID is equal to the reply creator's PID, then add class 'my'.
if($template_reply_pid == $row_reply_post['pid']) {
$if_reply_is_my = ' my'; }
else {
$if_reply_is_my = ' other'; }

	if($template_creator_mii_hash) {
if(($template_reply_feeling_id) == '0') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $template_creator_mii_hash . '_normal_face.png'; 
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!'); }
if(($template_reply_feeling_id) == '1') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $template_creator_mii_hash . '_happy_face.png'; 
$mii_face_feeling = 'happy'; }
$mii_face_miitoo = htmlspecialchars('Yeah!');
if(($template_reply_feeling_id) == '2') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $template_creator_mii_hash . '_like_face.png'; 
$mii_face_feeling = 'like';
$mii_face_miitoo = htmlspecialchars('Yeah♥'); }
if(($template_reply_feeling_id) == '3') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $template_creator_mii_hash . '_surprised_face.png'; 
$mii_face_feeling = 'surprised';
$mii_face_miitoo = htmlspecialchars('Yeah!?'); }
if(($template_reply_feeling_id) == '4') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $template_creator_mii_hash . '_frustrated_face.png'; 
$mii_face_feeling = 'frustrated';
$mii_face_miitoo = htmlspecialchars('Yeah...'); }
if(($template_reply_feeling_id) == '5') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $template_creator_mii_hash . '_puzzled_face.png'; 
$mii_face_feeling = 'puzzled';
$mii_face_miitoo = htmlspecialchars('Yeah...'); }
}
else {
if($template_creator_user_face) {
$mii_face_output = htmlspecialchars($template_creator_user_face);
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!');
} else {
$mii_face_output = '/img/mii/img_unknown_MiiIcon.png';
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!'); }
}

if($template_creator_official_user == 1) {
$is_replier_official_user = ' official-user';
}
else {
$is_replier_official_user = ''; }

	if(($template_reply_spoiler) == '1') {
	if(isset($_SESSION['pid']) && $template_reply_pid == $_SESSION['pid']) {
	$if_has_can_spoiler_reply = ''; }
	else {
	$if_has_can_spoiler_reply = ' hidden'; }
	}
	else {
	$if_has_can_spoiler_reply = ''; }


print '		   <li id="reply-' . $template_reply_id . '" class="test-fresh-reply scroll' . $if_reply_is_my . '' . $if_has_can_spoiler_reply . '">
  <a href="/users/' . htmlspecialchars($template_creator_user_id) . '" data-pjax="#body" class="scroll-focus user-icon-container' . $is_replier_official_user . '"><img src="' . $mii_face_output . '" class="user-icon"></a>
  ';
if($template_reply_is_hidden == 1 && $template_reply_hidden_resp == 0) {
require_once 'lib/olv-url-enc.php';
	print '<div class="reply-content">
        <p class="deleted-message">Deleted by administrator.</p>
        <p class="deleted-message">Comment ID: '.getPostID($template_reply_id).'</p>


      </div>
	  </li>';	}
else { print '
  <div class="reply-content">
    <header>
      <span class="user-name">' . htmlspecialchars($template_creator_screen_name) . '</span>
      <span class="timestamp">' . humanTiming(strtotime($template_reply_created_at)) . '</span>
	  ';
		if($template_reply_spoiler == '1') {       
		print '	  <span class="spoiler-status spoiler">Spoilers</span>'; }
		
	# Can the user give an empathy? Used later.
	if(isset($row_current_peopleban)) {
	$can_post_user_miitoo = ' disabled'; }
	elseif(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && $template_creator_pid != $_SESSION['pid']) {
	$can_post_user_miitoo = ''; }
	else {
	$can_post_user_miitoo = ' disabled'; }	
		
print '    </header>


<p class="reply-content-text">' . htmlspecialchars($template_reply_body) . '</p>';
	 if(isset($template_reply_screenshot)) {
     if(strlen($template_reply_screenshot) > 3) {
	print '<a href="#" role="button" class="title-capture-container capture-container" data-modal-open="#capture-page" data-large-capture-url="' . htmlspecialchars($template_reply_screenshot) . '"><img src="' . htmlspecialchars($template_reply_screenshot) . '" class="title-capture"></a>';	 
	 } }
	if ($template_reply_spoiler == '1') {
	if(isset($_SESSION['pid'])) {
	if($template_creator_pid != $_SESSION['pid']) {
	print '	<div class="hidden-content">
        <p>This comment contains spoilers.</p>
        <div><a href="#" class="hidden-content-button">View Post</a></div>
	</div>'; } }
	else {
	print '	<div class="hidden-content">
        <p>This comment contains spoilers.</p>
        <div><a href="#" class="hidden-content-button">View Post</a></div>
	</div>'; }
	}

// Has the user given this reply a yeah?
    if(isset($_SESSION['pid'])) {	
$sql_reply_hasempathy = 'SELECT * FROM empathies WHERE empathies.id = "' . mysqli_real_escape_string($mysql, $template_reply_id) . '" AND empathies.pid = "' . $_SESSION['pid'] . '"';
$result_reply_hasempathy = mysqli_query($mysql, $sql_reply_hasempathy);

if(mysqli_num_rows($result_reply_hasempathy)!=0) {
    $mii_face_miitoo = 'Unyeah';
	${"has_reply_miitoo_given_$template_reply_id"} = ' empathy-added'; 
$has_reply_miitoo_given_snd = 'SE_WAVE_MII_CANCEL'; }
	else {
	${"has_reply_miitoo_given_$template_reply_id"} = ''; 
	$has_reply_miitoo_given_snd = 'SE_WAVE_MII_ADD'; }
	}
	else {
	${"has_reply_miitoo_given_$template_reply_id"} = ''; 
	$has_reply_miitoo_given_snd = 'SE_WAVE_MII_ADD'; }
print '


    <div class="reply-meta">
      <button type="button"' . $can_post_user_miitoo . '
              class="submit miitoo-button' . ${"has_reply_miitoo_given_$template_reply_id"} . '"
              data-feeling="' . $mii_face_feeling . '"
              data-action="/replies/' . $template_reply_id . '/empathies"
              data-sound="' . $has_reply_miitoo_given_snd . '"
              data-url-id="' . $template_reply_id . '" data-track-label="reply" data-track-action="yeah" data-track-category="empathy"
      >' . $mii_face_miitoo . '</button>
      <a href="/replies/' . $template_reply_id . '" class="to-permalink-button" data-pjax="#body">
        <span class="feeling">' . mysqli_num_rows($result_reply_empathies) . '</span>
      </a>
    </div>
  </div>
</li>';
}
}





?>