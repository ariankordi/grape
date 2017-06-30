<?php
require_once '../grplib-php/init.php';
$pagetitle = 'Posts from Verified Users';
require_once 'lib/htm.php';
if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
printHeader(false);
printMenu();

print $GLOBALS['div_body_head'];
print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';

print '<div class="identified-user-info info-content">
  <span class="title">Posts from Verified Users</span>
  <span class="text">Get the latest news here!</span>
</div>';

print '<div class="body-content identified-user-page">
  <div class="tab-body">
    <div class="identified-user-page-content identified-user-list">'."\n".'';
}
$identified_users_select = $mysql->query('select a.*, bm.recent_created_at from (select pid, max(created_at) as recent_created_at from posts group by pid) bm inner join people a on bm.pid = a.pid WHERE a.official_user = "1" ORDER BY recent_created_at DESC LIMIT 50'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$_GET['offset'] : null));	

while($identified_users = $identified_users_select->fetch_assoc()) {
$person = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$identified_users['pid'].'"')->fetch_assoc();
$get_latest_post = $mysql->query('SELECT * FROM posts WHERE posts.pid = "'.$person['pid'].'" AND (posts.hidden_resp != 1 OR posts.hidden_resp IS NULL) AND posts.is_spoiler = "0" ORDER BY posts.created_at DESC LIMIT 1');
if($get_latest_post->num_rows != 0) {
$posts[] = $get_latest_post->fetch_assoc(); } }

print ' 	<ul class="list-content-with-icon-and-text js-post-list post-list test-identified-post-list" data-next-page-url="'.(isset($posts) && count($posts) > 49 ? '/identified_user_posts?offset='.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? 50 + $_GET['offset'] : '50') : null).'">';
if(count($posts) != 0) {
require_once 'lib/htmCommunity.php';
require_once '../grplib-php/community-helper.php';
require_once '../grplib-php/olv-url-enc.php';
foreach($posts as &$post_row) {
if($post_row['is_hidden'] != 1) {
print "<li class=\"scroll\">\n";
printPost($post_row, true, false, true);
print "\n</li>";                }
}

       }
print '      </ul>
';
if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
print '    </div>
  </div>
</div>';

require_once 'lib/htmTemplates.php';
unfollowConfirm();
print $GLOBALS['div_body_head_end'];
printFooter();
}