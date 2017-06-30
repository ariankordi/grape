<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

$search_user = prepared('SELECT * FROM people WHERE people.user_id = ?', [$_GET['user_id'] ?? null]);
require_once '../grplib-php/user-helper.php';

if(isset($_GET['mode']) && $_GET['mode'] == 'posts') {
# Display posts for user.
if(!$search_user || $search_user->num_rows == 0) {
generalError(404, 'The user could not be found.');  exit(); }

$user = $search_user->fetch_assoc();
if(!empty($_SESSION['pid']) && canUserView($_SESSION['pid'], $user['pid'])) {
require '404.php'; exit(); }
$mii = getMii($user, false);

$profile = getProfile($user);

require_once 'lib/htmUser.php';
$own_page = !empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'];
if(empty($_SERVER['HTTP_X_PJAX_CONTAINER']) || $_SERVER['HTTP_X_PJAX_CONTAINER'] != '.tab-body') {
$pagetitle = ($own_page ? 'User Page' : htmlspecialchars($user['screen_name']).'\'s Profile');

printHeader(false); printMenu();
    print $GLOBALS['div_body_head'];
	print '<header id="header">

	<h1 id="page-title">'.$pagetitle.'</h1>

</header>
';

#Begin body-content user-page
print '<div class="body-content user-page'.($own_page ? ' is-visitor' : '').'">
';

userInfo($user, $profile, $mii, 'posts');
userNavTab($user, 'posts');
print '<div class="tab-body">
';
}
userPostNavTab($user, 'posts');

$search_posts = $mysql->query('SELECT * FROM posts WHERE posts.pid = "'.$user['pid'].'" AND (posts.hidden_resp != 1 OR posts.hidden_resp IS NULL) ORDER BY posts.created_at DESC LIMIT 50'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$_GET['offset'] : ''));

