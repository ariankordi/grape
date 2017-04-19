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
if(!isset($merged)) { $body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a> gave <a href="/posts/'.$news['id'].'" class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; } else {
if(count($merged) == 1) {
$m2fpu = infoFromPID($merged[0]['from_pid']);	
$body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a> and <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a> gave <a href="/posts/'.$news['id'].'" class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }	
if(count($merged) == 2) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']);
$body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a>, and <a href="/users/'.htmlspecialchars($m3fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</a> gave <a href="/posts/'.$news['id'].'" class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }	
if(count($merged) == 3) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);
$body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m3fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</a>, and <a href="/users/'.htmlspecialchars($m4fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</a> gave <a href="/posts/'.$news['id'].'" class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }
if(count($merged) >= 4) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);  $subtr_news_curr = count($merged) - 3;
$body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m4fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</a>, and '.$subtr_news_curr.' '.(count($merged) == 4 ? 'other person' : 'others').' gave <a href="/posts/'.$news['id'].'" class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }		
    }
}
if($news['news_context'] == 3) {
if(!isset($merged)) { $body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a> gave <a href="/replies/'.$news['id'].'" class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; } else {
if(count($merged) == 1) {
$m2fpu = infoFromPID($merged[0]['from_pid']);	
$body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a> and <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a> gave <a href="/replies/'.$news['id'].'" class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }	
if(count($merged) == 2) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']);
$body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a>, and <a href="/users/'.htmlspecialchars($m3fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</a> gave <a href="/replies/'.$news['id'].'" class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }	
if(count($merged) == 3) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);
$body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m3fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</a>, and <a href="/users/'.htmlspecialchars($m4fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</a> gave <a href="/replies/'.$news['id'].'" class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }
if(count($merged) >= 4) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);  $subtr_news_curr = count($merged) - 3;
$body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m4fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</a>, and '.$subtr_news_curr.' '.(count($merged) == 4 ? 'other person' : 'others').' gave <a href="/replies/'.$news['id'].'" class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }		
    }
}

if($news['news_context'] == 4) {
if(!isset($merged)) { $body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a> commented on <a href="/posts/'.$news['id'].'" class="link">your post&nbsp;('.htmlspecialchars($news_body).')</a>.'; } else {
if(count($merged) == 1) {
$m2fpu = infoFromPID($merged[0]['from_pid']);	
$body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a> and <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a> commented on <a href="/posts/'.$news['id'].'" class="link">your post&nbsp;('.htmlspecialchars($news_body).')</a>.'; }	
if(count($merged) == 2) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']);
$body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a>, and <a href="/users/'.htmlspecialchars($m3fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</a> commented on <a href="/posts/'.$news['id'].'" class="link">your post&nbsp;('.htmlspecialchars($news_body).')</a>.'; }	
if(count($merged) == 3) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);
$body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m3fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</a>, and <a href="/users/'.htmlspecialchars($m4fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</a> commented on <a href="/posts/'.$news['id'].'" class="link">your post&nbsp;('.htmlspecialchars($news_body).')</a>.'; }
if(count($merged) >= 4) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);  $subtr_news_curr = count($merged) - 3;
$body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m4fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</a>, and '.$subtr_news_curr.' '.(count($merged) == 4 ? 'other person' : 'others').' commented on <a href="/posts/'.$news['id'].'" class="link">your post&nbsp;('.htmlspecialchars($news_body).')</a>.'; }		
    }
}

if($news['news_context'] == 5) {
if(!isset($merged)) { $body = '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a> commented on <a href="/posts/'.$news['id'].'" class="link">'.htmlspecialchars($user['screen_name']).'\'s post&nbsp;('.htmlspecialchars($news_body).')</a>.'; } else {
if(count($merged) >= 1) { }
       }
}

