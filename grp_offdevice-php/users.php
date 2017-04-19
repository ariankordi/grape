<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

$search_user = $mysql->query('SELECT * FROM people WHERE people.user_id = "'.(empty($_GET['user_id']) ? 'a' : $mysql->real_escape_string($_GET['user_id'])).'"');

if(isset($_GET['mode']) && $_GET['mode'] == 'posts') {
if($search_user->num_rows == 0) {
$pagetitle = 'Error'; printHeader('old'); printMenu('old'); notFound('users', false); printFooter('old'); grpfinish($mysql); exit();
}

$user = $search_user->fetch_assoc();
$mii = getMii($user, false);
$search_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1');
if($search_profile->num_rows == 0) {
$createprofile = $mysql->query('INSERT INTO profiles(pid, platform_id) VALUES("'.$user['pid'].'", "'.$user['platform_id'].'")');
} else {
$profile = $search_profile->fetch_assoc(); }
require_once 'lib/htmUser.php';
require_once 'lib/htmCommunity.php';
require_once '../grplib-php/community-helper.php';
if(!isset($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
$pagetitle = htmlspecialchars($user['screen_name']).'\'s Profile';
if(!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid']) { $mnselect = 'users'; }
printHeader('old'); printMenu('old'); print '
<div id="main-body">
';
# Start user-page
print '<div class="user-page">
';
userContent($user, $mii, 'posts', (isset($profile)) ? (!empty($profile['favorite_screenshot']) ? $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$profile['favorite_screenshot'].'" LIMIT 1')->fetch_assoc() : false) : false);
userNavMenu($user, 'posts');
print '
</div>
';
# End of user-page
tab2Activity($user, 'posts');
}
$search_user_posts = $mysql->query('SELECT * FROM posts WHERE posts.pid = "'.$user['pid'].'" ORDER BY posts.created_at DESC LIMIT 50'.(isset($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$mysql->real_escape_string($_GET['offset']) : ''));
print '<div class="list post-list" data-next-page-url="'.($search_user_posts->num_rows > 49 ? '?offset='.(isset($_GET['offset']) && is_numeric($_GET['offset']) ? 50 + $_GET['offset'] : 50) : '').'">
';
if($search_user_posts->num_rows == 0) {
print '<div id="user-page-no-content" class="no-content"><div>
    <p>No Miiverse posts have been made yet.</p>
</div></div>'; } else { 
print '<div id="user-page-no-content" class="none"></div>
';
require_once '../grplib-php/olv-url-enc.php';
while($posts = $search_user_posts->fetch_assoc()) {
printPost($posts);
}

}

print '
</div>';
if(!isset($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
print '
</div>';
printFooter('old');
}
exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'empathies') {
if($search_user->num_rows == 0) {
$pagetitle = 'Error'; printHeader('old'); printMenu('old'); notFound('users', false); printFooter('old'); grpfinish($mysql); exit();
}

$user = $search_user->fetch_assoc();
$mii = getMii($user, false);
$search_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1');
if($search_profile->num_rows == 0) {
$createprofile = $mysql->query('INSERT INTO profiles(pid, platform_id) VALUES("'.$user['pid'].'", "'.$user['platform_id'].'")');
} else {
$profile = $search_profile->fetch_assoc(); }
require_once 'lib/htmUser.php';
require_once 'lib/htmCommunity.php';
require_once '../grplib-php/community-helper.php';
if(!isset($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
$pagetitle = htmlspecialchars($user['screen_name']).'\'s Profile';
if(!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid']) { $mnselect = 'users'; }
printHeader('old'); printMenu('old'); print '
<div id="main-body">
';
# Start user-page
print '<div class="user-page">
';
userContent($user, $mii, 'posts', (isset($profile)) ? (!empty($profile['favorite_screenshot']) ? $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$profile['favorite_screenshot'].'" LIMIT 1')->fetch_assoc() : false) : false);
userNavMenu($user, 'posts');
print '
</div>
';
# End of user-page
tab2Activity($user, 'empathies');
}
$search_user_empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.pid = "'.$user['pid'].'" ORDER BY empathies.created_at DESC LIMIT 20'.(isset($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$mysql->real_escape_string($_GET['offset']) : ''));
print '<div class="list post-list" data-next-page-url="'.($search_user_empathies->num_rows > 19 ? '?offset='.(isset($_GET['offset']) && is_numeric($_GET['offset']) ? 20 + $_GET['offset'] : 20) : '').'">
';
if($search_user_empathies->num_rows == 0) {
print '<div id="user-page-no-content" class="no-content"><div>
    <p>There are no posts with Yeahs yet.</p>
</div></div>'; } else { 
print '<div id="user-page-no-content" class="none"></div>
';
require_once '../grplib-php/olv-url-enc.php';
while($empathies = $search_user_empathies->fetch_assoc()) {
$get_empathy_post = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$empathies['id'].'"  AND posts.is_hidden != "1" UNION ALL SELECT * from replies where replies.id = "'.$empathies['id'].'"  AND replies.is_hidden != "1"');
if($get_empathy_post->num_rows != 0) {
printPost($get_empathy_post->fetch_assoc()); }
}

}

print '
</div>';
if(!isset($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
print '
</div>';
printFooter('old');
}
exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'friends') {
if($search_user->num_rows == 0) {
$pagetitle = 'Error'; printHeader('old'); printMenu('old'); notFound('users', false); printFooter('old'); grpfinish($mysql); exit();
}

$user = $search_user->fetch_assoc();
$mii = getMii($user, false);
$search_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1');
if($search_profile->num_rows == 0) {
$createprofile = $mysql->query('INSERT INTO profiles(pid, platform_id) VALUES("'.$user['pid'].'", "'.$user['platform_id'].'")');
} else {
$profile = $search_profile->fetch_assoc(); }
require_once 'lib/htmUser.php';
$pagetitle = (!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'] ? 'User Page' : htmlspecialchars($user['screen_name']).'\'s Profile');

if(!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid']) { $mnselect = 'users'; }
printHeader('old'); printMenu('old'); print '
<div id="main-body">
';
# Start user-page
print '<div class="user-page">
';
userContent($user, $mii, 'posts', (isset($profile)) ? (!empty($profile['favorite_screenshot']) ? $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$profile['favorite_screenshot'].'" LIMIT 1')->fetch_assoc() : false) : false);
userNavMenu($user, 'friends');
print '
</div>
';
# End of user-page

$search_user_friends = $mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$user['pid'].'" OR friend_relationships.target = "'.$user['pid'].'" ORDER BY friend_relationships.relationship_id DESC');
print '<div class="list follow-list friends">
';
if($search_user_friends->num_rows == 0) {
print '<div id="user-page-no-content" class="no-content"><div>
    <p>No friends to display.</p>
</div></div>'; } else { 
print '<div id="user-page-no-content" class="none"></div>
  <ul class="list-content-with-icon-and-text arrow-list" id="friend-list-content" data-next-page-url="">';
while($friends = $search_user_friends->fetch_assoc()) {
$get_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.($friends['source'] == $user['pid'] ? $friends['target'] : $friends['source']).'" LIMIT 1')->fetch_assoc();
userObject($get_user, true, false);
}
print '
</ul>';
}

print '
</div>';
print '
</div>';
printFooter('old');
exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'following') {
if($search_user->num_rows == 0) {
$pagetitle = 'Error'; printHeader('old'); printMenu('old'); notFound('users', false); printFooter('old'); grpfinish($mysql); exit();
}

$user = $search_user->fetch_assoc();
$mii = getMii($user, false);
$search_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1');
if($search_profile->num_rows == 0) {
$createprofile = $mysql->query('INSERT INTO profiles(pid, platform_id) VALUES("'.$user['pid'].'", "'.$user['platform_id'].'")');
} else {
$profile = $search_profile->fetch_assoc(); }
require_once 'lib/htmUser.php';
$pagetitle = (!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'] ? 'User Page' : htmlspecialchars($user['screen_name']).'\'s Profile');

if(!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid']) { $mnselect = 'users'; }
printHeader('old'); printMenu('old'); print '
<div id="main-body">
';
# Start user-page
print '<div class="user-page">
';
userContent($user, $mii, 'posts', (isset($profile)) ? (!empty($profile['favorite_screenshot']) ? $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$profile['favorite_screenshot'].'" LIMIT 1')->fetch_assoc() : false) : false);
userNavMenu($user, 'following');
print '
</div>
';
# End of user-page

$search_user_following = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$user['pid'].'" AND relationships.is_me2me != "1" ORDER BY relationships.relationship_id DESC');
print '<div class="list follow-list following">
';
if($search_user_following->num_rows == 0) {
print '<div id="user-page-no-content" class="no-content"><div>
    <p>'; if(!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid']) { print 'No followed users.<br>
	To follow someone, select Follow from his or her profile screen.'; } else { print 'No followed users.'; } print '</p>
</div></div>'; } else {
print '<div id="user-page-no-content" class="none"></div>
  <ul class="list-content-with-icon-and-text arrow-list" id="following-list-content" data-next-page-url="">';
while($follow = $search_user_following->fetch_assoc()) {
$get_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$follow['target'].'" LIMIT 1')->fetch_assoc();
userObject($get_user, true, true);
}
print '
</ul>';
}

