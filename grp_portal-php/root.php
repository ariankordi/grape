<?php
require_once '../grplib-php/init.php';

# If user isn't logged in, then 403 them.
if(empty($_SESSION['pid'])) {
if(isset($grp_config_server_type) && $grp_config_server_type == 'prod') {
include_once 'communities.php';
exit();
}
else {
if(isset($_SERVER['HTTP_X_PJAX'])) {
header('Content-Type: application/json; charset=UTF-8');
header("HTTP/1.1 401 Unauthorized");
			print '{"success":0,"errors":[{"message":"You have been logged out.\nPlease log back in.","error_code":1510110}],"code":"401"}';
			exit ("\n");
}
else {
header('Content-Type: text/plain; charset=UTF-8');
header("HTTP/1.1 403 Forbidden");
exit("403 Forbidden\n");
}
}
}
if(!isset($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
$pagetitle = 'Activity Feed';
require_once 'lib/htm.php';
printHeader(false);
printMenu();

$act_feed_loading = '
<header id="header">
  
  <h1 id="page-title" class="">Activity Feed</h1>

</header>

<div class="body-content post-list js-post-list" id="activity-feed">
  <div class="no-content-window content-loading-window">
    <div class="window">
      <p>Loading activity feed...</p>
    </div>
  </div>
  <div class="no-content-window content-load-error-window none">
    <div class="window">
      <p>The activity feed could not be loaded. Check your Internet connection, wait a moment, and then try reloading.</p>
      <div class="window-bottom-buttons single-button">
        <a href="/" class="button" data-pjax="#body" data-pjax-replace="1">Reload</a>
      </div>
    </div>
  </div>
</div>';

# Requesting "loading activity feed" page.
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && isset($_SERVER['HTTP_X_PJAX'])) {
print $GLOBALS['div_body_head'];
print $act_feed_loading;
print $GLOBALS['div_body_head_end'];
}
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !isset($_SERVER['HTTP_X_PJAX'])) {
print $GLOBALS['div_body_head'];
print $act_feed_loading;
print $GLOBALS['div_body_head_end']; }
}
# User is trying to load the activity feed.
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !isset($_SERVER['HTTP_X_PJAX'])) {
print '<header id="header">
<h1 id="page-title">'.$pagetitle.'</h1>';


$sql_feed_me = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_feed_me = mysqli_query($mysql, $sql_feed_me);
$row_feed_me = mysqli_fetch_assoc($result_feed_me); 

if(isset($_GET['offset']) && is_numeric($_GET['offset'])) {
$sql_feed_my_following = 'select a.*, bm.recent_created_at from (select pid, max(created_at) as recent_created_at from posts group by pid) bm inner join relationships a on bm.pid = a.target WHERE a.source = "'.$_SESSION['pid'].'" ORDER BY recent_created_at DESC LIMIT 50 OFFSET '.mysqli_real_escape_string($mysql, $_GET['offset']).'';
$result_feed_my_following = mysqli_query($mysql, $sql_feed_my_following); } else {
$sql_feed_my_following = 'select a.*, bm.recent_created_at from (select pid, max(created_at) as recent_created_at from posts group by pid) bm inner join relationships a on bm.pid = a.target WHERE a.source = "'.$_SESSION['pid'].'" ORDER BY recent_created_at DESC LIMIT 50';
$result_feed_my_following = mysqli_query($mysql, $sql_feed_my_following);
}

$sql_feed_my_following2 = 'SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.is_me2me = "0"';
$result_feed_my_following2 = mysqli_query($mysql, $sql_feed_my_following2);

if(!isset($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
# Activity Feed post button + form
print '  <a id="header-post-button" class="header-button" href="#" data-modal-open="#add-post-page">Post</a>';
print '<div id="add-post-page" class="add-post-page ';

$row_my_poster2 = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_my_poster2 = mysqli_query($mysql, $row_my_poster2);
$row_my_poster2 = mysqli_fetch_assoc($result_my_poster2); 

if(strval($row_my_poster2['image_perm']) >= 1) {
	print 'official-user-post ';
  }

print 'none " data-modal-types="add-entry add-post require-body preview-body" data-is-template="1">
<header class="add-post-page-header">
';
print '<h1 class="page-title">Post to Activity Feed</h1>
	</header>
	'; 
	print '<form method="post" action="/posts" id="posts-form">
	    <input type="hidden" name="community_id" value="420">';
    print '<div ';
		 if(isset($_SESSION['pid'])) {
	 if(strval($row_my_poster2['image_perm']) >= 1) {
	  print 'style="position: absolute; left: 200px; top: 100px;" ';
	 }
	 }
	 print 'class="add-post-page-content">
	';
    if(isset($_SESSION['signed_in'])) {
	if($row_my_poster2['mii_hash']) {
	print '<div class="feeling-selector expression">
  <img src="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($row_my_poster2['mii_hash']) . '_normal_face.png" class="icon">
  <ul class="buttons"><li class="checked"><input type="radio" name="feeling_id" value="0" class="feeling-button-normal" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($_SESSION['mii_hash']) . '_normal_face.png" checked="" data-sound="SE_WAVE_MII_FACE_00"></li><li><input type="radio" name="feeling_id" value="1" class="feeling-button-happy" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($_SESSION['mii_hash']) . '_happy_face.png" data-sound="SE_WAVE_MII_FACE_01"></li><li><input type="radio" name="feeling_id" value="2" class="feeling-button-like" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($_SESSION['mii_hash']) . '_like_face.png" data-sound="SE_WAVE_MII_FACE_02"></li><li><input type="radio" name="feeling_id" value="3" class="feeling-button-surprised" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($_SESSION['mii_hash']) . '_surprised_face.png" data-sound="SE_WAVE_MII_FACE_03"></li><li><input type="radio" name="feeling_id" value="4" class="feeling-button-frustrated" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($_SESSION['mii_hash']) . '_frustrated_face.png" data-sound="SE_WAVE_MII_FACE_04"></li><li><input type="radio" name="feeling_id" value="5" class="feeling-button-puzzled" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($_SESSION['mii_hash']) . '_puzzled_face.png" data-sound="SE_WAVE_MII_FACE_05"></li>  </ul>
</div>';
	}
	if(isset($row_my_poster2['user_face'])) {
	if($row_my_poster2['user_face']) {	
	print '<div class="feeling-selector expression">
  <img src="' . htmlspecialchars($row_my_poster2['user_face']) . '" class="icon">
  
</div>';
	}
   }
  }
	print '<div class="textarea-container textarea-with-menu active-text">
        
          <menu class="textarea-menu">
            <li><label class="textarea-menu-text checked">
                <input type="radio" name="_post_type" value="body" checked="" data-sound="">
            </label></li>
            <li><label class="textarea-menu-memo">
              <input type="radio" name="_post_type" value="painting" data-sound="">
            </label></li>
          </menu>
        
        <textarea name="body" class="textarea-text" value="" maxlength="1000" placeholder="Write a post here to people who are following you."></textarea>
        <div class="textarea-memo trigger" data-sound=""><div class="textarea-memo-preview"></div><input type="hidden" name="painting"></div>
      </div>';
	 if(isset($_SESSION['pid'])) {
	 if(strval($row_my_poster2['image_perm']) >= 1) {
	 print '<input type="text" class="textarea-line url-form" name="url" placeholder="URL" maxlength="255">';
	 }
	 if(strval($row_my_poster2['image_perm']) >= 1) {
	 print '<input type="text" class="textarea-line url-form" name="screenshot" placeholder="Screenshot URL" maxlength="255">';
	 }
	}
	
	 
	 print '<input type="button" class="olv-modal-close-button fixed-bottom-button left" value="Cancel" data-sound="SE_WAVE_CANCEL">
	 <input type="submit" class="post-button fixed-bottom-button" value="Post" data-track-category="post" data-track-action="sendPost" data-track-label="default" data-community-id="6" data-title-id="1" data-post-content-type="text">
</form>
<label class="spoiler-button checkbox-button">
        Spoilers
        <input type="checkbox" name="is_spoiler" value="1">
      </label>';
print '

</div>

</header>';
# Put my menu search here when implemented properly without collisions!

# print '<span id="my-menu-search" class="scroll">Search Users<input name="query" class="scroll-focus user-search-query" minlength="1" maxlength="32" inputform="monospace" guidestring="Enter the ID or screen name of
#the user you want to find." data-pjax="#body"></span>';

if(mysqli_num_rows($result_feed_my_following2) == 0) {
$has_the_act_tutorial = ' id="activity-feed-tutorial"'; 

print '
  <div class="tutorial-window" id="activity-feed-tutorial">
  <p class="tutorial-text">In your activity feed, you can view posts from your friends and from people you&#39;re following. To get started, why not follow some people whose posts interest you? You can also search for friends using Search Users in the upper right.<br />
</p>
    <h3>Latest Updates from Verified Users</h3>';

$sql_act_getspecialuser = 'SELECT * FROM people WHERE people.official_user = "1" ORDER BY people.pid DESC LIMIT 1';
$result_act_getspecialuser = mysqli_query($mysql, $sql_act_getspecialuser);

print '
	
    <ul class="list-content-with-icon-and-text arrow-list">';
while($row_user_to_view = mysqli_fetch_assoc($result_act_getspecialuser)) {
	include 'lib/userlist-li-template.php';
}
print '    </ul>
  </div>';
}
else {
$has_the_act_tutorial = ' id="activity-feed"'; }
}
if(!empty($_GET['offset'])) { $my_new_offset1 = 50 + $_GET['offset']; }
print '<div class="body-content js-post-list post-list" id="activity-feed" data-next-page-url="'.(mysqli_num_rows($result_feed_my_following) > 60 ? '?offset='.$my_new_offset1.'' : '').'">';


while($row_feed_my_following = mysqli_fetch_assoc($result_feed_my_following)) {
$sql_act_followed_people = 'SELECT * FROM people WHERE people.pid = "' . $row_feed_my_following['target'] . '"';
$result_act_followed_people = mysqli_query($mysql, $sql_act_followed_people);
$row_act_followed_people = mysqli_fetch_assoc($result_act_followed_people);

$sql_act_people_posts1 = 'SELECT * FROM posts WHERE posts.pid = "'.$row_act_followed_people['pid'].'" AND posts.is_hidden != "1" ORDER BY posts.created_at DESC LIMIT 1';
$result_act_people_posts1 = mysqli_query($mysql, $sql_act_people_posts1);

$sql_act_people_posts = 'SELECT * FROM posts WHERE posts.pid = "'.$row_act_followed_people['pid'].'" AND posts.is_hidden != "1" ORDER BY posts.created_at DESC LIMIT 1';
$result_act_people_posts = mysqli_query($mysql, $sql_act_people_posts);
$row_act_people_posts = mysqli_fetch_assoc($result_act_people_posts);

$sql_act_people_posts_replies = 'SELECT * FROM replies WHERE replies.reply_to_id = "'.$row_act_people_posts['id'].'" AND replies.is_hidden != "1"';
$result_act_people_posts_replies = mysqli_query($mysql, $sql_act_people_posts_replies);
$sql_act_people_posts_empathies = 'SELECT * FROM empathies WHERE empathies.id = "'.$row_act_people_posts['id'].'"';
$result_act_people_posts_empathies = mysqli_query($mysql, $sql_act_people_posts_empathies);

if(mysqli_num_rows($result_act_people_posts) == 0) {
print null;
}

else {
$row_temp_current_post = $row_act_people_posts;
$result_temp_current_post_replies = $result_act_people_posts_replies;
$result_temp_current_post_empathies = $result_act_people_posts_empathies;
$row_temp_current_post_user = $row_act_followed_people;
$is_activity_feed_post = 1;
include 'lib/userpage-post-template.php'; }

}

if(!isset($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
# If no posts are shown

$sql_feed_search_my_posts = 'SELECT * FROM posts WHERE posts.pid = "'.$_SESSION['pid'].'" AND posts.is_hidden = "0" LIMIT 1';
$result_feed_search_my_posts = mysqli_query($mysql, $sql_feed_search_my_posts);

if(mysqli_num_rows($result_feed_my_following2) == 0 && mysqli_num_rows($result_feed_search_my_posts) == 0) {
print '
    <div class="tutorial-window no-content js-no-content" id="activity-feed-tutorial">
      <p>There are no posts to display.</p>
    </div>
';
} }
# End body-content js-post-list
print '</div>'; 
}
(!isset($_SERVER['HTTP_X_AUTOPAGERIZE']) && !isset($_SERVER['HTTP_X_PJAX']) && !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? printFooter() : '');
