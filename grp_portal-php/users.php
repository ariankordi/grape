<?php
require_once '../grplib-php/init.php';

if(isset($_GET['mode'])) {
if($_GET['mode'] == 'posts') {
# Display posts for user.
# Display standard user page.
$sql_userpage_user = 'SELECT * FROM people WHERE people.user_id = "' . mysqli_real_escape_string($mysql, $_GET['user_id']) . '"';
$result_userpage_user = mysqli_query($mysql, $sql_userpage_user);
$row_userpage_user = mysqli_fetch_assoc($result_userpage_user);

if(isset($_SESSION['signed_in'])) {
$sql_userpage_me = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_userpage_me = mysqli_query($mysql, $sql_userpage_me);
$row_userpage_me = mysqli_fetch_assoc($result_userpage_me); }

// Who posesses the post?
if($row_userpage_user['pid']) {
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true) {
if($_SESSION['pid'] == $row_userpage_user['pid']) {
$pagetitle = 'User Page';
}
else {
$pagetitle = htmlspecialchars($row_userpage_user['screen_name']) . "'s Profile"; 
}    } 
else {
$pagetitle = htmlspecialchars($row_userpage_user['screen_name']) . "'s Profile"; }
}
else {
$pagetitle = 'Error'; }
require_once 'lib/htm.php';
printHeader(false);
printMenu();
// DB error.
if(!$result_userpage_user)
{
http_response_code(500);
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="500">';
$no_content_message = ( 'Server error.' );
include 'lib/no-content-window.php';
}
else
{
	// User wasn't found.
    if(mysqli_num_rows($result_userpage_user) == 0)
    {
(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The user could not be found.' );
include 'lib/no-content-window.php';
    }
    else
    {
$sql_userpage_user_profile = 'SELECT * FROM profiles WHERE profiles.pid = "' . $row_userpage_user['pid'] . '"';
$result_userpage_user_profile = mysqli_query($mysql, $sql_userpage_user_profile);
$row_userpage_user_profile = mysqli_fetch_assoc($result_userpage_user_profile); 
  
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid'] && mysqli_num_rows($result_userpage_user_profile) == 0) {
mysqli_query($mysql, "INSERT INTO
                    profiles(pid, platform_id)
                VALUES('" . mysqli_real_escape_string($mysql, $_SESSION['pid']) . "',
                       '" . $row_userpage_user['platform_id'] . "')");
     }
	if(isset($_GET['offset']) && is_numeric($_GET['offset']) && strlen($_GET['offset']) >= 1) {
		
		
$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "' . $row_userpage_user['pid'] . '" AND posts.is_hidden != "1"';
$result_userpage_user_posts = mysqli_query($mysql, $sql_userpage_user_posts);

$sql_userpage_user_empathies = 'SELECT * FROM empathies WHERE empathies.pid = "' . $row_userpage_user['pid'] . '"';
$result_userpage_user_empathies = mysqli_query($mysql, $sql_userpage_user_empathies);

$sql_userpage_user_posts_view = 'SELECT * FROM posts WHERE posts.pid = "' . $row_userpage_user['pid'] . '" ORDER BY posts.created_at DESC LIMIT 50 OFFSET '.mysqli_real_escape_string($mysql, $_GET['offset']).'';
$result_userpage_user_posts_view = mysqli_query($mysql, $sql_userpage_user_posts_view);	

if(mysqli_num_rows($result_userpage_user_posts_view) > 49) {
$what_is_my_new_offset1 = $_GET['offset'] + 50;
$what_is_my_new_offset = '/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts?offset='.$what_is_my_new_offset1.'';
}
else {
$what_is_my_new_offset= ''; }

print '  <menu class="user-menu-activity tab-header">
    <li id="tab-header-user-posts" class="tab-button selected"
        ><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"
        ><span class="label">Posts</span><span class="number">'.mysqli_num_rows($result_userpage_user_posts).'</span></a></li>
    <li id="tab-header-user-empathies" class="tab-button"
        ><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/empathies" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"
        ><span class="label">Yeahs</span><span class="number">'.mysqli_num_rows($result_userpage_user_empathies).'</span></a></li>
  </menu>
<div class="user-page-content js-post-list post-list" data-next-page-url="'.$what_is_my_new_offset.'">
  <div id="user-page-no-content" class="none"></div>';

		
# display in offset here !
while($row_userpage_user_posts_view = mysqli_fetch_assoc($result_userpage_user_posts_view)) {

$sql_userpage_user_posts_view_user = 'SELECT * FROM people WHERE people.pid = "' . $row_userpage_user_posts_view['pid'] . '"';
$result_userpage_user_posts_view_user = mysqli_query($mysql, $sql_userpage_user_posts_view_user);
$row_userpage_user_posts_view_user = mysqli_fetch_assoc($result_userpage_user_posts_view_user); 

$sql_userpage_user_posts_view_replies = 'SELECT * FROM replies WHERE replies.reply_to_id = "' . $row_userpage_user_posts_view['id'] . '" AND replies.is_hidden != "1"';
$result_userpage_user_posts_view_replies = mysqli_query($mysql, $sql_userpage_user_posts_view_replies);

$sql_userpage_user_posts_view_empathies = 'SELECT * FROM empathies WHERE empathies.id = "' . $row_userpage_user_posts_view['id'] . '"';
$result_userpage_user_posts_view_empathies = mysqli_query($mysql, $sql_userpage_user_posts_view_empathies);

$row_temp_current_post = $row_userpage_user_posts_view;
$row_temp_current_post_user = $row_userpage_user_posts_view_user;
$result_temp_current_post_replies = $result_userpage_user_posts_view_replies;
$result_temp_current_post_empathies = $result_userpage_user_posts_view_empathies;

include 'lib/userpage-post-template.php';

}
# End of js-post-list
print '</div>';


exit();
}
	

if(isset($_SERVER['HTTP_X_PJAX_CONTAINER']) && $_SERVER['HTTP_X_PJAX_CONTAINER'] == '.tab-body') {

$sql_userpage_user_posts_view = 'SELECT * FROM posts WHERE pid = "'.$row_userpage_user['pid'].'" AND hidden_resp = 0 OR hidden_resp IS NULL AND pid = "'.$row_userpage_user['pid'].'" ORDER BY posts.created_at DESC LIMIT 50';
$result_userpage_user_posts_view = mysqli_query($mysql, $sql_userpage_user_posts_view);
$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "' . $row_userpage_user['pid'] . '" AND posts.is_hidden != "1"';
$result_userpage_user_posts = mysqli_query($mysql, $sql_userpage_user_posts);

$sql_userpage_user_empathies = 'SELECT * FROM empathies WHERE empathies.pid = "' . $row_userpage_user['pid'] . '"';
$result_userpage_user_empathies = mysqli_query($mysql, $sql_userpage_user_empathies);

if(mysqli_num_rows($result_userpage_user_posts) > 49) {
$do_i_have_my_offset = '/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts?offset=50';
}
else {
$do_i_have_my_offset = ''; }

	print '<title>'.$pagetitle.'</title>';
print '  <menu class="user-menu-activity tab-header">
    <li id="tab-header-user-posts" class="tab-button selected"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Posts</span><span class="number">'.mysqli_num_rows($result_userpage_user_posts).'</span></a></li>
    <li id="tab-header-user-empathies" class="tab-button"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/empathies" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Yeahs</span><span class="number">'.mysqli_num_rows($result_userpage_user_empathies).'</span></a></li>
  </menu>
<div class="user-page-content js-post-list post-list" data-next-page-url="'.$do_i_have_my_offset.'">';
if(mysqli_num_rows($result_userpage_user_posts_view) == 0) {
print '  <div id="user-page-no-content" class="no-content-window js-no-content"><div class="window">
    <p>No Miiverse posts have been made yet.</p>
</div></div>'; }
while($row_userpage_user_posts_view = mysqli_fetch_assoc($result_userpage_user_posts_view)) {

$sql_userpage_user_posts_view_user = 'SELECT * FROM people WHERE people.pid = "' . $row_userpage_user_posts_view['pid'] . '"';
$result_userpage_user_posts_view_user = mysqli_query($mysql, $sql_userpage_user_posts_view_user);
$row_userpage_user_posts_view_user = mysqli_fetch_assoc($result_userpage_user_posts_view_user); 

$sql_userpage_user_posts_view_replies = 'SELECT * FROM replies WHERE replies.reply_to_id = "' . $row_userpage_user_posts_view['id'] . '" AND replies.is_hidden != "1"';
$result_userpage_user_posts_view_replies = mysqli_query($mysql, $sql_userpage_user_posts_view_replies);

$sql_userpage_user_posts_view_empathies = 'SELECT * FROM empathies WHERE empathies.id = "' . $row_userpage_user_posts_view['id'] . '"';
$result_userpage_user_posts_view_empathies = mysqli_query($mysql, $sql_userpage_user_posts_view_empathies);

$row_temp_current_post = $row_userpage_user_posts_view;
$row_temp_current_post_user = $row_userpage_user_posts_view_user;
$result_temp_current_post_replies = $result_userpage_user_posts_view_replies;
$result_temp_current_post_empathies = $result_userpage_user_posts_view_empathies;

include 'lib/userpage-post-template.php';

}
# End of js-post-list
print '</div>';
}
else {

    print $GLOBALS['div_body_head'];
	print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';

if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
$is_user_me_page_visitor = ' is-visitor'; }
else {
$is_user_me_page_visitor = ''; }

if($row_userpage_user['official_user'] == 1) {
$is_user_info_official_user = ' official-user'; }
else {
$is_user_info_official_user = ''; }

$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "' . $row_userpage_user['pid'] . '" AND posts.is_hidden != "1"';
$result_userpage_user_posts = mysqli_query($mysql, $sql_userpage_user_posts);

$sql_userpage_user_empathies = 'SELECT * FROM empathies WHERE empathies.pid = "' . $row_userpage_user['pid'] . '"';
$result_userpage_user_empathies = mysqli_query($mysql, $sql_userpage_user_empathies);

$sql_userpage_user_friends = 'SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" ORDER BY friend_relationships.relationship_id LIMIT 100';
$result_userpage_user_friends = mysqli_query($mysql, $sql_userpage_user_friends);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = mysqli_query($mysql, $sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_following = mysqli_query($mysql, $sql_userpage_user_following);

#Begin body-content user-page
print '<div class="body-content user-page'.$is_user_me_page_visitor.'">
';
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
print '<a id="header-mymenu-button" href="/my_menu" data-pjax="#body">User Menu</a>
'; }


		if($row_userpage_user['mii_hash']) {
$user_page_info_mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_userpage_user['mii_hash'] . '_normal_face.png'; }
else {
if($row_userpage_user['user_face']) {
$user_page_info_mii_face_output = htmlspecialchars($row_userpage_user['user_face']); }
 else {
$user_page_info_mii_face_output = '/img/mii/img_unknown_MiiIcon.png'; }
}

print '<div class="user-info info-content'.$is_user_info_official_user.'">'."\n".'';

if(empty($row_userpage_user_profile['favorite_screenshot']) && strlen($row_userpage_user_profile['favorite_screenshot']) < 5) {
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user_profile['pid']) {
print '<div class="user-profile-memo-container no-profile-memo">Your favorite post can be displayed here.</div>
'; } else {
print '<div class="user-profile-memo-container no-profile-memo"></div>'; }
}
else {
$result_posts_getfavoritepost = mysqli_query($mysql, 'SELECT * FROM posts WHERE posts.id = "'.mysqli_real_escape_string($mysql, $row_userpage_user_profile['favorite_screenshot']).'"');
print '<a href="/posts/'.htmlspecialchars($row_userpage_user_profile['favorite_screenshot']).'" data-pjax="#body" class="user-profile-memo-container">
    <img src="'.htmlspecialchars(mysqli_fetch_assoc($result_posts_getfavoritepost)['screenshot']).'" class="user-profile-memo">
  </a>'; }

print '    <span class="icon-container'.$is_user_info_official_user.'"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'"><img src="'.$user_page_info_mii_face_output.'" class="icon"></a></span>
';
if($row_userpage_user['official_user'] == 1) {
print '<p class="user-organization">'.htmlspecialchars($row_userpage_user['organization']).'</p>'; }
print '  <p class="title">
    <span class="nick-name">'.htmlspecialchars($row_userpage_user['screen_name']).'</span>
    <span class="id-name">'.htmlspecialchars($row_userpage_user['user_id']).'</span>
  </p>
  
  ';

if($row_userpage_user['official_user'] == 1) {
$is_identified_user_value = ' data-is-identified="1"'; }
else {
$is_identified_user_value = ''; }
 
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && 2 >= $row_userpage_user['status']) {
$if_user_follow_can = ''; }
else {
$if_user_follow_can = ' disabled'; }	
 
print '<a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'" data-pjax="#body" class="button profile-back-button">To Top</a>';
# End user-info info-content
print '</div>';

print '<menu class="user-menu tab-header">
  <li class="test-user-posts-count tab-button-profile selected"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Posts</span><span class="number">'.mysqli_num_rows($result_userpage_user_posts).'</span></a></li>
  <li class="test-user-friends-count tab-button-activity"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/friends" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Friends</span><span class="number">'.mysqli_num_rows($result_userpage_user_friends).' / 100</span></a></li>
  <li class="test-user-followings-count tab-button-activity"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/following" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Following</span><span class="number"><span class="js-following-count">'.mysqli_num_rows($result_userpage_user_following).'</span> / 1000</span></a></li>
  <li class="test-user-followers-count tab-button-relationship"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/followers" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Followers</span><span class="number">'.mysqli_num_rows($result_userpage_user_followers).'</span></a></li>
</menu>';

$sql_userpage_user_posts_view = 'SELECT * FROM posts WHERE pid = "'.$row_userpage_user['pid'].'" AND hidden_resp = 0 OR hidden_resp IS NULL AND pid = "'.$row_userpage_user['pid'].'" ORDER BY posts.created_at DESC LIMIT 50';
$result_userpage_user_posts_view = mysqli_query($mysql, $sql_userpage_user_posts_view);

if(mysqli_num_rows($result_userpage_user_posts) > 49) {
$do_i_have_my_offset = '/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts?offset=50';
}
else {
$do_i_have_my_offset = ''; }

print '<div class="tab-body">
  <menu class="user-menu-activity tab-header">
    <li id="tab-header-user-posts" class="tab-button selected"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Posts</span><span class="number">'.mysqli_num_rows($result_userpage_user_posts).'</span></a></li>
    <li id="tab-header-user-empathies" class="tab-button"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/empathies" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Yeahs</span><span class="number">'.mysqli_num_rows($result_userpage_user_empathies).'</span></a></li>
  </menu>
<div class="user-page-content js-post-list post-list" data-next-page-url="'.$do_i_have_my_offset.'">';
if(mysqli_num_rows($result_userpage_user_posts_view) == 0) {
print '  <div id="user-page-no-content" class="no-content-window js-no-content"><div class="window">
    <p>No Miiverse posts have been made yet.</p>
</div></div>'; }
while($row_userpage_user_posts_view = mysqli_fetch_assoc($result_userpage_user_posts_view)) {

$sql_userpage_user_posts_view_user = 'SELECT * FROM people WHERE people.pid = "' . $row_userpage_user_posts_view['pid'] . '"';
$result_userpage_user_posts_view_user = mysqli_query($mysql, $sql_userpage_user_posts_view_user);
$row_userpage_user_posts_view_user = mysqli_fetch_assoc($result_userpage_user_posts_view_user); 

$sql_userpage_user_posts_view_replies = 'SELECT * FROM replies WHERE replies.reply_to_id = "' . $row_userpage_user_posts_view['id'] . '" AND replies.is_hidden != "1"';
$result_userpage_user_posts_view_replies = mysqli_query($mysql, $sql_userpage_user_posts_view_replies);

$sql_userpage_user_posts_view_empathies = 'SELECT * FROM empathies WHERE empathies.id = "' . $row_userpage_user_posts_view['id'] . '"';
$result_userpage_user_posts_view_empathies = mysqli_query($mysql, $sql_userpage_user_posts_view_empathies);

$row_temp_current_post = $row_userpage_user_posts_view;
$row_temp_current_post_user = $row_userpage_user_posts_view_user;
$result_temp_current_post_replies = $result_userpage_user_posts_view_replies;
$result_temp_current_post_empathies = $result_userpage_user_posts_view_empathies;

include 'lib/userpage-post-template.php';

}
# End of js-post-list
print '</div>

  </div>';



include 'lib/user-page-footer.php';
# End body-content user-page
print '</div>';
	print $GLOBALS['div_body_head_end'];	
	printFooter();
	}
	}
	}
  }
if($_GET['mode'] == 'empathies') {
# Display user's yeahed posts.
# Display standard user page.
$sql_userpage_user = 'SELECT * FROM people WHERE people.user_id = "' . mysqli_real_escape_string($mysql, $_GET['user_id']) . '"';
$result_userpage_user = mysqli_query($mysql, $sql_userpage_user);
$row_userpage_user = mysqli_fetch_assoc($result_userpage_user);

if(isset($_SESSION['signed_in'])) {
$sql_userpage_me = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_userpage_me = mysqli_query($mysql, $sql_userpage_me);
$row_userpage_me = mysqli_fetch_assoc($result_userpage_me); } 

// Who posesses the post?
if($row_userpage_user['pid']) {
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true) {
if($_SESSION['pid'] == $row_userpage_user['pid']) {
$pagetitle = 'User Page';
}
else {
$pagetitle = htmlspecialchars($row_userpage_user['screen_name']) . "'s Profile"; 
}    } 
else {
$pagetitle = htmlspecialchars($row_userpage_user['screen_name']) . "'s Profile"; }
}
else {
$pagetitle = 'Error'; }
require_once 'lib/htm.php';
printHeader(false);
printMenu();
// DB error.
if(!$result_userpage_user)
{
http_response_code(500);
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="500">';
$no_content_message = ( 'Server error.' );
include 'lib/no-content-window.php';
}
else
{
	// User wasn't found.
    if(mysqli_num_rows($result_userpage_user) == 0)
    {
(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The user could not be found.' );
include 'lib/no-content-window.php';
    }
    else
    {
		
			if(isset($_GET['offset']) && is_numeric($_GET['offset']) && strlen($_GET['offset']) >= 1) {
		
		
$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "' . $row_userpage_user['pid'] . '" AND posts.is_hidden != "1"';
$result_userpage_user_posts = mysqli_query($mysql, $sql_userpage_user_posts);

$sql_userpage_user_empathies = 'SELECT * FROM empathies WHERE empathies.pid = "' . $row_userpage_user['pid'] . '" ORDER BY empathies.created_at DESC';
$result_userpage_user_empathies = mysqli_query($mysql, $sql_userpage_user_empathies);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = mysqli_query($mysql, $sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_following = mysqli_query($mysql, $sql_userpage_user_following);

$sql_userpage_user_empathies_view = 'SELECT * FROM empathies WHERE empathies.pid = "' . $row_userpage_user['pid'] . '" ORDER BY empathies.created_at DESC LIMIT 20 OFFSET '.mysqli_real_escape_string($mysql, $_GET['offset']).'';
$result_userpage_user_empathies_view = mysqli_query($mysql, $sql_userpage_user_empathies_view);

if(mysqli_num_rows($result_userpage_user_empathies_view) > 19) {
$what_is_my_new_offset1 = $_GET['offset'] + 20;
$what_is_my_new_offset = '/users/'.htmlspecialchars($row_userpage_user['user_id']).'/empathies?offset='.$what_is_my_new_offset1.'';
}
else {
$what_is_my_new_offset= ''; }

print '  <menu class="user-menu-activity tab-header">
    <li id="tab-header-user-posts" class="tab-button"
        ><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"
        ><span class="label">Posts</span><span class="number">'.mysqli_num_rows($result_userpage_user_posts).'</span></a></li>
    <li id="tab-header-user-empathies" class="tab-button selected"
        ><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/empathies" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"
        ><span class="label">Yeahs</span><span class="number">'.mysqli_num_rows($result_userpage_user_empathies).'</span></a></li>
  </menu>
<div class="user-page-content js-post-list post-list" data-next-page-url="'.$what_is_my_new_offset.'">
  <div id="user-page-no-content" class="none"></div>';

  
# display in offset here !
while($row_userpage_user_empathies_view = mysqli_fetch_assoc($result_userpage_user_empathies_view)) {

$sql_userpage_user_empathies_view_posts = 'SELECT * FROM posts WHERE posts.id = "'.$row_userpage_user_empathies_view['id'].'" AND posts.is_hidden != "1" UNION ALL SELECT * from replies where replies.id = "'.$row_userpage_user_empathies_view['id'].'" AND replies.is_hidden != "1"';
$result_userpage_user_empathies_view_posts = mysqli_query($mysql, $sql_userpage_user_empathies_view_posts); 	
$row_userpage_user_empathies_view_posts = mysqli_fetch_assoc($result_userpage_user_empathies_view_posts); 	

$sql_userpage_user_empathies_view_postsuser = 'SELECT * FROM people where people.pid = "'.$row_userpage_user_empathies_view_posts['pid'].'"';
$result_userpage_user_empathies_view_postsuser = mysqli_query($mysql, $sql_userpage_user_empathies_view_postsuser); 
$row_userpage_user_empathies_view_postsuser = mysqli_fetch_assoc($result_userpage_user_empathies_view_postsuser);

$sql_userpage_user_empathies_view_postsreplies = 'SELECT * FROM replies WHERE replies.reply_to_id = "' . $row_userpage_user_empathies_view_posts['id'] . '" AND replies.is_hidden != "1"';
$result_userpage_user_empathies_view_postsreplies = mysqli_query($mysql, $sql_userpage_user_empathies_view_postsreplies);

$sql_userpage_user_empathies_view_postsempathies = 'SELECT * FROM empathies WHERE empathies.id = "' . $row_userpage_user_empathies_view_posts['id'] . '"';
$result_userpage_user_empathies_view_postsempathies = mysqli_query($mysql, $sql_userpage_user_empathies_view_postsempathies);



$row_temp_current_post = $row_userpage_user_empathies_view_posts;
$row_temp_current_post_user = $row_userpage_user_empathies_view_postsuser;
$result_temp_current_post_replies = $result_userpage_user_empathies_view_postsreplies;
$result_temp_current_post_empathies = $result_userpage_user_empathies_view_postsempathies;

include 'lib/userpage-post-template.php';

}
# End of js-post-list
print '</div>';


exit();
}
	
		
		
if(isset($_SERVER['HTTP_X_PJAX_CONTAINER']) && $_SERVER['HTTP_X_PJAX_CONTAINER'] == '.tab-body') {

$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "' . $row_userpage_user['pid'] . '" AND posts.is_hidden != "1"';
$result_userpage_user_posts = mysqli_query($mysql, $sql_userpage_user_posts);

$sql_userpage_user_empathies = 'SELECT * FROM empathies WHERE empathies.pid = "' . $row_userpage_user['pid'] . '" ORDER BY empathies.created_at DESC';
$result_userpage_user_empathies = mysqli_query($mysql, $sql_userpage_user_empathies);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = mysqli_query($mysql, $sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_following = mysqli_query($mysql, $sql_userpage_user_following);

$sql_userpage_user_empathies = 'SELECT * FROM empathies WHERE empathies.pid = "' . $row_userpage_user['pid'] . '" ORDER BY empathies.created_at DESC';
$result_userpage_user_empathies = mysqli_query($mysql, $sql_userpage_user_empathies);

$sql_userpage_user_empathies_view = 'SELECT * FROM empathies WHERE empathies.pid = "' . $row_userpage_user['pid'] . '" ORDER BY empathies.created_at DESC LIMIT 20';
$result_userpage_user_empathies_view = mysqli_query($mysql, $sql_userpage_user_empathies_view);

if(mysqli_num_rows($result_userpage_user_empathies) > 19) {
$do_i_have_my_offset = '/users/'.htmlspecialchars($row_userpage_user['user_id']).'/empathies?offset=20';
}
else {
$do_i_have_my_offset = ''; }

print '
  <menu class="user-menu-activity tab-header">
    <li id="tab-header-user-posts" class="tab-button"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Posts</span><span class="number">'.mysqli_num_rows($result_userpage_user_posts).'</span></a></li>
    <li id="tab-header-user-empathies" class="tab-button selected"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/empathies" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Yeahs</span><span class="number">'.mysqli_num_rows($result_userpage_user_empathies).'</span></a></li>
  </menu>
<div class="user-page-content js-post-list post-list" data-next-page-url="'.$do_i_have_my_offset.'">';
if(mysqli_num_rows($result_userpage_user_empathies) == 0) {
print '  <div id="user-page-no-content" class="no-content-window js-no-content"><div class="window">
    <p>There are no posts with Yeahs yet.</p>
</div></div>'; }
else {
while($row_userpage_user_empathies_view = mysqli_fetch_assoc($result_userpage_user_empathies_view)) {

$sql_userpage_user_empathies_view_posts = 'SELECT * FROM posts WHERE posts.id = "'.$row_userpage_user_empathies_view['id'].'"  AND posts.is_hidden != "1" UNION ALL SELECT * from replies where replies.id = "'.$row_userpage_user_empathies_view['id'].'"  AND replies.is_hidden != "1"';
$result_userpage_user_empathies_view_posts = mysqli_query($mysql, $sql_userpage_user_empathies_view_posts); 	
$row_userpage_user_empathies_view_posts = mysqli_fetch_assoc($result_userpage_user_empathies_view_posts); 	

if(mysqli_num_rows($result_userpage_user_empathies_view_posts) == 0) {
print null;
}

$sql_userpage_user_empathies_view_postsuser = 'SELECT * FROM people where people.pid = "'.$row_userpage_user_empathies_view_posts['pid'].'"';
$result_userpage_user_empathies_view_postsuser = mysqli_query($mysql, $sql_userpage_user_empathies_view_postsuser); 
$row_userpage_user_empathies_view_postsuser = mysqli_fetch_assoc($result_userpage_user_empathies_view_postsuser);

$sql_userpage_user_empathies_view_postsreplies = 'SELECT * FROM replies WHERE replies.reply_to_id = "' . $row_userpage_user_empathies_view_posts['id'] . '"';
$result_userpage_user_empathies_view_postsreplies = mysqli_query($mysql, $sql_userpage_user_empathies_view_postsreplies);

$sql_userpage_user_empathies_view_postsempathies = 'SELECT * FROM empathies WHERE empathies.id = "' . $row_userpage_user_empathies_view_posts['id'] . '"';
$result_userpage_user_empathies_view_postsempathies = mysqli_query($mysql, $sql_userpage_user_empathies_view_postsempathies);


if(mysqli_num_rows($result_userpage_user_empathies_view_posts) == 0) {
print null;
} else {
$row_temp_current_post = $row_userpage_user_empathies_view_posts;
$row_temp_current_post_user = $row_userpage_user_empathies_view_postsuser;
$result_temp_current_post_replies = $result_userpage_user_empathies_view_postsreplies;
$result_temp_current_post_empathies = $result_userpage_user_empathies_view_postsempathies;

include 'lib/userpage-post-template.php';
}


}	
}
}
else {
$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "' . $row_userpage_user['pid'] . '" AND posts.is_hidden != "1"';
$result_userpage_user_posts = mysqli_query($mysql, $sql_userpage_user_posts);

$sql_userpage_user_empathies = 'SELECT * FROM empathies WHERE empathies.pid = "' . $row_userpage_user['pid'] . '" ORDER BY empathies.created_at DESC';
$result_userpage_user_empathies = mysqli_query($mysql, $sql_userpage_user_empathies);

$sql_userpage_user_friends = 'SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" ORDER BY friend_relationships.relationship_id LIMIT 100';
$result_userpage_user_friends = mysqli_query($mysql, $sql_userpage_user_friends);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = mysqli_query($mysql, $sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_following = mysqli_query($mysql, $sql_userpage_user_following);
	
	

$sql_userpage_user_profile = 'SELECT * FROM profiles WHERE profiles.pid = "' . $row_userpage_user['pid'] . '"';
$result_userpage_user_profile = mysqli_query($mysql, $sql_userpage_user_profile);
$row_userpage_user_profile = mysqli_fetch_assoc($result_userpage_user_profile); 
  
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid'] && mysqli_num_rows($result_userpage_user_profile) == 0) {
mysqli_query($mysql, "INSERT INTO
                    profiles(pid, platform_id)
                VALUES('" . mysqli_real_escape_string($mysql, $_SESSION['pid']) . "',
                       '" . $row_userpage_user['platform_id'] . "')");
     }
else {

    print $GLOBALS['div_body_head'];
	print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';

if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
$is_user_me_page_visitor = ' is-visitor'; }
else {
$is_user_me_page_visitor = ''; }

if($row_userpage_user['official_user'] == 1) {
$is_user_info_official_user = ' official-user'; }
else {
$is_user_info_official_user = ''; }

$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "' . $row_userpage_user['pid'] . '" AND posts.is_hidden != "1"';
$result_userpage_user_posts = mysqli_query($mysql, $sql_userpage_user_posts);

$sql_userpage_user_empathies = 'SELECT * FROM empathies WHERE empathies.pid = "' . $row_userpage_user['pid'] . '" ORDER BY empathies.created_at DESC';
$result_userpage_user_empathies = mysqli_query($mysql, $sql_userpage_user_empathies);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = mysqli_query($mysql, $sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_following = mysqli_query($mysql, $sql_userpage_user_following);



#Begin body-content user-page
print '<div class="body-content user-page'.$is_user_me_page_visitor.'">
';
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
print '<a id="header-mymenu-button" href="/my_menu" data-pjax="#body">User Menu</a>
'; }


		if($row_userpage_user['mii_hash']) {
$user_page_info_mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_userpage_user['mii_hash'] . '_normal_face.png'; }
else {
if($row_userpage_user['user_face']) {
$user_page_info_mii_face_output = htmlspecialchars($row_userpage_user['user_face']); }
 else {
$user_page_info_mii_face_output = '/img/mii/img_unknown_MiiIcon.png'; }
}

print '<div class="user-info info-content'.$is_user_info_official_user.'">'."\n".'';

if(empty($row_userpage_user_profile['favorite_screenshot']) && strlen($row_userpage_user_profile['favorite_screenshot']) < 5) {
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user_profile['pid']) {
print '<div class="user-profile-memo-container no-profile-memo">Your favorite post can be displayed here.</div>
'; } else {
print '<div class="user-profile-memo-container no-profile-memo"></div>'; }
}
else {
$result_posts_getfavoritepost = mysqli_query($mysql, 'SELECT * FROM posts WHERE posts.id = "'.mysqli_real_escape_string($mysql, $row_userpage_user_profile['favorite_screenshot']).'"');
print '<a href="/posts/'.htmlspecialchars($row_userpage_user_profile['favorite_screenshot']).'" data-pjax="#body" class="user-profile-memo-container">
    <img src="'.htmlspecialchars(mysqli_fetch_assoc($result_posts_getfavoritepost)['screenshot']).'" class="user-profile-memo">
  </a>'; }

print '    <span class="icon-container'.$is_user_info_official_user.'"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'"><img src="'.$user_page_info_mii_face_output.'" class="icon"></a></span>
';
if($row_userpage_user['official_user'] == 1) {
print '<p class="user-organization">'.htmlspecialchars($row_userpage_user['organization']).'</p>'; }
print '  <p class="title">
    <span class="nick-name">'.htmlspecialchars($row_userpage_user['screen_name']).'</span>
    <span class="id-name">'.htmlspecialchars($row_userpage_user['user_id']).'</span>
  </p>
  
  ';

if($row_userpage_user['official_user'] == 1) {
$is_identified_user_value = ' data-is-identified="1"'; }
else {
$is_identified_user_value = ''; }
 
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && 2 >= $row_userpage_user['status']) {
$if_user_follow_can = ''; }
else {
$if_user_follow_can = ' disabled'; }	

$sql_userpage_user_empathies_view = 'SELECT * FROM empathies WHERE empathies.pid = "' . $row_userpage_user['pid'] . '" ORDER BY empathies.created_at DESC LIMIT 20';
$result_userpage_user_empathies_view = mysqli_query($mysql, $sql_userpage_user_empathies_view);
 
print '<a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'" data-pjax="#body" class="button profile-back-button">To Top</a>';
# End user-info info-content
print '</div>';

print '<menu class="user-menu tab-header">
  <li class="test-user-posts-count tab-button-profile selected"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Posts</span><span class="number">'.mysqli_num_rows($result_userpage_user_posts).'</span></a></li>
  <li class="test-user-friends-count tab-button-activity"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/friends" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Friends</span><span class="number">'.mysqli_num_rows($result_userpage_user_friends).' / 100</span></a></li>
  <li class="test-user-followings-count tab-button-activity"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/following" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Following</span><span class="number"><span class="js-following-count">'.mysqli_num_rows($result_userpage_user_following).'</span> / 1000</span></a></li>
  <li class="test-user-followers-count tab-button-relationship"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/followers" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Followers</span><span class="number">'.mysqli_num_rows($result_userpage_user_followers).'</span></a></li>
</menu>';

if(mysqli_num_rows($result_userpage_user_empathies) > 19) {
$do_i_have_my_offset = '/users/'.htmlspecialchars($row_userpage_user['user_id']).'/empathies?offset=20';
}
else {
$do_i_have_my_offset = ''; }

print '<div class="tab-body">
  <menu class="user-menu-activity tab-header">
    <li id="tab-header-user-posts" class="tab-button"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Posts</span><span class="number">'.mysqli_num_rows($result_userpage_user_posts).'</span></a></li>
    <li id="tab-header-user-empathies" class="tab-button selected"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/empathies" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Yeahs</span><span class="number">'.mysqli_num_rows($result_userpage_user_empathies).'</span></a></li>
  </menu>
<div class="user-page-content js-post-list post-list" data-next-page-url="'.$do_i_have_my_offset.'">';
if(mysqli_num_rows($result_userpage_user_empathies) == 0) {
print '  <div id="user-page-no-content" class="no-content-window js-no-content"><div class="window">
    <p>There are no posts with Yeahs yet.</p>
</div></div>'; }
else {
while($row_userpage_user_empathies_view = mysqli_fetch_assoc($result_userpage_user_empathies_view)) {

$sql_userpage_user_empathies_view_posts = 'SELECT * FROM posts WHERE posts.id = "'.$row_userpage_user_empathies_view['id'].'"  AND posts.is_hidden != "1" UNION ALL SELECT * from replies where replies.id = "'.$row_userpage_user_empathies_view['id'].'"  AND replies.is_hidden != "1"';
$result_userpage_user_empathies_view_posts = mysqli_query($mysql, $sql_userpage_user_empathies_view_posts); 	
$row_userpage_user_empathies_view_posts = mysqli_fetch_assoc($result_userpage_user_empathies_view_posts); 	

if(mysqli_num_rows($result_userpage_user_empathies_view_posts) == 0) {
print null;
}

$sql_userpage_user_empathies_view_postsuser = 'SELECT * FROM people where people.pid = "'.$row_userpage_user_empathies_view_posts['pid'].'"';
$result_userpage_user_empathies_view_postsuser = mysqli_query($mysql, $sql_userpage_user_empathies_view_postsuser); 
$row_userpage_user_empathies_view_postsuser = mysqli_fetch_assoc($result_userpage_user_empathies_view_postsuser);

$sql_userpage_user_empathies_view_postsreplies = 'SELECT * FROM replies WHERE replies.reply_to_id = "' . $row_userpage_user_empathies_view_posts['id'] . '"';
$result_userpage_user_empathies_view_postsreplies = mysqli_query($mysql, $sql_userpage_user_empathies_view_postsreplies);

$sql_userpage_user_empathies_view_postsempathies = 'SELECT * FROM empathies WHERE empathies.id = "' . $row_userpage_user_empathies_view_posts['id'] . '"';
$result_userpage_user_empathies_view_postsempathies = mysqli_query($mysql, $sql_userpage_user_empathies_view_postsempathies);


if(mysqli_num_rows($result_userpage_user_empathies_view_posts) == 0) {
print null;
} else {
$row_temp_current_post = $row_userpage_user_empathies_view_posts;
$row_temp_current_post_user = $row_userpage_user_empathies_view_postsuser;
$result_temp_current_post_replies = $result_userpage_user_empathies_view_postsreplies;
$result_temp_current_post_empathies = $result_userpage_user_empathies_view_postsempathies;

include 'lib/userpage-post-template.php';
}


}
}
# End of js-post-list
print '</div>

  </div>';



include 'lib/user-page-footer.php';
# End body-content user-page
print '</div>';
	print $GLOBALS['div_body_head_end'];	
	printFooter();
	}
}
	}
  }	
}
if($_GET['mode'] == 'following') {
# Display standard user page.
$sql_userpage_user = 'SELECT * FROM people WHERE people.user_id = "' . mysqli_real_escape_string($mysql, $_GET['user_id']) . '"';
$result_userpage_user = mysqli_query($mysql, $sql_userpage_user);
$row_userpage_user = mysqli_fetch_assoc($result_userpage_user);

if(isset($_SESSION['signed_in'])) {
$sql_userpage_me = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_userpage_me = mysqli_query($mysql, $sql_userpage_me);
$row_userpage_me = mysqli_fetch_assoc($result_userpage_me); }

// Who posesses the post?
if($row_userpage_user['pid']) {
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true) {
if($_SESSION['pid'] == $row_userpage_user['pid']) {
$pagetitle = 'User Page';
}
else {
$pagetitle = htmlspecialchars($row_userpage_user['screen_name']) . "'s Profile"; 
}    } 
else {
$pagetitle = htmlspecialchars($row_userpage_user['screen_name']) . "'s Profile"; }
}
else {
$pagetitle = 'Error'; }
require_once 'lib/htm.php';
printHeader(false);
printMenu();
// DB error.
if(!$result_userpage_user)
{
http_response_code(500);
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="500">';
$no_content_message = ( 'Server error.' );
include 'lib/no-content-window.php';
}
else
{
	// User wasn't found.
    if(mysqli_num_rows($result_userpage_user) == 0)
    {
(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The user could not be found.' );
include 'lib/no-content-window.php';
    }
    else
    {
$sql_userpage_user_profile = 'SELECT * FROM profiles WHERE profiles.pid = "' . $row_userpage_user['pid'] . '"';
$result_userpage_user_profile = mysqli_query($mysql, $sql_userpage_user_profile);
$row_userpage_user_profile = mysqli_fetch_assoc($result_userpage_user_profile); 

// Uncomment when /welcome/profile is implemented, please!
  
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid'] && mysqli_num_rows($result_userpage_user_profile) == 0) {
mysqli_query($mysql, "INSERT INTO
                    profiles(pid, platform_id)
                VALUES('" . mysqli_real_escape_string($mysql, $_SESSION['pid']) . "',
                       '" . $row_userpage_user['platform_id'] . "')");
     }
else {

    print $GLOBALS['div_body_head'];
	print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';

if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
$is_user_me_page_visitor = ' is-visitor'; }
else {
$is_user_me_page_visitor = ''; }

if($row_userpage_user['official_user'] == 1) {
$is_user_info_official_user = ' official-user'; }
else {
$is_user_info_official_user = ''; }

$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "' . $row_userpage_user['pid'] . '" AND posts.is_hidden != "1"';
$result_userpage_user_posts = mysqli_query($mysql, $sql_userpage_user_posts);

$sql_userpage_user_friends = 'SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" ORDER BY friend_relationships.relationship_id LIMIT 100';
$result_userpage_user_friends = mysqli_query($mysql, $sql_userpage_user_friends);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = mysqli_query($mysql, $sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_following = mysqli_query($mysql, $sql_userpage_user_following);

#Begin body-content user-page
print '<div class="body-content user-page'.$is_user_me_page_visitor.'">
';
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
print '<a id="header-mymenu-button" href="/my_menu" data-pjax="#body">User Menu</a>
'; }


		if($row_userpage_user['mii_hash']) {
$user_page_info_mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_userpage_user['mii_hash'] . '_normal_face.png'; }
else {
if($row_userpage_user['user_face']) {
$user_page_info_mii_face_output = htmlspecialchars($row_userpage_user['user_face']); }
 else {
$user_page_info_mii_face_output = '/img/mii/img_unknown_MiiIcon.png'; }
}

print '<div class="user-info info-content'.$is_user_info_official_user.'">'."\n".'';

if(empty($row_userpage_user_profile['favorite_screenshot']) && strlen($row_userpage_user_profile['favorite_screenshot']) < 5) {
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user_profile['pid']) {
print '<div class="user-profile-memo-container no-profile-memo">Your favorite post can be displayed here.</div>
'; } else {
print '<div class="user-profile-memo-container no-profile-memo"></div>'; }
}
else {
$result_posts_getfavoritepost = mysqli_query($mysql, 'SELECT * FROM posts WHERE posts.id = "'.mysqli_real_escape_string($mysql, $row_userpage_user_profile['favorite_screenshot']).'"');
print '<a href="/posts/'.htmlspecialchars($row_userpage_user_profile['favorite_screenshot']).'" data-pjax="#body" class="user-profile-memo-container">
    <img src="'.htmlspecialchars(mysqli_fetch_assoc($result_posts_getfavoritepost)['screenshot']).'" class="user-profile-memo">
  </a>'; }

print '    <span class="icon-container'.$is_user_info_official_user.'"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'"><img src="'.$user_page_info_mii_face_output.'" class="icon"></a></span>
';
if($row_userpage_user['official_user'] == 1) {
print '<p class="user-organization">'.htmlspecialchars($row_userpage_user['organization']).'</p>'; }
print '  <p class="title">
    <span class="nick-name">'.htmlspecialchars($row_userpage_user['screen_name']).'</span>
    <span class="id-name">'.htmlspecialchars($row_userpage_user['user_id']).'</span>
  </p>
  
  ';

if($row_userpage_user['official_user'] == 1) {
$is_identified_user_value = ' data-is-identified="1"'; }
else {
$is_identified_user_value = ''; }
 
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && 2 >= $row_userpage_user['status']) {
$if_user_follow_can = ''; }
else {
$if_user_follow_can = ' disabled'; }	

print '<a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'" data-pjax="#body" class="button profile-back-button">To Top</a>';
# End user-info info-content
print '</div>';

print '<menu class="user-menu tab-header">
  <li class="test-user-posts-count tab-button-profile"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Posts</span><span class="number">'.mysqli_num_rows($result_userpage_user_posts).'</span></a></li>
  <li class="test-user-friends-count tab-button-activity"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/friends" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Friends</span><span class="number">'.mysqli_num_rows($result_userpage_user_friends).' / 100</span></a></li>
  <li class="test-user-followings-count tab-button-activity selected"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/following" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Following</span><span class="number"><span class="js-following-count">'.mysqli_num_rows($result_userpage_user_following).'</span> / 1000</span></a></li>
  <li class="test-user-followers-count tab-button-relationship"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/followers" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Followers</span><span class="number">'.mysqli_num_rows($result_userpage_user_followers).'</span></a></li>
</menu>';

print '<div class="tab-body">
<div class="user-page-content friend-list following">
';



$sql_search_relationships = 'SELECT * FROM relationships WHERE relationships.source = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0" ORDER BY relationships.relationship_id DESC';
$result_search_relationships = mysqli_query($mysql, $sql_search_relationships);

if(mysqli_num_rows($result_search_relationships) == 0) {
if($_SESSION['pid'] == $row_userpage_user['pid']) {
# Changed from 'his or her' to 'their'.
$no_content_message = 'No followed users.<br>
      To follow someone, select Follow from their profile screen.';
include 'lib/no-content-window.php';
}
else {
$no_content_message = 'No followed users.';
include 'lib/no-content-window.php';
   }
}

else {
	# Please implement next-page-url!
print '<ul class="list-content-with-icon-and-text arrow-list" id="friend-list-content" data-next-page-url="">';

while($row_search_relationships = mysqli_fetch_assoc($result_search_relationships)) {

$sql_relationships_users = 'SELECT * FROM people WHERE people.pid = "'.$row_search_relationships['target'].'"';
$result_relationships_users = mysqli_query($mysql, $sql_relationships_users);
$row_relationships_users = mysqli_fetch_assoc($result_relationships_users);

$row_user_to_view = $row_relationships_users;
$is_mutual_list = true;
include 'lib/userlist-li-template.php';
	
	}
	# End ul
	print '</ul>';

}

# End of tab-body & user-page-content
print '
</div>

  </div>';

include 'lib/user-page-footer.php';
# End body-content user-page
print '</div>';
	print $GLOBALS['div_body_head_end'];	
	printFooter();
      }
	}
  }
}
if($_GET['mode'] == 'followers') {
# Display standard user page.
$sql_userpage_user = 'SELECT * FROM people WHERE people.user_id = "' . mysqli_real_escape_string($mysql, $_GET['user_id']) . '"';
$result_userpage_user = mysqli_query($mysql, $sql_userpage_user);
$row_userpage_user = mysqli_fetch_assoc($result_userpage_user);

if(isset($_SESSION['signed_in'])) {
$sql_userpage_me = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_userpage_me = mysqli_query($mysql, $sql_userpage_me);
$row_userpage_me = mysqli_fetch_assoc($result_userpage_me); }

// Who posesses the post?
if($row_userpage_user['pid']) {
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true) {
if($_SESSION['pid'] == $row_userpage_user['pid']) {
$pagetitle = 'User Page';
}
else {
$pagetitle = htmlspecialchars($row_userpage_user['screen_name']) . "'s Profile"; 
}    } 
else {
$pagetitle = htmlspecialchars($row_userpage_user['screen_name']) . "'s Profile"; }
}
else {
$pagetitle = 'Error'; }
require_once 'lib/htm.php';
printHeader(false);
printMenu();
// DB error.
if(!$result_userpage_user)
{
http_response_code(500);
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="500">';
$no_content_message = ( 'Server error.' );
include 'lib/no-content-window.php';
}
else
{
	// User wasn't found.
    if(mysqli_num_rows($result_userpage_user) == 0)
    {
(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The user could not be found.' );
include 'lib/no-content-window.php';
    }
    else
    {
$sql_userpage_user_profile = 'SELECT * FROM profiles WHERE profiles.pid = "' . $row_userpage_user['pid'] . '"';
$result_userpage_user_profile = mysqli_query($mysql, $sql_userpage_user_profile);
$row_userpage_user_profile = mysqli_fetch_assoc($result_userpage_user_profile); 

// Uncomment when /welcome/profile is implemented, please!
  
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid'] && mysqli_num_rows($result_userpage_user_profile) == 0) {
mysqli_query($mysql, "INSERT INTO
                    profiles(pid, platform_id)
                VALUES('" . mysqli_real_escape_string($mysql, $_SESSION['pid']) . "',
                       '" . $row_userpage_user['platform_id'] . "')");
     }
else {

    print $GLOBALS['div_body_head'];
	print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';

if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
$is_user_me_page_visitor = ' is-visitor'; }
else {
$is_user_me_page_visitor = ''; }

if($row_userpage_user['official_user'] == 1) {
$is_user_info_official_user = ' official-user'; }
else {
$is_user_info_official_user = ''; }

$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "' . $row_userpage_user['pid'] . '" AND posts.is_hidden != "1"';
$result_userpage_user_posts = mysqli_query($mysql, $sql_userpage_user_posts);

$sql_userpage_user_friends = 'SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" ORDER BY friend_relationships.relationship_id LIMIT 100';
$result_userpage_user_friends = mysqli_query($mysql, $sql_userpage_user_friends);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = mysqli_query($mysql, $sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_following = mysqli_query($mysql, $sql_userpage_user_following);

#Begin body-content user-page
print '<div class="body-content user-page'.$is_user_me_page_visitor.'">
';
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
print '<a id="header-mymenu-button" href="/my_menu" data-pjax="#body">User Menu</a>
'; }


		if($row_userpage_user['mii_hash']) {
$user_page_info_mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_userpage_user['mii_hash'] . '_normal_face.png'; }
else {
if($row_userpage_user['user_face']) {
$user_page_info_mii_face_output = htmlspecialchars($row_userpage_user['user_face']); }
 else {
$user_page_info_mii_face_output = '/img/mii/img_unknown_MiiIcon.png'; }
}

print '<div class="user-info info-content'.$is_user_info_official_user.'">'."\n".'';

if(empty($row_userpage_user_profile['favorite_screenshot']) && strlen($row_userpage_user_profile['favorite_screenshot']) < 5) {
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user_profile['pid']) {
print '<div class="user-profile-memo-container no-profile-memo">Your favorite post can be displayed here.</div>
'; } else {
print '<div class="user-profile-memo-container no-profile-memo"></div>'; }
}
else {
$result_posts_getfavoritepost = mysqli_query($mysql, 'SELECT * FROM posts WHERE posts.id = "'.mysqli_real_escape_string($mysql, $row_userpage_user_profile['favorite_screenshot']).'"');
print '<a href="/posts/'.htmlspecialchars($row_userpage_user_profile['favorite_screenshot']).'" data-pjax="#body" class="user-profile-memo-container">
    <img src="'.htmlspecialchars(mysqli_fetch_assoc($result_posts_getfavoritepost)['screenshot']).'" class="user-profile-memo">
  </a>'; }

print '    <span class="icon-container'.$is_user_info_official_user.'"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'"><img src="'.$user_page_info_mii_face_output.'" class="icon"></a></span>
';
if($row_userpage_user['official_user'] == 1) {
print '<p class="user-organization">'.htmlspecialchars($row_userpage_user['organization']).'</p>'; }
print '  <p class="title">
    <span class="nick-name">'.htmlspecialchars($row_userpage_user['screen_name']).'</span>
    <span class="id-name">'.htmlspecialchars($row_userpage_user['user_id']).'</span>
  </p>
  
  ';

if($row_userpage_user['official_user'] == 1) {
$is_identified_user_value = ' data-is-identified="1"'; }
else {
$is_identified_user_value = ''; }
 
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && 2 >= $row_userpage_user['status']) {
$if_user_follow_can = ''; }
else {
$if_user_follow_can = ' disabled'; }	

print '<a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'" data-pjax="#body" class="button profile-back-button">To Top</a>';
# End user-info info-content
print '</div>';

print '<menu class="user-menu tab-header">
  <li class="test-user-posts-count tab-button-profile"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Posts</span><span class="number">'.mysqli_num_rows($result_userpage_user_posts).'</span></a></li>
  <li class="test-user-friends-count tab-button-activity"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/friends" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Friends</span><span class="number">'.mysqli_num_rows($result_userpage_user_friends).' / 100</span></a></li>
  <li class="test-user-followings-count tab-button-activity"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/following" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Following</span><span class="number"><span class="js-following-count">'.mysqli_num_rows($result_userpage_user_following).'</span> / 1000</span></a></li>
  <li class="test-user-followers-count tab-button-relationship selected"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/followers" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Followers</span><span class="number">'.mysqli_num_rows($result_userpage_user_followers).'</span></a></li>
</menu>';

print '<div class="tab-body">
<div class="user-page-content friend-list following">
';



$sql_search_relationships = 'SELECT * FROM relationships WHERE relationships.target = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0" ORDER BY relationships.relationship_id DESC';
$result_search_relationships = mysqli_query($mysql, $sql_search_relationships);

if(mysqli_num_rows($result_search_relationships) == 0) {
if($_SESSION['pid'] == $row_userpage_user['pid']) {
$no_content_message = 'You have no followers.';
include 'lib/no-content-window.php';	
}
else {
$no_content_message = 'This user has no followers.';
include 'lib/no-content-window.php';	
 }
}

else {
	# Please implement next-page-url!
print '<ul class="list-content-with-icon-and-text arrow-list" id="friend-list-content" data-next-page-url="">';

while($row_search_relationships = mysqli_fetch_assoc($result_search_relationships)) {

$sql_relationships_users = 'SELECT * FROM people WHERE people.pid = "'.$row_search_relationships['source'].'"';
$result_relationships_users = mysqli_query($mysql, $sql_relationships_users);
$row_relationships_users = mysqli_fetch_assoc($result_relationships_users);

$row_user_to_view = $row_relationships_users;
$is_mutual_list = true;
include 'lib/userlist-li-template.php';

	}
	# End ul
	print '</ul>';
	
}

# End of tab-body & user-page-content
print '
</div>

  </div>';

include 'lib/user-page-footer.php';
# End body-content user-page
print '</div>';
	print $GLOBALS['div_body_head_end'];	
	printFooter();
      }
	}
  }
}
if($_GET['mode'] == 'friends') {
# Display standard user page.
$sql_userpage_user = 'SELECT * FROM people WHERE people.user_id = "' . mysqli_real_escape_string($mysql, $_GET['user_id']) . '"';
$result_userpage_user = mysqli_query($mysql, $sql_userpage_user);
$row_userpage_user = mysqli_fetch_assoc($result_userpage_user);

if(isset($_SESSION['signed_in'])) {
$sql_userpage_me = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_userpage_me = mysqli_query($mysql, $sql_userpage_me);
$row_userpage_me = mysqli_fetch_assoc($result_userpage_me); }

// Who posesses the post?
if($row_userpage_user['pid']) {
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true) {
if($_SESSION['pid'] == $row_userpage_user['pid']) {
$pagetitle = 'User Page';
}
else {
$pagetitle = htmlspecialchars($row_userpage_user['screen_name']) . "'s Profile"; 
}    } 
else {
$pagetitle = htmlspecialchars($row_userpage_user['screen_name']) . "'s Profile"; }
}
else {
$pagetitle = 'Error'; }
require_once 'lib/htm.php';
printHeader(false);
printMenu();
// DB error.
if(!$result_userpage_user)
{
http_response_code(500);
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="500">';
$no_content_message = ( 'Server error.' );
include 'lib/no-content-window.php';
}
else
{
	// User wasn't found.
    if(mysqli_num_rows($result_userpage_user) == 0)
    {
(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The user could not be found.' );
include 'lib/no-content-window.php';
    }
    else
    {
$sql_userpage_user_profile = 'SELECT * FROM profiles WHERE profiles.pid = "' . $row_userpage_user['pid'] . '"';
$result_userpage_user_profile = mysqli_query($mysql, $sql_userpage_user_profile);
$row_userpage_user_profile = mysqli_fetch_assoc($result_userpage_user_profile); 

// Uncomment when /welcome/profile is implemented, please!
  
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid'] && mysqli_num_rows($result_userpage_user_profile) == 0) {
mysqli_query($mysql, "INSERT INTO
                    profiles(pid, platform_id)
                VALUES('" . mysqli_real_escape_string($mysql, $_SESSION['pid']) . "',
                       '" . $row_userpage_user['platform_id'] . "')");
     }
else {

    print $GLOBALS['div_body_head'];
	print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';

if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
$is_user_me_page_visitor = ' is-visitor'; }
else {
$is_user_me_page_visitor = ''; }

if($row_userpage_user['official_user'] == 1) {
$is_user_info_official_user = ' official-user'; }
else {
$is_user_info_official_user = ''; }

$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "' . $row_userpage_user['pid'] . '" AND posts.is_hidden != "1"';
$result_userpage_user_posts = mysqli_query($mysql, $sql_userpage_user_posts);

$sql_userpage_user_friends = 'SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" ORDER BY friend_relationships.relationship_id LIMIT 100';
$result_userpage_user_friends = mysqli_query($mysql, $sql_userpage_user_friends);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = mysqli_query($mysql, $sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_following = mysqli_query($mysql, $sql_userpage_user_following);

#Begin body-content user-page
print '<div class="body-content user-page'.$is_user_me_page_visitor.'">
';
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
print '<a id="header-mymenu-button" href="/my_menu" data-pjax="#body">User Menu</a>
'; }


		if($row_userpage_user['mii_hash']) {
$user_page_info_mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_userpage_user['mii_hash'] . '_normal_face.png'; }
else {
if($row_userpage_user['user_face']) {
$user_page_info_mii_face_output = htmlspecialchars($row_userpage_user['user_face']); }
 else {
$user_page_info_mii_face_output = '/img/mii/img_unknown_MiiIcon.png'; }
}

print '<div class="user-info info-content'.$is_user_info_official_user.'">'."\n".'';

if(empty($row_userpage_user_profile['favorite_screenshot']) && strlen($row_userpage_user_profile['favorite_screenshot']) < 5) {
if(!empty($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user_profile['pid']) {
print '<div class="user-profile-memo-container no-profile-memo">Your favorite post can be displayed here.</div>
'; } else {
print '<div class="user-profile-memo-container no-profile-memo"></div>'; }
}
else {
$result_posts_getfavoritepost = mysqli_query($mysql, 'SELECT * FROM posts WHERE posts.id = "'.mysqli_real_escape_string($mysql, $row_userpage_user_profile['favorite_screenshot']).'"');
print '<a href="/posts/'.htmlspecialchars($row_userpage_user_profile['favorite_screenshot']).'" data-pjax="#body" class="user-profile-memo-container">
    <img src="'.htmlspecialchars(mysqli_fetch_assoc($result_posts_getfavoritepost)['screenshot']).'" class="user-profile-memo">
  </a>'; }

print '    <span class="icon-container'.$is_user_info_official_user.'"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'"><img src="'.$user_page_info_mii_face_output.'" class="icon"></a></span>
';
if($row_userpage_user['official_user'] == 1) {
print '<p class="user-organization">'.htmlspecialchars($row_userpage_user['organization']).'</p>'; }
print '  <p class="title">
    <span class="nick-name">'.htmlspecialchars($row_userpage_user['screen_name']).'</span>
    <span class="id-name">'.htmlspecialchars($row_userpage_user['user_id']).'</span>
  </p>
  
  ';

if($row_userpage_user['official_user'] == 1) {
$is_identified_user_value = ' data-is-identified="1"'; }
else {
$is_identified_user_value = ''; }
 
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && 2 >= $row_userpage_user['status']) {
$if_user_follow_can = ''; }
else {
$if_user_follow_can = ' disabled'; }	

print '<a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'" data-pjax="#body" class="button profile-back-button">To Top</a>';
# End user-info info-content
print '</div>';

print '<menu class="user-menu tab-header">
  <li class="test-user-posts-count tab-button-profile"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Posts</span><span class="number">'.mysqli_num_rows($result_userpage_user_posts).'</span></a></li>
  <li class="test-user-friends-count tab-button-activity selected"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/friends" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Friends</span><span class="number">'.mysqli_num_rows($result_userpage_user_friends).' / 100</span></a></li>
  <li class="test-user-followings-count tab-button-activity"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/following" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Following</span><span class="number"><span class="js-following-count">'.mysqli_num_rows($result_userpage_user_following).'</span> / 1000</span></a></li>
  <li class="test-user-followers-count tab-button-relationship"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/followers" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Followers</span><span class="number">'.mysqli_num_rows($result_userpage_user_followers).'</span></a></li>
</menu>';

print '<div class="tab-body">
<div class="user-page-content friend-list friends">
';

$sql_search_friend_relationships = 'SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" ORDER BY friend_relationships.relationship_id DESC LIMIT 100';
$result_search_friend_relationships = mysqli_query($mysql, $sql_search_friend_relationships);

if(mysqli_num_rows($result_search_friend_relationships) == 0) {
if($_SESSION['pid'] == $row_userpage_user['pid']) {
$no_content_message = 'No friends to display.
<br>

	To become friends with someone, send them a friend request from their profile screen.';
include 'lib/no-content-window.php';	
}
else {
$no_content_message = 'No friends to display.';
include 'lib/no-content-window.php';	
 }
}

else {

	# Please implement next-page-url!
print '<ul class="list-content-with-icon-and-text arrow-list" id="friend-list-content" data-next-page-url="">';

$has_can_friend_request = (isset($_SESSION['pid']) && $row_userpage_me['pid'] == $row_userpage_user['pid'] ? true : false);

while($row_search_friend_relationships = mysqli_fetch_assoc($result_search_friend_relationships)) {

$sql_search_friend_relationships1 = 'SELECT * FROM people WHERE people.pid = "'.($row_search_friend_relationships['source'] == $row_userpage_user['pid'] ? $row_search_friend_relationships['target'] : $row_search_friend_relationships['source']).'"';
$result_search_friend_relationships1 = mysqli_query($mysql, $sql_search_friend_relationships1);
$row_search_friend_relationships1 = mysqli_fetch_assoc($result_search_friend_relationships1);
$row_user_to_view = $row_search_friend_relationships1;
if(isset($_SESSION['pid']) && $row_userpage_me['pid'] == $row_userpage_user['pid']) {
$is_friends_added_list = true; } else {
$is_always_button_have = true; }


$is_mutual_list = true;
include 'lib/userlist-li-template.php'; 
    }


if(isset($_SESSION['pid']) && $row_userpage_user['pid'] == $row_userpage_me['pid']) {
$sql_search_friend_request = 'SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$row_userpage_user['pid'].'" AND friend_requests.finished = "0" ORDER BY created_at DESC';
$result_search_friend_request = mysqli_query($mysql, $sql_search_friend_request);
if(mysqli_num_rows($result_search_friend_request) != 0) {
while($row_search_friend_request = mysqli_fetch_assoc($result_search_friend_request)) {

$sql_relationships_users = 'SELECT * FROM people WHERE people.pid = "'.$row_search_friend_request['recipient'].'"';
$result_relationships_users = mysqli_query($mysql, $sql_relationships_users);
$row_relationships_users = mysqli_fetch_assoc($result_relationships_users);

$row_user_to_view = $row_relationships_users; 
$is_friends_added_list = true;
$is_friends_pending_list = true;
$is_mutual_list = true;
include 'lib/userlist-li-template.php';

} } }

	# End ul
	print '</ul>';
	
}

# End of tab-body & user-page-content
print '
</div>

  </div>';

include 'lib/user-page-footer.php';
# End body-content user-page
print '</div>';
	print $GLOBALS['div_body_head_end'];	
	printFooter();
      }
	}
  }
}
if($_GET['mode'] == 'favorites') {
# Search for user first, then use template
$sql_userpage_user = 'SELECT * FROM people WHERE people.user_id = "' . mysqli_real_escape_string($mysql, $_GET['user_id']) . '"';
$result_userpage_user = mysqli_query($mysql, $sql_userpage_user);
$row_userpage_user = mysqli_fetch_assoc($result_userpage_user);

// Who posesses the post?
if($row_userpage_user['pid']) {
$is_fav_own = $row_userpage_user['pid'];
include_once 'communities-showfavorites.php';
}
else {
$pagetitle = 'Error';
require_once 'lib/htm.php';
printHeader(false);
printMenu(); }
// DB error.
if(!$result_userpage_user)
{
http_response_code(500);
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="500">';
$no_content_message = ( 'Server error.' );
include 'lib/no-content-window.php';
}
else
{
	// User wasn't found.
    if(mysqli_num_rows($result_userpage_user) == 0)
    {
(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The user could not be found.' );
include 'lib/no-content-window.php';
    }
}
}
if($_GET['mode'] == 'follow') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# If method isn't POST, display 404.
include_once '404.php'; }
else {
# Method is POST.

		if(empty($_SESSION['pid'])) {
            $error_message[] = 'You are not logged in.\nLog in to follow users.';
			$error_code[] = '1512005';
        }
		else {

if($_SESSION['pid']) {	
$sql_post_getuser = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_post_getuser = mysqli_query($mysql, $sql_post_getuser);
$row_post_getuser = mysqli_fetch_assoc($result_post_getuser); 

$sql_post_getotheruser = 'SELECT * FROM people WHERE people.pid = "' . mysqli_real_escape_string($mysql, $_GET['user_id']) . '"';
$result_post_getotheruser = mysqli_query($mysql, $sql_post_getotheruser);
$row_post_getotheruser = mysqli_fetch_assoc($result_post_getotheruser); 
		
        if(strval($row_post_getuser['status'] >= 2) || strval($row_post_getuser['empathy_restriction'] >= 1)) {
			$error_message[] = 'You are not permitted to follow other users.';
			$error_code[] = '1512006';
		}
}
$sql_userwho = 'SELECT * FROM people WHERE people.user_id = "' . mysqli_real_escape_string($mysql, $_GET['user_id']) . '"';
$result_userwho = mysqli_query($mysql, $sql_userwho);
$row_post_userwho = mysqli_fetch_assoc($result_userwho);

if(mysqli_num_rows($result_userwho)==0) {
			$error_message[] = 'The user could not be found.';
			$error_code[] = '1512012';	
        }
if(isset($_SESSION['pid'])) {	
if($row_post_userwho['pid'] == $row_post_getuser['pid']) {
			$error_message[] = 'You cannot follow yourself.';
			$error_code[] = '1512008';				  
} 

$sql_search_relationship2 = 'SELECT * FROM relationships WHERE relationships.source = "'.$row_post_getuser['pid'].'" AND relationships.target = "'.$row_post_userwho['pid'].'" AND relationships.is_me2me = "0"';
$result_search_relationship2 = mysqli_query($mysql, $sql_search_relationship2);

if(strval(mysqli_num_rows($result_search_relationship2) >= 1)) {
			$error_message[] = 'You are already following this user.';
			$error_code[] = '1512013';	
}
	    if(!empty($error_code) || !empty($error_message) ) /*Got errors?*/
    {
		// JSON response for errors.
			http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
			print '{"success":0,"errors":[{"message":"' . $error_message[0] . '","error_code":' . $error_code[0] . '}],"code":"400"}';
			print "\n";
    }
    else {
$sql_post_getuforfollow = 'SELECT * FROM people WHERE people.user_id = "' . mysqli_real_escape_string($mysql, $_GET['user_id']) . '"';
$result_post_getuforfollow = mysqli_query($mysql, $sql_post_getuforfollow);
$row_post_getuforfollow = mysqli_fetch_assoc($result_post_getuforfollow); 
// User checks over. Is eligible to follow.
        $sql_relationshipcreate = 'INSERT INTO relationships(source, target) VALUES ("' . $_SESSION['pid'] . '", "' . $row_post_getuforfollow['pid'] . '")';
		$sql_newscreate = 'INSERT INTO news(from_pid, to_pid, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$row_post_getuforfollow['pid'].'", "6", "0")';
        $result_relationshipcreate = mysqli_query($mysql, $sql_relationshipcreate);
			// If the user gave the same type of notification 8 seconds ago, then don't send this.
	$result_check_fastnews = mysqli_query($mysql, 'SELECT news.to_pid, news.created_at FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_post_getuforfollow['pid'].'" AND news.news_context = "6" AND news.created_at > NOW() - 8 ORDER BY news.created_at DESC');
    if(mysqli_num_rows($result_check_fastnews) == 0) {
    $result_check_ownusernews = mysqli_query($mysql, 'SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_post_getuforfollow['pid'].'" AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
$row_check_ownusernews = mysqli_fetch_assoc($result_check_ownusernews);
	$result_check_mergedusernews = mysqli_query($mysql, 'SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_post_getuforfollow['pid'].'" AND news.merged IS NOT NULL AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
 if(mysqli_num_rows($result_check_mergedusernews) != 0) {
	$result_update_mergedusernewsagain = mysqli_query($mysql, 'UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.mysqli_fetch_assoc($result_check_mergedusernews)['merged'].'"');	
	}
	elseif(mysqli_num_rows($result_check_ownusernews) != 0) {
	$result_update_ownusernewsagain = mysqli_query($mysql, 'UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$row_check_ownusernews['news_id'].'"');
	}
else {
	$result_update_newsmergesearch = mysqli_query($mysql, 'SELECT * FROM news WHERE news.to_pid = "'.$row_post_getuforfollow['pid'].'" AND news.news_context = "6" AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
	if(mysqli_num_rows($result_update_newsmergesearch) != 0) {
$row_update_newsmergesearch = mysqli_fetch_assoc($result_update_newsmergesearch);
	
	$result_newscreatemerge = mysqli_query($mysql, 'INSERT INTO news(from_pid, to_pid, merged, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$row_post_getuforfollow['pid'].'", "'.$row_update_newsmergesearch['news_id'].'", "6", "0")');
$result_update_newsformerge = mysqli_query($mysql, 'UPDATE news SET has_read = "0", created_at = NOW() WHERE news.news_id = "'.$row_update_newsmergesearch['news_id'].'"');
		}
else {
        $result_newscreate = mysqli_query($mysql, 'INSERT INTO news(from_pid, to_pid, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$row_post_getuforfollow['pid'].'", "6", "0")'); 	
	} }
		
		
	}
        if(!$result_relationshipcreate)
        {
http_response_code(400);
header('Content-Type: application/json; charset=utf-8');
print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($mysql) . '}],"code":"400"}';
print "\n";
	}
        else
        {
$sql_my_followingcount = 'SELECT * FROM relationships WHERE relationships.source = '.$_SESSION['pid'].'';
$result_my_followingcount = mysqli_query($mysql, $sql_my_followingcount);
header('Content-Type: application/json; charset=utf-8');
print '{"success":1,"can_follow_more":true,"following_count":'.mysqli_num_rows($result_my_followingcount).'}';
print "\n";
        }
		
   }
  }
}
}
}
if($_GET['mode'] == 'unfollow') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
# If method isn't POST, display 404.
include_once '404.php'; }
else {
# Method is POST.

		if(empty($_SESSION['pid'])) {
            $error_message[] = 'You are not logged in.\nLog in to follow users.';
			$error_code[] = '1512005';
        }
		else {

if($_SESSION['pid']) {	
$sql_post_getuser = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_post_getuser = mysqli_query($mysql, $sql_post_getuser);
$row_post_getuser = mysqli_fetch_assoc($result_post_getuser); 

$sql_post_getotheruser = 'SELECT * FROM people WHERE people.pid = "' . mysqli_real_escape_string($mysql, $_GET['user_id']) . '"';
$result_post_getotheruser = mysqli_query($mysql, $sql_post_getotheruser);
$row_post_getotheruser = mysqli_fetch_assoc($result_post_getotheruser); 
		
        if(strval($row_post_getuser['status'] >= 2) || strval($row_post_getuser['empathy_restriction'] >= 1)) {
			$error_message[] = 'You are not permitted to follow other users.';
			$error_code[] = '1512006';
		}
}
$sql_userwho = 'SELECT * FROM people WHERE people.user_id = "' . mysqli_real_escape_string($mysql, $_GET['user_id']) . '"';
$result_userwho = mysqli_query($mysql, $sql_userwho);
$row_userwho = mysqli_fetch_assoc($result_userwho);

if(mysqli_num_rows($result_userwho)==0) {
			$error_message[] = 'The user could not be found.';
			$error_code[] = '1512012';	
        }
if(isset($_SESSION['pid'])) {	
if($row_userwho['pid'] == $row_post_getuser['pid']) {
			$error_message[] = 'You cannot follow yourself.';
			$error_code[] = '1512008';				  
} 

$sql_userwho = 'SELECT * FROM people WHERE people.user_id = "' . mysqli_real_escape_string($mysql, $_GET['user_id']) . '"';
$result_userwho = mysqli_query($mysql, $sql_userwho);

$sql_search_relationship = 'SELECT * FROM relationships WHERE relationships.source = "'.$row_post_getuser['pid'].'" AND relationships.target = "'.$row_userwho['pid'].'" AND relationships.is_me2me = "0"';
$result_search_relationship = mysqli_query($mysql, $sql_search_relationship);

if(strval(mysqli_num_rows($result_search_relationship) <=0)) {
			$error_message[] = 'You are not following this user.';
			$error_code[] = '1512014';	
}
	    if(!empty($error_code) || !empty($error_message) ) /*Got errors?*/
    {
		// JSON response for errors.
			http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
#print $sql_search_relationship;
			print '{"success":0,"errors":[{"message":"' . $error_message[0] . '","error_code":' . $error_code[0] . '}],"code":"400"}';
			print "\n";
    }
    else {
$sql_post_getuforfollow = 'SELECT * FROM people WHERE people.user_id = "' . mysqli_real_escape_string($mysql, $_GET['user_id']) . '"';
$result_post_getuforfollow = mysqli_query($mysql, $sql_post_getuforfollow);
$row_post_getuforfollow = mysqli_fetch_assoc($result_post_getuforfollow); 
// User checks over. Is eligible to follow.
        $sql_relationshipdelete = 'DELETE FROM relationships WHERE source = "'.$_SESSION['pid'].'" AND target = "'.$row_post_getuforfollow['pid'].'"';
        $result_relationshipdelete = mysqli_query($mysql, $sql_relationshipdelete);
        if(!$result_relationshipdelete)
        {
http_response_code(400);
header('Content-Type: application/json; charset=utf-8');
print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160' . mysqli_errno($mysql) . '}],"code":"400"}';
print "\n";
	}
        else
        {
header('Content-Type: application/json; charset=utf-8');
print '{"success":1}';
print "\n";
        }
	}
		
   }
  }
}
}


# Define other modes here. 
 
}

#else {
#include_once '404.php'; }


  
 else {
# Display standard user page.
$sql_userpage_user = 'SELECT * FROM people WHERE people.user_id = "' . mysqli_real_escape_string($mysql, $_GET['user_id']) . '"';
$result_userpage_user = mysqli_query($mysql, $sql_userpage_user);
$row_userpage_user = mysqli_fetch_assoc($result_userpage_user);

if(isset($_SESSION['signed_in'])) {
$sql_userpage_me = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_userpage_me = mysqli_query($mysql, $sql_userpage_me);
$row_userpage_me = mysqli_fetch_assoc($result_userpage_me); }

// Who posesses the post?
if($row_userpage_user['pid']) {
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true) {
if($_SESSION['pid'] == $row_userpage_user['pid']) {
$pagetitle = 'User Page';
}
else {
$pagetitle = htmlspecialchars($row_userpage_user['screen_name']) . "'s Profile"; 
}    } 
else {
$pagetitle = htmlspecialchars($row_userpage_user['screen_name']) . "'s Profile"; }
}
else {
$pagetitle = 'Error'; }
require_once 'lib/htm.php';
printHeader(false);
printMenu();
// DB error.
if(!$result_userpage_user)
{
http_response_code(500);
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="500">';
$no_content_message = ( 'Server error.' );
include 'lib/no-content-window.php';
}
else
{
	// User wasn't found.
    if(mysqli_num_rows($result_userpage_user) == 0)
    {
(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
$pagetitle = ('Error');
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The user could not be found.' );
include 'lib/no-content-window.php';
    }
    else
    {
$sql_userpage_user_profile = 'SELECT * FROM profiles WHERE profiles.pid = "' . $row_userpage_user['pid'] . '"';
$result_userpage_user_profile = mysqli_query($mysql, $sql_userpage_user_profile);
$row_userpage_user_profile = mysqli_fetch_assoc($result_userpage_user_profile); 
  
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid'] && mysqli_num_rows($result_userpage_user_profile) == 0) {
mysqli_query($mysql, "INSERT INTO
                    profiles(pid, platform_id)
                VALUES('" . mysqli_real_escape_string($mysql, $_SESSION['pid']) . "',
                       '" . $row_userpage_user['platform_id'] . "')");
     }
else {

    print $GLOBALS['div_body_head'];
	print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';

if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
$is_user_me_page_visitor = ' is-visitor'; }
else {
$is_user_me_page_visitor = ''; }

if($row_userpage_user['official_user'] == 1) {
$is_user_info_official_user = ' official-user'; }
else {
$is_user_info_official_user = ''; }

$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "' . $row_userpage_user['pid'] . '" AND posts.is_hidden != "1"';
$result_userpage_user_posts = mysqli_query($mysql, $sql_userpage_user_posts);

$sql_userpage_user_friends = 'SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" ORDER BY friend_relationships.relationship_id LIMIT 100';
$result_userpage_user_friends = mysqli_query($mysql, $sql_userpage_user_friends);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = mysqli_query($mysql, $sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "' . $row_userpage_user['pid'] . '" AND relationships.is_me2me = "0"';
$result_userpage_user_following = mysqli_query($mysql, $sql_userpage_user_following);

#Begin body-content user-page
print '<div class="body-content user-page'.$is_user_me_page_visitor.'">
';
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
print '<a id="header-mymenu-button" href="/my_menu" data-pjax="#body">User Menu</a>
'; }


		if($row_userpage_user['mii_hash']) {
$user_page_info_mii_face_output = 'https://mii-secure.cdn.nintendo.net/' . $row_userpage_user['mii_hash'] . '_normal_face.png'; }
else {
if($row_userpage_user['user_face']) {
$user_page_info_mii_face_output = htmlspecialchars($row_userpage_user['user_face']); }
 else {
$user_page_info_mii_face_output = '/img/mii/img_unknown_MiiIcon.png'; }
}

print '<div class="user-info info-content'.$is_user_info_official_user.'">'."\n".'';

if(empty($row_userpage_user_profile['favorite_screenshot']) && strlen($row_userpage_user_profile['favorite_screenshot']) < 5) {
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user_profile['pid']) {
print '<div class="user-profile-memo-container no-profile-memo">Your favorite post can be displayed here.</div>
'; } else {
print '<div class="user-profile-memo-container no-profile-memo"></div>'; }
}
else {
$result_posts_getfavoritepost = mysqli_query($mysql, 'SELECT * FROM posts WHERE posts.id = "'.mysqli_real_escape_string($mysql, $row_userpage_user_profile['favorite_screenshot']).'"');
print '<a href="/posts/'.htmlspecialchars($row_userpage_user_profile['favorite_screenshot']).'" data-pjax="#body" class="user-profile-memo-container">
    <img src="'.htmlspecialchars(mysqli_fetch_assoc($result_posts_getfavoritepost)['screenshot']).'" class="user-profile-memo">
  </a>'; }

print '    <span class="icon-container'.$is_user_info_official_user.'"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'"><img src="'.$user_page_info_mii_face_output.'" class="icon"></a></span>
';
if($row_userpage_user['official_user'] == 1) {
print '<p class="user-organization">'.htmlspecialchars($row_userpage_user['organization']).'</p>'; }
print '  <p class="title">
    <span class="nick-name">'.htmlspecialchars($row_userpage_user['screen_name']).'</span>
    <span class="id-name">'.htmlspecialchars($row_userpage_user['user_id']).'</span>
  </p>
  
  ';

if($row_userpage_user['official_user'] == 1) {
$is_identified_user_value = ' data-is-identified="1"'; }
else {
$is_identified_user_value = ''; }
 
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && 2 >= $row_userpage_user['status']) {
$if_user_follow_can = ''; }
else {
$if_user_follow_can = ' disabled'; }	

if(isset($row_userpage_me)) {
$sql_search_relationship = 'SELECT * FROM relationships WHERE relationships.source = "'.$row_userpage_me['pid'].'" AND relationships.target = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_search_relationship = mysqli_query($mysql, $sql_search_relationship);

if(mysqli_num_rows($result_search_relationship) != 0) {
$relationship_has_follow = ' none';
$relationship_has_unfollow = '';     }
else {
$relationship_has_follow = '';
$relationship_has_unfollow = ' none'; }
} else {
$relationship_has_follow = '';
$relationship_has_unfollow = ' none'; }

if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
print '<a href="/settings/profile" data-pjax="#body" class="button edit-button">Profile Settings</a>'; }
else {
print '<div class="toggle-button">
    <a class="follow-button button add-button'.$relationship_has_follow.' relationship-button'.$if_user_follow_can.'" href="#" data-action="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/follow" data-sound="SE_WAVE_FRIEND_ADD" data-community-id="" data-url-id="" data-track-label="user" data-title-id="" data-track-action="follow" data-track-category="follow">Follow</a>
    <a href="#" class="unfollow-button button remove-button'.$relationship_has_unfollow.' relationship-button" data-modal-open="#unfollow-confirm-page" data-user-id="'.htmlspecialchars($row_userpage_user['user_id']).'" data-screen-name="'.htmlspecialchars($row_userpage_user['screen_name']).'" data-mii-face-url="'.$user_page_info_mii_face_output.'" data-action="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/unfollow"'.$is_identified_user_value.'" data-community-id="" data-url-id="" data-track-label="user" data-title-id="" data-track-action="openUnfollowModal" data-track-category="follow">Follow</a>
</div>';	
}
// Make this better
if(isset($row_userpage_user_profile['pid']) || !isset($_SESSION['pid'])) {
$has_can_friend_request = true; } else {
$has_can_friend_request = false; }

if(isset($_SESSION['pid']) && $row_userpage_me['pid'] != $row_userpage_user['pid']) {
if($has_can_friend_request = false) {
print '
<div class="button-with-option dropdown">
            <a class="main-button friend-request-button disabled">Friend Request</a>
        <div class="dropdown-menu">
        </div>
</div>'; }
else {
if($has_can_friend_request = true) {
$sql_search_friend_request = 'SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$row_userpage_me['pid'].'" AND friend_requests.recipient = "'.$row_userpage_user['pid'].'" AND friend_requests.finished = "0"';
$result_search_friend_request = mysqli_query($mysql, $sql_search_friend_request);
$row_pending_friend_request = mysqli_fetch_assoc($result_search_friend_request);
$amt_rows_search_fr = mysqli_num_rows($result_search_friend_request);
if($amt_rows_search_fr >= 1) {
print '
<div class="button-with-option dropdown">
          <a href="#" class="main-button friend-requested-button dropdown-toggle main-option-button" data-toggle="dropdown" data-sound="SE_WAVE_BALLOON_OPEN">Request Pending</a>
        <div class="dropdown-menu">
            <a href="#" class="button cancel-request-button relationship-button" data-modal-open="#sent-request-confirm-page" '.($row_userpage_user['official_user'] == 1 ? 'data-is-identified="1" ': '').'data-user-id="'.htmlspecialchars($row_userpage_user['user_id']).'" data-screen-name="'.htmlspecialchars($row_userpage_user['screen_name']).'" data-mii-face-url="'.$user_page_info_mii_face_output.'" data-pid="'.$row_userpage_user['pid'].'" data-body="'.htmlspecialchars($row_pending_friend_request['message']).'" data-timestamp="'.date("m/d/Y g:i A",strtotime($row_pending_friend_request['created_at'])).'" data-sound="SE_WAVE_OK_SUB">Check Request</a>
        </div>
</div>
';
}
$frcheck = $mysql->query('SELECT * FROM friend_requests WHERE friend_requests.recipient = "'.$_SESSION['pid'].'" AND friend_requests.sender = "'.$row_userpage_user['pid'].'"');
if(mysqli_num_rows(mysqli_query($mysql, 'SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$row_userpage_me['pid'].'" AND friend_requests.recipient = "'.$row_userpage_user['pid'].'" AND friend_requests.finished = "0"')) == 0 && mysqli_num_rows(mysqli_query($mysql, 'SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$row_userpage_me['pid'].'" AND friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" AND friend_relationships.target = "'.$row_userpage_me['pid'].'"')) == 0 && $frcheck->num_rows == 0) {
print '
<div class="button-with-option dropdown">
            <a href="#" data-modal-open="#friend-request-post-page" class="main-button friend-request-button" data-sound="SE_WAVE_FRIEND_ADD">Friend Request</a>
        <div class="dropdown-menu">
        </div>
      </div>';
}
if(mysqli_num_rows($result_search_friend_request) <= 0) {
$sql_friend_relationship = 'SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$row_userpage_me['pid'].'" AND friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" AND friend_relationships.target = "'.$row_userpage_me['pid'].'"';
$result_friend_relationship = mysqli_query($mysql, $sql_friend_relationship);
if(mysqli_num_rows($result_friend_relationship) == 1) {
$row_friend_relationship = mysqli_fetch_assoc($result_friend_relationship);
print '
<div class="button-with-option dropdown">
          <a href="#" class="friend-button dropdown-toggle main-option-button" data-toggle="dropdown" data-sound="SE_WAVE_BALLOON_OPEN">Friends</a>
        <div class="dropdown-menu">
            <a href="#" class="button breakup-button relationship-button" data-modal-open="#breakup-confirm-page" '.($row_userpage_user['official_user'] == 1 ? 'data-is-identified="1" ': '').'data-user-id="'.htmlspecialchars($row_userpage_user['user_id']).'" data-screen-name="'.htmlspecialchars($row_userpage_user['screen_name']).'" data-mii-face-url="'.$user_page_info_mii_face_output.'" data-pid="'.$row_userpage_user['pid'].'" data-sound="SE_WAVE_OK_SUB">Remove Friend</a>
        </div>
      </div>';
}	}
if($mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$_SESSION['pid'].'" OR friend_relationships.target = "'.$_SESSION['pid'].'"')->num_rows == 0 && $frcheck->num_rows >=1) {
print '<div class="button-with-option dropdown">
          <a href="#" class="main-button friend-request-button relationship-button" data-modal-open="#received-request-confirm-page" '.($row_userpage_user['official_user'] == 1 ? 'data-is-identified="1" ': '').'data-user-id="'.htmlspecialchars($row_userpage_user['user_id']).'" data-screen-name="'.htmlspecialchars($row_userpage_user['screen_name']).'" data-mii-face-url="'.$user_page_info_mii_face_output.'" data-pid="'.$row_userpage_user['pid'].'" data-body="'.htmlspecialchars($frcheck->fetch_assoc()['message']).'">View Friend Request</a>
        <div class="dropdown-menu">
        </div>
      </div>';
}
	
} } }

# End user-info info-content
print '</div>';

print '<menu class="user-menu tab-header">
  <li class="test-user-posts-count tab-button-profile"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/posts" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Posts</span><span class="number">'.mysqli_num_rows($result_userpage_user_posts).'</span></a></li>
  <li class="test-user-friends-count tab-button-activity"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/friends" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Friends</span><span class="number">'.mysqli_num_rows($result_userpage_user_friends).' / 100</span></a></li>
  <li class="test-user-followings-count tab-button-activity"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/following" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Following</span><span class="number"><span class="js-following-count">'.mysqli_num_rows($result_userpage_user_following).'</span> / 1000</span></a></li>
  <li class="test-user-followers-count tab-button-relationship"><a href="/users/'.htmlspecialchars($row_userpage_user['user_id']).'/followers" data-pjax="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="label">Followers</span><span class="number">'.mysqli_num_rows($result_userpage_user_followers).'</span></a></li>
</menu>';

if(mysqli_num_rows($result_userpage_user_profile) == 0) {
$userpage_profile_comment = '';
$userpage_profile_country = 'Not Set'; }
else {
$userpage_profile_comment = $row_userpage_user_profile['comment']; 
$userpage_profile_country = $row_userpage_user_profile['country'];
}

if(empty($userpage_profile_country)) {
$userpage_profile_country = 'Not Set'; }

if($row_userpage_user_profile['game_experience']) {
if($row_userpage_user_profile['game_experience'] == "0") {
$user_profile_experience_style_output = 'beginner';
$user_profile_experience_text_output = 'Beginner      '; }
if($row_userpage_user_profile['game_experience'] == "1") {
$user_profile_experience_style_output = 'intermediate';
$user_profile_experience_text_output = 'Intermediate      '; }
if($row_userpage_user_profile['game_experience'] == "2") {
$user_profile_experience_style_output = 'expert';
$user_profile_experience_text_output = 'Expert      '; }
}
else {
$user_profile_experience_style_output = 'beginner';
$user_profile_experience_text_output = 'Beginner      '; }

if($row_userpage_user_profile['gender']) {
if($row_userpage_user_profile['gender'] == '1') {
$user_profile_sex_text_output = 'Male      '; }
if($row_userpage_user_profile['gender'] == '2') {
$user_profile_sex_text_output = 'Female      '; }
if($row_userpage_user_profile['gender'] == '3') {
$user_profile_sex_text_output = 'N/A      '; }
}
else {
$user_profile_sex_text_output = 'Not Set      '; }


if($row_userpage_user_profile['platform_id']) {
if($row_userpage_user_profile['platform_id'] == "0") {
$user_profile_platform_style_output = '3ds';
$user_profile_platform_text_output = 'System in the Nintendo 3DS Family'; }
if($row_userpage_user_profile['platform_id'] == "1") {
$user_profile_platform_style_output = 'wiiu';
$user_profile_platform_text_output = 'Wii U'; }
}
else {
$user_profile_platform_style_output = 'wiiu';
$user_profile_platform_text_output = 'Wii U'; }

print '<div class="tab-body">

	<div class="profile-content">

	
	
	
	
	';
if(mb_strlen($userpage_profile_comment, 'utf-8') >= 1) {
print '<p class="text">'.htmlspecialchars($userpage_profile_comment).'</p>'; }
 print '
  <div class="user-data"><table>
    <tbody><tr>
      <th><span>User Region</span></th>
      <td><span>'.$userpage_profile_country.'</span></td>
	  <th class="birthday">
	  <span>Gender</span></th>
	  <td>
        <span>'.$user_profile_sex_text_output.'</span>
      </td>
    </tr>
    <tr class="game-skill">
      <th><span>Game Experience</span></th>
      <td>
        <span class="'.$user_profile_experience_style_output.'">'.$user_profile_experience_text_output.'</span></td>
    </tr>
    <tr class="game">
	';
print '      <th><span>Systems Owned</span></th>
      <td><span class="device-'.$user_profile_platform_style_output.'">'.$user_profile_platform_text_output.'</span>
      </td>
    </tr>
  </tbody></table></div>
</div>
';
  
print '<div class="favorite-communities scroll">
  <h2 class="headline">Favorite Communities</h2>
  <ul class="list-content-with-icon arrow-list">
  ';
$get_user_favorite_communities = mysqli_fetch_all(mysqli_query($mysql, 'select a.*, bm.* from (select * from communities group by community_id) bm inner join favorites a on bm.community_id = a.community_id WHERE a.pid = "'.$row_userpage_user['pid'].'" ORDER BY a.created_at DESC'), MYSQLI_ASSOC);
require_once 'lib/community-template.php';
$empty_community = array(1 => 'dummy');

	print favoriteWithIcon((isset($get_user_favorite_communities[0]) ? $get_user_favorite_communities[0] : $empty_community), (!empty($get_user_favorite_communities[0]['community_id']) ? true : false));
 
	print favoriteWithIcon((isset($get_user_favorite_communities[1]) ? $get_user_favorite_communities[1] : $empty_community), (!empty($get_user_favorite_communities[1]['community_id']) ? true : false));

	print favoriteWithIcon((isset($get_user_favorite_communities[2]) ? $get_user_favorite_communities[2] : $empty_community), (!empty($get_user_favorite_communities[2]['community_id']) ? true : false));

	print favoriteWithIcon((isset($get_user_favorite_communities[3]) ? $get_user_favorite_communities[3] : $empty_community), (!empty($get_user_favorite_communities[3]['community_id']) ? true : false));

	print favoriteWithIcon((isset($get_user_favorite_communities[4]) ? $get_user_favorite_communities[4] : $empty_community), (!empty($get_user_favorite_communities[4]['community_id']) ? true : false));
  
	print favoriteWithIcon((isset($get_user_favorite_communities[5]) ? $get_user_favorite_communities[5] : $empty_community), (!empty($get_user_favorite_communities[5]['community_id']) ? true : false));

	print favoriteWithIcon((isset($get_user_favorite_communities[6]) ? $get_user_favorite_communities[6] : $empty_community), (!empty($get_user_favorite_communities[6]['community_id']) ? true : false));

	print favoriteWithIcon((isset($get_user_favorite_communities[7]) ? $get_user_favorite_communities[7] : $empty_community), (!empty($get_user_favorite_communities[7]['community_id']) ? true : false));
	print '
    <li>
      <a href="'.(isset($_SESSION['pid']) && !empty($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid'] ? '/communities/favorites' : '/users/'.htmlspecialchars($row_userpage_user['user_id']).'/favorites').'" data-pjax="#body" class="arrow-button"></a>
    </li>
  </ul>
</div>







  



 
 </div>
  ';

include 'lib/user-page-footer.php';
# End body-content user-page
print '</div>';
	print $GLOBALS['div_body_head_end'];	
	printFooter();
      }
	}
  }
}