print '
</div>';
print '
</div>';
printFooter('old');
exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'followers') {
if($search_user->num_rows == 0) {
$pagetitle = 'Error'; printHeader('old'); printMenu('old'); notFound('users', false); printFooter('old'); grpfinish($mysql); exit();
}

$user = $search_user->fetch_assoc();
$mii = getMii($user, false);
$search_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1');
if($search_profile->num_rows == 0) {
$createprofile = $mysql->query('INSERT INTO profiles(pid, platform_id) VALUES("'.$user['pid'].'", "'.$user['platform_id'].'")');
} else {
$profile = $search_profile->fetch_assoc(); }
require_once 'lib/htmUser.php';
$pagetitle = (!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'] ? 'User Page' : htmlspecialchars($user['screen_name']).'\'s Profile');

if(!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid']) { $mnselect = 'users'; }
printHeader('old'); printMenu('old'); print '
<div id="main-body">
';
# Start user-page
print '<div class="user-page">
';
userContent($user, $mii, 'posts', (isset($profile)) ? (!empty($profile['favorite_screenshot']) ? $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$profile['favorite_screenshot'].'" LIMIT 1')->fetch_assoc() : false) : false);
userNavMenu($user, 'followers');
print '
</div>
';
# End of user-page

$search_user_following = $mysql->query('SELECT * FROM relationships WHERE relationships.target = "'.$user['pid'].'" AND relationships.is_me2me != "1" ORDER BY relationships.relationship_id DESC');
print '<div class="list follow-list followers">
';
if($search_user_following->num_rows == 0) {
print '<div id="user-page-no-content" class="no-content"><div>
    <p>'; if(!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid']) { print 'You have no followers.'; } else { print 'This user has no followers.'; } print '</p>