if($news['news_context'] == 6) {
if(!isset($merged)) { $body = 'Followed by <a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a>.'; } else {
if(count($merged) == 1) {
$m2fpu = infoFromPID($merged[0]['from_pid']);	
$body = 'Followed by <a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a> and <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a>.'; }	
if(count($merged) == 2) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']);
$body = 'Followed by <a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a>, and <a href="/users/'.htmlspecialchars($m3fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</a>.'; }	
if(count($merged) == 3) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);
$body = 'Followed by <a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m3fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</a>, and <a href="/users/'.htmlspecialchars($m4fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</a>.'; }
if(count($merged) >= 4) {
$m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);  $subtr_news_curr = count($merged) - 3;
$body = 'Followed by <a href="/users/'.htmlspecialchars($user['user_id']).'" class="nick-name">'.htmlspecialchars($user['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m2fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</a>, <a href="/users/'.htmlspecialchars($m4fpu['user_id']).'" class="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</a>, and '.$subtr_news_curr.' '.(count($merged) == 4 ? 'other person' : 'others').'.'; }		
    }
}

print '<div class="news-list-content'.($news['has_read'] == 0 ?  ' notify' : '').' trigger" tabindex="0" data-href="'.$newsurl.'">
  
  <a href="/users/'.htmlspecialchars($user['user_id']).'" class="icon-container'.($usermii['official'] == true ? ' official-user' : '').'"><img src="'.$usermii['output'].'" class="icon"></a>
  <div class="body">'.(empty($body) ? 'Sorry, not implemented.' : $body).'
      

';
if($news['news_context'] == 6 && $has_user_follow == false) {
print '<div class="toggle-button">
    <button type="button" data-action="/users/'.htmlspecialchars($user['user_id']).'.follow.json" class="follow-button button symbol" data-community-id="" data-url-id="" data-track-label="user" data-title-id="" data-track-action="follow" data-track-category="follow">Follow</button>
      <button type="button" class="button follow-done-button relationship-button symbol none" disabled="">Follow</button>
</div>'; }
print '
    <span class="timestamp"> '.humanTiming(strtotime($news['created_at'])).'</span>
  </div>
</div>';


}

function userContent($user, $mii, $type, $favorite) {
global $mysql;
if($favorite != false) {
print '<a href="/posts/'.$favorite['id'].'" class="user-profile-memo-container" style="background-image:url('.htmlspecialchars($favorite['screenshot']).')">
      <img src="'.htmlspecialchars($favorite['screenshot']).'" class="user-profile-screenshot">
    </a>';
}
print '<div id="user-content" class="'.($mii['official'] == true ? 'official-user ' : '').''.($favorite != false ? '' : ' no-profile-post-user').'">
    <span class="icon-container'.($mii['official'] == true ? ' official-user' : '').'"><img src="'.$mii['output'].'" class="icon"></span>
	';
	if($user['official_user'] == 1) { print '<span class="user-organization">'.htmlspecialchars($user['organization']).'</span>'; }
print '    <div class="nick-name">'.htmlspecialchars($user['screen_name']).'<span class="id-name">'.htmlspecialchars($user['user_id']).'</span></div>
    ';
	if($type == 'profile' && !empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid']) {
	print '<div id="user-menu">
      <div id="my-menu"><a href="/act/logout" class="button symbol">Log Out</a></div>
      <div id="edit-profile-settings"><a class="button symbol" href="/settings/profile">Profile Settings</a></div>
    </div>'; } elseif($type == 'profile') {
print '<div class="user-action-content">
	';
	if(!empty($_SESSION['pid'])) {
$get_follow_user = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$user['pid'].'"');
$has_user_follow = ($get_follow_user->num_rows != 0 ? true : false);
	print '<div class="toggle-button">
    <button type="button" data-action="/users/'.htmlspecialchars($user['user_id']).'.follow.json" class="follow-button button symbol'.($has_user_follow ? ' none' : '').'">Follow</button>
    <button type="button" data-action="/users/'.htmlspecialchars($user['user_id']).'.unfollow.json" class="unfollow-button button symbol'.($has_user_follow ? '' : ' none').'" data-screen-name="'.htmlspecialchars($user['screen_name']).'">Follow</button>
	</div>
	 </div>'; }
elseif(empty($_SESSION['pid'])) {
print '
</div>';
}	} else {
	print '<a href="/users/'.htmlspecialchars($user['user_id']).'" class="profile-page-button button">To Top</a>';
	}
 print '
  </div>';
}

function userNavMenu($user, $mode) {
global $mysql;
$userid = htmlspecialchars($user['user_id']);
$num_posts = $mysql->query('SELECT * FROM posts WHERE posts.pid = "'.$user['pid'].'" AND posts.is_hidden != "1"')->num_rows;
$num_friends = $mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$user['pid'].'" OR friend_relationships.target = "'.$user['pid'].'"')->num_rows;
$num_following = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$user['pid'].'" AND relationships.is_me2me = 0')->num_rows;
$num_followers = $mysql->query('SELECT * FROM relationships WHERE relationships.target = "'.$user['pid'].'" AND relationships.is_me2me = 0')->num_rows;
print '<div id="nav-menu" class="nav-4">
    <a href="/users/'.$userid.'/posts" class="'.($mode == 'posts' ? 'selected' : '').'">
      <span class="number">'.$num_posts.'</span>
      <span class="name">Posts</span>
    </a>
    <a href="/users/'.$userid.'/friends" class="'.($mode == 'friends' ? 'selected' : '').'">
      <span class="number">'.$num_friends.' / 100</span>
      <span class="name">Friends</span>
    </a>
    <a href="/users/'.$userid.'/following" class="'.($mode == 'following' ? 'selected' : '').'">
      <span class="number">'.$num_following.' / 1000</span>
      <span class="name">Following</span>
    </a>
    <a href="/users/'.$userid.'/followers" class="'.($mode == 'followers' ? 'selected' : '').'">
      <span class="number">'.$num_followers.'</span>
      <span class="name">Followers</span>
    </a>
  </div>';
}

