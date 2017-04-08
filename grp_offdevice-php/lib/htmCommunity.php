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
if($row['platform_type'] == '1' && $row['platform_id'] != '1') { $platformIDtext = '3DS Games'; } 
if($row['platform_type'] == '2') { $platformIDtext = '3DS Games'; } 
if($row['platform_type'] == '3') { $platformIDtext = 'Virtual Console'; } 
if($row['platform_type'] == '4') { $platformIDtext = 'Others'; } }
print '
      <span class="text">'.(isset($platformIDtext) ? $platformIDtext : 'Others').'</span>
'; }
print '  </div>
  ';
  if($more == true) {
print '  <a href="/titles/'.$row['olive_community_id'].'" class="siblings symbol"><span class="symbol-label">Related Communities</span></a>'; }
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

function printPost($row) {
if($row['hidden_resp'] != '1') {
global $mysql;
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
	print '
      <a class="timestamp" '.($row['is_spoiler'] == 1 ? 'data-href-hidden' : 'href').'="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'">'.humanTiming(strtotime($row['created_at'])).'</a>
    </p>
  <p class="user-name"><a href="/users/'.htmlspecialchars($user['user_id']).'">'.htmlspecialchars($user['screen_name']).'</a></p>
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
if($row['is_spoiler'] == 1) { if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row['pid']) {} else { print '<div class="hidden-content"><p>This '.($reply == false ? 'comment' : 'post').' contains spoilers.
            <button type="button" class="hidden-content-button">View '.($reply == false ? 'Comment' : 'Post').'</button>
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
        <button type="button"'.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').' class="symbol submit empathy-button'.(isset($my_empathy_added) && $my_empathy_added == true ? ' empathy-added' : '').''.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').'" data-feeling="'.(!empty($usermii['feeling']) ? $usermii['feeling'] : 'normal').'" data-action="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'/empathies"><span class="empathy-button-text">'.(isset($my_empathy_added) && $my_empathy_added == true ? 'Unyeah' : (!empty($usermii['miitoo']) ? $usermii['miitoo'] : 'Yeah!')).'</span></button>
        <div class="empathy symbol"><span class="symbol-label">Yeahs</span><span class="empathy-count">'.$mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$row['id'].'"')->num_rows.'</span></div>
        <div class="reply symbol"><span class="symbol-label">Comment</span><span class="reply-count">'.$mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$row['id'].'"')->num_rows.'</span></div>
      </div>
</div>
	  ';
if($reply == false) {
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
</div>

'.(isset($row_recent_replies_user) ? '</div>' : '');
} }


