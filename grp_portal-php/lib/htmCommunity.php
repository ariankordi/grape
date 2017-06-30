<?php

function getIcon($row) {
if(empty($row['icon']) || strlen($row['icon']) <= 1) { return 'https://miiverse.nintendo.net/img/title-icon-default.png'; } else {
return htmlspecialchars($row['icon']); }
}

function favButton() {
print '<a id="header-favorites-button" href="/communities/favorites" data-pjax="#body">'.loc('community', 'grp.portal.favorites_my').'</a>';
}

function printTitle($row) {
global $mysql;
			print '<li id="community-'.$row['olive_community_id'].'" class="">
			<span class="icon-container"><img src="'.getIcon($row).'" class="icon"></span>';
	if(findMore($row)) {				
		print '
		<a href="/titles/'.$row['olive_title_id'].'" data-pjax="#body" class="list-button button">Related Communities</a>
		';
	}
     print '<a href="/titles/'.$row['olive_title_id'].'/'.$row['olive_community_id'].'" data-pjax="#body" class="scroll to-community-button"></a>
   <div class="body">
     <div class="body-content">
        <span class="community-name title">'.htmlspecialchars($row['name']).'</span>
		
		';
if(!empty($row['platform_type'])) {
if($row['platform_type'] == '1' && $row['platform_id'] == '1') { $platformIDtext = loc('community', 'grp.wiiu_games'); }
elseif($row['platform_type'] == '1' && $row['platform_id'] != '1') { $platformIDtext = loc('community', 'grp.3ds_games'); } 
elseif($row['platform_type'] == '2') { $platformIDtext = loc('community', 'grp.3ds_games'); } 
elseif($row['platform_type'] == '3') { $platformIDtext = loc('community', 'grp.virtualconsole'); } 
else { $platformIDtext = loc('community', 'grp.other'); } }
	
	if(!empty($row['platform_id'])) {
print '<span class="platform-tag platform-tag-'.($row['platform_id'] == 1 ? 'wiiu' : '3ds').'"></span>
<span class="text">'.$platformIDtext.'</span>
';
	}
	
print '
  </div>
 </div>
</li>
';
}

function printCommunityforTitle($row, $title) {
			if($row['type'] != 5) {
       print '<li id="community-'.htmlspecialchars($row['olive_community_id']).'" class="'.($row['type'] >= 1 ? 'with-news-community-badge' : null).'">
       <span class="icon-container"><img src="'.getIcon($row).'" class="icon"></span>
       <a href="/titles/'.$row['olive_title_id'].'/'.$row['olive_community_id'].'" data-pjax="#body" class="scroll arrow-button"></a>
	   <div class="body">
       <div class="body-content">
       ';
       if($row['type'] >= 1) {
       print '<span class="news-community-badge">'.($row['type'] == 2 ? 'Announcement Community' : 'Main Community').'</span>'; }
	print '<span class="community-name title">'.htmlspecialchars($row['name']).'</span>
	       <span class="text">'.htmlspecialchars($title['name']).'</span>
						
						';
} }

function favoriteWithTitle($row_community) {
global $mysql;
$row_get_titles_from_cid = $mysql->query('SELECT * FROM titles WHERE titles.olive_title_id = "'.$row_community['olive_title_id'].'"')->fetch_assoc();
if(!empty($row_get_titles_from_cid['platform_id'])) {
if($row_get_titles_from_cid['platform_id'] == '0') { $platform_id_text = "3ds"; }
if($row_get_titles_from_cid['platform_id'] == '1') { $platform_id_text = "wiiu"; }
if($row_get_titles_from_cid['platform_id'] == '2') { $platform_id_text = "3ds"; }
}
return '
<li id="community-'.$row_community['olive_community_id'].'" class="">
  <span class="icon-container"><img src="'.getIcon($row_community).'" class="icon"></span>
  <a href="/titles/'.htmlspecialchars($row_community['olive_title_id']).'/'.htmlspecialchars($row_community['olive_community_id']).'" data-pjax="#body" class="scroll arrow-button"></a>
  <div class="body">
    <div class="body-content">      <span class="community-name title">'.htmlspecialchars($row_community['name']).'</span>
'.
	(isset($platform_id_text) ? '<span class="platform-tag platform-tag-'.$platform_id_text.'"></span>' : '') .'

	
      <span class="text">'.htmlspecialchars($row_get_titles_from_cid['name']).'</span>
      
      
    </div>
  </div>
</li>

';
}

