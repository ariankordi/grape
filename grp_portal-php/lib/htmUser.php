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
  
  <a href="/users/'.htmlspecialchars($user['user_id']).'" data-pjax="#body" class="icon-container'.($usermii['official'] == true ? ' official-user' : '').($news['has_read'] == 0 ? ' notify' : '').'"><img src="'.$usermii['output'].'" class="icon"></a>
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
print '<div id="add-message-page" class="add-post-page official-user-post none" data-modal-types="add-entry add-message require-body preview-body" data-is-template="1">
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

require_once '../grplib-php/user-helper.php';
if(!empty($_SESSION['pid']) && canUserView($_SESSION['pid'], $user['pid'])) {
return null; }

$get_profile = $mysql->query('SELECT comment FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1');
$profile = $get_profile->fetch_assoc();

print '<li class="scroll test-user-'.htmlspecialchars($user['user_id']).'">
    <a href="/users/'.htmlspecialchars($user['user_id']).'" class="scroll-focus icon-container'.($usermii['official'] ? ' official-user' : '').'" data-pjax="#body"><img src="'.$usermii['output'].'" class="icon"></a>
    

	
	

';
if($type == 'search') {
print '<div>
    <a class="button" href="/users/'.htmlspecialchars($user['user_id']).'" data-pjax="#body">View Profile</a>
  </div>';
}

if($has_button && !empty($_SESSION['pid']) && $type != 'search' && $_SESSION['pid'] != $user['pid']) {

if($type == 'friends') {
// already friends
print '
<button type="button" class="button friend-button relationship-button remove-button" data-modal-open="#breakup-confirm-page"'.($usermii['official'] ? ' data-is-identified="1"': '').' data-user-id="'.htmlspecialchars($user['user_id']).'" data-screen-name="'.htmlspecialchars($user['screen_name']).'" data-mii-face-url="'.$usermii['output'].'" data-pid="'.$user['pid'].'">Friends</button>
';
$has_arrow = false;
}
elseif($type == 'friend_request') {
// friend request
$friend_request = $mysql->query('SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$_SESSION['pid'].'" AND friend_requests.recipient = "'.$user['pid'].'" AND friend_requests.finished = "0" ORDER BY friend_requests.news_id DESC LIMIT 1')->fetch_assoc();
print '
<button type="button" class="button friend-requested-button relationship-button remove-button" data-modal-open="#sent-request-confirm-page"'.($usermii['official'] ? ' data-is-identified="1"': '').' data-user-id="'.htmlspecialchars($user['user_id']).'" data-is-identified="1" data-screen-name="'.htmlspecialchars($user['screen_name']).'" data-mii-face-url="'.$usermii['output'].'" data-pid="'.$user['pid'].'" data-body="'.htmlspecialchars($friend_request['message']).'" data-timestamp="'.date("m/d/Y g:i A",strtotime($friend_request['created_at'])).'">Request Pending</button>
';
$has_arrow = false;
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
else {
$has_arrow = true;
		}
print '
</div>';
	}
} else {
$has_arrow = true;
	   }
print '
<a href="/users/'.htmlspecialchars($user['user_id']).'" class="scroll-focus'.($has_arrow ? ' arrow-button' : '').'" data-pjax="#body"></a>
';

print '
  <div class="body">
  ';
  if($has_memo && $get_profile->num_rows != 0 && !empty($profile['favorite_screenshot'])) {
$fav_scrnsht_post = $mysql->query('SELECT screenshot FROM posts WHERE posts.id = "'.$mysql->real_escape_string($profile['favorite_screenshot']).'" AND posts.is_hidden = "0" LIMIT 1');
if($fav_scrnsht_post && $fav_scrnsht_post->num_rows != 0) {
	  print '<div class="user-profile-memo-content">
      <img src="'.htmlspecialchars($fav_scrnsht_post->fetch_assoc()['screenshot']).'" class="user-profile-memo">
    </div>';	  
}  }
  print '    <p class="title">
      <span class="nick-name">'.htmlspecialchars($user['screen_name']).'</span>
      <span class="id-name">'.htmlspecialchars($user['user_id']).'</span>
    </p>
    <p class="text">'.($get_profile->num_rows != 0 ? getProfileComment($user, $profile) : '').'</p>
  </div>
</li>';
}

function userDropdown($user, $mii) {
$user_page_my = !empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'];
if(!empty($_SESSION['pid']) && !$user_page_my) {
print '
<div id="dropdown-user-report" class="dropdown">
  <a href="#" class="option-button user-option-menu dropdown-toggle setting-button" data-toggle="dropdown" data-sound="SE_WAVE_BALLOON_OPEN"></a>
  <div class="dropdown-menu">
      
      <a href="#" class="button block-button relationship-button" data-modal-open="#block-confirm-page" data-user-id="'.htmlspecialchars($user['user_id']).'"'.($mii['official'] ? ' data-is-identified="1"' : '').' data-screen-name="'.htmlspecialchars($user['screen_name']).'" data-mii-face-url="'.$mii['output'].'" data-action="/users/'.htmlspecialchars($user['user_id']).'/blacklist.create.json" data-sound="SE_WAVE_OK_SUB">Block</a>
  </div>
</div>
';
	}
}

