<?php

#$row_temp_current_post = $row_userpage_user_posts_view;
#$result_temp_current_post_replies = $row_userpage_user_posts_replies;
#$result_temp_current_post_empathies = $row_userpage_user_posts_empathies;
#$row_temp_current_post_user = $row_userpage_user_posts_view_user;
#$is_activity_feed_post = '1'
#$is_identified_user_post = '1'

if(!isset($row_temp_current_post['id'])) {
print null; }  else {
if($row_temp_current_post['is_hidden'] == 1 && $row_temp_current_post['hidden_resp'] == '1') {
print null;	} else {
if(strlen($row_temp_current_post['_post_type']) > 10 ) {
$row_temp_current_post_type = 'replies'; 
}
else {
$row_temp_current_post_type = 'posts'; }

	if($row_temp_current_post_user['mii_hash']) {
if($row_temp_current_post['feeling_id'] == '0') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_temp_current_post_user['mii_hash'] . '_normal_face.png'; 
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!'); }
if($row_temp_current_post['feeling_id'] == '1') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_temp_current_post_user['mii_hash'] . '_happy_face.png'; 
$mii_face_feeling = 'happy'; }
$mii_face_miitoo = htmlspecialchars('Yeah!');
if($row_temp_current_post['feeling_id'] == '2') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_temp_current_post_user['mii_hash'] . '_like_face.png'; 
$mii_face_feeling = 'like';
$mii_face_miitoo = htmlspecialchars('Yeah♥'); }
if($row_temp_current_post['feeling_id'] == '3') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_temp_current_post_user['mii_hash'] . '_surprised_face.png'; 
$mii_face_feeling = 'surprised';
$mii_face_miitoo = htmlspecialchars('Yeah!?'); }
if($row_temp_current_post['feeling_id'] == '4') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_temp_current_post_user['mii_hash'] . '_frustrated_face.png'; 
$mii_face_feeling = 'frustrated';
$mii_face_miitoo = htmlspecialchars('Yeah...'); }
if($row_temp_current_post['feeling_id'] == '5') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_temp_current_post_user['mii_hash'] . '_puzzled_face.png'; 
$mii_face_feeling = 'puzzled';
$mii_face_miitoo = htmlspecialchars('Yeah...'); }
}
else {
if($row_temp_current_post_user['user_face']) {
$mii_face_output = htmlspecialchars($row_temp_current_post_user['user_face']);
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!');
} else {
$mii_face_output = '/img/mii/img_unknown_MiiIcon.png';
$mii_face_feeling = 'normal';
$mii_face_miitoo = htmlspecialchars('Yeah!'); }
}

if($row_temp_current_post_user['official_user'] == 1) {
$is_poster_official_user = ' official-user';
}
else {
$is_poster_official_user = ''; }

$row_temp_post_id = $row_temp_current_post['id'];

	if(isset($row_temp_current_post['is_spoiler']) && $row_temp_current_post['is_spoiler'] == '1') {
	if(isset($_SESSION['pid']) && $row_temp_current_post['pid'] == $_SESSION['pid']) {
	$if_has_can_spoiler_post = ''; }
	else {
	$if_has_can_spoiler_post = ' hidden'; }
	}
	else {
	$if_has_can_spoiler_post = ''; }

if(isset($row_temp_current_post['url'])) {
if (strpos( $row_temp_current_post['url'], 'www.youtube.com/watch?v=') !== false) {
if(substr($row_temp_current_post['url'], 0, 4) == "http") {
${"template_post_yt_url_$row_temp_post_id"} = substr($row_temp_current_post['url'], 31, 11);
$if_has_can_video_post = ' with-video-image';
}
if(substr($row_temp_current_post['url'], 0, 5) == "https") {
${"template_post_yt_url_$row_temp_post_id"} = substr($row_temp_current_post['url'], 32, 11);
$if_has_can_video_post = ' with-video-image';
}
    }
}

if(isset($row_temp_current_post['screenshot'])) {
if(strlen($row_temp_current_post['screenshot']) > 3) {
$if_has_can_screenshot_post = ' with-image'; }
else {
    $if_has_can_screenshot_post = '';
}  }
else {
    $if_has_can_screenshot_post = '';
}