print '<div class="user-page-content js-post-list post-list" data-next-page-url="'.($search_posts->num_rows > 49 ? '?offset='.(isset($_GET['offset']) && is_numeric($_GET['offset']) ? 50 + $_GET['offset'] : 50) : '').'">';
if(!$search_posts || $search_posts->num_rows == 0) {
print '  <div id="user-page-no-content" class="no-content-window js-no-content"><div class="window">
    <p>No Miiverse posts have been made yet.</p>
</div></div>
'; }
else {
require_once 'lib/htmCommunity.php';
require_once '../grplib-php/community-helper.php';
require_once '../grplib-php/olv-url-enc.php';
	while($posts = $search_posts->fetch_assoc()) {
	printPost($posts, true, false, false);
	}
}
# End of js-post-list
print '</div>
';
if(empty($_SERVER['HTTP_X_PJAX_CONTAINER']) || $_SERVER['HTTP_X_PJAX_CONTAINER'] != '.tab-body') {
print '  </div>';

require_once 'lib/htmTemplates.php';
userPageTemplate($user, $mii);
# End body-content user-page
print '</div>';
print $GLOBALS['div_body_head_end'];	
printFooter();

}
 exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'empathies') {
# Display empathies for user.
if(!$search_user || $search_user->num_rows == 0) {
generalError(404, 'The user could not be found.');  exit(); }

$user = $search_user->fetch_assoc();
if(!empty($_SESSION['pid']) && canUserView($_SESSION['pid'], $user['pid'])) {
require '404.php'; exit(); }
$mii = getMii($user, false);

$profile = getProfile($user);

require_once 'lib/htmUser.php';
$own_page = !empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'];
if(empty($_SERVER['HTTP_X_PJAX_CONTAINER']) || $_SERVER['HTTP_X_PJAX_CONTAINER'] != '.tab-body') {
$pagetitle = ($own_page ? 'User Page' : htmlspecialchars($user['screen_name']).'\'s Profile');

printHeader(false); printMenu();
    print $GLOBALS['div_body_head'];
	print '<header id="header">

	<h1 id="page-title">'.$pagetitle.'</h1>

</header>
';

#Begin body-content user-page
print '<div class="body-content user-page'.($own_page ? ' is-visitor' : '').'">
';

userInfo($user, $profile, $mii, 'empathies');
userNavTab($user, 'posts');
print '<div class="tab-body">
';
}
userPostNavTab($user, 'empathies');

$search_empathies = $mysql->query('SELECT * FROM empathies WHERE empathies.pid = "'.$user['pid'].'" ORDER BY empathies.created_at DESC LIMIT 20'.(isset($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$_GET['offset'] : ''));
print '<div class="user-page-content js-post-list post-list" data-next-page-url="'.($search_empathies->num_rows > 19 ? '?offset='.(isset($_GET['offset']) && is_numeric($_GET['offset']) ? 20 + $_GET['offset'] : 20) : '').'">';
if(!$search_empathies || $search_empathies->num_rows == 0) {
print '  <div id="user-page-no-content" class="no-content-window js-no-content"><div class="window">
    <p>There are no posts with Yeahs yet.</p>
</div></div>
'; }
else {
require_once 'lib/htmCommunity.php';
require_once '../grplib-php/community-helper.php';
require_once '../grplib-php/olv-url-enc.php';
	while($posts = $search_empathies->fetch_assoc()) {
$get_post = getPost($posts['id']);
if($get_post->num_rows != 0) {
printPost($get_post->fetch_assoc(), true, false, false); 
		}
	}
}
# End of js-post-list
print '</div>
';
if(empty($_SERVER['HTTP_X_PJAX_CONTAINER']) || $_SERVER['HTTP_X_PJAX_CONTAINER'] != '.tab-body') {
print '  </div>';

require_once 'lib/htmTemplates.php';
userPageTemplate($user, $mii);
# End body-content user-page
print '</div>';
print $GLOBALS['div_body_head_end'];	
printFooter();

}
 exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'following') {
if(!$search_user || $search_user->num_rows == 0) {
generalError(404, 'The user could not be found.');  exit(); }

$user = $search_user->fetch_assoc();
if(!empty($_SESSION['pid']) && canUserView($_SESSION['pid'], $user['pid'])) {
require '404.php'; exit(); }
$mii = getMii($user, false);

$profile = getProfile($user);

require_once 'lib/htmUser.php';
$own_page = !empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'];
if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
$pagetitle = ($own_page ? 'User Page' : htmlspecialchars($user['screen_name']).'\'s Profile');

printHeader(false); printMenu();
    print $GLOBALS['div_body_head'];
	print '<header id="header">

	<h1 id="page-title">'.$pagetitle.'</h1>

</header>
';

#Begin body-content user-page
print '<div class="body-content user-page'.($own_page ? ' is-visitor' : '').'">
';
userInfo($user, $profile, $mii, 'following');
userNavTab($user, 'following');
$can_view = $own_page || !empty($_SESSION['pid']) && profileRelationshipVisible($_SESSION['pid'], $user['pid'], $profile['relationship_visibility']);

if($can_view) {
$search_relationships = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$user['pid'].'" AND relationships.is_me2me = "0" ORDER BY relationships.relationship_id DESC LIMIT 20'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$_GET['offset'] : ''));
}

print '<div class="tab-body">
';
}
if(!$can_view) {
noContentWindow('This information is private and cannot be viewed.');
	} else {
print '<div class="user-page-content friend-list following">
';
if(!$search_relationships || $search_relationships->num_rows == 0) {
$my = 'No followed users.<br>
      To follow someone, select Follow from their profile screen.';
$other = 'No followed users.';
noContentWindow($own_page ? $my : $other);
} else {
print '
  <ul class="list-content-with-icon-and-text arrow-list" id="friend-list-content" data-next-page-url="'.($search_relationships->num_rows > 49 ? '?offset='.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? 50 + $_GET['offset'] : 50) : '').'">
';
while($relationship_users = $search_relationships->fetch_assoc()) {
$relationship_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$relationship_users['target'].'" LIMIT 1')->fetch_assoc();
userObject($relationship_user, true, true, null);
}
print '
  </ul>
';
}
print '  </div>
';
}
if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
print '  </div>';

# End body-content user-page
print '
</div>';

