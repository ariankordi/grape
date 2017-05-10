<?php

function printNews($news) {
global $mysql;
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$news['from_pid'].'" LIMIT 1')->fetch_assoc();
$usermii = getMii($user, false);

// Types - 0, test, 1 admin message, 2, empathy, 3, comment empathy, 4, my comment, 5, poster comment, 6, follow

$find_merged_news = $mysql->query('SELECT * FROM news WHERE news.merged = "'.$news['news_id'].'" ORDER BY news.created_at LIMIT 20');
if($find_merged_news->num_rows != 0) {
$merged = $find_merged_news->fetch_all(MYSQLI_ASSOC); }

if($news['news_context'] == 2 || $news['news_context'] == 4 || $news['news_context'] == 5) {
$newsurl = '/posts/'.$news['id'];
$news_post1 = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$news['id'].'" LIMIT 1');
$news_post = ($news_post1->num_rows == 0 ? array('_post_type' => 'body', 'body' => 'not found') : $news_post1->fetch_assoc());
$news_body = ($news_post['_post_type'] == 'artwork' ? 'handwritten' : truncate($news_post['body'], 17)); }
if($news['news_context'] == 3) {
$newsurl = '/replies/'.$news['id'];
$news_post1 = $mysql->query('SELECT * FROM replies WHERE replies.id = "'.$news['id'].'" LIMIT 1');
$news_post = ($news_post1->num_rows == 0 ? array('body' => 'not found') : $news_post1->fetch_assoc());
$news_body = truncate($news_post['body'], 17); }
if($news['news_context'] == 6) {
$newsurl = '/users/'.htmlspecialchars($user['user_id']);
$get_follow_user = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$user['pid'].'"');
$has_user_follow = (isset($merged) && count($merged) >= 1 ? true : ($get_follow_user->num_rows != 0 ? true : false));
}
require_once '../grplib-php/user-helper.php';

