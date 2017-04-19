<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

$pagetitle = 'Posts from Verified Users'; $bodyClass = 'identified_user';
if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
printHeader('old'); printMenu('old');
# Start of main-body
print '<div id="main-body">

<div id="image-header-content">
  <span class="image-header-title">
    <span class="title">Posts from Verified Users</span>
    <span class="text">Get the latest news here!</span>
  </span>
  <img src="https://d13ph7xrk1ee39.cloudfront.net/img/identified-user.png">
</div>';
}
$identified_users_select = $mysql->query('select a.*, bm.recent_created_at from (select pid, max(created_at) as recent_created_at from posts group by pid) bm inner join people a on bm.pid = a.pid WHERE a.official_user = "1" ORDER BY recent_created_at DESC LIMIT 50'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? 'OFFSET '.$mysql->real_escape_string($_GET['offset']) : null));	

while($identified_users = $identified_users_select->fetch_assoc()) {
$person = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$identified_users['pid'].'"')->fetch_assoc();
$get_latest_post = $mysql->query('SELECT * FROM posts WHERE posts.pid = "'.$person['pid'].'" AND posts.hidden_resp != 1 OR posts.pid = "'.$person['pid'].'" AND posts.hidden_resp IS NULL ORDER BY posts.created_at DESC LIMIT 1');
if($get_latest_post->num_rows != 0) {
$posts[] = $get_latest_post->fetch_assoc(); } }

if(isset($posts) && count($posts) >= 1) {
require_once 'lib/htmCommunity.php';
require_once '../grplib-php/community-helper.php';
print '<div class="list post-list" data-next-page-url="'.(isset($posts) && count($posts) > 49 ? '/identified_user_posts?offset='.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? 50 + $_GET['offset'] : '50') : null).'">
';

foreach($posts as &$post_row) {
$identified = true;
printPost($post_row);	
}

print '
</div>';
}

if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
# End of main-body
print '
</div>';
printFooter('old');
} grpfinish($mysql);