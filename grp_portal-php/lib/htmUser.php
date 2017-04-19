<?php

function userpageTemplate($row, $is_activity, $is_official) {
global $mysql;
if(!isset($row['id'])) { return null; } elseif($row['is_hidden'] == 1 && $row['hidden_resp'] == '1') { return null; } else {
if(strlen($row['_post_type']) > 10) { $reply = true; } else { $reply = false; }

$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$row['pid'].'" LIMIT 1')->fetch_assoc();
if($reply != true) {
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
	
print '<div id="post-'.$row['id'].'" class="post scroll post-subtype-default'.($row['is_spoiler'] == 1 ? (isset($_SESSION['pid']) && $_SESSION['pid'] == $row['pid'] ? null : ' hidden') : null).''.(isset($videopost) ? ' with-video-image' : null).''.(!empty($row['screenshot']) ? ' with-image' : null).'" data-post-permalink-url="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'">
  <a href="/users/'.htmlspecialchars($user['user_id']).'" class="user-icon-container scroll-focus'.($usermii['official'] ? ' official-user' : null).'" data-pjax="#body"><img src="'.$usermii['output'].'" class="user-icon"></a>
  ';
if(!$is_official) {
print '  <div class="post-body-content">'; }
print '    <div class="post-body">
      <header>
        <span class="user-name">'.htmlspecialchars($user['screen_name']).'</span>
		';
		if($is_official) {
$get_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1');
print '<p class="text">'.($get_profile->num_rows != 0 ? htmlspecialchars($get_profile->fetch_assoc()['comment']) : null).'</p>';
		} else {
print '        <span class="timestamp">'.humanTiming(strtotime($row['created_at'])).'</span>
 ';    
		print '	  <span class="spoiler-status'.(!empty($row['is_spoiler']) && $row['is_spoiler'] == '1' ? ' spoiler' : null).'">Spoilers</span>';
		}
		
print '        
      </header>          
';
if($reply == true) {
print '        <a href="/posts/'.$ogpost['id'].'" class="community-content test-post-target-href" data-pjax="#body">
          <span class="title-icon-container'.($reply == true ? ' user-icon-container' : null).'" data-pjax="#body"><img src="'.$ogpost_user_mii['output'].'" class="title-icon'.($reply == true ? ' user-icon' : null).'"></span>
          <span class="community-name">'.htmlspecialchars($ogpost_user['screen_name']).'';
		  print "'s Post";
		  print '</span>
        </a>';
}
else {
if($community['type'] == '5') {
print '<a class="community-content test-post-target-href" data-pjax="#body">
          <span class="title-icon-container" data-pjax="#body"><img src="'.htmlspecialchars($community['icon']).'" class="title-icon"></span>
          <span class="community-name">'.htmlspecialchars($community['name']).'</span>
        </a>';
}
else {
print '        <a href="/titles/'.$community['olive_title_id'].'/'.$community['olive_community_id'].'" class="community-content test-post-target-href" data-pjax="#body">
          <span class="title-icon-container'.($reply == true ? ' user-icon-container' : null).'" data-pjax="#body"><img src="'.getIcon($community).'" class="title-icon"></span>
<span class="community-name">'.htmlspecialchars($community['name']).'</span></a>';
} }
if($row['is_hidden'] != 1 && $row['hidden_resp'] != '0') {
	  if(!empty($videopost)) {

print '<div class="title-capture-container video-container">
<a class="video-thumbnail" href="/posts/'.$row['id'].'#post-video" data-pjax="#body">
<span><img width="120" height="90" src="https://i.ytimg.com/vi/'.$videopost.'/default.jpg"></span></a></div>';
}
if(!empty($row['screenshot'])) {
print '<a href="#" role="button" class="title-capture-container capture-container" data-modal-open="#capture-page" data-large-capture-url="' . htmlspecialchars($row['screenshot']) . '"><img src="'.htmlspecialchars($row['screenshot']).'" class="title-capture"></a>';
  } }
print '
      <div class="post-content">

';
if($row['is_hidden'] == 1 && $row['hidden_resp'] == 0) {
require_once '../../grplib-php/olv-url-enc.php';
print '
        <p class="deleted-message">Deleted by administrator.</p>
        <p class="deleted-message">Post ID: '.getPostID($row['id']).'</p>
</div>
</div>
</div>'; }
else {
$truncate_post_body = (mb_strlen($row['body'], 'utf-8') >= 204 ? mb_substr($row['body'], 0, 200, 'utf-8').'...' : $row['body']);

if($row['_post_type'] == 'artwork') {
print '<p class="post-content-memo"><img src="'.htmlspecialchars($row['body']).'" class="post-memo"></p>
	  </div>'; } else {
print '

            <p class="post-content-text">'.preg_replace("/[\r\n]+/", "\n", $truncate_post_body).'</p>
      </div>
	  
	  
'; }
	 if(isset($row['is_spoiler']) && $row['is_spoiler'] == '1') {

if($row['is_spoiler'] == 1) { if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row['pid']) { } else {  print '	<div class="hidden-content">
        <p>This post contains spoilers.</p>
        <div><a href="#" class="hidden-content-button">View Post</a></div>
</div>'; } }
	  }
$empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$row['id'].'"')->num_rows;
$replies = $mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$row['id'].'"')->num_rows;
if($is_official) {
print '<div class="post-meta">
              <span class="timestamp">'.humanTiming(strtotime($row['created_at'])).'</span>
              <a href="/posts/'.$row['id'].'" class="to-permalink-button" data-pjax="#body">
                <span class="feeling">'.$empathies.'</span>
                <span class="reply">'.$replies.'</span>
              </a>
            </div>
			';
} else {	
if(!empty($_SESSION['pid'])) {
$canmiitoo = miitooCan($_SESSION['pid'], $row['id'], 'posts'); 
$my_empathy_added = ($mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$row['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'" LIMIT 1')->num_rows == 1 ? true : false);
}
print '


      <div class="post-meta">
        <button type="button"'.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').' class="submit miitoo-button'.(isset($my_empathy_added) && $my_empathy_added == true ? ' empathy-added' : '').''.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').'" data-feeling="'.$usermii['feeling'].'" data-action="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'/empathies" data-sound="SE_WAVE_MII_'.(isset($my_empathy_added) && $my_empathy_added == true ? 'CANCEL' : 'ADD').'" data-community-id="'.$community['olive_community_id'].'" data-url-id="'.$row['id'].'" data-track-label="default" data-title-id="'.$community['olive_title_id'].'" data-track-action="yeah" data-track-category="empathy">'.(isset($my_empathy_added) && $my_empathy_added == true ? 'Unyeah' : (!empty($usermii['miitoo']) ? $usermii['miitoo'] : 'Yeah!')).'</button>
        <a href="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'" class="to-permalink-button" data-pjax="#body">
          <span class="feeling">'.$empathies.'</span>
		  ';
if($reply == true) {
	print '<span class="reply">'.$replies.'</span>'; }
print '        </a>
      </div>';
}
	  
print '
    </div>';
if($is_activity) {
print '<div class="another-posts">
      <a href="/users/'.$user['user_id'].'/posts" data-pjax="#body">他の投稿を見る</a>
    </div>'; }
if(!$is_official) {
	print '
</div>'; }
  
if($is_official) {
if(isset($_SESSION['pid'])) {
$sql_relationship_identified_user_post = 'SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$user['pid'].'"';
$result_relationship_identified_user_post = mysqli_query($mysql, $sql_relationship_identified_user_post);
if(mysqli_num_rows($result_relationship_identified_user_post) != 0 || $_SESSION['pid'] == $user['pid']) {
print '<div class="toggle-button">

</div>'; }

else {
print '<div class="toggle-button">
    <a class="follow-button button add-button" href="#" data-action="/users/'.htmlspecialchars($user['user_id']).'/follow" data-sound="SE_WAVE_FRIEND_ADD" data-community-id="" data-url-id="" data-track-label="user" data-title-id="" data-track-action="follow" data-track-category="follow">Follow</a>
      <button class="button follow-done-button relationship-button done-button none" disabled="">Follow</button>
</div>'; }
} else {
print '<div class="toggle-button">

</div>'; }
}
 
if(!$is_activity && !$is_official && time() - strtotime($row['created_at']) <= 432000) {
$get_recent_repliesall = $mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$row['id'].'" AND replies.is_hidden != "1" ORDER BY replies.created_at DESC');
$get_recent_replies = $mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$row['id'].'" AND replies.is_hidden != "1" AND replies.pid != "'.$row['pid'].'" AND replies.is_spoiler != "1" ORDER BY replies.created_at DESC');

	if($get_recent_replies->num_rows >=1) {
	while($reply = $get_recent_replies->fetch_assoc()) {
	if($reply['is_spoiler'] == '1' || $reply['is_hidden'] == '1' ) { }
	else {
	$reply_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$ogpost['pid'].'" LIMIT 1')->fetch_assoc();
    $reply_user_mii = getMii($reply_user, $reply['feeling_id']);

	print '<div id="recent-reply-' . $reply['id'] . '" class="recent-reply">
  <a href="/users/' . htmlspecialchars($reply_user['user_id']) . '" class="user-icon-container scroll-focus'.($reply_user_mii['official'] ? ' official-user' : null).'" data-pjax="#body">
    <img src="'.$reply_user_mii['output'].'" class="user-icon">
  </a>

  <div class="recent-reply-body-content">
    <div class="recent-reply-body">
      <header>
        <span class="user-name">'.htmlspecialchars($reply_user['screen_name']).'</span>
        <span class="timestamp">'.humanTiming(strtotime($reply['created_at'])).'</span>
      </header>

      <div class="recent-reply-content">

        <p class="recent-reply-content-text">'.preg_replace("/\r|\n/"," ",(mb_strlen($reply['body'], 'utf-8') >= 204 ? mb_substr($reply['body'], 0, 200, 'utf-8').'...' : $reply['body'])).'</p>

      </div>
      <a href="/posts/'.$row['id'].'" class="to-permalink-button" data-pjax="#body"></a>
	  ';
	 if($get_recent_repliesall->num_rows >=3) {
$replies_minus_one = $get_recent_repliesall->num_rows - 1;
	 print '</div><a href="/posts/'.$row['id'].'" class="button read-more-button to-permalink-button" data-pjax="#body">
      View More Comments ('.$replies_minus_one.')
    </a>';
	}
	 if($get_recent_repliesall->num_rows == 2) {
$replies_minus_one = $get_recent_repliesall->num_rows - 1;
print '</div><a href="/posts/'.$row['id'].'" class="button read-more-button to-permalink-button" data-pjax="#body">
      View Other Comment ('.$replies_minus_one.')
    </a>';
	}
		 if($get_recent_repliesall->num_rows >=2) {
		 print '</div></div>'; }
		 else {
		 print '</div></div></div>'; }
	}}
		 if($is_activity) {
		 print '</div></div></div>'; }
	  }
} }
print '</div>';
}
}