require_once 'lib/htmTemplates.php';
userPageTemplate($user, $mii);
# End body-content user-page
print '</div>';
print $GLOBALS['div_body_head_end'];	
printFooter();
	}
 exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'followers') {
if(!$search_user || $search_user->num_rows == 0) {
generalError(404, 'The user could not be found.');  exit(); }

$user = $search_user->fetch_assoc();
if(!empty($_SESSION['pid']) && canUserView($_SESSION['pid'], $user['pid'])) {
require '404.php'; exit(); }
$mii = getMii($user, false);

$profile = getProfile($user);

require_once 'lib/htmUser.php';
$own_page = !empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'];
if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
$pagetitle = ($own_page ? 'User Page' : htmlspecialchars($user['screen_name']).'\'s Profile');

printHeader(false); printMenu();
    print $GLOBALS['div_body_head'];
	print '<header id="header">

	<h1 id="page-title">'.$pagetitle.'</h1>

</header>
';

#Begin body-content user-page
print '<div class="body-content user-page'.($own_page ? ' is-visitor' : '').'">
';
userInfo($user, $profile, $mii, 'followers');
userNavTab($user, 'followers');
$can_view = $own_page || !empty($_SESSION['pid']) && profileRelationshipVisible($_SESSION['pid'], $user['pid'], $profile['relationship_visibility']);

if($can_view) {
$search_relationships = $mysql->query('SELECT * FROM relationships WHERE relationships.target = "'.$user['pid'].'" AND relationships.is_me2me = "0" ORDER BY relationships.relationship_id DESC LIMIT 20'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$_GET['offset'] : ''));
}

print '<div class="tab-body">
';
}
if(!$can_view) {
noContentWindow('This information is private and cannot be viewed.');
	} else {
print '<div class="user-page-content friend-list followers">
';
if(!$search_relationships || $search_relationships->num_rows == 0) {
$my = 'You have no followers.';
$other = 'This user has no followers.';
noContentWindow($own_page ? $my : $other);
} else {
print '
  <ul class="list-content-with-icon-and-text arrow-list" id="friend-list-content" data-next-page-url="'.($search_relationships->num_rows > 49 ? '?offset='.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? 50 + $_GET['offset'] : 50) : '').'">
';
while($relationship_users = $search_relationships->fetch_assoc()) {
$relationship_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$relationship_users['source'].'" LIMIT 1')->fetch_assoc();
userObject($relationship_user, true, true, null);
}
print '
  </ul>
';
	}
print '  </div>
';
}
if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
print '  </div>';

# End body-content user-page
print '
</div>';

require_once 'lib/htmTemplates.php';
userPageTemplate($user, $mii);
# End body-content user-page
print '</div>';
print $GLOBALS['div_body_head_end'];	
printFooter();
	}
 exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'friends') {
if(!$search_user || $search_user->num_rows == 0) {
generalError(404, 'The user could not be found.');  exit(); }

$user = $search_user->fetch_assoc();
if(!empty($_SESSION['pid']) && canUserView($_SESSION['pid'], $user['pid'])) {
require '404.php'; exit(); }
$mii = getMii($user, false);

$profile = getProfile($user);

require_once 'lib/htmUser.php';
$own_page = !empty($_SESSION['pid']) && $_SESSION['pid'] == $user['pid'];
if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
$pagetitle = ($own_page ? 'User Page' : htmlspecialchars($user['screen_name']).'\'s Profile');

printHeader(false); printMenu();
    print $GLOBALS['div_body_head'];
	print '<header id="header">

	<h1 id="page-title">'.$pagetitle.'</h1>

</header>
';

#Begin body-content user-page
print '<div class="body-content user-page'.($own_page ? ' is-visitor' : '').'">
';
userInfo($user, $profile, $mii, 'friends');
userNavTab($user, 'friends');
$can_view = $own_page || !empty($_SESSION['pid']) && profileRelationshipVisible($_SESSION['pid'], $user['pid'], $profile['relationship_visibility']);

if($can_view) {
$search_relationships = $mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.target = "'.$user['pid'].'" OR friend_relationships.source = "'.$user['pid'].'" ORDER BY friend_relationships.relationship_id DESC LIMIT 100'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$_GET['offset'] : ''));

if($search_relationships && $search_relationships->num_rows != 0) {
while($relationship = $search_relationships->fetch_assoc()) {
$relationship = $mysql->query('SELECT * FROM people WHERE people.pid = "'.($relationship['target'] == $user['pid'] ? $relationship['source'] : $relationship['target']).'" LIMIT 1')->fetch_assoc();
$relationship['type'] = 1;
$friends[] = $relationship;
	}
if($own_page) {
$get_requests = $mysql->query('SELECT * FROM friend_requests WHERE friend_requests.sender = "'.$user['pid'].'" AND friend_requests.finished = "0" ORDER BY friend_requests.created_at DESC');
while($relationship_nf = $get_requests->fetch_assoc()) {
$get_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$relationship_nf['recipient'].'" LIMIT 1')->fetch_assoc();
$get_user['type'] = 0;
$friends[] = $get_user;
	} }
} else {
$friends = false;
	}

}