function userInfo($user, $profile, $mii, $page) {
global $mysql;
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
if($page == false) {
$relationship_exists = !empty($_SESSION['pid']) && $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$user['pid'].'" LIMIT 1')->num_rows != 0;

if($user_page_my) {
print '<a href="/settings/profile" data-pjax="#body" class="button edit-button">Profile Settings</a>'; }
else {
print '<div class="toggle-button">
    <a class="follow-button button add-button'.($relationship_exists ? ' none' : '').' relationship-button'.(!empty($_SESSION['pid']) ? '' : ' disabled').'" href="#" data-action="/users/'.htmlspecialchars($user['user_id']).'.follow.json" data-sound="SE_WAVE_FRIEND_ADD" data-community-id="" data-url-id="" data-track-label="user" data-title-id="" data-track-action="follow" data-track-category="follow">Follow</a>
    <a href="#" class="unfollow-button button remove-button'.($relationship_exists ? '' : ' none').' relationship-button" data-modal-open="#unfollow-confirm-page" data-user-id="'.htmlspecialchars($user['user_id']).'" data-screen-name="'.htmlspecialchars($user['screen_name']).'" data-mii-face-url="'.$mii['output'].'" data-action="/users/'.htmlspecialchars($user['user_id']).'.unfollow.json"'.($mii['official'] ? ' data-is-identified="1"' : '').'" data-community-id="" data-url-id="" data-track-label="user" data-title-id="" data-track-action="openUnfollowModal" data-track-category="follow">Follow</a>
</div>';	
}
if(!$user_page_my) {
// Cases: 0 for no relationship or unfinished request, 1 for an unfinished request to the user, 2 for an unfinished request from the user, 3 for a friend relationship
// Put this into its own function soon
print '<div class="button-with-option dropdown">
';
if(empty($_SESSION['pid'])) {
print '            <a class="main-button friend-request-button disabled">Friend Request</a>
        <div class="dropdown-menu">
        </div>';
} else {
$friend_req_search_me = $mysql->query('SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$_SESSION['pid'].'" AND friend_requests.recipient = "'.$user['pid'].'" AND friend_requests.finished = "0"');
$friend_req_search_other = 	$mysql->query('SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$user['pid'].'" AND friend_requests.recipient = "'.$_SESSION['pid'].'" AND friend_requests.finished = "0"');
$friend_relation_search = $mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" AND friend_relationships.target = "'.$user['pid'].'" OR friend_relationships.source = "'.$user['pid'].'" AND friend_relationships.target = "'.$_SESSION['pid'].'"');
if($friend_req_search_me->num_rows != 0) { $friend_case = 1; }
elseif($friend_req_search_other->num_rows != 0) { $friend_case = 2; }
elseif($friend_relation_search->num_rows != 0) { $friend_case = 3; }
else { $friend_case = 0; }
if($friend_case == 0) {
	if($profile['allow_request'] == 0) {
print '
            <a class="main-button friend-request-button disabled">Friend Request</a>
        <div class="dropdown-menu">
        </div>
		';
} else {
print '
            <a href="#" data-modal-open="#friend-request-post-page" class="main-button friend-request-button" data-sound="SE_WAVE_FRIEND_ADD">Friend Request</a>
        <div class="dropdown-menu">
        </div>
		';
}	}
elseif($friend_case == 1) {
$request = $friend_req_search_me->fetch_assoc();
print '
          <a href="#" class="main-button friend-requested-button dropdown-toggle main-option-button" data-toggle="dropdown" data-sound="SE_WAVE_BALLOON_OPEN">Request Pending</a>
        <div class="dropdown-menu">
            <a href="#" class="button cancel-request-button relationship-button" data-modal-open="#sent-request-confirm-page"'.($mii['official'] ? ' data-is-identified="1"': '').' data-user-id="'.htmlspecialchars($user['user_id']).'" data-screen-name="'.htmlspecialchars($user['screen_name']).'" data-mii-face-url="'.$mii['output'].'" data-pid="'.$user['pid'].'" data-body="'.htmlspecialchars($request['message']).'" data-timestamp="'.date("m/d/Y g:i A",strtotime($request['created_at'])).'" data-sound="SE_WAVE_OK_SUB">Check Request</a>
        </div>
		';
		}
elseif($friend_case == 2) {
$request = $friend_req_search_other->fetch_assoc();
print '
          <a href="" class="main-button friend-request-button relationship-button" data-modal-open="#received-request-confirm-page"'.($mii['official'] ? ' data-is-identified="1"': '').' data-user-id="'.htmlspecialchars($user['user_id']).'" data-screen-name="'.htmlspecialchars($user['screen_name']).'" data-mii-face-url="'.$mii['output'].'" data-pid="'.$user['pid'].'" data-body="'.htmlspecialchars($request['message']).'">View Friend Request</a>
        <div class="dropdown-menu">
        </div>
		';
		}
elseif($friend_case == 3) {
print '
          <a href="#" class="friend-button dropdown-toggle main-option-button" data-toggle="dropdown" data-sound="SE_WAVE_BALLOON_OPEN">Friends</a>
        <div class="dropdown-menu">
            <a href="#" class="button breakup-button relationship-button" data-modal-open="#breakup-confirm-page"'.($mii['official'] ? ' data-is-identified="1"': '').' data-user-id="'.htmlspecialchars($user['user_id']).'" data-screen-name="'.htmlspecialchars($user['screen_name']).'" data-mii-face-url="'.$mii['output'].'" data-pid="'.$user['pid'].'" data-sound="SE_WAVE_OK_SUB">Remove Friend</a>
        </div>
		';
		}
}
print '
      </div>';
}
	}
else {
print '
<a href="/users/'.htmlspecialchars($user['user_id']).'" data-pjax="#body" class="button profile-back-button">To Top</a>
';
		}
# End user-info info-content
print '</div>';
}

function userNavTab($user, $page) {
global $mysql;
$num_posts = $mysql->query('SELECT COUNT(id) FROM posts WHERE posts.pid = "'.$user['pid'].'" AND posts.is_hidden = "0"')->fetch_assoc()['COUNT(id)'];
$num_friends = $mysql->query('SELECT COUNT(relationship_id) FROM friend_relationships WHERE friend_relationships.source = "'.$user['pid'].'" OR friend_relationships.target = "'.$user['pid'].'"')->fetch_assoc()['COUNT(relationship_id)'];
$num_following = $mysql->query('SELECT COUNT(relationship_id) FROM relationships WHERE relationships.source = "'.$user['pid'].'" AND relationships.is_me2me != "1"')->fetch_assoc()['COUNT(relationship_id)'];
$num_followers = $mysql->query('SELECT COUNT(relationship_id) FROM relationships WHERE relationships.target = "'.$user['pid'].'" AND relationships.is_me2me != "1"')->fetch_assoc()['COUNT(relationship_id)'];
global $profile;
$can_view = (!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid']) || !empty($_SESSION['pid']) && profileRelationshipVisible($_SESSION['pid'], $user['pid'], $profile['relationship_visibility']);

print '
<menu class="user-menu tab-header">
  <li class="test-user-posts-count tab-button-profile'.($page == 'posts' ? ' selected' : '').'"><a href="/users/'.htmlspecialchars($user['user_id']).'/posts" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Posts</span><span class="number">'.$num_posts.'</span></a></li>
  <li class="test-user-friends-count tab-button-activity'.($page == 'friends' ? ' selected' : '').'"><a href="/users/'.htmlspecialchars($user['user_id']).'/friends" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Friends</span><span class="number">'.($can_view ? $num_friends : '-').' / 100</span></a></li>
  <li class="test-user-followings-count tab-button-activity'.($page == 'following' ? ' selected' : '').'"><a href="/users/'.htmlspecialchars($user['user_id']).'/following" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Following</span><span class="number"><span class="js-following-count">'.($can_view ? $num_following : '-').'</span> / 1000</span></a></li>
  <li class="test-user-followers-count tab-button-relationship'.($page == 'followers' ? ' selected' : '').'"><a href="/users/'.htmlspecialchars($user['user_id']).'/followers" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Followers</span><span class="number">'.($can_view ? $num_followers : '-').'</span></a></li>
</menu>
';
}

function userPostNavTab($user, $page) {
global $mysql;
$num_posts = $mysql->query('SELECT COUNT(id) FROM posts WHERE posts.pid = "'.$user['pid'].'" AND posts.is_hidden = "0"')->fetch_assoc()['COUNT(id)'];
$num_empathies = $mysql->query('SELECT COUNT(id) FROM empathies WHERE empathies.pid = "'.$user['pid'].'"')->fetch_assoc()['COUNT(id)'];
print '
<menu class="user-menu-activity tab-header">
    <li id="tab-header-user-posts" class="tab-button'.($page == 'posts' ? ' selected' : '').'"><a href="/users/'.htmlspecialchars($user['user_id']).'/posts" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Posts</span><span class="number">'.$num_posts.'</span></a></li>
    <li id="tab-header-user-empathies" class="tab-button'.($page == 'empathies' ? ' selected' : '').'"><a href="/users/'.htmlspecialchars($user['user_id']).'/empathies" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Yeahs</span><span class="number">'.$num_empathies.'</span></a></li>
  </menu>
  ';
}