if (empty(${"template_post_yt_url_$row_temp_post_id"})) {
	$if_has_can_video_post = '';
}
	
	
print '<div id="post-'.$row_temp_current_post['id'].'" class="post scroll post-subtype-default'.$if_has_can_spoiler_post.''.$if_has_can_video_post.''.$if_has_can_screenshot_post.'" data-post-permalink-url="/'.$row_temp_current_post_type.'/'.$row_temp_current_post['id'].'">
  <a href="/users/'.htmlspecialchars($row_temp_current_post_user['user_id']).'" class="user-icon-container scroll-focus'.$is_poster_official_user.'" data-pjax="#body"><img src="'.$mii_face_output.'" class="user-icon"></a>
  ';
if(!isset($is_identified_user_post)) {
print '  <div class="post-body-content">'; }
print '    <div class="post-body">
      <header>
        <span class="user-name">'.htmlspecialchars($row_temp_current_post_user['screen_name']).'</span>
		';
		if(isset($is_identified_user_post)) {
$sql_userpage_identified_user_profile = 'SELECT * FROM profiles WHERE profiles.pid = "'.$row_temp_current_post_user['pid'].'"';
$result_userpage_identified_user_profile = mysqli_query($mysql, $sql_userpage_identified_user_profile);
if(mysqli_num_rows($result_userpage_identified_user_profile) == 0) {
print '<p class="text"></p>';
} else {
print '<p class="text">'.htmlspecialchars(mysqli_fetch_assoc($result_userpage_identified_user_profile)['comment']).'</p>'; }
		} else {
print '        <span class="timestamp">'.humanTiming(strtotime($row_temp_current_post['created_at'])).'</span>
 ';    		if(isset($row_temp_current_post['is_spoiler']) && $row_temp_current_post['is_spoiler'] == '1') {       
		print '	  <span class="spoiler-status spoiler">Spoilers</span>'; }
		}

$sql_userpage_user_posts_view_user_community = 'SELECT * FROM communities WHERE communities.community_id = "' . $row_temp_current_post['community_id'] . '"';
$result_userpage_user_posts_view_user_community = mysqli_query($mysql, $sql_userpage_user_posts_view_user_community);
$row_userpage_user_posts_view_user_community = mysqli_fetch_assoc($result_userpage_user_posts_view_user_community); 

if(strlen($row_temp_current_post['_post_type']) > 10 ) {
$is_post_reply1 = ' user-icon-container';
$is_post_reply2 = ' user-icon'; }
else {
$is_post_reply1 = '';
$is_post_reply2 = '';
}

print '        
      </header>          
