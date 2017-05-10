<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

$search_user = $mysql->query('SELECT * FROM people WHERE people.user_id = "'.(empty($_GET['user_id']) ? 'a' : $mysql->real_escape_string($_GET['user_id'])).'"');

if(isset($_GET['mode']) && $_GET['mode'] == 'posts') {
# Display posts for user.
if(!$search_user || $search_user->num_rows == 0) {
generalError(404, 'The user could not be found.'); grpfinish($mysql); exit(); }

$user = $search_user->fetch_assoc();
$mii = getMii($user, false);

require_once 'lib/htmUser.php';
$own_page = !empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'];
$pagetitle = ($own_page ? 'User Page' : htmlspecialchars($user['screen_name']).'\'s Profile');

printHeader(false); printMenu();
    print $GLOBALS['div_body_head'];
	print '<header id="header">

	<h1 id="page-title">'.$pagetitle.'</h1>

</header>
';

#Begin body-content user-page
print '<div class="body-content user-page'.(!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'] ? ' is-visitor' : '').'">
';

userInfo($user, $profile, $mii, 'posts');

userNavTab($user, 'posts');
userPostNavTab($user, 'posts');

$sql_userpage_user_posts_view = 'SELECT * FROM posts WHERE pid = "'.$row_userpage_user['pid'].'" AND hidden_resp = 0 OR hidden_resp IS NULL AND pid = "'.$row_userpage_user['pid'].'" ORDER BY posts.created_at DESC LIMIT 50';
$result_userpage_user_posts_view = $mysql->query($sql_userpage_user_posts_view);

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

$sql_userpage_user_posts_view_user = 'SELECT * FROM people WHERE people.pid = "'.$row_userpage_user_posts_view['pid'].'"';
$result_userpage_user_posts_view_user = $mysql->query($sql_userpage_user_posts_view_user);
$row_userpage_user_posts_view_user = mysqli_fetch_assoc($result_userpage_user_posts_view_user); 

$sql_userpage_user_posts_view_replies = 'SELECT * FROM replies WHERE replies.reply_to_id = "'.$row_userpage_user_posts_view['id'].'" AND replies.is_hidden != "1"';
$result_userpage_user_posts_view_replies = $mysql->query($sql_userpage_user_posts_view_replies);

$sql_userpage_user_posts_view_empathies = 'SELECT * FROM empathies WHERE empathies.id = "'.$row_userpage_user_posts_view['id'].'"';
$result_userpage_user_posts_view_empathies = $mysql->query($sql_userpage_user_posts_view_empathies);

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
grpfinish($mysql); exit();
  }
if(isset($_GET['mode']) && $_GET['mode'] == 'empathies') {
# Display user's yeahed posts.
# Display standard user page.
$sql_userpage_user = 'SELECT * FROM people WHERE people.user_id = "'.$mysql->real_escape_string($_GET['user_id']).'"';
$result_userpage_user = $mysql->query($sql_userpage_user);
$row_userpage_user = mysqli_fetch_assoc($result_userpage_user);

if(isset($_SESSION['signed_in'])) {
$sql_userpage_me = 'SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'"';
$result_userpage_me = $mysql->query($sql_userpage_me);
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
<h1 id="page-title" class="left">'.$pagetitle.'</h1>
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
<h1 id="page-title" class="left">'.$pagetitle.'</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The user could not be found.' );
include 'lib/no-content-window.php';
    }
    else
    {
		
			if(isset($_GET['offset']) && is_numeric($_GET['offset']) && strlen($_GET['offset']) >= 1) {
		
		
$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "'.$row_userpage_user['pid'].'" AND posts.is_hidden != "1"';
$result_userpage_user_posts = $mysql->query($sql_userpage_user_posts);

$sql_userpage_user_empathies = 'SELECT * FROM empathies WHERE empathies.pid = "'.$row_userpage_user['pid'].'" ORDER BY empathies.created_at DESC';
$result_userpage_user_empathies = $mysql->query($sql_userpage_user_empathies);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = $mysql->query($sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_userpage_user_following = $mysql->query($sql_userpage_user_following);

$sql_userpage_user_empathies_view = 'SELECT * FROM empathies WHERE empathies.pid = "'.$row_userpage_user['pid'].'" ORDER BY empathies.created_at DESC LIMIT 20 OFFSET "'.$mysql->real_escape_string($_GET['offset']).'"'.'';
$result_userpage_user_empathies_view = $mysql->query($sql_userpage_user_empathies_view);

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
$result_userpage_user_empathies_view_posts = $mysql->query($sql_userpage_user_empathies_view_posts); 	
$row_userpage_user_empathies_view_posts = mysqli_fetch_assoc($result_userpage_user_empathies_view_posts); 	

$sql_userpage_user_empathies_view_postsuser = 'SELECT * FROM people where people.pid = "'.$row_userpage_user_empathies_view_posts['pid'].'"';
$result_userpage_user_empathies_view_postsuser = $mysql->query($sql_userpage_user_empathies_view_postsuser); 
$row_userpage_user_empathies_view_postsuser = mysqli_fetch_assoc($result_userpage_user_empathies_view_postsuser);

$sql_userpage_user_empathies_view_postsreplies = 'SELECT * FROM replies WHERE replies.reply_to_id = "'.$row_userpage_user_empathies_view_posts['id'].'" AND replies.is_hidden != "1"';
$result_userpage_user_empathies_view_postsreplies = $mysql->query($sql_userpage_user_empathies_view_postsreplies);

$sql_userpage_user_empathies_view_postsempathies = 'SELECT * FROM empathies WHERE empathies.id = "'.$row_userpage_user_empathies_view_posts['id'].'"';
$result_userpage_user_empathies_view_postsempathies = $mysql->query($sql_userpage_user_empathies_view_postsempathies);



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

$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "'.$row_userpage_user['pid'].'" AND posts.is_hidden != "1"';
$result_userpage_user_posts = $mysql->query($sql_userpage_user_posts);

$sql_userpage_user_empathies = 'SELECT * FROM empathies WHERE empathies.pid = "'.$row_userpage_user['pid'].'" ORDER BY empathies.created_at DESC';
$result_userpage_user_empathies = $mysql->query($sql_userpage_user_empathies);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = $mysql->query($sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_userpage_user_following = $mysql->query($sql_userpage_user_following);

$sql_userpage_user_empathies = 'SELECT * FROM empathies WHERE empathies.pid = "'.$row_userpage_user['pid'].'" ORDER BY empathies.created_at DESC';
$result_userpage_user_empathies = $mysql->query($sql_userpage_user_empathies);

$sql_userpage_user_empathies_view = 'SELECT * FROM empathies WHERE empathies.pid = "'.$row_userpage_user['pid'].'" ORDER BY empathies.created_at DESC LIMIT 20';
$result_userpage_user_empathies_view = $mysql->query($sql_userpage_user_empathies_view);

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
$result_userpage_user_empathies_view_posts = $mysql->query($sql_userpage_user_empathies_view_posts); 	
$row_userpage_user_empathies_view_posts = mysqli_fetch_assoc($result_userpage_user_empathies_view_posts); 	

if(mysqli_num_rows($result_userpage_user_empathies_view_posts) == 0) {
print null;
}

$sql_userpage_user_empathies_view_postsuser = 'SELECT * FROM people where people.pid = "'.$row_userpage_user_empathies_view_posts['pid'].'"';
$result_userpage_user_empathies_view_postsuser = $mysql->query($sql_userpage_user_empathies_view_postsuser); 
$row_userpage_user_empathies_view_postsuser = mysqli_fetch_assoc($result_userpage_user_empathies_view_postsuser);

$sql_userpage_user_empathies_view_postsreplies = 'SELECT * FROM replies WHERE replies.reply_to_id = "'.$row_userpage_user_empathies_view_posts['id'].'"';
$result_userpage_user_empathies_view_postsreplies = $mysql->query($sql_userpage_user_empathies_view_postsreplies);

$sql_userpage_user_empathies_view_postsempathies = 'SELECT * FROM empathies WHERE empathies.id = "'.$row_userpage_user_empathies_view_posts['id'].'"';
$result_userpage_user_empathies_view_postsempathies = $mysql->query($sql_userpage_user_empathies_view_postsempathies);


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
$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "'.$row_userpage_user['pid'].'" AND posts.is_hidden != "1"';
$result_userpage_user_posts = $mysql->query($sql_userpage_user_posts);

$sql_userpage_user_empathies = 'SELECT * FROM empathies WHERE empathies.pid = "'.$row_userpage_user['pid'].'" ORDER BY empathies.created_at DESC';
$result_userpage_user_empathies = $mysql->query($sql_userpage_user_empathies);

$sql_userpage_user_friends = 'SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" ORDER BY friend_relationships.relationship_id LIMIT 100';
$result_userpage_user_friends = $mysql->query($sql_userpage_user_friends);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = $mysql->query($sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_userpage_user_following = $mysql->query($sql_userpage_user_following);
	
	

$sql_userpage_user_profile = 'SELECT * FROM profiles WHERE profiles.pid = "'.$row_userpage_user['pid'].'"';
$result_userpage_user_profile = $mysql->query($sql_userpage_user_profile);
$row_userpage_user_profile = mysqli_fetch_assoc($result_userpage_user_profile); 
  
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid'] && mysqli_num_rows($result_userpage_user_profile) == 0) {
$mysql->query("INSERT INTO
                    profiles(pid, platform_id)
                VALUES('" . $mysql->real_escape_string($_SESSION['pid']) . "',
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

$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "'.$row_userpage_user['pid'].'" AND posts.is_hidden != "1"';
$result_userpage_user_posts = $mysql->query($sql_userpage_user_posts);

$sql_userpage_user_empathies = 'SELECT * FROM empathies WHERE empathies.pid = "'.$row_userpage_user['pid'].'" ORDER BY empathies.created_at DESC';
$result_userpage_user_empathies = $mysql->query($sql_userpage_user_empathies);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = $mysql->query($sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_userpage_user_following = $mysql->query($sql_userpage_user_following);



#Begin body-content user-page
print '<div class="body-content user-page'.$is_user_me_page_visitor.'">
';
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
print '<a id="header-mymenu-button" href="/my_menu" data-pjax="#body">User Menu</a>
'; }


		if($row_userpage_user['mii_hash']) {
$user_page_info_mii_face_output = 'https://mii-secure.cdn.nintendo.net/'.$row_userpage_user['mii_hash'].'_normal_face.png'; }
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
$result_posts_getfavoritepost = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$mysql->real_escape_string($row_userpage_user_profile['favorite_screenshot']).'"');
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

$sql_userpage_user_empathies_view = 'SELECT * FROM empathies WHERE empathies.pid = "'.$row_userpage_user['pid'].'" ORDER BY empathies.created_at DESC LIMIT 20';
$result_userpage_user_empathies_view = $mysql->query($sql_userpage_user_empathies_view);
 
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
$result_userpage_user_empathies_view_posts = $mysql->query($sql_userpage_user_empathies_view_posts); 	
$row_userpage_user_empathies_view_posts = mysqli_fetch_assoc($result_userpage_user_empathies_view_posts); 	

if(mysqli_num_rows($result_userpage_user_empathies_view_posts) == 0) {
print null;
}

$sql_userpage_user_empathies_view_postsuser = 'SELECT * FROM people where people.pid = "'.$row_userpage_user_empathies_view_posts['pid'].'"';
$result_userpage_user_empathies_view_postsuser = $mysql->query($sql_userpage_user_empathies_view_postsuser); 
$row_userpage_user_empathies_view_postsuser = mysqli_fetch_assoc($result_userpage_user_empathies_view_postsuser);

$sql_userpage_user_empathies_view_postsreplies = 'SELECT * FROM replies WHERE replies.reply_to_id = "'.$row_userpage_user_empathies_view_posts['id'].'"';
$result_userpage_user_empathies_view_postsreplies = $mysql->query($sql_userpage_user_empathies_view_postsreplies);

$sql_userpage_user_empathies_view_postsempathies = 'SELECT * FROM empathies WHERE empathies.id = "'.$row_userpage_user_empathies_view_posts['id'].'"';
$result_userpage_user_empathies_view_postsempathies = $mysql->query($sql_userpage_user_empathies_view_postsempathies);


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
grpfinish($mysql); exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'following') {
# Display standard user page.
$sql_userpage_user = 'SELECT * FROM people WHERE people.user_id = "'.$mysql->real_escape_string($_GET['user_id']).'"';
$result_userpage_user = $mysql->query($sql_userpage_user);
$row_userpage_user = mysqli_fetch_assoc($result_userpage_user);

if(isset($_SESSION['signed_in'])) {
$sql_userpage_me = 'SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'"';
$result_userpage_me = $mysql->query($sql_userpage_me);
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
<h1 id="page-title" class="left">'.$pagetitle.'</h1>
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
<h1 id="page-title" class="left">'.$pagetitle.'</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The user could not be found.' );
include 'lib/no-content-window.php';
    }
    else
    {
$sql_userpage_user_profile = 'SELECT * FROM profiles WHERE profiles.pid = "'.$row_userpage_user['pid'].'"';
$result_userpage_user_profile = $mysql->query($sql_userpage_user_profile);
$row_userpage_user_profile = mysqli_fetch_assoc($result_userpage_user_profile); 

// Uncomment when /welcome/profile is implemented, please!
  
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid'] && mysqli_num_rows($result_userpage_user_profile) == 0) {
$mysql->query("INSERT INTO
                    profiles(pid, platform_id)
                VALUES('" . $mysql->real_escape_string($_SESSION['pid']) . "',
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

$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "'.$row_userpage_user['pid'].'" AND posts.is_hidden != "1"';
$result_userpage_user_posts = $mysql->query($sql_userpage_user_posts);

$sql_userpage_user_friends = 'SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" ORDER BY friend_relationships.relationship_id LIMIT 100';
$result_userpage_user_friends = $mysql->query($sql_userpage_user_friends);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = $mysql->query($sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_userpage_user_following = $mysql->query($sql_userpage_user_following);

#Begin body-content user-page
print '<div class="body-content user-page'.$is_user_me_page_visitor.'">
';
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
print '<a id="header-mymenu-button" href="/my_menu" data-pjax="#body">User Menu</a>
'; }


		if($row_userpage_user['mii_hash']) {
$user_page_info_mii_face_output = 'https://mii-secure.cdn.nintendo.net/'.$row_userpage_user['mii_hash'].'_normal_face.png'; }
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
$result_posts_getfavoritepost = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$mysql->real_escape_string($row_userpage_user_profile['favorite_screenshot']).'"');
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
$result_search_relationships = $mysql->query($sql_search_relationships);

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
$result_relationships_users = $mysql->query($sql_relationships_users);
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
grpfinish($mysql); exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'followers') {
# Display standard user page.
$sql_userpage_user = 'SELECT * FROM people WHERE people.user_id = "'.$mysql->real_escape_string($_GET['user_id']).'"';
$result_userpage_user = $mysql->query($sql_userpage_user);
$row_userpage_user = mysqli_fetch_assoc($result_userpage_user);

if(isset($_SESSION['signed_in'])) {
$sql_userpage_me = 'SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'"';
$result_userpage_me = $mysql->query($sql_userpage_me);
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
<h1 id="page-title" class="left">'.$pagetitle.'</h1>
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
<h1 id="page-title" class="left">'.$pagetitle.'</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The user could not be found.' );
include 'lib/no-content-window.php';
    }
    else
    {
$sql_userpage_user_profile = 'SELECT * FROM profiles WHERE profiles.pid = "'.$row_userpage_user['pid'].'"';
$result_userpage_user_profile = $mysql->query($sql_userpage_user_profile);
$row_userpage_user_profile = mysqli_fetch_assoc($result_userpage_user_profile); 

// Uncomment when /welcome/profile is implemented, please!
  
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid'] && mysqli_num_rows($result_userpage_user_profile) == 0) {
$mysql->query("INSERT INTO
                    profiles(pid, platform_id)
                VALUES('" . $mysql->real_escape_string($_SESSION['pid']) . "',
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

$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "'.$row_userpage_user['pid'].'" AND posts.is_hidden != "1"';
$result_userpage_user_posts = $mysql->query($sql_userpage_user_posts);

$sql_userpage_user_friends = 'SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" ORDER BY friend_relationships.relationship_id LIMIT 100';
$result_userpage_user_friends = $mysql->query($sql_userpage_user_friends);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = $mysql->query($sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_userpage_user_following = $mysql->query($sql_userpage_user_following);

#Begin body-content user-page
print '<div class="body-content user-page'.$is_user_me_page_visitor.'">
';
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
print '<a id="header-mymenu-button" href="/my_menu" data-pjax="#body">User Menu</a>
'; }


		if($row_userpage_user['mii_hash']) {
$user_page_info_mii_face_output = 'https://mii-secure.cdn.nintendo.net/'.$row_userpage_user['mii_hash'].'_normal_face.png'; }
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
$result_posts_getfavoritepost = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$mysql->real_escape_string($row_userpage_user_profile['favorite_screenshot']).'"');
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
$result_search_relationships = $mysql->query($sql_search_relationships);

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
$result_relationships_users = $mysql->query($sql_relationships_users);
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
grpfinish($mysql); exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'friends') {
# Display standard user page.
$sql_userpage_user = 'SELECT * FROM people WHERE people.user_id = "'.$mysql->real_escape_string($_GET['user_id']).'"';
$result_userpage_user = $mysql->query($sql_userpage_user);
$row_userpage_user = mysqli_fetch_assoc($result_userpage_user);

if(isset($_SESSION['signed_in'])) {
$sql_userpage_me = 'SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'"';
$result_userpage_me = $mysql->query($sql_userpage_me);
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
<h1 id="page-title" class="left">'.$pagetitle.'</h1>
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
<h1 id="page-title" class="left">'.$pagetitle.'</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The user could not be found.' );
include 'lib/no-content-window.php';
    }
    else
    {
$sql_userpage_user_profile = 'SELECT * FROM profiles WHERE profiles.pid = "'.$row_userpage_user['pid'].'"';
$result_userpage_user_profile = $mysql->query($sql_userpage_user_profile);
$row_userpage_user_profile = mysqli_fetch_assoc($result_userpage_user_profile); 

// Uncomment when /welcome/profile is implemented, please!
  
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid'] && mysqli_num_rows($result_userpage_user_profile) == 0) {
$mysql->query("INSERT INTO
                    profiles(pid, platform_id)
                VALUES('" . $mysql->real_escape_string($_SESSION['pid']) . "',
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

$sql_userpage_user_posts = 'SELECT * FROM posts WHERE posts.pid = "'.$row_userpage_user['pid'].'" AND posts.is_hidden != "1"';
$result_userpage_user_posts = $mysql->query($sql_userpage_user_posts);

$sql_userpage_user_friends = 'SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$row_userpage_user['pid'].'" OR friend_relationships.source = "'.$row_userpage_user['pid'].'" ORDER BY friend_relationships.relationship_id LIMIT 100';
$result_userpage_user_friends = $mysql->query($sql_userpage_user_friends);

$sql_userpage_user_followers = 'SELECT * FROM relationships WHERE relationships.target = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_userpage_user_followers = $mysql->query($sql_userpage_user_followers);

$sql_userpage_user_following = 'SELECT * FROM relationships WHERE relationships.source = "'.$row_userpage_user['pid'].'" AND relationships.is_me2me = "0"';
$result_userpage_user_following = $mysql->query($sql_userpage_user_following);

#Begin body-content user-page
print '<div class="body-content user-page'.$is_user_me_page_visitor.'">
';
if(isset($_SESSION['pid']) && $_SESSION['pid'] == $row_userpage_user['pid']) {
print '<a id="header-mymenu-button" href="/my_menu" data-pjax="#body">User Menu</a>
'; }


		if($row_userpage_user['mii_hash']) {
$user_page_info_mii_face_output = 'https://mii-secure.cdn.nintendo.net/'.$row_userpage_user['mii_hash'].'_normal_face.png'; }
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
$result_posts_getfavoritepost = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$mysql->real_escape_string($row_userpage_user_profile['favorite_screenshot']).'"');
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
$result_search_friend_relationships = $mysql->query($sql_search_friend_relationships);

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
$result_search_friend_relationships1 = $mysql->query($sql_search_friend_relationships1);
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
$result_search_friend_request = $mysql->query($sql_search_friend_request);
if(mysqli_num_rows($result_search_friend_request) != 0) {
while($row_search_friend_request = mysqli_fetch_assoc($result_search_friend_request)) {

$sql_relationships_users = 'SELECT * FROM people WHERE people.pid = "'.$row_search_friend_request['recipient'].'"';
$result_relationships_users = $mysql->query($sql_relationships_users);
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
grpfinish($mysql); exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'favorites') {
# Search for user first, then use template
$sql_userpage_user = 'SELECT * FROM people WHERE people.user_id = "'.$mysql->real_escape_string($_GET['user_id']).'"';
$result_userpage_user = $mysql->query($sql_userpage_user);
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
<h1 id="page-title" class="left">'.$pagetitle.'</h1>
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
<h1 id="page-title" class="left">'.$pagetitle.'</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The user could not be found.' );
include 'lib/no-content-window.php';
    }
}
grpfinish($mysql); exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'follow') {
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
$sql_post_getuser = 'SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'"';
$result_post_getuser = $mysql->query($sql_post_getuser);
$row_post_getuser = mysqli_fetch_assoc($result_post_getuser); 

$sql_post_getotheruser = 'SELECT * FROM people WHERE people.pid = "'.$mysql->real_escape_string($_GET['user_id']).'"';
$result_post_getotheruser = $mysql->query($sql_post_getotheruser);
$row_post_getotheruser = mysqli_fetch_assoc($result_post_getotheruser); 
		
        if(strval($row_post_getuser['status'] >= 2) || strval($row_post_getuser['empathy_restriction'] >= 1)) {
			$error_message[] = 'You are not permitted to follow other users.';
			$error_code[] = '1512006';
		}
}
$sql_userwho = 'SELECT * FROM people WHERE people.user_id = "'.$mysql->real_escape_string($_GET['user_id']).'"';
$result_userwho = $mysql->query($sql_userwho);
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
$result_search_relationship2 = $mysql->query($sql_search_relationship2);

if(strval(mysqli_num_rows($result_search_relationship2) >= 1)) {
			$error_message[] = 'You are already following this user.';
			$error_code[] = '1512013';	
}
	    if(!empty($error_code) || !empty($error_message) ) /*Got errors?*/
    {
		// JSON response for errors.
			http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
			print '{"success":0,"errors":[{"message":"'.$error_message[0].'","error_code":'.$error_code[0].'}],"code":"400"}';
			print "\n";
    }
    else {
$sql_post_getuforfollow = 'SELECT * FROM people WHERE people.user_id = "'.$mysql->real_escape_string($_GET['user_id']).'"';
$result_post_getuforfollow = $mysql->query($sql_post_getuforfollow);
$row_post_getuforfollow = mysqli_fetch_assoc($result_post_getuforfollow); 
// User checks over. Is eligible to follow.
        $sql_relationshipcreate = 'INSERT INTO relationships(source, target) VALUES ("'.$_SESSION['pid'].'", "'.$row_post_getuforfollow['pid'].'")';
		$sql_newscreate = 'INSERT INTO news(from_pid, to_pid, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$row_post_getuforfollow['pid'].'", "6", "0")';
        $result_relationshipcreate = $mysql->query($sql_relationshipcreate);
			// If the user gave the same type of notification 8 seconds ago, then don't send this.
	$result_check_fastnews = $mysql->query('SELECT news.to_pid, news.created_at FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_post_getuforfollow['pid'].'" AND news.news_context = "6" AND news.created_at > NOW() - 8 ORDER BY news.created_at DESC');
    if(mysqli_num_rows($result_check_fastnews) == 0) {
    $result_check_ownusernews = $mysql->query('SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_post_getuforfollow['pid'].'" AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
$row_check_ownusernews = mysqli_fetch_assoc($result_check_ownusernews);
	$result_check_mergedusernews = $mysql->query('SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$row_post_getuforfollow['pid'].'" AND news.merged IS NOT NULL AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
 if(mysqli_num_rows($result_check_mergedusernews) != 0) {
	$result_update_mergedusernewsagain = $mysql->query('UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.mysqli_fetch_assoc($result_check_mergedusernews)['merged'].'"');	
	}
	elseif(mysqli_num_rows($result_check_ownusernews) != 0) {
	$result_update_ownusernewsagain = $mysql->query('UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$row_check_ownusernews['news_id'].'"');
	}
else {
	$result_update_newsmergesearch = $mysql->query('SELECT * FROM news WHERE news.to_pid = "'.$row_post_getuforfollow['pid'].'" AND news.news_context = "6" AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
	if(mysqli_num_rows($result_update_newsmergesearch) != 0) {
$row_update_newsmergesearch = mysqli_fetch_assoc($result_update_newsmergesearch);
	
	$result_newscreatemerge = $mysql->query('INSERT INTO news(from_pid, to_pid, merged, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$row_post_getuforfollow['pid'].'", "'.$row_update_newsmergesearch['news_id'].'", "6", "0")');
$result_update_newsformerge = $mysql->query('UPDATE news SET has_read = "0", created_at = NOW() WHERE news.news_id = "'.$row_update_newsmergesearch['news_id'].'"');
		}
else {
        $result_newscreate = $mysql->query('INSERT INTO news(from_pid, to_pid, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$row_post_getuforfollow['pid'].'", "6", "0")'); 	
	} }
		
		
	}
        if(!$result_relationshipcreate)
        {
http_response_code(400);
header('Content-Type: application/json; charset=utf-8');
print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160'.mysqli_errno($mysql).'}],"code":"400"}';
print "\n";
	}
        else
        {
$sql_my_followingcount = 'SELECT * FROM relationships WHERE relationships.source = '.$_SESSION['pid'].'';
$result_my_followingcount = $mysql->query($sql_my_followingcount);
header('Content-Type: application/json; charset=utf-8');
print '{"success":1,"can_follow_more":true,"following_count":'.mysqli_num_rows($result_my_followingcount).'}';
print "\n";
        }
		
   }
  }
}
}
grpfinish($mysql); exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'unfollow') {
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
$sql_post_getuser = 'SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'"';
$result_post_getuser = $mysql->query($sql_post_getuser);
$row_post_getuser = mysqli_fetch_assoc($result_post_getuser); 

$sql_post_getotheruser = 'SELECT * FROM people WHERE people.pid = "'.$mysql->real_escape_string($_GET['user_id']).'"';
$result_post_getotheruser = $mysql->query($sql_post_getotheruser);
$row_post_getotheruser = mysqli_fetch_assoc($result_post_getotheruser); 
		
        if(strval($row_post_getuser['status'] >= 2) || strval($row_post_getuser['empathy_restriction'] >= 1)) {
			$error_message[] = 'You are not permitted to follow other users.';
			$error_code[] = '1512006';
		}
}
$sql_userwho = 'SELECT * FROM people WHERE people.user_id = "'.$mysql->real_escape_string($_GET['user_id']).'"';
$result_userwho = $mysql->query($sql_userwho);
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

$sql_userwho = 'SELECT * FROM people WHERE people.user_id = "'.$mysql->real_escape_string($_GET['user_id']).'"';
$result_userwho = $mysql->query($sql_userwho);

$sql_search_relationship = 'SELECT * FROM relationships WHERE relationships.source = "'.$row_post_getuser['pid'].'" AND relationships.target = "'.$row_userwho['pid'].'" AND relationships.is_me2me = "0"';
$result_search_relationship = $mysql->query($sql_search_relationship);

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
			print '{"success":0,"errors":[{"message":"'.$error_message[0].'","error_code":'.$error_code[0].'}],"code":"400"}';
			print "\n";
    }
    else {
$sql_post_getuforfollow = 'SELECT * FROM people WHERE people.user_id = "'.$mysql->real_escape_string($_GET['user_id']).'"';
$result_post_getuforfollow = $mysql->query($sql_post_getuforfollow);
$row_post_getuforfollow = mysqli_fetch_assoc($result_post_getuforfollow); 
// User checks over. Is eligible to follow.
        $sql_relationshipdelete = 'DELETE FROM relationships WHERE source = "'.$_SESSION['pid'].'" AND target = "'.$row_post_getuforfollow['pid'].'"';
        $result_relationshipdelete = $mysql->query($sql_relationshipdelete);
        if(!$result_relationshipdelete)
        {
http_response_code(400);
header('Content-Type: application/json; charset=utf-8');
print '{"success":0,"errors":[{"message":"A database error has occurred.\nPlease try again later, or report the\nerror code to the webmaster.","error_code":160'.mysqli_errno($mysql).'}],"code":"400"}';
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
grpfinish($mysql); exit();
}

if(isset($_GET['mode'])) { if($_GET['mode'] != 'posts' || $_GET['mode'] != 'empathies' || $_GET['mode'] != 'following' || $_GET['mode'] != 'followers' || $_GET['mode'] != 'favorites' || $_GET['mode'] != 'follow' || $_GET['mode'] != 'unfollow') { 
# Display 404 if mode is undefined
include_once '404.php'; grpfinish($mysql); exit(); } }

if(!$search_user || $search_user->num_rows == 0) {
generalError(404, 'The user could not be found.'); grpfinish($mysql); exit(); }

$user = $search_user->fetch_assoc();
$mii = getMii($user, false);
$search_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1');
if($search_profile->num_rows == 0) {
$createprofile = $mysql->query('INSERT INTO profiles(pid, platform_id) VALUES("'.$user['pid'].'", "'.$user['platform_id'].'")');
$profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1')->fetch_assoc();
} else {
$profile = $search_profile->fetch_assoc(); }
require_once 'lib/htmUser.php';
$own_page = !empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'];
$pagetitle = ($own_page ? 'User Page' : htmlspecialchars($user['screen_name']).'\'s Profile');

printHeader(false); printMenu();
    print $GLOBALS['div_body_head'];
	print '<header id="header">
';
userDropdown($user, $mii)."\n";
print '  <h1 id="page-title">'.$pagetitle.'</h1>

</header>
';

#Begin body-content user-page
print '<div class="body-content user-page'.(!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'] ? ' is-visitor' : '').'">
';

userInfo($user, $profile, $mii, false);

userNavTab($user, false);

if(isset($profile['game_experience'])) {
if($profile['game_experience'] == 0) { $pge_experience = 'beginner'; $pge_extext = 'Beginner'; }
elseif($profile['game_experience'] == 1) { $pge_experience = 'intermediate'; $pge_extext = 'Intermediate'; }
elseif($profile['game_experience'] == 2) { $pge_experience = 'expert'; $pge_extext = 'Expert'; }
else { $pge_experience = 'beginner'; $pge_extext = 'Beginner'; }
} else { $pge_experience = 'beginner'; $pge_extext = 'Beginner'; }

if(isset($profile['gender'])) {
if($profile['gender'] == 1) { $pge_sex = 'Male'; }
elseif($profile['gender'] == '2') { $pge_sex = 'Female'; }
else { $pge_sex = 'Not Set'; }
} else { $pge_sex = 'Not Set'; }

if(isset($profile['platform_id'])) {
if($profile['platform_id'] == 0) { $pge_platform = '3ds'; $pge_patext = 'Nintendo 3DS'; }
elseif($profile['platform_id'] == 1) { $pge_platform = 'wiiu'; $pge_patext = 'Wii U'; }
elseif($profile['platform_id'] == 2) { $pge_platform = 'wiiu'; $pge_patext = 'Off-Device'; }
else { $pge_platform = 'wiiu'; $pge_patext = 'Off-Device'; }
} else { $pge_platform = 'wiiu'; $pge_patext = 'Off-Device'; }

print '<div class="tab-body">

	<div class="profile-content">

	
	
	
	
	';
if(mb_strlen($profile['comment'], 'utf-8') >= 1) {
print '<p class="text">'.htmlspecialchars($profile['comment']).'</p>'; }
 print '
  <div class="user-data"><table>
    <tbody><tr>
      <th><span>User Region</span></th>
      <td><span>'.(empty($profile['country']) ? 'Not Set' : htmlspecialchars($profile['country'])).'</span></td>
	  <th class="birthday">
	  <span>Gender</span></th>
	  <td>
        <span>'.$pge_sex.'</span>
      </td>
    </tr>
    <tr class="game-skill">
      <th><span>Game Experience</span></th>
      <td>
        <span class="'.$pge_experience.'">'.$pge_extext.'</span></td>
    </tr>
    <tr class="game">
	';
print '      <th><span>Systems Owned</span></th>
      <td><span class="device-'.$pge_platform.'">'.$pge_patext.'</span>
      </td>
    </tr>
  </tbody></table></div>
</div>
';
  
print '<div class="favorite-communities scroll">
  <h2 class="headline">Favorite Communities</h2>
  <ul class="list-content-with-icon arrow-list">
  ';
require_once 'lib/htmCommunity.php';
$favorite_communities_search = $mysql->query('select a.*, bm.* from (select * from communities group by community_id) bm inner join favorites a on bm.community_id = a.community_id WHERE a.pid = "'.$user['pid'].'" ORDER BY a.created_at DESC');
while($favorite_communities = $favorite_communities_search->fetch_assoc()) {
print favoriteWithIcon($favorite_communities, true);
}
for($x=$favorite_communities_search->num_rows; $x<8; $x++){
print favoriteWithIcon([1], false);
}

	print '
    <li>
      <a href="'.($own_page ? '/communities/favorites' : '/users/'.htmlspecialchars($user['user_id']).'/favorites').'" data-pjax="#body" class="arrow-button"></a>
    </li>
  </ul>
</div>







  



 
 </div>
  ';

require_once 'lib/htmTemplates.php';
userPageTemplate($user, $mii);
# End body-content user-page
print '</div>';
	print $GLOBALS['div_body_head_end'];	
	printFooter();