function favoriteWithIcon($row_community, $existence) {
if($existence == false) {
return '<li class="favorite-community empty">
      <span class="icon-container"></span>
    </li>';
} else {
global $mysql;
$row_get_titles_from_cid = $mysql->query('SELECT * FROM titles WHERE titles.olive_title_id = "'.$row_community['olive_title_id'].'"')->fetch_assoc();
if(!empty($row_get_titles_from_cid['platform_id'])) {
if($row_get_titles_from_cid['platform_id'] == '0') { $platform_id_text = "3ds"; }
if($row_get_titles_from_cid['platform_id'] == '1') { $platform_id_text = "wiiu"; }
if($row_get_titles_from_cid['platform_id'] == '2') { $platform_id_text = "3ds"; }
}
return '<li class="favorite-community">
      <a href="/titles/'.htmlspecialchars($row_community['olive_title_id']).'/'.htmlspecialchars($row_community['olive_community_id']).'" data-pjax="#body"><span class="icon-container"><img class="icon" src="'.getIcon($row_community).'"></span></a>'.      (isset($platform_id_text) ? '<span class="platform-tag platform-tag-'.$platform_id_text.'"></span>' : '').'
    </li>';	
} }

function printPost($row, $is_user, $is_activity, $is_official) {
global $mysql;
if(empty($row['id'])) { return null; } elseif($row['is_hidden'] == 1 && $row['hidden_resp'] == '1') { return null; } else {
if(strlen($row['_post_type']) > 10) { $reply = true; } else { $reply = false; }

require_once '../grplib-php/user-helper.php';
if(!empty($_SESSION['pid']) && canUserView($_SESSION['pid'], $row['pid'])) {
return null; }

global $pref_id;
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$row['pid'].'" LIMIT 1')->fetch_assoc();
if($reply != true) {
$community = $mysql->query('SELECT * FROM communities WHERE communities.community_id = "'.$row['community_id'].'" LIMIT 1')->fetch_assoc();
$title = $mysql->query('SELECT * FROM titles WHERE titles.olive_title_id = "'.$community['olive_title_id'].'" LIMIT 1')->fetch_assoc();
} else {
$ogpost = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$row['_post_type'].'" LIMIT 1')->fetch_assoc();
$ogpost_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$ogpost['pid'].'" LIMIT 1')->fetch_assoc();
$ogpost_user_mii = getMii($ogpost_user, $ogpost['feeling_id']);
$community = $mysql->query('SELECT * FROM communities WHERE communities.community_id = "'.$ogpost['community_id'].'" LIMIT 1')->fetch_assoc();
$title = $mysql->query('SELECT * FROM titles WHERE titles.olive_title_id = "'.$community['olive_title_id'].'" LIMIT 1')->fetch_assoc();
}
$usermii = getMii($user, $row['feeling_id']);

$admin_del = $row['is_hidden'] == '1' && $row['hidden_resp'] == 0;
$my_post = !empty($_SESSION['pid']) && $_SESSION['pid'] == $row['pid'];

if($reply == false) {
if(!empty($row['url']) && strpos($row['url'], 'www.youtube.com/watch?v=') !== false) {
if(substr($row['url'], 0, 4) == "http" || substr($row['url'], 0, 5) == "https") {
$videopost = substr($row['url'], (substr($row['url'], 0, 5) == "https" ? 32 : 31), 11);
} } }	

if(!isset($pref_id)) {
	if(!empty($_SESSION['pid'])) { 
	$search_settings = $mysql->query('SELECT * FROM settings_title WHERE settings_title.pid = "'.$_SESSION['pid'].'" AND settings_title.olive_title_id = "'.$title['olive_title_id'].'" LIMIT 1');
	$pref_id = $search_settings->num_rows != 0 ? $search_settings->fetch_assoc()['value'] : 0;
	} else {
$pref_id = 0; 
} }
$show_spoiler = (!empty($_SESSION['pid']) && $_SESSION['pid'] == $row['pid']) || $pref_id == 1;