if($news['news_context'] == 2) {
if(!isset($merged)) { $body = span_u($user['screen_name']).' gave <span class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</span> a Yeah.'; } else {
if(count($merged) == 1) {
$m2fpu = infoFromPID($merged[0]['from_pid']);	
$body = span_u($user['screen_name']).' and '.span_u($m2fpu['screen_name']).' gave <span class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</span> a Yeah.'; }	
if(count($merged) == 2) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']);
$body = span_u($user['screen_name']).', '.span_u($m2fpu['screen_name']).', and '.span_u($m3fpu['screen_name']).' gave <span class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</span> a Yeah.'; }
if(count($merged) == 3) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);
$body = span_u($user['screen_name']).', '.span_u($m2fpu['screen_name']).', '.span_u($m3fpu['screen_name']).', and '.span_u($m4fpu['screen_name']).' gave <span class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</span> a Yeah.'; }
if(count($merged) >= 4) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);  $subtr_news_curr = count($merged) - 3;
$body = span_u($user['screen_name']).', '.span_u($m2fpu['screen_name']).', '.span_u($m3fpu['screen_name']).', '.span_u($m4fpu['screen_name']).', and '.$subtr_news_curr.' '.(count($merged) == 4 ? 'other person' : 'others').' gave <a href="/posts/'.$news['id'].'" class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }		
    }
}
if($news['news_context'] == 3) {
if(!isset($merged)) { $body = span_u($user['screen_name']).' gave <span class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</span> a Yeah.'; } else {
if(count($merged) == 1) {
$m2fpu = infoFromPID($merged[0]['from_pid']);	
$body = span_u($user['screen_name']).' and '.span_u($m2fpu['screen_name']).' gave <span class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</span> a Yeah.'; }	
if(count($merged) == 2) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']);
$body = span_u($user['screen_name']).', '.span_u($m2fpu['screen_name']).', and '.span_u($m3fpu['screen_name']).' gave <span class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</span> a Yeah.'; }
if(count($merged) == 3) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);
$body = span_u($user['screen_name']).', '.span_u($m2fpu['screen_name']).', '.span_u($m3fpu['screen_name']).', and '.span_u($m4fpu['screen_name']).' gave <span class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</span> a Yeah.'; }
if(count($merged) >= 4) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);  $subtr_news_curr = count($merged) - 3;
$body = span_u($user['screen_name']).', '.span_u($m2fpu['screen_name']).', '.span_u($m3fpu['screen_name']).', '.span_u($m4fpu['screen_name']).', and '.$subtr_news_curr.' '.(count($merged) == 4 ? 'other person' : 'others').' gave <a href="/posts/'.$news['id'].'" class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }		
    }
}
if($news['news_context'] == 4) {
if(!isset($merged)) { $body = span_u($user['screen_name']).' commented on <span class="link">your post&nbsp;('.htmlspecialchars($news_body).')</span>.'; } else {
if(count($merged) == 1) {
$m2fpu = infoFromPID($merged[0]['from_pid']);	
$body = span_u($user['screen_name']).' and '.span_u($m2fpu['screen_name']).' commented on <span class="link">your post&nbsp;('.htmlspecialchars($news_body).')</span>.'; }	
if(count($merged) == 2) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']);
$body = span_u($user['screen_name']).', '.span_u($m2fpu['screen_name']).', and '.span_u($m3fpu['screen_name']).' commented on <span class="link">your post&nbsp;('.htmlspecialchars($news_body).')</span>.'; }	
if(count($merged) == 3) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);
$body = span_u($user['screen_name']).', '.span_u($m2fpu['screen_name']).', '.span_u($m3fpu['screen_name']).', and '.span_u($m4fpu['screen_name']).' commented on <span class="link">your post&nbsp;('.htmlspecialchars($news_body).')</span>.'; }
if(count($merged) >= 4) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);  $subtr_news_curr = count($merged) - 3;
$body = span_u($user['screen_name']).', '.span_u($m2fpu['screen_name']).', '.span_u($m3fpu['screen_name']).', and '.$subtr_news_curr.' '.(count($merged) == 4 ? 'other person' : 'others').' commented on <span class="link">your post&nbsp;('.htmlspecialchars($news_body).')</span>.'; }		
    }
}
if($news['news_context'] == 5) {
if(!isset($merged)) { $body = span_u($user['screen_name']).' commented on <span class="link">'.htmlspecialchars($user['screen_name']).'\'s post&nbsp;('.htmlspecialchars($news_body).')</span>.'; } else {
if(count($merged) >= 1) { }
       }
}
if($news['news_context'] == 6) {
if(!isset($merged)) { $body = 'Followed by '.span_u($user['screen_name']).'.'; } else {
if(count($merged) == 1) {
$m2fpu = infoFromPID($merged[0]['from_pid']);	
$body = 'Followed by '.span_u($user['screen_name']).' and '.span_u($m2fpu['screen_name']).'.'; }	
if(count($merged) == 2) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']);
$body = 'Followed by '.span_u($user['screen_name']).', '.span_u($m2fpu['screen_name']).', and '.span_u($m3fpu['screen_name']).'.'; }	
if(count($merged) == 3) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);
$body = 'Followed by '.span_u($user['screen_name']).', '.span_u($m2fpu['screen_name']).', '.span_u($m3fpu['screen_name']).', and '.span_u($m4fpu['screen_name']).'.'; }
if(count($merged) >= 4) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);  $subtr_news_curr = count($merged) - 3;
$body = 'Followed by '.span_u($user['screen_name']).', '.span_u($m2fpu['screen_name']).', '.span_u($m3fpu['screen_name']).', and '.$subtr_news_curr.' '.(count($merged) == 4 ? 'other person' : 'others').'.'; }		
    }
}

print '<li>
  
  <a href="/users/'.htmlspecialchars($user['user_id']).'" data-pjax="#body" class="icon-container'.($usermii['official'] == true ? ' official-user' : '').''.($news['has_read'] == 0 ? ' notify' : '').'"><img src="'.$usermii['output'].'" class="icon"></a>
  <a href="'.$newsurl.'" data-pjax="#body" class="'.($news['news_context'] == 6 && $has_user_follow == false ? null : 'arrow-button ').'scroll"></a>

';
if($news['news_context'] == 6 && $has_user_follow == false) {
print '<div class="toggle-button">
    <a class="follow-button button add-button" href="#" data-action="/users/'.htmlspecialchars($user['user_id']).'.follow.json" data-sound="SE_WAVE_FRIEND_ADD" data-track-label="user" data-track-action="follow" data-track-category="follow">Follow</a>
      <button class="button follow-done-button relationship-button done-button none">Follow</button>
</div>'; } print '
  <div class="body"><p class="text">'.(empty($body) ? 'Sorry, not implemented.' : $body).'
      

';
print '    <span class="timestamp"> '.humanTiming(strtotime($news['created_at'])).'</span>
</p>
  </div>
</li>';


}

