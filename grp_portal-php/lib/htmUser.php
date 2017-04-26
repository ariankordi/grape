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