print '<div id="post-'.$row['id'].'" class="post scroll post-subtype-default'.($row['is_spoiler'] == 1 ? ($show_spoiler ? null : ' hidden') : null).(!empty($videopost) ? ' with-video-image' : null).(!empty($row['screenshot']) ? ' with-image' : null).'" data-post-permalink-url="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'">
  <a href="/users/'.htmlspecialchars($user['user_id']).'" class="user-icon-container scroll-focus'.($usermii['official'] ? ' official-user' : null).'" data-pjax="#body"><img src="'.$usermii['output'].'" class="user-icon"></a>
  ';
if(!$is_official) {
print '  <div class="post-body-content">'; }
print '    <div class="post-body">
      <header>
        <span class="user-name">'.htmlspecialchars($user['screen_name']).'</span>
		';
		if($is_official) {
require_once '../grplib-php/user-helper.php';
print '<p class="text">'.getProfileComment($user, false).'</p>';
		} else {
print '        <span class="timestamp">'.humanTiming(strtotime($row['created_at'])).'</span>
 ';    
		print '	  <span class="spoiler-status'.(!empty($row['is_spoiler']) && $row['is_spoiler'] == '1' ? ' spoiler' : null).'">Spoilers</span>';
		}
		
print '        
      </header>          
';
if($is_user == true) { if($reply == true) {
print '        <a href="/posts/'.$ogpost['id'].'" class="community-content test-post-target-href" data-pjax="#body">
          <span class="title-icon-container'.($reply == true ? ' user-icon-container' : null).'" data-pjax="#body"><img src="'.$ogpost_user_mii['output'].'" class="title-icon'.($reply == true ? ' user-icon' : null).'"></span>
          <span class="community-name">'.htmlspecialchars($ogpost_user['screen_name']).'';
		  print "'s Post";
		  print '</span>
        </a>';
}
else {
print '        <a'.($community['type'] != '5' ? ' href="/titles/'.$community['olive_title_id'].'/'.$community['olive_community_id'].'"' : null).' class="community-content test-post-target-href" data-pjax="#body">
          <span class="title-icon-container'.($reply == true ? ' user-icon-container' : null).'" data-pjax="#body"><img src="'.getIcon($community).'" class="title-icon"></span>
<span class="community-name">'.htmlspecialchars($community['name']).'</span></a>';
} }
if(!($admin_del && !$my_post)) {
	  if(!empty($videopost)) {

print '<div class="title-capture-container video-container">
<a class="video-thumbnail" href="/posts/'.$row['id'].'#post-video" data-pjax="#body">
<span><img width="120" height="90" src="https://i.ytimg.com/vi/'.$videopost.'/default.jpg"></span></a></div>';
}
if(!empty($row['screenshot'])) {
print '<a href="#" role="button" class="title-capture-container capture-container" data-modal-open="#capture-page" data-large-capture-url="'.htmlspecialchars($row['screenshot']).'"><img src="'.htmlspecialchars($row['screenshot']).'" class="title-capture"></a>';
  } }
print '
      <div class="post-content">