function printMessage($row) {
global $mysql;
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$row['pid'].'"')->fetch_assoc();
$mii = getMii($user, $row['feeling_id']);

print '<div id="message-'.$row['id'].'" class="post scroll '.($user['pid'] == $_SESSION['pid'] ? 'my' : 'other').'-post">
  <a href="/users/'.htmlspecialchars($user['user_id']).'" data-pjax="#body" class="scroll-focus user-icon-container'.($mii['official'] ? ' official-user' : '').'"><img src="'.$mii['output'].'" class="user-icon"></a>
  <header>
    <span class="timestamp">'.humanTiming(strtotime($row['created_at'])).'</span>
    
  </header>
  <div class="post-body">


      <p class="post-content">'.htmlspecialchars($row['body']).'</p>

      ';
	  if(!empty($row['screenshot'])) {
	  print '<a href="#" role="button" class="title-capture-container capture-container" data-modal-open="#capture-page" data-large-capture-url="'.htmlspecialchars($row['screenshot']).'"><img src="'.htmlspecialchars($row['screenshot']).'" class="title-capture"></a>'; }
	  print '
        
      
  </div>
</div>';
}

function messageForm($user, $other_person) {
global $mysql;
print '<div id="add-message-page" class="add-post-page'.($user['privilege'] >= 2 || $user['image_perm'] == 1 || $user['official_user'] == 1 ? 
' official-user-post' : '').' none" data-modal-types="add-entry add-message require-body preview-body" data-is-template="1">
  <header class="add-post-page-header">
  ';
if($other_person) {
  print '
    <h1 class="page-title">Message to '.htmlspecialchars($other_person['screen_name']).' ('.htmlspecialchars($other_person['user_id']).')</h1>
  </header>
'; }
else {
  print '
    <h1 class="page-title">Message to ConversationID '.htmlspecialchars($_GET['conversation_id']).'</h1>
  </header>
';	}
if($other_person) {
print '  <form method="post" action="/friend_messages">
<input type="hidden" name="message_to_user_id" value="'.htmlspecialchars($other_person['user_id']).'">'; } else {
print '  <form method="post" action="/friend_messages?conversation_id='.htmlspecialchars($_GET['conversation_id']).'">
<input type="hidden" name="conversation_id" value="'.htmlspecialchars($_GET['conversation_id']).'">';	
}
print '
	<input type="hidden" name="view_id" value="00000000000000000000000000000000">
	<input type="hidden" name="page_param" value="{&quot;upinfo&quot;:&quot;1400000000.00000,1400000000,1400000000.00000&quot;,&quot;reftime&quot;:&quot;+1400000000&quot;,&quot;order&quot;:&quot;desc&quot;,&quot;per_page&quot;:&quot;20&quot;}">
    <div class="add-post-page-content">
 ';
 
	print '<div class="feeling-selector expression">
  <img src="'.getMii($user, 0)['output'].'" class="icon">
  <ul class="buttons"><li class="checked"><input type="radio" name="feeling_id" value="0" class="feeling-button-normal" data-mii-face-url="'.getMii($user, 0)['output'].'" checked="" data-sound="SE_WAVE_MII_FACE_00"></li><li><input type="radio" name="feeling_id" value="1" class="feeling-button-happy" data-mii-face-url="'.getMii($user, 1)['output'].'" data-sound="SE_WAVE_MII_FACE_01"></li><li><input type="radio" name="feeling_id" value="2" class="feeling-button-like" data-mii-face-url="'.getMii($user, 2)['output'].'" data-sound="SE_WAVE_MII_FACE_02"></li><li><input type="radio" name="feeling_id" value="3" class="feeling-button-surprised" data-mii-face-url="'.getMii($user, 3)['output'].'" data-sound="SE_WAVE_MII_FACE_03"></li><li><input type="radio" name="feeling_id" value="4" class="feeling-button-frustrated" data-mii-face-url="'.getMii($user, 4)['output'].'" data-sound="SE_WAVE_MII_FACE_04"></li><li><input type="radio" name="feeling_id" value="5" class="feeling-button-puzzled" data-mii-face-url="'.getMii($user, 5)['output'].'" data-sound="SE_WAVE_MII_FACE_05"></li>  </ul>
</div>';

print '


      <div class="textarea-container textarea-with-menu active-text">
        <menu class="textarea-menu">
          <li><label class="textarea-menu-text checked">
              <input type="radio" name="_post_type" value="body" checked="" data-sound="">
          </label></li>
          <li><label class="textarea-menu-memo">
              <input type="radio" name="_post_type" value="painting" data-sound="">
          </label></li>
        </menu>
           <textarea name="body" class="textarea-text" value="" maxlength="1000" placeholder="Write a message to a friend here."></textarea>
        <div class="textarea-memo trigger" data-sound=""><div class="textarea-memo-preview"></div><input type="hidden" name="painting"></div>
      </div>
	';
	 print '<input type="text" class="textarea-line url-form" name="screenshot" placeholder="Screenshot URL" maxlength="255">';
print '
	</div>

      <input type="button" class="olv-modal-close-button fixed-bottom-button left" value="Cancel" data-sound="SE_WAVE_CANCEL">
      <input type="submit" class="post-button fixed-bottom-button" value="Send" data-track-category="message" data-track-action="sendMessage" data-post-content-type="text" data-post-with-screenshot="nodata">
  </form>
</div>';
}

function userObject($user, $has_memo, $has_button, $type) {
global $mysql;
$user_id = htmlspecialchars($user['user_id']);
$usermii = getMii($user, false);
$get_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1');
$profile = $get_profile->fetch_assoc();

print '<li class="scroll test-user-'.htmlspecialchars($user['user_id']).'">
    <a href="/users/'.htmlspecialchars($user['user_id']).'" class="scroll-focus icon-container'.($mii['official'] ? ' official-user' : '').'" data-pjax="#body"><img src="'.$mii['output'].'" class="icon"></a>
    

	
	

';
if($type == 'search') {
print '<div>
    <a class="button" href="/users/'.htmlspecialchars($user['user_id']).'" data-pjax="#body">View Profile</a>
  </div>';
}

if($has_button && !empty($_SESSION['pid'])) {

if($type == 'friends') {
// already friends
print '
<button type="button" class="button friend-button relationship-button remove-button" data-modal-open="#breakup-confirm-page"'.($usermii['official'] ? ' data-is-identified="1"': '').' data-user-id="'.htmlspecialchars($user['user_id']).'" data-screen-name="'.htmlspecialchars($user['screen_name']).'" data-mii-face-url="'.$usermii['output'].'" data-pid="'.$user['pid'].'">Friends</button>
';
}
elseif($type == 'friend_request') {
// friend request
$friend_request = $mysql->query('SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$_SESSION['pid'].'" AND friend_requests.recipient = "'.$user['pid'].'" AND friend_requests.finished = "0" ORDER BY friend_requests.news_id DESC LIMIT 1')->fetch_assoc();
print '
<button type="button" class="button friend-requested-button relationship-button remove-button" data-modal-open="#sent-request-confirm-page"'.($usermii['official'] ? ' data-is-identified="1"': '').' data-user-id="'.htmlspecialchars($user['user_id']).'" data-is-identified="1" data-screen-name="'.htmlspecialchars($user['screen_name']).'" data-mii-face-url="'.$usermii['output'].'" data-pid="'.$user['pid'].'" data-body="'.htmlspecialchars($friend_request['message']).'" data-timestamp="'.date("m/d/Y g:i A",strtotime($friend_request['created_at'])).'">Request Pending</button>
';
}

else {
// follow
$relationship_exists = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$user['pid'].'" LIMIT 1')->num_rows != 0;
print '
<div class="toggle-button">
';

if(!$relationship_exists) {
// change2followjson
print '<a class="follow-button button add-button relationship-button" href="#" data-action="/users/'.htmlspecialchars($user['user_id']).'.follow.json" data-sound="SE_WAVE_FRIEND_ADD" data-community-id="" data-url-id="" data-track-label="user" data-title-id="" data-track-action="follow" data-track-category="follow">Follow</a>
      <button class="button follow-done-button relationship-button done-button none" disabled="">Follow</button>';
$has_arrow = false;
	  }
print '
</div>';
	}
} else {
$has_button = true;
	   }
print '
<a href="/users/'.htmlspecialchars($user['user_id']).'" class="scroll-focus'.($has_arrow ? ' arrow-button' : '').'" data-pjax="#body"></a>
';

print '
  <div class="body">
  ';
  if($has_memo && $get_profile->num_rows != 0 && !empty($profile['favorite_screenshot'])) {
	  print '<div class="user-profile-memo-content">
      <img src="'.htmlspecialchars($profile['favorite_screenshot']).'" class="user-profile-memo">
    </div>';	  
  }
  print '    <p class="title">
      <span class="nick-name">'.htmlspecialchars($user['screen_name']).'</span>
      <span class="id-name">'.htmlspecialchars($user['user_id']).'</span>
    </p>
    <p class="text">'.($get_profile->num_rows != 0 ? htmlspecialchars($profile['comment']) : '').'</p>
  </div>
</li>';
}

function userInfo($user, $profile, $mii, $page) {
$user_page_my = !empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'];
if($user_page_my) {
print '<a id="header-mymenu-button" href="/my_menu" data-pjax="#body">User Menu</a>
'; }

print '<div class="user-info info-content'.($mii['official'] ? ' official-user' : '').'">
';

if(empty($profile['favorite_screenshot'])) {
print '<div class="user-profile-memo-container no-profile-memo">'.($user_page_my ? 'Your favorite post can be displayed here.' : '').'</div>
'; }
else {
$fav_post_get = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$mysql->real_escape_string($profile['favorite_screenshot']).'" AND posts.is_hidden != "1" LIMIT 1');
if($fav_post_get->num_rows != 0) {
$fav_post = $fav_post_get->fetch_assoc();
print '<a href="/posts/'.$fav_post['id'].'" data-pjax="#body" class="user-profile-memo-container">
    <img src="'.htmlspecialchars($fav_post['screenshot']).'" class="user-profile-memo">
</a>'; } }

print '    <span class="icon-container'.($mii['official'] ? ' official-user' : '').'"><a href="/users/'.htmlspecialchars($user['user_id']).'"><img src="'.$mii['output'].'" class="icon"></a></span>
';
if($mii['official']) {
print '<p class="user-organization">'.htmlspecialchars($user['organization']).'</p>'; }
print '  <p class="title">
    <span class="nick-name">'.htmlspecialchars($user['screen_name']).'</span>
    <span class="id-name">'.htmlspecialchars($user['user_id']).'</span>
  </p>
  
  ';

if(isset($row_userpage_me)) {
$sql_search_relationship = 'SELECT * FROM relationships WHERE relationships.source = "'.$row_userpage_me['pid'].'" AND relationships.target = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_search_relationship = $mysql->query($sql_search_relationship);

if(mysqli_num_rows($result_search_relationship) != 0) {
$relationship_has_follow = ' none';
$relationship_has_unfollow = '';     }
else {
$relationship_has_follow = '';
$relationship_has_unfollow = ' none'; }
} else {
$relationship_has_follow = '';
$relationship_has_unfollow = ' none'; }

if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
print '<a href="/settings/profile" data-pjax="#body" class="button edit-button">Profile Settings</a>'; }
else {
print '<div class="toggle-button">
    <a class="follow-button button add-button'.$relationship_has_follow.' relationship-button'.(!empty($_SESSION['pid']) ? '' : ' disabled').'" href="#" data-action="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/follow" data-sound="SE_WAVE_FRIEND_ADD" data-community-id="" data-url-id="" data-track-label="user" data-title-id="" data-track-action="follow" data-track-category="follow">Follow</a>
    <a href="#" class="unfollow-button button remove-button'.$relationship_has_unfollow.' relationship-button" data-modal-open="#unfollow-confirm-page" data-user-id="'.htmlspecialchars($row_userpage_user['user_id']).'" data-screen-name="'.htmlspecialchars($row_userpage_user['screen_name']).'" data-mii-face-url="'.$user_page_info_mii_face_output.'" data-action="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/unfollow"'.($mii['official'] ? ' data-is-identified="1"' : '').'" data-community-id="" data-url-id="" data-track-label="user" data-title-id="" data-track-action="openUnfollowModal" data-track-category="follow">Follow</a>
</div>';	
}
// Make this better
if(isset($row_userpage_user_profile['pid']) || !isset($_SESSION['pid'])) {
$has_can_friend_request = true; } else {
$has_can_friend_request = false; }

if(isset($_SESSION['pid']) && $row_userpage_me['pid'] != $row_userpage_user['pid']) {
if($has_can_friend_request = false) {
print '
<div class="button-with-option dropdown">
            <a class="main-button friend-request-button disabled">Friend Request</a>
        <div class="dropdown-menu">
        </div>
</div>'; }
else {
if($has_can_friend_request = true) {
$sql_search_friend_request = 'SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$row_userpage_me['pid'].'" AND friend_requests.recipient = "'.$row_userpage_user['pid'].'" AND friend_requests.finished = "0"';
$result_search_friend_request = $mysql->query($sql_search_friend_request);
$row_pending_friend_request = mysqli_fetch_assoc($result_search_friend_request);
$amt_rows_search_fr = mysqli_num_rows($result_search_friend_request);
if($amt_rows_search_fr >= 1) {
print '
<div class="button-with-option dropdown">
          <a href="#" class="main-button friend-requested-button dropdown-toggle main-option-button" data-toggle="dropdown" data-sound="SE_WAVE_BALLOON_OPEN">Request Pending</a>
        <div class="dropdown-menu">
            <a href="#" class="button cancel-request-button relationship-button" data-modal-open="#sent-request-confirm-page" '.($row_userpage_user['official_user'] == 1 ? 'data-is-identified="1" ': '').'data-user-id="'.htmlspecialchars($row_userpage_user['user_id']).'" data-screen-name="'.htmlspecialchars($row_userpage_user['screen_name']).'" data-mii-face-url="'.$user_page_info_mii_face_output.'" data-pid="'.$row_userpage_user['pid'].'" data-body="'.htmlspecialchars($row_pending_friend_request['message']).'" data-timestamp="'.date("m/d/Y g:i A",strtotime($row_pending_friend_request['created_at'])).'" data-sound="SE_WAVE_OK_SUB">Check Request</a>
        </div>
</div>
';
}
$frcheck = $mysql->query('SELECT * FROM friend_requests WHERE friend_requests.recipient = "'.$_SESSION['pid'].'" AND friend_requests.sender = "'.$row_userpage_user['pid'].'"');
if(mysqli_num_rows($mysql->query('SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$row_userpage_me['pid'].'" AND friend_requests.recipient = "'.$row_userpage_user['pid'].'" AND friend_requests.finished = "0"')) == 0 && mysqli_num_rows($mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$row_userpage_me['pid'].'" AND friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" AND friend_relationships.target = "'.$row_userpage_me['pid'].'"')) == 0 && $frcheck->num_rows == 0) {
print '
<div class="button-with-option dropdown">
            <a href="#" data-modal-open="#friend-request-post-page" class="main-button friend-request-button" data-sound="SE_WAVE_FRIEND_ADD">Friend Request</a>
        <div class="dropdown-menu">
        </div>
      </div>';
}
if(mysqli_num_rows($result_search_friend_request) <= 0) {
$sql_friend_relationship = 'SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$row_userpage_me['pid'].'" AND friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" AND friend_relationships.target = "'.$row_userpage_me['pid'].'"';
$result_friend_relationship = $mysql->query($sql_friend_relationship);
if(mysqli_num_rows($result_friend_relationship) == 1) {
$row_friend_relationship = mysqli_fetch_assoc($result_friend_relationship);
print '
<div class="button-with-option dropdown">
          <a href="#" class="friend-button dropdown-toggle main-option-button" data-toggle="dropdown" data-sound="SE_WAVE_BALLOON_OPEN">Friends</a>
        <div class="dropdown-menu">
            <a href="#" class="button breakup-button relationship-button" data-modal-open="#breakup-confirm-page" '.($row_userpage_user['official_user'] == 1 ? 'data-is-identified="1" ': '').'data-user-id="'.htmlspecialchars($row_userpage_user['user_id']).'" data-screen-name="'.htmlspecialchars($row_userpage_user['screen_name']).'" data-mii-face-url="'.$user_page_info_mii_face_output.'" data-pid="'.$row_userpage_user['pid'].'" data-sound="SE_WAVE_OK_SUB">Remove Friend</a>
        </div>
      </div>';
}	}
if($mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" OR friend_relationships.target = "'.$_SESSION['pid'].'"')->num_rows == 0 && $frcheck->num_rows >=1) {
print '<div class="button-with-option dropdown">
          <a href="#" class="main-button friend-request-button relationship-button" data-modal-open="#received-request-confirm-page" '.($row_userpage_user['official_user'] == 1 ? 'data-is-identified="1" ': '').'data-user-id="'.htmlspecialchars($row_userpage_user['user_id']).'" data-screen-name="'.htmlspecialchars($row_userpage_user['screen_name']).'" data-mii-face-url="'.$user_page_info_mii_face_output.'" data-pid="'.$row_userpage_user['pid'].'" data-body="'.htmlspecialchars($frcheck->fetch_assoc()['message']).'">View Friend Request</a>
        <div class="dropdown-menu">
        </div>
      </div>';
}
	
} } }

# End user-info info-content
print '</div>';
}