print '<div class="tab-body">
';
}
if(!$can_view) {
noContentWindow('This information is private and cannot be viewed.');
	} else {
print '<div class="user-page-content friend-list friends">
';
if(!$friends) {
$my = 'No friends to display.
<br>

	To become friends with someone, send them a friend request from their profile screen.';
$other = 'No friends to display.';
noContentWindow($own_page ? $my : $other);
} else {
print '
  <ul class="list-content-with-icon-and-text arrow-list" id="friend-list-content" data-next-page-url="'.(count($friends) > 49 ? '?offset='.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? 50 + $_GET['offset'] : 50) : '').'">
';
foreach($friends as &$users) {
if($own_page) {
userObject($users, true, true, ($users['type'] == 1 ? 'friends' : 'friend_request'));
	} else {
	userObject($users, true, false, null);
	}
}
print '
  </ul>
';
}
print '  </div>
';
}
if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
print '  </div>';

# End body-content user-page
print '
</div>';

require_once 'lib/htmTemplates.php';
userPageTemplate($user, $mii);
# End body-content user-page
print '</div>';
print $GLOBALS['div_body_head_end'];	
printFooter();
	}
 exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'favorites') {
# Search for user first, then use template
if(!$search_user || $search_user->num_rows == 0) {
generalError(404, 'The user could not be found.');  exit(); }

$user = $search_user->fetch_assoc();
$is_fav_own = $user['pid'];
include_once 'communities-showfavorites.php';

 exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'follow') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
include_once '404.php'; }

if($search_user->num_rows == 0) { jsonErr(404); }

if(empty($_SESSION['pid'])) {
jsonErr(403); }

$user = $search_user->fetch_assoc();

if($_SESSION['pid'] == $user['pid']) {
jsonErr(400); 
}

if(!empty($_SESSION['pid']) && canUserView($_SESSION['pid'], $user['pid'])) {
require '404.php'; exit(); }

$search_relationship = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$user['pid'].'" AND relationships.is_me2me = "0"');

if($search_relationship->num_rows != 0) {
jsonErr(400); 
}


// User checks over. Is eligible to follow.
        $create_relationship = $mysql->query('INSERT INTO grape.relationships(source, target) VALUES ("'.$_SESSION['pid'].'", "'.$user['pid'].'")');

sendNews($_SESSION['pid'], $user['pid'], 6, null);
        if(!$create_relationship) {
http_response_code(500);
header('Content-Type: application/json'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
	} else {
header('Content-Type: application/json'); print 
json_encode(array('success' => 1, 'can_follow_more' => true));
        }
		    exit();
}
if(isset($_GET['mode']) && $_GET['mode'] == 'unfollow') {
if($_SERVER['REQUEST_METHOD'] != 'POST') {
include_once '404.php'; }

if($search_user->num_rows == 0) { jsonErr(404); }

if(empty($_SESSION['pid'])) {
jsonErr(403); }

$user = $search_user->fetch_assoc();

if($_SESSION['pid'] == $user['pid']) {
jsonErr(400); 
}

$search_relationship = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$user['pid'].'" AND relationships.is_me2me = "0"');

if($search_relationship->num_rows <= 0) {
jsonErr(400); 
}

// User checks over. Is eligible to follow.
        $delete_relationship = $mysql->query('DELETE FROM relationships WHERE source = "'.$_SESSION['pid'].'" AND target = "'.$user['pid'].'"');
        if(!$delete_relationship) {
http_response_code(500);
header('Content-Type: application/json'); print 
json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
	} else {
header('Content-Type: application/json'); print 
json_encode(array('success' => 1));
        }
            exit();		
}

if(isset($_GET['mode'])) { if($_GET['mode'] != 'posts' || $_GET['mode'] != 'empathies' || $_GET['mode'] != 'following' || $_GET['mode'] != 'followers' || $_GET['mode'] != 'favorites' || $_GET['mode'] != 'follow' || $_GET['mode'] != 'unfollow') { 
# Display 404 if mode is undefined
include_once '404.php';  exit(); } }

if(!$search_user || $search_user->num_rows == 0) {
generalError(404, 'The user could not be found.');  exit(); }

$user = $search_user->fetch_assoc();
if(!empty($_SESSION['pid']) && canUserView($_SESSION['pid'], $user['pid'])) {
require '404.php'; exit(); }
$mii = getMii($user, false);

$profile = getProfile($user);

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
if(mb_strlen($profile['comment']) >= 1) {
print '<p class="text">'.getProfileComment($user, $profile).'</p>'; }
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

exit();