';
if($admin_del && !$my_post) {
require_once '../grplib-php/olv-url-enc.php';
print '
        <p class="deleted-message">Deleted by administrator.</p>
        <p class="deleted-message">Post ID: '.getPostID($row['id']).'</p>
</div>
</div>
</div>'; }
else {
if($admin_del) {
require_once '../grplib-php/olv-url-enc.php';
print '
        <p class="deleted-message">Deleted by administrator.</p>
        <p class="deleted-message">Post ID: '.getPostID($row['id']).'</p>
		';
}
$truncate_post_body = (mb_strlen($row['body']) >= 204 ? mb_substr($row['body'], 0, 200).'...' : $row['body']);

if($row['_post_type'] == 'artwork') {
print '<p class="post-content-memo"><img src="'.htmlspecialchars($row['body']).'" class="post-memo"></p>
	  </div>'; } else {
print '

            <p class="post-content-text">'.htmlspecialchars(preg_replace("/[\r\n]+/", "\n", $truncate_post_body)).'</p>
      </div>
	  
	  
'; }
	if($row['is_spoiler'] == 1) {
if(!$show_spoiler) {  print '	<div class="hidden-content">
        <p>This post contains spoilers.</p>
        <div><a href="#" class="hidden-content-button">View Post</a></div>
</div>'; } }
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
$my_empathy_added = $mysql->query('SELECT * FROM empathies WHERE empathies.id = "'.$row['id'].'" AND empathies.pid = "'.$_SESSION['pid'].'" LIMIT 1')->num_rows == 1;
}
print '


      <div class="post-meta">
        <button type="button"'.(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').' class="submit miitoo-button'.(isset($my_empathy_added) && $my_empathy_added == true ? ' empathy-added' : '').(empty($_SESSION['pid']) || !$canmiitoo ? ' disabled' : '').'" data-feeling="'.$usermii['feeling'].'" data-action="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'/empathies" data-sound="SE_WAVE_MII_'.(isset($my_empathy_added) && $my_empathy_added == true ? 'CANCEL' : 'ADD').'" data-community-id="'.$community['olive_community_id'].'" data-url-id="'.$row['id'].'" data-track-label="default" data-title-id="'.$community['olive_title_id'].'" data-track-action="yeah" data-track-category="empathy">'.(isset($my_empathy_added) && $my_empathy_added == true ? $usermii['miitoo_delete'] : (!empty($usermii['miitoo']) ? $usermii['miitoo'] : 'Yeah!')).'</button>
        <a href="/'.($reply == true ? 'replies' : 'posts').'/'.$row['id'].'" class="to-permalink-button" data-pjax="#body">
          <span class="feeling">'.$empathies.'</span>
		  ';
if($reply == false) {
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
if(!empty($_SESSION['pid'])) {
$sql_relationship_identified_user_post = 'SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$user['pid'].'"';
$result_relationship_identified_user_post = $mysql->query($sql_relationship_identified_user_post);
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
 
if(!$is_activity && !$is_official && !$admin_del && time() - strtotime($row['created_at']) <= 432000) {
$get_recent_repliesall = $mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$row['id'].'" AND replies.is_hidden != "1" ORDER BY replies.created_at DESC');
$get_recent_replies = $mysql->query('SELECT * FROM replies WHERE replies.reply_to_id = "'.$row['id'].'" AND replies.is_hidden != "1" AND replies.pid != "'.$row['pid'].'" AND replies.is_spoiler != "1" ORDER BY replies.created_at DESC');

	if($get_recent_replies->num_rows >=1) {
	$reply = $get_recent_replies->fetch_assoc();
	if($reply['is_spoiler'] == '1' || $reply['is_hidden'] == '1' ) { }
	else {
	$reply_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$reply['pid'].'" LIMIT 1')->fetch_assoc();
    $reply_user_mii = getMii($reply_user, $reply['feeling_id']);

	print '<div id="recent-reply-'.$reply['id'].'" class="recent-reply">
  <a href="/users/'.htmlspecialchars($reply_user['user_id']).'" class="user-icon-container scroll-focus'.($reply_user_mii['official'] ? ' official-user' : null).'" data-pjax="#body">
    <img src="'.$reply_user_mii['output'].'" class="user-icon">
  </a>

  <div class="recent-reply-body-content">
    <div class="recent-reply-body">
      <header>
        <span class="user-name">'.htmlspecialchars($reply_user['screen_name']).'</span>
        <span class="timestamp">'.humanTiming(strtotime($reply['created_at'])).'</span>
      </header>

      <div class="recent-reply-content">

        <p class="recent-reply-content-text">'.preg_replace("/\r|\n/"," ",(mb_strlen($reply['body']) >= 204 ? mb_substr($reply['body'], 0, 200).'...' : $reply['body'])).'</p>

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
	}
		 if($is_activity) {
		 print '</div></div></div>'; }
	  }
} }
print '</div>';
}
	}

