<?php

function getIcon($row) {
if(empty($row['icon']) || strlen($row['icon']) <= 1) { return 'https://miiverse.nintendo.net/img/title-icon-default.png'; } else {
return htmlspecialchars($row['icon']); }
}

function printTitle($row, $more) {
print '<li id="community-'.$row['olive_community_id'].'" class="trigger " data-href="/titles/'.$row['olive_title_id'].'/'.$row['olive_community_id'].'" tabindex="0">
  <span class="icon-container"><img src="'.getIcon($row).'" class="icon"></span>
  <div class="body">
      <a class="title" href="/titles/'.$row['olive_title_id'].'/'.$row['olive_community_id'].'" tabindex="-1">'.htmlspecialchars($row['name']).'</a>
	  ';
if(!empty($row['platform_id'])) {
print '        <span class="platform-tag"><img src="https://i.imgur.com/'.($row['platform_id'] == 1 ? 'nZkp8NW' : 'VaXHOg6').'.png"></span>
';
if(!empty($row['platform_type'])) {
if($row['platform_type'] == '1' && $row['platform_id'] == '1') { $platformIDtext = 'Wii U Games'; }
elseif($row['platform_type'] == '1' && $row['platform_id'] != '1') { $platformIDtext = '3DS Games'; } 
elseif($row['platform_type'] == '2') { $platformIDtext = '3DS Games'; } 
elseif($row['platform_type'] == '3') { $platformIDtext = 'Virtual Console'; } 
else { $platformIDtext = 'Others'; } }
print '
      <span class="text">'.$platformIDtext.'</span>
'; }
print '  </div>
  ';
  if($more == true) {
print '  <a href="/titles/'.$row['olive_title_id'].'" class="siblings symbol"><span class="symbol-label">Related Communities</span></a>'; }
print '</li>';
}

function printTitle2($row, $title) {
print '<li id="community-'.$row['olive_community_id'].'" class="trigger " data-href="/titles/'.$row['olive_title_id'].'/'.$row['olive_community_id'].'" tabindex="0">
  <span class="icon-container"><img src="'.getIcon($row).'" class="icon"></span>
  <div class="body">';
if(!empty($row['type']) && $row['type'] >= 1) {
  print '<span class="news-community-badge">'.($row['type'] == 2 ? 'Announcement Community' : 'Main Community').'</span>'; } print '      <a class="title" href="/titles/'.$row['olive_title_id'].'/'.$row['olive_community_id'].'" tabindex="-1">'.htmlspecialchars($row['name']).'</a>
      <span class="text">'.htmlspecialchars($title['name']).'</span>
  </div>
</li>';
}

function postForm($community, $user, $placeholder) {
if(postPermission($user, $community) == true) {
	print '
<form id="post-form" method="post" action="/posts" class="folded'.($user['official_user'] == '1' || $user['privilege'] >= 1 || $user['image_perm'] == '1' ? ' for-identified-user' : '').'">
  
  
  <input type="hidden" name="community_id" value="'.$community['community_id'].'">

  <div class="feeling-selector"><label class="symbol feeling-button feeling-button-normal checked"><input type="radio" name="feeling_id" value="0" checked=""><span class="symbol-label">normal</span></label><label class="symbol feeling-button feeling-button-happy"><input type="radio" name="feeling_id" value="1"><span class="symbol-label">happy</span></label><label class="symbol feeling-button feeling-button-like"><input type="radio" name="feeling_id" value="2"><span class="symbol-label">like</span></label><label class="symbol feeling-button feeling-button-surprised"><input type="radio" name="feeling_id" value="3"><span class="symbol-label">surprised</span></label><label class="symbol feeling-button feeling-button-frustrated"><input type="radio" name="feeling_id" value="4"><span class="symbol-label">frustrated</span></label><label class="symbol feeling-button feeling-button-puzzled"><input type="radio" name="feeling_id" value="5"><span class="symbol-label">puzzled</span></label>
  </div>


  <textarea name="body" class="textarea-text textarea" maxlength="1000" placeholder="'.($placeholder ? $placeholder : 'Share your thoughts in a post to this community.').'" data-open-folded-form="" data-required=""></textarea>
  ';
if($user['official_user'] == '1' || $user['privilege'] >= 1 || $user['image_perm'] == '1') {
print '
<input type="text" class="textarea-line url-form" name="url" placeholder="URL" maxlength="255">
<label class="file-button-container">
      <span class="input-label">Screenshot <span>JPEG/PNG/BMP</span></span>
      <input type="file" class="file-button" accept="image/jpeg">
      <input type="hidden" name="screenshot" value="">
    </label>
'; }
print '
  <label class="spoiler-button symbol">
    <input type="checkbox" id="is_spoiler" name="is_spoiler" value="1">
    Spoilers
  </label>
  
  
  <div class="form-buttons">
    <input type="submit" class="black-button post-button" value="Send">
  </div>
</form>
'; } 
}

function printPost($row) {
if($row['hidden_resp'] != '1') {
global $mysql; global $actFeed; global $identified;
if(strlen($row['_post_type']) > 10) { $reply = true; } else { $reply = false; }

$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$row['pid'].'" LIMIT 1')->fetch_assoc();
if($reply == false) {
$community = $mysql->query('SELECT * FROM communities WHERE communities.community_id = "'.$row['community_id'].'" LIMIT 1')->fetch_assoc(); } else {
$ogpost = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$row['_post_type'].'" LIMIT 1')->fetch_assoc();
$ogpost_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$ogpost['pid'].'" LIMIT 1')->fetch_assoc();
$ogpost_user_mii = getMii($ogpost_user, $ogpost['feeling_id']);
}
$usermii = getMii($user, $row['feeling_id']);

if($reply == false) {
if(!empty($row['url']) && strpos($row['url'], 'www.youtube.com/watch?v=') !== false) {
if(substr($row['url'], 0, 4) == "http" || substr($row['url'], 0, 5) == "https") {
$videopost = substr($row['url'], (substr($row['url'], 0, 5) == "https" ? 32 : 31), 11);
} } }

print '<div id="post-'.$row['id'].'" data-href="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'" class="post trigger'.(!empty($row['screenshot']) || isset($videopost) ? ' with-image' : '').($row['is_spoiler'] == 1 ? (isset($_SESSION['pid']) && $_SESSION['pid'] == $row['pid'] ? '' : ' hidden') : '').'" tabindex="0">
  <a href="/users/'.htmlspecialchars($user['user_id']).'" class="icon-container'.($usermii['official'] ? ' official-user' : '').'"><img src="'.$usermii['output'].'" class="icon"></a>
    <p class="timestamp-container">
	';
	if($row['is_spoiler'] == 1) {
	print '    <span class="spoiler">Spoilers</span>
    ·'; }
if(isset($identified)) {
$get_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$row['pid'].'" LIMIT 1');
print '<p class="user-name"><a href="/users/'.htmlspecialchars($user['user_id']).'">'.htmlspecialchars($user['screen_name']).'</a></p>
       <p class="text">'.($get_profile->num_rows != 0 ? htmlspecialchars($get_profile->fetch_assoc()['comment']) : '').'</p>';
	} else {
	print '
      <a class="timestamp" '.($row['is_spoiler'] == 1 ? 'data-href-hidden' : 'href').'="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'">'.humanTiming(strtotime($row['created_at'])).'</a>
    </p>
	<p class="user-name"><a href="/users/'.htmlspecialchars($user['user_id']).'">'.htmlspecialchars($user['screen_name']).'</a></p>'; } print '
  <p class="community-container">';   if($reply == false) { print '<a'.($community['type'] == 5 ? '' : ' href="/titles/'.$community['olive_title_id'].'/'.$community['olive_community_id'].'"').'><img src="'.getIcon($community).'" class="community-icon">'.htmlspecialchars($community['name']).'</a>'; } else { print '<a href="/posts/'.$ogpost['id'].'"><img src="'.$ogpost_user_mii['output'].'" class="community-icon"><span class="reply symbol"></span>Comment on '.htmlspecialchars($ogpost_user['screen_name']).'\'s Post</a>';
} print '</p>';
print '
  <div class="body">
  ';
    if($row['is_hidden'] == '1' && $row['hidden_resp'] == '0') {
print '<div class="post-content">
      <p class="deleted-message">
        Deleted by administrator.<br>
        Post ID: '.getPostID($row['id']).'
      </p>
    </div>'; } else {
print '    <div class="post-content">


';
if(isset($videopost)) {
print '<a href="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'" class="screenshot-container video"><img height="48" src="https://i.ytimg.com/vi/'.$videopost.'/default.jpg"></a>'; }

if($row['_post_type'] == 'artwork') {
print '<p class="post-content-memo"><img src="'.htmlspecialchars($row['body']).'" class="post-memo"></p>'; } else {
$truncate_post_body = (mb_strlen($row['body'], 'utf-8') >= 204 ? mb_substr($row['body'], 0, 200, 'utf-8').'...' : $row['body']);
print '      <p class="post-content-text">'.preg_replace("/[\r\n]+/", "\n", $truncate_post_body).'</p>
	  '; } 
if($row['is_spoiler'] == 1) { if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row['pid']) {} else { print '<div class="hidden-content"><p>This '.($reply == true ? 'comment' : 'post').' contains spoilers.
            <button type="button" class="hidden-content-button">View '.($reply == true ? 'Comment' : 'Post').'</button>
      </p></div>'; } }
	  print '
';
if(!empty($row['screenshot'])) {
print '<a href="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'" class="screenshot-container still-image"><img src="'.htmlspecialchars($row['screenshot']).'"></a>'; }
if(!empty($_SESSION['pid'])) {
$canmiitoo = miitooCan($_SESSION['pid'], $row['id'], 'posts'); 
$my_empathy_added = ($mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$row['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'" LIMIT 1')->num_rows == 1 ? true : false);
}
print '
      <div class="post-meta">
	  ';
	  if(isset($identified)) {
	  print '<p class="timestamp-container">
            <a class="timestamp">'.humanTiming(strtotime($row['created_at'])).'</a>
          </p>';	  
	  } else {
	  print '        <button type="button"'.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').' class="symbol submit empathy-button'.(isset($my_empathy_added) && $my_empathy_added == true ? ' empathy-added' : '').''.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').'" data-feeling="'.(!empty($usermii['feeling']) ? $usermii['feeling'] : 'normal').'" data-action="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'/empathies"><span class="empathy-button-text">'.(isset($my_empathy_added) && $my_empathy_added == true ? 'Unyeah' : (!empty($usermii['miitoo']) ? $usermii['miitoo'] : 'Yeah!')).'</span></button>'; } print '
        <div class="empathy symbol"><span class="symbol-label">Yeahs</span><span class="empathy-count">'.$mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$row['id'].'"')->num_rows.'</span></div>'; if($reply == false) { print '
        <div class="reply symbol"><span class="symbol-label">Comment</span><span class="reply-count">'.$mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$row['id'].'"')->num_rows.'</span></div>'; }
print '      </div>
</div>
	  ';
	  
if($reply == false || !isset($actFeed) || !isset($identified)) {
$get_recent_repliesall = $mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$row['id'].'" AND replies.is_hidden != "1" ORDER BY replies.created_at DESC');
$get_recent_replies = $mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$row['id'].'" AND replies.is_hidden != "1" AND replies.pid != "'.$row['pid'].'" AND replies.is_spoiler != "1" ORDER BY replies.created_at DESC');
	if($get_recent_replies->num_rows >=1 && time() - strtotime($row['created_at']) <= 432000) {

	print '
	
	<div class="recent-reply-content">
';
if($get_recent_repliesall->num_rows >=2) {
$newnumrs = $get_recent_repliesall->num_rows - 1;
print '  
    <div class="recent-reply-read-more-container" data-href="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'" tabindex="0">
      View '.($newnumrs >= 2 ? 'More Comments' : 'Other Comment').' ('.$newnumrs.')
    </div>
'; }
$row_recent_replies = $get_recent_replies->fetch_assoc();
if($row_recent_replies['is_spoiler'] != '1' && $row_recent_replies['pid']) {
$row_recent_replies_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$row_recent_replies['pid'].'"')->fetch_assoc();
$recentreplymii = getMii($row_recent_replies_user, $row_recent_replies['feeling_id']);
print '
  <div id="recent-reply-'.$row_recent_replies['id'].'" data-href="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'" tabindex="0" class="recent-reply trigger">
    <a href="/users/'.htmlspecialchars($row_recent_replies_user['user_id']).'" class="icon-container'.($recentreplymii['official'] ? ' official-user' : '').'"><img src="'.$recentreplymii['output'].'" class="icon"></a>
    <p class="timestamp-container">
        <a class="timestamp" href="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'">'.humanTiming(strtotime($row_recent_replies['created_at'])).'</a>
    </p>
    <p class="user-name"><a href="/users/'.htmlspecialchars($row_recent_replies_user['user_id']).'">'.htmlspecialchars($row_recent_replies_user['screen_name']).'</a></p>
    <div class="body">
      <div class="post-content">
          <p class="recent-reply-content-text">'.htmlspecialchars($row_recent_replies['body']).'</p>
  </div>
 </div>
';

        }       }
print (isset($row_recent_replies_user) ? '
   </div>
' : '
    ').' '; } }
print ' </div>
';
if(isset($actFeed)) {
print (isset($row_recent_replies_user) ? '</div>' : '').'<a href="/users/'.htmlspecialchars($user['user_id']).'/posts" class="another-posts symbol">'.htmlspecialchars($user['screen_name']).'\'s Posts</a>';
}

print '
</div>  

'.(!isset($actFeed) && isset($row_recent_replies_user) ? '</div>' : '');
} }


