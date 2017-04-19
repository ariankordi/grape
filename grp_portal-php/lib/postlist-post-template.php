<?php

#Include example:


#$template_creator_pid = $row_post_user['pid'];
#$template_creator_user_id = $row_post_user['user_id'];
#$template_creator_screen_name = $row_post_user['screen_name'];
#$template_creator_mii_hash = $row_post_user['mii_hash'];
#$template_creator_user_face = $row_post_user['user_face'];
#$template_creator_official_user = $row_post_user['official_user'];
#$template_post_id = $row_post['id'];
#$template_post_pid = $row_post['pid'];
#$template_post_body = $row_post['body'];
#$template_post_url = $row_post['url'];
#$template_post_type = $row_post['_post_type'];
#$template_post_is_hidden = $row_post['is_hidden'];
#$template_post_screenshot = $row_post['screenshot'];
#$template_post_created_at = $row_post['created_at'];
#$template_post_spoiler = $row_post['is_spoiler'];
#$template_post_feeling_id = $row_post['feeling_id'];

#$template_result_post_empathies = $result_post_empathies;
#$template_result_post_replies = $result_post_replies;

#$sql_post_recent_replies = 'SELECT * FROM replies WHERE replies.reply_to_id = "' . $row_post['id'] . '" ORDER BY replies.created_at DESC LIMIT 1';
#$result_post_recent_replies = mysqli_query($mysql, $sql_post_recent_replies);

#include 'lib/postlist-post-template.php';