function postForm($type, $community, $user) {
global $act_feed;
global $pagetitle;
global $grp_config_allow_allimages;
$can_image = (!$grp_config_allow_allimages ? $user['official_user'] == '1' || $user['privilege'] >= 1 || $user['image_perm'] == '1' : true);
print '<div id="add-'.($type == 'replies' ? 'reply' : 'post').'-page" class="add-post-page'.($can_image ? ' official-user-post' : '').' none" data-modal-types="add-entry add-'.($type == 'replies' ? 'reply' : 'post').' require-body preview-body" data-is-template="1">
  <header class="add-post-page-header">
    <h1 class="page-title">'.($type == 'replies' ? 'Comment on '.$pagetitle : 'Post to '.htmlspecialchars($community['name'])).'</h1>
  </header>
  <form method="post" enctype="multipart/form-data" action="/posts'.($type == 'replies' ? '/'.$community['id'].'/replies' : null).'" class="'.($type == 'replies' ? 'test-reply-form' : 'posts-form').'">
  ';
if($type != 'replies') {
print '
  <input type="hidden" name="community_id" value="'.$community['community_id'].'">
';
}
print '
    <div '.($can_image && $type == 'posts' ? 'style="position: absolute; left: 200px; top: 100px;" ' : null).'class="add-post-page-content">
';
	print '<div class="feeling-selector expression">
  <img src="'.getMii($user, 0)['output'].'" class="icon">
  <ul class="buttons"><li class="checked"><input type="radio" name="feeling_id" value="0" class="feeling-button-normal" data-mii-face-url="'.getMii($user, 0)['output'].'" checked="" data-sound="SE_WAVE_MII_FACE_00"></li><li><input type="radio" name="feeling_id" value="1" class="feeling-button-happy" data-mii-face-url="'.getMii($user, 1)['output'].'" data-sound="SE_WAVE_MII_FACE_01"></li><li><input type="radio" name="feeling_id" value="2" class="feeling-button-like" data-mii-face-url="'.getMii($user, 2)['output'].'" data-sound="SE_WAVE_MII_FACE_02"></li><li><input type="radio" name="feeling_id" value="3" class="feeling-button-surprised" data-mii-face-url="'.getMii($user, 3)['output'].'" data-sound="SE_WAVE_MII_FACE_03"></li><li><input type="radio" name="feeling_id" value="4" class="feeling-button-frustrated" data-mii-face-url="'.getMii($user, 4)['output'].'" data-sound="SE_WAVE_MII_FACE_04"></li><li><input type="radio" name="feeling_id" value="5" class="feeling-button-puzzled" data-mii-face-url="'.getMii($user, 5)['output'].'" data-sound="SE_WAVE_MII_FACE_05"></li>  </ul>
</div>';

print '

      <div class="textarea-container textarea-with-menu active-text">
        <menu class="textarea-menu">
          <li><label class="textarea-menu-text  checked">
              <input type="radio" name="_post_type" value="body" checked="" data-sound="">
          </label></li>
          
            <li class="test-painting-tab"><label class="textarea-menu-memo">
                <input type="radio" name="_post_type" value="painting" data-sound="">
            </label></li>
          
        </menu>
        <textarea type="text" name="body" class="textarea-text" value="" maxlength="1000" placeholder="'.($type == 'replies' ? 'Add a comment here.' : (!empty($act_feed) && $act_feed ? 'Write a post here to people who are following you.' : 'Share your thoughts in a post to this community.')).'"></textarea>
        <div class="textarea-memo trigger" data-sound=""><div class="textarea-memo-preview"></div><input type="hidden" name="painting"></div>
      </div>
	  ';
	 if($can_image) {
    if($type == 'posts') {
	 print '<input type="text" class="textarea-line url-form" name="url" placeholder="URL" maxlength="255">';	
	}
	 print '<input type="text" class="textarea-line url-form" name="screenshot" placeholder="Screenshot URL" maxlength="255">';
	/*print '
	  <input type="hidden" name="screenshot" id="screenshot">
<input type="file" onchange=\'var file = document.querySelectorAll("input[type=file]")[1].files[1]; var reader = new FileReader(); reader.addEventListener("load", function () { document.querySelectorAll("input[id=screenshot]")[1].value = reader.result.split(",")[1]; }, false); if(file) { reader.readAsDataURL(file); }\'>
';*/
	 }
print '
      <label class="spoiler-button checkbox-button">
        Spoilers
        <input type="checkbox" name="is_spoiler" value="1">
      </label>
    </div>
      <input type="button" class="olv-modal-close-button fixed-bottom-button left" value="Cancel" data-sound="SE_WAVE_CANCEL">
      <input type="button" class="post-button fixed-bottom-button" value="Post" data-track-label="default" data-track-action="send'.($type == 'replies' ? 'Reply' : 'Post').'" data-track-category="'.($type == 'replies' ? 'reply' : 'post').'" data-post-content-type="text" data-post-with-screenshot="nodata">
  </form>
</div>';

}