function tab2Activity($user, $page) {
global $mysql;
$userid = htmlspecialchars($user['user_id']);
$num_posts = $mysql->query('SELECT * FROM posts WHERE posts.pid = "'.$user['pid'].'" AND posts.is_hidden != "1"')->num_rows;
$num_empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.pid = "'.$user['pid'].'"')->num_rows;
print '<div class="tab2 user-menu-activity">
  <a href="/users/'.$userid.'/posts" class="'.($page == 'posts' ? 'selected' : '').'"><span class="label">Posts</span><span class="number">'.$num_posts.'</span></a>
  <a href="/users/'.$userid.'/empathies" class="'.($page == 'empathies' ? 'selected' : '').'"><span class="label">Yeahs</span><span class="number">'.$num_empathies.'</span></a>
</div>';
}

function userObject($user, $has_memo, $has_button) {
global $mysql;
$user_id = htmlspecialchars($user['user_id']);
$usermii = getMii($user, false);
$get_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1')->fetch_assoc();
print '<li class="trigger" data-href="/users/'.$user_id.'">
    <a href="/users/'.$user_id.'" class="icon-container'.($usermii['official'] ? ' official-user' : '').'"><img src="'.$usermii['output'].'" class="icon"></a>

  


';
if(!empty($_SESSION['pid']) && $has_button == true) { $find_relationship = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$user['pid'].'"'); 
if($find_relationship->num_rows == 0) {
print '<div class="toggle-button">
    <button type="button" data-action="/users/'.$user_id.'.follow.json" class="follow-button button symbol relationship-button">Follow</button>
      <button type="button" class="button follow-done-button relationship-button symbol none" disabled="">Follow</button>
</div>'; } } print '


  <div class="body">
    <p class="title">
      <span class="nick-name"><a href="/users/'.$user_id.'">'.htmlspecialchars($user['screen_name']).'</a></span>
      <span class="id-name">'.$user_id.'</span>
    </p>
    <p class="text">'.(!empty($get_profile['comment']) ? htmlspecialchars($get_profile['comment']) : '').'</p>';
	if(!empty($get_profile['favorite_screenshot'])) {
$get_fav_screenshot_post = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$get_profile['favorite_screenshot'].'" LIMIT 1')->fetch_assoc();
print '      <div class="user-profile-memo-content">
        <img src="'.htmlspecialchars($get_fav_screenshot_post['screenshot']).'" class="user-profile-memo">
	</div>'; } print '
  </div>
</li>';
}