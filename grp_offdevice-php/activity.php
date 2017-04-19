<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

if(empty($_SESSION['pid'])) {
noLogin(); grpfinish($mysql); exit(); }

$pagetitle = 'Activity Feed'; $mnselect = 'feed';
printHeader('old'); printMenu('old');
# Start of main-body
print '<div id="main-body">
';

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {

print '<div class="headline">
  <h2 class="headline-text">Activity Feed</h2>
  
<form class="search" action="/users" method="GET"><!--
  --><input type="text" name="query" title="Search Users" placeholder="Search Users" minlength="1" maxlength="16"><!--
  --><input type="submit" value="q" title="Search">
</form>

</div>';

print '  <div id="js-main">
    <div class="activity-feed content-loading-window">
      <div>
        <img src="/img/loading-image-green.gif" alt=""></img>
        <p class="tleft"><span>Loading activity feed...</span></p>
      </div>
    </div>
    <div class="activity-feed content-load-error-window none">
      <div>
        <p>The activity feed could not be loaded. Check your Internet connection, wait a moment, and then try reloading.</p>
        <div class="buttons-content"><a href="/activity" class="button">Reload</a></div>
      </div>
    </div>
  </div>';
}
else {
require_once '../grplib-php/community-helper.php';
require_once 'lib/htmCommunity.php';
require_once 'lib/htmUser.php';
require_once '../grplib-php/olv-url-enc.php';
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
$act_feed_community = $mysql->query('SELECT * FROM communities WHERE communities.type = 5 LIMIT 1');
if($act_feed_community->num_rows != 0) {
postForm($act_feed_community->fetch_assoc(), $user, 'Write a post here to people who are following you.');
}

$search_relationships_real = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.is_me2me = "0"');

$search_relationships_by_post = $mysql->query('select a.*, bm.recent_created_at from (select pid, max(created_at) as recent_created_at from posts group by pid) bm inner join relationships a on bm.pid = a.target WHERE a.source = "'.$_SESSION['pid'].'" ORDER BY recent_created_at DESC LIMIT 50'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$mysql->real_escape_string($_GET['offset']) : ''));
if($search_relationships_by_post->num_rows != 0) {
$posts = array();
while($row_relationship_posts = $search_relationships_by_post->fetch_assoc()) {
$person = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$row_relationship_posts['target'].'"')->fetch_assoc();
$get_latest_post = $mysql->query('SELECT * FROM posts WHERE posts.pid = "'.$person['pid'].'" AND posts.hidden_resp != 1 OR posts.pid = "'.$person['pid'].'" AND posts.hidden_resp IS NULL ORDER BY posts.created_at DESC LIMIT 1');
if($get_latest_post->num_rows != 0) {
$posts[] = $get_latest_post->fetch_assoc(); }
}
} else {
$posts = false; }

if($search_relationships_real->num_rows == 0) {
print '<div id="activity-feed-tutorial">
  <p class="tleft">In your activity feed, you can view posts from your friends and from people you\'re following. To get started, why not follow some people whose posts interest you? You can also search for friends using Search Users in the upper right.<br>
</p>
  <img src="https://d13ph7xrk1ee39.cloudfront.net/img/tutorial/tutorial-activity-feed.png?TPsXJlkWDzZaF8SSsZSQLw" class="tutorial-image">
    <h3>Latest Updates from Verified Users</h3>
    <ul class="list list-content-with-icon-and-text arrow-list follow-list">
	';
$get_officials = $mysql->query('SELECT * FROM people WHERE people.official_user = "1" ORDER BY people.pid DESC LIMIT 1');
if($get_officials->num_rows != 0) {
while($user = $get_officials->fetch_assoc()) {
userObject($user, false, true); }
}
	print '
    </ul>
  </div>';
}

if($posts) {
print '
<div class="list post-list js-post-list" data-next-page-url="'.(count($posts) > 49 ? '/activity?offset='.(empty($_GET['offset']) ? 50 : 50 + count($posts)) : '').'">
';
$actFeed = true;
foreach($posts as &$post_row) {
printPost($post_row);
}

} else {
# There are no posts to display
$no_posts = true;
print '<div id="activity-feed-tutorial" class="no-content">
    <p>There are no posts to display.</p>
';
}

print '	
<input type="hidden" name="view_id" value="00000000000000000000000000000000">
<input type="hidden" name="page_param" value="{&quot;upinfo&quot;:&quot;1400000000.00000,1400000000,1400000000.00000&quot;,&quot;reftime&quot;:&quot;+1400000000&quot;,&quot;order&quot;:&quot;desc&quot;,&quot;per_page&quot;:&quot;20&quot;}">
';
if(isset($no_posts)) { print '
</div>'; }

}

# End of main-body
print '</div>
';


if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) { printFooter('old'); }