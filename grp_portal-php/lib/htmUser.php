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
    <a class="follow-button button add-button" href="#" data-action="/users/'.htmlspecialchars($user['user_id']).'/follow" data-sound="SE_WAVE_FRIEND_ADD" data-track-label="user" data-track-action="follow" data-track-category="follow">Follow</a>
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
$user_id = htmlspecialchars($user['user_id']);
$usermii = getMii($user, false);
$get_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1')->fetch_assoc();

if(isset($is_always_button_have)) {
$has_follow_scroll = ' arrow-button';
}
else {
$sql_userwho = 'SELECT * FROM people WHERE people.pid = "'.$row_user_to_view['pid'].'"';
$result_userwho = $mysql->query($sql_userwho);

if(isset($_SESSION['pid'])) {
$sql_search_relationship = 'SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$row_user_to_view['pid'].'"';
$result_search_relationship = $mysql->query($sql_search_relationship); }

if(isset($_SESSION['pid']) && strval(mysqli_num_rows($result_search_relationship) == 0)) {
$has_follow_scroll = '';
}
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_user_to_view['pid']) {
$has_follow_scroll = ' arrow-button';
}
else {
$has_follow_scroll = ' arrow-button'; }
}

print '<li class="scroll test-user-'.htmlspecialchars($row_user_to_view['user_id']).'">
    <a href="/users/'.htmlspecialchars($row_user_to_view['user_id']).'" class="scroll-focus icon-container'.($mii['official'] ? ' official-user' : '').'" data-pjax="#body"><img src="'.$mii['output'].'" class="icon"></a>
    

	
	

';
if(isset($is_user_search)) {
print '<div>
    <a class="button" href="/users/'.htmlspecialchars($row_user_to_view['user_id']).'" data-pjax="#body">View Profile</a>
  </div>';
}

else {

if(isset($is_friends_added_list)) {
if($is_friends_added_list = true && isset($is_friends_pending_list)) {
$search_frienduserlistli1 = $mysql->query('SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$_SESSION['pid'].'" AND friend_requests.recipient = "'.$row_user_to_view['pid'].'" AND friend_requests.finished = "0"');
$search_frienduserlistli = mysqli_fetch_assoc($search_frienduserlistli1);
print '<button type="button" class="button friend-requested-button relationship-button remove-button" data-modal-open="#sent-request-confirm-page" '.($row_user_to_view['official_user'] == 1 ? 'data-is-identified="1" ': '').'data-user-id="'.htmlspecialchars($row_user_to_view['user_id']).'" data-screen-name="'.htmlspecialchars($row_user_to_view['screen_name']).'" data-mii-face-url="'.$mii['output'].'" data-pid="'.$search_frienduserlistli['recipient'].'" data-body="'.htmlspecialchars($search_frienduserlistli['message']).'" data-timestamp="'.date("m/d/Y g:i A",strtotime($search_frienduserlistli['created_at'])).'">Request Pending</button>';
} 

else {
print '<button type="button" class="button friend-button relationship-button remove-button" data-modal-open="#breakup-confirm-page" '.($row_user_to_view['official_user'] == 1 ? 'data-is-identified="1" ': '').'data-user-id="'.htmlspecialchars($row_user_to_view['user_id']).'" data-screen-name="'.htmlspecialchars($row_user_to_view['screen_name']).'" data-mii-face-url="'.$mii['output'].'" data-pid="'.htmlspecialchars($row_user_to_view['pid']).'">Friends</button>'; }
}
	
else {
if(isset($is_always_button_have)) {
print '<a href="/users/'.htmlspecialchars($row_user_to_view['user_id']).'" class="scroll-focus arrow-button" data-pjax="#body"></a>'; } else {
print '<div class="toggle-button">';
if(isset($_SESSION['pid']) && $_SESSION['pid'] != $row_user_to_view['pid']) {
if(isset($_SESSION['pid']) && strval(mysqli_num_rows($result_search_relationship) == 0)) {
print '<a class="follow-button button add-button relationship-button" href="#" data-action="/users/'.htmlspecialchars($row_user_to_view['user_id']).'/follow" data-sound="SE_WAVE_FRIEND_ADD" data-track-label="user" data-track-action="follow" data-track-category="follow">Follow</a>
      <button class="button follow-done-button relationship-button done-button none" disabled>Follow</button>';
print '</div>
  <a href="/users/'.htmlspecialchars($row_user_to_view['user_id']).'" class="scroll-focus" data-pjax="#body"></a>

';
}
else {
print '<a href="/users/'.htmlspecialchars($row_user_to_view['user_id']).'" class="scroll-focus arrow-button" data-pjax="#body"></a>'; }
}
else {
print '<a href="/users/'.htmlspecialchars($row_user_to_view['user_id']).'" class="scroll-focus arrow-button" data-pjax="#body"></a>'; }
}

}


}
$sql_user_to_view_profile = 'SELECT * FROM profiles WHERE profiles.pid = "'.$row_user_to_view['pid'].'"';
$result_user_to_view_profile = $mysql->query($sql_user_to_view_profile);
$row_user_to_view_profile = mysqli_fetch_assoc($result_user_to_view_profile);

if(empty($row_user_to_view_profile['comment'])) {
$row_user_to_view_profile['comment'] = null; }

print '
  <div class="body">
  ';
  if(isset($is_mutual_list) && isset($row_user_to_view_profile['favorite_screenshot']) && strlen($row_user_to_view_profile['favorite_screenshot']) > 3) {
$result_posts_getfavoritepost = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$mysql->real_escape_string($row_user_to_view_profile['favorite_screenshot']).'"');
  print '<div class="user-profile-memo-content">
      <img src="'.htmlspecialchars(mysqli_fetch_assoc($result_posts_getfavoritepost)['screenshot']).'" class="user-profile-memo">
    </div>';	  
  }
  print '    <p class="title">
      <span class="nick-name">'.htmlspecialchars($row_user_to_view['screen_name']).'</span>
      <span class="id-name">'.htmlspecialchars($row_user_to_view['user_id']).'</span>
    </p>
    <p class="text">'.htmlspecialchars($row_user_to_view_profile['comment']).'</p>
  </div>
</li>';
}