if($template_post_is_hidden == true) {
	print null;
}
else {
	if($template_creator_mii_hash) {
if(($template_post_feeling_id) == '0') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $template_creator_mii_hash . '_normal_face.png'; 
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!'); }
if(($template_post_feeling_id) == '1') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $template_creator_mii_hash . '_happy_face.png'; 
$mii_face_feeling = 'happy'; }
$mii_face_miitoo = htmlspecialchars('Yeah!');
if(($template_post_feeling_id) == '2') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $template_creator_mii_hash . '_like_face.png'; 
$mii_face_feeling = 'like';
$mii_face_miitoo = htmlspecialchars('Yeah♥'); }
if(($template_post_feeling_id) == '3') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $template_creator_mii_hash . '_surprised_face.png'; 
$mii_face_feeling = 'surprised';
$mii_face_miitoo = htmlspecialchars('Yeah!?'); }
if(($template_post_feeling_id) == '4') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $template_creator_mii_hash . '_frustrated_face.png'; 
$mii_face_feeling = 'frustrated';
$mii_face_miitoo = htmlspecialchars('Yeah...'); }
if(($template_post_feeling_id) == '5') {
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
	
	# Is the user the original poster? If so, don't display the spoiler.
	
	if(($template_post_spoiler) == '1') {
	if(isset($_SESSION['pid']) && $template_post_pid == $_SESSION['pid']) {
	$if_has_can_spoiler_post = ''; }
	else {
	$if_has_can_spoiler_post = ' hidden'; }
	}
	else {
	$if_has_can_spoiler_post = ''; }
	
	
// If you need any comments (like for admins), PUT THEM HERE!!!!!	
	
	
	
# Is there a YT video? If so, display it.
if(isset($template_post_url)) {
if (strpos( $template_post_url, 'www.youtube.com/watch?v=') !== false) {
if(substr($template_post_url, 0, 4) == "http") {
${"template_post_yt_url_$template_post_id"} = substr($template_post_url, 31, 11);
$if_has_can_video_post = ' with-video-image';
}
if(substr($template_post_url, 0, 5) == "https") {
${"template_post_yt_url_$template_post_id"} = substr($template_post_url, 32, 11);
$if_has_can_video_post = ' with-video-image';
}
    }
}

if (empty(${"template_post_yt_url_$template_post_id"})) {
	$if_has_can_video_post = '';
}

// If a screenshot was specified, define the class for it.
if(isset($template_post_screenshot)) {
if(strlen($template_post_screenshot) > 3) {
$if_has_can_screenshot_post = ' with-image'; }
else {
    $if_has_can_screenshot_post = '';
}  }
else {
    $if_has_can_screenshot_post = '';
}

// Is the user verified?
if($template_creator_official_user == 1) {
$is_poster_official_user = ' official-user';
}
else {
$is_poster_official_user = ''; }
	
	print '<div id="post-' . $template_post_id . '" class="post scroll post-subtype-default' . $if_has_can_spoiler_post . '' . $if_has_can_video_post . '' . $if_has_can_screenshot_post . '" data-post-permalink-url="/posts/' . $template_post_id . '">
  <a href="/users/' . htmlspecialchars($template_creator_user_id) . '" class="user-icon-container scroll-focus' . $is_poster_official_user . '" data-pjax="#body"><img src="' . $mii_face_output . '" class="user-icon"></a>
  <div class="post-body-content">
    <div class="post-body">
      <header>
        <span class="user-name">' . htmlspecialchars($template_creator_screen_name) . '</span>';
print '
        <span class="timestamp">' . humanTiming(strtotime($template_post_created_at)) . '</span>';
        // Spoilers
		
		if($template_post_spoiler == '1') { 
		print '<span class="spoiler-status spoiler">Spoilers</span>';
		}
		print '';
      print '</header>';
	 
// If there was a YT video previously stated, display it now.
if(!empty(${"template_post_yt_url_$template_post_id"})) {

print '<div class="title-capture-container video-container">
<a class="video-thumbnail" href="/posts/' . $template_post_id . '#post-video" data-pjax="#body">
<span><img width="120" height="90" src="https://i.ytimg.com/vi/' . ${"template_post_yt_url_$template_post_id"} . '/default.jpg"></span></a></div>';
}

// If screenshot was specified, display it (screenshot was checked before this)
if(isset($template_post_screenshot)) {
if(strlen($template_post_screenshot) > 3) {
print '<a href="#" role="button" class="title-capture-container capture-container" data-modal-open="#capture-page" data-large-capture-url="' . htmlspecialchars($template_post_screenshot) . '"><img src="' . htmlspecialchars($template_post_screenshot) . '" class="title-capture"></a>';
  }
}
	  
      print '<div class="post-content">
';
$truncate_post_bodyp1 = mb_substr(($template_post_body), 0, 200, 'utf-8');
if(mb_strlen($truncate_post_bodyp1, 'utf-8') >= 200) {
$truncate_post_body = "$truncate_post_bodyp1..."; }
else {
$truncate_post_body = $truncate_post_bodyp1; }
if($template_post_type == 'artwork') {
print '<p class="post-content-memo"><img src="'.htmlspecialchars($template_post_body).'" class="post-memo"></p>
	  </div>'; } else {
print '
            <p class="post-content-text">' . preg_replace("/[\r\n]+/", "\n", $truncate_post_body) . '</p>
      </div>
	  '; }
	if ($template_post_spoiler == '1' && isset($_SESSION['pid'])) {
	if($template_creator_pid != $_SESSION['pid']) {
	print '	<div class="hidden-content">
        <p>This post contains spoilers.</p>
        <div><a href="#" class="hidden-content-button">View Post</a></div>
      </div>'; } }
else {	
	print '	<div class="hidden-content">
        <p>This post contains spoilers.</p>
        <div><a href="#" class="hidden-content-button">View Post</a></div>
      </div>'; }

    // Has the user given this post an empathy?

    if(isset($_SESSION['pid'])) {	
$sql_hasempathy = 'SELECT * FROM empathies WHERE empathies.id = "' . mysqli_real_escape_string($mysql, $template_post_id) . '" AND empathies.pid = "' . $_SESSION['pid'] . '"';
$result_hasempathy = mysqli_query($mysql, $sql_hasempathy);

if(mysqli_num_rows($result_hasempathy)!=0) {
    $mii_face_miitoo = 'Unyeah';
	${"has_post_miitoo_given_$template_post_id"} = ' empathy-added'; 
$has_post_miitoo_given_snd = 'SE_WAVE_MII_CANCEL'; }
	} 
	else {
	${"has_post_miitoo_given_$template_post_id"} = ''; 
	$has_post_miitoo_given_snd = 'SE_WAVE_MII_ADD'; }
	  
	  print '<div class="post-meta">
        <button type="button"';
		// Can the user give empathies?
if(isset($row_current_peopleban)) {
$can_post_user_miitoo = ' disabled'; }
		elseif(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && $template_creator_pid != $_SESSION['pid']) {
	$can_post_user_miitoo = ''; }
	else {
	$can_post_user_miitoo = ' disabled'; }	
	
	if(empty(${"has_post_miitoo_given_$template_post_id"})) {
${"has_post_miitoo_given_$template_post_id"} = ''; }

		print $can_post_user_miitoo;
		print '
		class="submit miitoo-button' . ${"has_post_miitoo_given_$template_post_id"} . '' . $can_post_user_miitoo . '" data-feeling="' . $mii_face_feeling . '" data-action="/posts/' . $template_post_id . '/empathies" data-sound="SE_WAVE_MII_ADD" data-url-id="' . $template_post_id . '" data-track-label="default" data-track-action="yeah" data-track-category="empathy">' . $mii_face_miitoo . '</button>
        <a href="/posts/' . $template_post_id . '" class="to-permalink-button" data-pjax="#body">
          <span class="feeling">' . mysqli_num_rows($result_post_empathies) . '</span>
          <span class="reply">' . mysqli_num_rows($result_post_replies) . '</span>
        </a>
      </div>
    </div>
  </div>';
 	// Put recent-reply here.
	if(strval(mysqli_num_rows($result_post_replies)) >=1 && time() - strtotime($template_post_created_at) <= 432000) {
	while($row_post_recent_replies = mysqli_fetch_assoc($result_post_recent_replies)) {
	
	if($row_post_recent_replies['is_spoiler'] == '1') {
	print null;
	}
	
	else {
	
	$sql_post_recent_replies_user = 'SELECT * FROM people WHERE people.pid = "' . $row_post_recent_replies['pid'] . '"';
	$result_post_recent_replies_user = mysqli_query($mysql, $sql_post_recent_replies_user);
	$row_post_recent_replies_user = mysqli_fetch_assoc($result_post_recent_replies_user);
	
	
	if($row_post_recent_replies_user['official_user'] == 1) {
    $is_recent_reply_poster_official_user = ' official-user';
    }
	else {
	$is_recent_reply_poster_official_user = ''; }
	
		if($row_post_recent_replies_user['mii_hash']) {
if(($row_post_recent_replies['feeling_id']) == '0') {
$mii_face_recent_reply_output = 'https://mii-secure.cdn.nintendo.net/' . $row_post_recent_replies_user['mii_hash'] . '_normal_face.png'; }
if(($row_post_recent_replies['feeling_id']) == '1') {
$mii_face_recent_reply_output = 'https://mii-secure.cdn.nintendo.net/' . $row_post_recent_replies_user['mii_hash'] . '_happy_face.png'; }
if(($row_post_recent_replies['feeling_id']) == '2') {
$mii_face_recent_reply_output = 'https://mii-secure.cdn.nintendo.net/' . $row_post_recent_replies_user['mii_hash'] . '_like_face.png'; }
if(($row_post_recent_replies['feeling_id']) == '3') {
$mii_face_recent_reply_output = 'https://mii-secure.cdn.nintendo.net/' . $row_post_recent_replies_user['mii_hash'] . '_surprised_face.png'; }
if(($row_post_recent_replies['feeling_id']) == '4') {
$mii_face_recent_reply_output = 'https://mii-secure.cdn.nintendo.net/' . $row_post_recent_replies_user['mii_hash'] . '_frustrated_face.png'; }
if(($row_post_recent_replies['feeling_id']) == '5') {
$mii_face_recent_reply_output = 'https://mii-secure.cdn.nintendo.net/' . $row_post_recent_replies_user['mii_hash'] . '_puzzled_face.png'; }
}
else {
if($row_post_recent_replies_user['user_face']) {
$mii_face_recent_reply_output = htmlspecialchars($row_post_recent_replies_user['user_face']); }
 else {
$mii_face_recent_reply_output = '/img/mii/img_unknown_MiiIcon.png'; }
}

$truncate_reply_bodyp1 = substr((htmlspecialchars($row_post_recent_replies['body'])), 0, 200);
if(strlen($truncate_reply_bodyp1) >= 25) {
$truncate_reply_body = "$truncate_reply_bodyp1..."; }
else {
$truncate_reply_body = $truncate_reply_bodyp1; }

	print '<div id="recent-reply-' . $row_post_recent_replies['id'] . '" class="recent-reply">
  <a href="/users/' . htmlspecialchars($row_post_recent_replies_user['user_id']) . '" class="user-icon-container scroll-focus' . $is_recent_reply_poster_official_user . '" data-pjax="#body">
    <img src="'.$mii_face_recent_reply_output.'" class="user-icon">
  </a>

  <div class="recent-reply-body-content">
    <div class="recent-reply-body">
      <header>
        <span class="user-name">'.htmlspecialchars($row_post_recent_replies_user['screen_name']).'</span>';
print '
        <span class="timestamp">'.humanTiming(strtotime($row_post_recent_replies['created_at'])).'</span>
      </header>

      <div class="recent-reply-content">

        <p class="recent-reply-content-text">'.$truncate_reply_body.'</p>

      </div>
      <a href="/posts/'.$template_post_id.'" class="to-permalink-button" data-pjax="#body"></a>
	  ';
	 if(strval(mysqli_num_rows($result_post_replies)) >=3) {
$replies_minus_one = strval(mysqli_num_rows($result_post_replies)) - 1;
	 print '</div><a href="/posts/'.$template_post_id.'" class="button read-more-button to-permalink-button" data-pjax="#body">
      View More Comments ('.$replies_minus_one.')
    </a>';
	}
	 if(strval(mysqli_num_rows($result_post_replies)) == 2) {
$replies_minus_one = strval(mysqli_num_rows($result_post_replies)) - 1;
print '</div><a href="/posts/'.$template_post_id.'" class="button read-more-button to-permalink-button" data-pjax="#body">
      View Other Comment ('.$replies_minus_one.')
    </a>';
	}
		 if(strval(mysqli_num_rows($result_post_replies)) >=2) {
		 print '</div></div>'; }
		 else {
		 print '</div></div></div>'; }
	  }
	  }
	}
	  print '</div>';
}			
				?>