</div></div>'; } else {
print '<div id="user-page-no-content" class="none"></div>
  <ul class="list-content-with-icon-and-text arrow-list" id="follower-list-content" data-next-page-url="">';
while($follow = $search_user_following->fetch_assoc()) {
$get_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$follow['source'].'" LIMIT 1')->fetch_assoc();
userObject($get_user, true, true);
}
print '
</ul>';
}

print '
</div>';
print '
</div>';
printFooter('old');
exit();
}

if(isset($_GET['mode']) && $_GET['mode'] == 'follow') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
include_once '404alli.php'; }

if($search_user->num_rows == 0) { http_response_code(404); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 404)); grpfinish($mysql); exit(); }

if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 403)); grpfinish($mysql); exit(); }

$user = $search_user->fetch_assoc();

if($_SESSION['pid'] == $user['pid']) {
http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 400)); grpfinish($mysql); exit(); 
}

$search_relationship = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$user['pid'].'" AND relationships.is_me2me = "0"');

if($search_relationship->num_rows != 0) {
http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 400)); grpfinish($mysql); exit(); 
}


// User checks over. Is eligible to follow.
        $create_relationship = $mysql->query('INSERT INTO grape.relationships(source, target) VALUES ("'.$_SESSION['pid'].'", "'.$user['pid'].'")');
			// If the user gave the same type of notification 8 seconds ago, then don't send this.
	$result_check_fastnews = $mysql->query('SELECT news.to_pid, news.created_at FROM grape.news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$user['pid'].'" AND news.news_context = "6" AND news.created_at > NOW() - 8 ORDER BY news.created_at DESC');
    if($result_check_fastnews->num_rows == 0) {
    $result_check_ownusernews = $mysql->query('SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$user['pid'].'" AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
$row_check_ownusernews = $result_check_ownusernews->fetch_assoc();
	$result_check_mergedusernews = $mysql->query('SELECT * FROM news WHERE news.from_pid = "'.$_SESSION['pid'].'" AND news.to_pid = "'.$user['pid'].'" AND news.merged IS NOT NULL AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
 if($result_check_mergedusernews->num_rows != 0) {
	$result_update_mergedusernewsagain = $mysql->query('UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$result_check_mergedusernews->fetch_assoc()['merged'].'"');	
	}
	elseif($result_check_ownusernews->num_rows != 0) {
	$result_update_ownusernewsagain = $mysql->query('UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$row_check_ownusernews['news_id'].'"');
	}
else {
	$result_update_newsmergesearch = $mysql->query('SELECT * FROM news WHERE news.to_pid = "'.$user['pid'].'" AND news.news_context = "6" AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
	if($result_update_newsmergesearch->num_rows != 0) {
$row_update_newsmergesearch = $result_update_newsmergesearch->fetch_assoc();
	$result_newscreatemerge = $mysql->query('INSERT INTO grape.news(from_pid, to_pid, merged, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$user['pid'].'", "'.$row_update_newsmergesearch['news_id'].'", "6", "0")');
$result_update_newsformerge = $mysql->query('UPDATE news SET has_read = "0", created_at = NOW() WHERE news.news_id = "'.$row_update_newsmergesearch['news_id'].'"');
		}
else {
        $result_newscreate = $mysql->query('INSERT INTO grape.news(from_pid, to_pid, news_context, has_read) VALUES ("'.$_SESSION['pid'].'", "'.$user['pid'].'", "6", "0")'); 	
	} }	
	}
        if(!$create_relationship) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
	} else {
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 1, 'can_follow_more' => true));
        }
		   grpfinish($mysql); exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'unfollow') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
include_once '404alli.php'; }

if($search_user->num_rows == 0) { http_response_code(404); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 404)); grpfinish($mysql); exit(); }

