<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

# If user isn't logged in, then 403 them.
if(empty($_SESSION['pid'])) {
if($dev_server) {
plainErr(403, '403 Forbidden'); exit();
}
else {
include_once 'communities.php';
exit();
     }
}

if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
$pagetitle = 'Activity Feed';
printHeader(false); printMenu();

function actFeedLoading() {
print $GLOBALS['div_body_head'];
print '
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
</div>
';
print $GLOBALS['div_body_head_end'];
}

# Requesting "loading activity feed" page.
if((isset($_SERVER['HTTP_X_REQUESTED_WITH'], $_SERVER['HTTP_X_PJAX'])) || (empty($_SERVER['HTTP_X_REQUESTED_WITH']) && empty($_SERVER['HTTP_X_PJAX']))) {
actFeedLoading();
         }
}
# User is trying to load the activity feed.
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && empty($_SERVER['HTTP_X_PJAX'])) {
print '
<header id="header">
<h1 id="page-title">'.$pagetitle.'</h1>
';
require_once '../grplib-php/community-helper.php';
require_once '../grplib-php/user-helper.php';
require_once 'lib/htmCommunity.php';
require_once 'lib/htmUser.php';
require_once '../grplib-php/olv-url-enc.php';

$search_relationships_real = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.is_me2me = "0"');

$posts = getActivity();

if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
# Activity Feed post button + form if the community exists
$act_feed_community = $mysql->query('SELECT * FROM communities WHERE communities.type = 5 LIMIT 1');
if($act_feed_community->num_rows != 0) {
require_once 'lib/htmCommunity.php';
print '  <a id="header-post-button" class="header-button" href="#" data-modal-open="#add-post-page">Post</a>';

$act_feed = true;
postForm('posts', $act_feed_community->fetch_assoc(), $me);

}
print '
</header>';
# Put my menu search here when implemented properly without collisions!

# print '<span id="my-menu-search" class="scroll">Search Users<input name="query" class="scroll-focus user-search-query" minlength="1" maxlength="32" inputform="monospace" guidestring="Enter the ID or screen name of
#the user you want to find." data-pjax="#body"></span>';

if($search_relationships_real->num_rows == 0) {
print '
  <div class="tutorial-window" id="activity-feed-tutorial">
  <p class="tutorial-text">In your activity feed, you can view posts from your friends and from people you&#39;re following. To get started, why not follow some people whose posts interest you? You can also search for friends using Search Users in the upper right.<br />
</p>
    <h3>Latest Updates from Verified Users</h3>
	
    <ul class="list-content-with-icon-and-text arrow-list">';
$get_officials = $mysql->query('SELECT * FROM people WHERE people.official_user = "1" ORDER BY people.created_at ASC LIMIT 1');
if($get_officials->num_rows != 0) {
while($user = $get_officials->fetch_assoc()) {
userObject($user, false, true, null); 
		}
}
print '    </ul>
  </div>';
	}
}
print '<div class="body-content js-post-list post-list" id="activity-feed" data-next-page-url="'.($posts && count($posts) > 49 ? '/activity?offset='.(empty($_GET['offset']) && is_numeric($_GET['offset']) ? 50 : 50 + count($posts)) : '').'">';
require_once 'lib/htmCommunity.php';
require_once '../grplib-php/community-helper.php';

if($posts && count($posts) >= 1) {
foreach($posts as &$post_row) {
printPost($post_row, true, true, false);
}

} else {
# There are no posts to display
print '    <div class="tutorial-window no-content js-no-content" id="activity-feed-tutorial">
      <p>There are no posts to display.</p>
    </div>
';
}

# End body-content js-post-list
print '</div>'; 
}
(empty($_SERVER['HTTP_X_AUTOPAGERIZE']) && empty($_SERVER['HTTP_X_PJAX']) && empty($_SERVER['HTTP_X_REQUESTED_WITH']) ? printFooter() : '');