';
if(strlen($row_temp_current_post['_post_type']) > 10 ) {
print '        <a href="/posts/'.$row_temp_current_post['_post_type'].'" class="community-content test-post-target-href" data-pjax="#body">
          <span class="title-icon-container'.$is_post_reply1.'" data-pjax="#body"><img src="';


$sql_userpage_user_posts_view_user_userreply = 'SELECT * FROM posts WHERE posts.id = "' . $row_temp_current_post['_post_type'] . '"';
$result_userpage_user_posts_view_user_userreply = mysqli_query($mysql, $sql_userpage_user_posts_view_user_userreply);
$row_userpage_user_posts_view_user_userreply = mysqli_fetch_assoc($result_userpage_user_posts_view_user_userreply); 

$sql_userpage_user_posts_view_user_userreplypp = 'SELECT * FROM people WHERE people.pid = "' . $row_userpage_user_posts_view_user_userreply['pid'] . '"';
$result_userpage_user_posts_view_user_userreplypp = mysqli_query($mysql, $sql_userpage_user_posts_view_user_userreplypp);
$row_userpage_user_posts_view_user_userreplypp = mysqli_fetch_assoc($result_userpage_user_posts_view_user_userreplypp); 	

	if($row_userpage_user_posts_view_user_userreplypp['mii_hash']) {
if(($row_userpage_user_posts_view_user_userreply['feeling_id']) == '0') {
$mii_face_outputr2 = 'https://mii-secure.cdn.nintendo.net/' . $row_userpage_user_posts_view_user_userreplypp['mii_hash'] . '_normal_face.png'; }
if(($row_userpage_user_posts_view_user_userreply['feeling_id']) == '1') {
$mii_face_outputr2 = 'https://mii-secure.cdn.nintendo.net/' . $row_userpage_user_posts_view_user_userreplypp['mii_hash'] . '_happy_face.png'; }
if(($row_userpage_user_posts_view_user_userreply['feeling_id']) == '2') {
$mii_face_outputr2 = 'https://mii-secure.cdn.nintendo.net/' . $row_userpage_user_posts_view_user_userreplypp['mii_hash'] . '_like_face.png'; }
if(($row_userpage_user_posts_view_user_userreply['feeling_id']) == '3') {
$mii_face_outputr2 = 'https://mii-secure.cdn.nintendo.net/' . $row_userpage_user_posts_view_user_userreplypp['mii_hash'] . '_surprised_face.png'; }
if(($row_userpage_user_posts_view_user_userreply['feeling_id']) == '4') {
$mii_face_outputr2 = 'https://mii-secure.cdn.nintendo.net/' . $row_userpage_user_posts_view_user_userreplypp['mii_hash'] . '_frustrated_face.png'; }
if(($row_userpage_user_posts_view_user_userreply['feeling_id']) == '5') {
$mii_face_outputr2 = 'https://mii-secure.cdn.nintendo.net/' . $row_userpage_user_posts_view_user_userreplypp['mii_hash'] . '_puzzled_face.png'; }
}
else {
if($row_userpage_user_posts_view_user_userreplypp['user_face']) {
$mii_face_outputr2 = htmlspecialchars($row_userpage_user_posts_view_user_userreplypp['user_face']);
} else {
$mii_face_outputr2 = '/img/mii/img_unknown_MiiIcon.png'; }
}

print $mii_face_outputr2;

print '" class="title-icon'.$is_post_reply2.'"></span>
          <span class="community-name">'.htmlspecialchars($row_userpage_user_posts_view_user_userreplypp['screen_name']).'';
		  print "'s Post";
		  print '</span>
        </a>';
}
else {
if(strval($row_userpage_user_posts_view_user_community['type']) == 5) {
print '<a class="community-content test-post-target-href" data-pjax="#body">
          <span class="title-icon-container" data-pjax="#body"><img src="'.htmlspecialchars($row_userpage_user_posts_view_user_community['icon']).'" class="title-icon"></span>
          <span class="community-name">'.htmlspecialchars($row_userpage_user_posts_view_user_community['name']).'</span>
        </a>';
}
else {
if(empty($row_userpage_user_posts_view_user_community['icon'])) {
$row_userpage_user_posts_view_user_community['icon'] = 'https://miiverse.nintendo.net/img/title-icon-default.png'; }
print '        <a href="/titles/'.$row_userpage_user_posts_view_user_community['olive_title_id'].'/'.$row_userpage_user_posts_view_user_community['olive_community_id'].'" class="community-content test-post-target-href" data-pjax="#body">
          <span class="title-icon-container'.$is_post_reply1.'" data-pjax="#body"><img src="';
print htmlspecialchars($row_userpage_user_posts_view_user_community['icon']); 
print '" class="title-icon"></span>
<span class="community-name">'.htmlspecialchars($row_userpage_user_posts_view_user_community['name']).'</span></a>';
} }
if($row_temp_current_post['is_hidden'] != 1 && $row_temp_current_post['hidden_resp'] != '0') {
	  if(!empty(${"template_post_yt_url_$row_temp_post_id"})) {

print '<div class="title-capture-container video-container">
<a class="video-thumbnail" href="/posts/' . $row_temp_post_id . '#post-video" data-pjax="#body">
<span><img width="120" height="90" src="https://i.ytimg.com/vi/' . ${"template_post_yt_url_$row_temp_post_id"} . '/default.jpg"></span></a></div>';
}
if(isset($row_temp_current_post['screenshot'])) {
if(strlen($row_temp_current_post['screenshot']) > 3) {
print '<a href="#" role="button" class="title-capture-container capture-container" data-modal-open="#capture-page" data-large-capture-url="' . htmlspecialchars($row_temp_current_post['screenshot']) . '"><img src="' . htmlspecialchars($row_temp_current_post['screenshot']) . '" class="title-capture"></a>';
  }
} }
print '
      <div class="post-content">