if(empty($_SESSION['pid'])) {
http_response_code(403); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 403)); grpfinish($mysql); exit(); }

$user = $search_user->fetch_assoc();

if($_SESSION['pid'] == $user['pid']) {
http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 400)); grpfinish($mysql); exit(); 
}

$search_relationship = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$user['pid'].'" AND relationships.is_me2me = "0"');

if($search_relationship->num_rows <= 0) {
http_response_code(400); header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 0, 'errors' => [], 'code' => 400)); grpfinish($mysql); exit(); 
}

// User checks over. Is eligible to follow.
        $delete_relationship = $mysql->query('DELETE FROM relationships WHERE source = "'.$_SESSION['pid'].'" AND target = "'.$user['pid'].'"');
        if(!$delete_relationship) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
	} else {
header('Content-Type: application/json; charset=utf-8'); print 
json_encode(array('success' => 1));
        }
           grpfinish($mysql); exit();		
}

if(isset($_GET['mode'])) { if($_GET['mode'] != 'posts' || $_GET['mode'] != 'empathies' || $_GET['mode'] != 'following' || $_GET['mode'] != 'followers' || $_GET['mode'] != 'follow' || $_GET['mode'] != 'unfollow') { 
# Display 404 if mode is undefined
include_once '404alli.php'; } }

if($search_user->num_rows == 0) {
$pagetitle = 'Error'; printHeader('old'); printMenu('old'); notFound('users', false); printFooter('old'); grpfinish($mysql); exit();
}

$user = $search_user->fetch_assoc();
$mii = getMii($user, false);
$search_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1');
if($search_profile->num_rows == 0) {
$createprofile = $mysql->query('INSERT INTO profiles(pid, platform_id) VALUES("'.$user['pid'].'", "'.$user['platform_id'].'")');
} else {
$profile = $search_profile->fetch_assoc(); }
require_once 'lib/htmUser.php';
$pagetitle = (!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'] ? 'User Page' : htmlspecialchars($user['screen_name']).'\'s Profile');
if(!empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid']) { $mnselect = 'users'; }
printHeader('old'); printMenu('old'); print '
<div id="main-body">
';
# Start user-page
print '<div class="user-page">
';
userContent($user, $mii, 'profile', (isset($profile)) ? (!empty($profile['favorite_screenshot']) ? $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$profile['favorite_screenshot'].'" LIMIT 1')->fetch_assoc() : false) : false);
userNavMenu($user, 'profile');
print '
</div>
';
# End of user-page
if(!isset($profile)) {
$profile = array('comment' => null, 'country' => 'Not Set', 'gender' => 3); }

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

if(!empty($profile['comment'])) {
print '<p class="profile-comment">
'.nl2br(htmlspecialchars($profile['comment'])).'</p>'; }

print '<div class="user-data">
    <div class="user-main-profile data-content">
      <h4><span>Country</span></h4>
      <div class="note">'.(empty($profile['country']) ? 'Not Set' : htmlspecialchars($profile['country'])).'</div>
      <h4><span>Gender</span></h4>
      <div class="note birthday">'.$pge_sex.'      </div>
    </div>
    <div class="game-skill data-content">
      <h4><span>Game Experience</span></h4>
      <div class="note">
        <span class="'.$pge_experience.'">'.$pge_extext.'        </span>
      </div>
    </div>
    <div class="game data-content">
      <h4><span>Systems Owned</span></h4>
      <div class="note"><div class="device-'.$pge_platform.'">
	  ';
	  if($pge_patext != 'Off-Device') {
	  print '<img src="https://d13ph7xrk1ee39.cloudfront.net/img/'.$pge_platform.'.png" class="'.$pge_platform.'-icon">'; } print '<span>'.$pge_patext.'</span></div>
      </div>
    </div>
  </div>';
  
print '
</div>';
printFooter('old');