';
if($row_temp_current_post['is_hidden'] == 1 && $row_temp_current_post['hidden_resp'] == 0) {
require_once 'olv-url-enc.php';
print '
        <p class="deleted-message">Deleted by administrator.</p>
        <p class="deleted-message">Post ID: '.getPostID($row_temp_current_post['id']).'</p>
</div>
</div>
</div>'; }
else {
$truncate_post_bodyp1 = mb_substr((htmlspecialchars($row_temp_current_post['body'])), 0, 200, 'utf-8');
if(mb_strlen($truncate_post_bodyp1, 'utf-8') >= 200) {
$truncate_post_body = "$truncate_post_bodyp1..."; }
else {
$truncate_post_body = $truncate_post_bodyp1; }

if(isset($_SESSION['signed_in'])) {
if(isset($row_current_peopleban)) {
$can_post_user_miitoo = ' disabled'; }
    elseif(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && $row_temp_current_post['pid'] != $_SESSION['pid']) {
$can_post_user_miitoo = ''; }
else {
$can_post_user_miitoo = ' disabled';	
} }
	else {
	$can_post_user_miitoo = ' disabled'; }
if($row_temp_current_post['_post_type'] == 'artwork') {
print '<p class="post-content-memo"><img src="'.htmlspecialchars($row_temp_current_post['body']).'" class="post-memo"></p>
	  </div>'; } else {
print '

            <p class="post-content-text">'.preg_replace("/[\r\n]+/", "\n", $truncate_post_body).'</p>
      </div>
	  
	  
'; }
	 if(isset($row_temp_current_post['is_spoiler']) && $row_temp_current_post['is_spoiler'] == '1') {

if(isset($_SESSION['pid'])) {
	if($row_temp_current_post['pid'] != $_SESSION['pid']) {
	print '	<div class="hidden-content">
        <p>This post contains spoilers.</p>
        <div><a href="#" class="hidden-content-button">View Post</a></div>
</div>'; } }
else {
	print '	<div class="hidden-content">
        <p>This post contains spoilers.</p>
        <div><a href="#" class="hidden-content-button">View Post</a></div>
</div>'; }


	  }
	
	
    if(isset($_SESSION['pid'])) {
$sql_hasempathy = 'SELECT * FROM empathies WHERE empathies.id = "' . mysqli_real_escape_string($mysql, $row_temp_current_post['id']) . '" AND empathies.pid = "' . $_SESSION['pid'] . '"';
$result_hasempathy = mysqli_query($mysql, $sql_hasempathy);

if(mysqli_num_rows($result_hasempathy)!=0) {
    $mii_face_miitoo = 'Unyeah';
	${"has_post_miitoo_given_$row_temp_post_id"} = ' empathy-added'; 
$has_post_miitoo_given_snd = 'SE_WAVE_MII_CANCEL'; }
else {
	${"has_post_miitoo_given_$row_temp_post_id"} = ''; 
	$has_post_miitoo_given_snd = 'SE_WAVE_MII_ADD'; }
	}
	
	else {
	${"has_post_miitoo_given_$row_temp_post_id"} = ''; 
	$has_post_miitoo_given_snd = 'SE_WAVE_MII_ADD'; }
	
if(isset($is_identified_user_post)) {
print '<div class="post-meta">
              <span class="timestamp">'.humanTiming(strtotime($row_temp_current_post['created_at'])).'</span>
              <a href="/posts/'.$row_temp_current_post['id'].'" class="to-permalink-button" data-pjax="#body">
                <span class="feeling">'.mysqli_num_rows($result_temp_current_post_empathies).'</span>
                <span class="reply">'.mysqli_num_rows($result_temp_current_post_replies).'</span>
              </a>
            </div>
			';
} else {	
print '


      <div class="post-meta">
        <button type="button"'.$can_post_user_miitoo.' class="submit miitoo-button'.${"has_post_miitoo_given_$row_temp_post_id"}.''.$can_post_user_miitoo.'" data-feeling="'.$mii_face_feeling.'" data-action="/'.$row_temp_current_post_type.'/'.$row_temp_current_post['id'].'/empathies" data-sound="'.$has_post_miitoo_given_snd.'" data-community-id="'.$row_userpage_user_posts_view_user_community['olive_community_id'].'" data-url-id="'.$row_temp_current_post['id'].'" data-track-label="default" data-title-id="'.$row_userpage_user_posts_view_user_community['olive_title_id'].'" data-track-action="yeah" data-track-category="empathy">'.$mii_face_miitoo.'</button>
        <a href="/'.$row_temp_current_post_type.'/'.$row_temp_current_post['id'].'" class="to-permalink-button" data-pjax="#body">
          <span class="feeling">'.mysqli_num_rows($result_temp_current_post_empathies).'</span>
		  ';
if(strlen($row_temp_current_post['_post_type']) < 10 ) {
	print '<span class="reply">'.mysqli_num_rows($result_temp_current_post_replies).'</span>'; }
print '        </a>
      </div>';
}
	  
print '
    </div>';
if(isset($is_activity_feed_post)) {
print '<div class="another-posts">
      <a href="/users/'.$row_temp_current_post_user['user_id'].'/posts" data-pjax="#body">他の投稿を見る</a>
    </div>'; }
if(!isset($is_identified_user_post)) {
	print '
</div>'; }
  
if(isset($is_identified_user_post)) {
if(isset($_SESSION['pid'])) {
$sql_relationship_identified_user_post = 'SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$row_temp_current_post_user['pid'].'"';
$result_relationship_identified_user_post = mysqli_query($mysql, $sql_relationship_identified_user_post);
if(mysqli_num_rows($result_relationship_identified_user_post) != 0 || $_SESSION['pid'] == $row_temp_current_post_user['pid']) {
print '<div class="toggle-button">

</div>'; }

else {
print '<div class="toggle-button">
    <a class="follow-button button add-button" href="#" data-action="/users/'.htmlspecialchars($row_temp_current_post_user['user_id']).'/follow" data-sound="SE_WAVE_FRIEND_ADD" data-community-id="" data-url-id="" data-track-label="user" data-title-id="" data-track-action="follow" data-track-category="follow">Follow</a>
      <button class="button follow-done-button relationship-button done-button none" disabled="">Follow</button>
</div>'; }
} else {
print '<div class="toggle-button">

</div>'; }
}
  
if(!isset($is_activity_feed_post) && !isset($is_identified_user_post) && time() - strtotime($row_temp_current_post['created_at']) <= 432000) {
	$sql_post_recent_replies = 'SELECT * FROM replies WHERE replies.reply_to_id = "' . $row_temp_current_post['id'] . '" AND replies.is_spoiler != "1" AND replies.is_hidden != "1" AND replies.pid !="'.$row_temp_current_post['pid'].'" ORDER BY replies.created_at DESC LIMIT 1';
	$result_post_recent_replies = mysqli_query($mysql, $sql_post_recent_replies);

	if(strval(mysqli_num_rows($result_temp_current_post_replies)) >=1) {
	while($row_post_recent_replies = mysqli_fetch_assoc($result_post_recent_replies)) {
	
	if($row_post_recent_replies['is_spoiler'] == '1' || $row_post_recent_replies['is_hidden'] == '1' ) {
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
        <span class="user-name">'.htmlspecialchars($row_post_recent_replies_user['screen_name']).'</span>
        <span class="timestamp">'.humanTiming(strtotime($row_post_recent_replies['created_at'])).'</span>
      </header>

      <div class="recent-reply-content">

        <p class="recent-reply-content-text">'.preg_replace( "/\r|\n/", " ", $truncate_reply_body ).'</p>

      </div>
      <a href="/posts/'.$row_temp_current_post['id'].'" class="to-permalink-button" data-pjax="#body"></a>
	  ';
	 if(strval(mysqli_num_rows($result_temp_current_post_replies)) >=3) {
$replies_minus_one = strval(mysqli_num_rows($result_temp_current_post_replies)) - 1;
	 print '</div><a href="/posts/'.$row_temp_current_post['id'].'" class="button read-more-button to-permalink-button" data-pjax="#body">
      View More Comments ('.$replies_minus_one.')
    </a>';
	}
	 if(strval(mysqli_num_rows($result_temp_current_post_replies)) == 2) {
$replies_minus_one = strval(mysqli_num_rows($result_temp_current_post_replies)) - 1;
print '</div><a href="/posts/'.$row_temp_current_post['id'].'" class="button read-more-button to-permalink-button" data-pjax="#body">
      View Other Comment ('.$replies_minus_one.')
    </a>';
	}
		 if(strval(mysqli_num_rows($result_temp_current_post_replies)) >=2) {
		 print '</div></div>'; }
		 else {
		 print '</div></div></div>'; }
	}}
		 if(isset($is_activity_feed_post)) {
		 print '</div></div></div>'; }
	  }
} }
print '</div>';
}
}

?>