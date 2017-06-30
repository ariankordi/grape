<?php
require_once '../grplib-php/init.php';

# If user isn't logged in, then 403 them.
if(empty($_SESSION['pid'])) {
require 'lib/htm.php';
notLoggedIn();  exit();
}

$pagetitle = 'Notifications';
require_once 'lib/htm.php';
require_once '../grplib-php/user-helper.php';
if(empty($_SERVER['HTTP_X_PJAX_CONTAINER'])) { $_SERVER['HTTP_X_PJAX_CONTAINER'] = ''; }
if($_SERVER['HTTP_X_PJAX_CONTAINER'] != '.tab-body') {
printHeader(false);
printMenu();
# Insert PJAX // same as user page
print $GLOBALS['div_body_head'];
print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';
# Put checks for closing this notification
print '<div class="body-content tab2-content" id="news-page">
';

$find_user_newstutorial = $mysql->query('SELECT * FROM settings_tutorial WHERE settings_tutorial.pid = "'.$_SESSION['pid'].'" AND settings_tutorial.my_news = "1"');

if($find_user_newstutorial->num_rows == 0) {
print '<div class="tutorial-window">
  <p>Check Notifications to see how other people have reacted to your posts or comments and to see if you have any pending friend requests. If you have new notifications, an orange icon will appear to let you know.</p>
<a href="#" class="button tutorial-close-button" data-tutorial-name="my_news">Close</a>
</div>'; }
  
  print '<menu class="tab-header">
  <li id="tab-header-my-news" class="tab-button selected"><a href="/news/my_news" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB" class="tab-icon-my-news"><span>Updates</span></a></li>
  <li id="tab-header-friend-request" class="tab-button'.(getMessageNotify() ? ' notify' : null).'"><a href="/news/friend_requests" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB" class="tab-icon-friend-request"><span>Friend Requests</span></a></li>
</menu>
';
}
$find_user_news = $mysql->query('SELECT * FROM news WHERE news.to_pid = "'.$_SESSION['pid'].'" AND news.merged IS NULL ORDER BY news.created_at DESC LIMIT 65');

print '
    <div class="tab-body">';

if($find_user_news->num_rows == 0) {
nocontentWindow('No updates.'); }
else {
// Found news, display it here inside a div, a ul, and close them.
// Types - 0, test, 1 admin message, 2, empathy, 3, comment empathy, 4, my comment, 5, poster comment, 6, follow

print '<ul class="list-content-with-icon-and-text arrow-list" id="news-list-content">';
require_once 'lib/htmUser.php';
function span_u($name) { return '<span class="nick-name">'.htmlspecialchars($name).'</span>'; }
while($news = $find_user_news->fetch_assoc()) {
printNews($news);

// Mark all as read
$update = $mysql->query('UPDATE news SET news.has_read = "1" WHERE news.news_id = "'.$news['news_id'].'"');
}

}
// End tab-content, body-content tab2-content.
print '</div>';

if($_SERVER['HTTP_X_PJAX_CONTAINER'] != '.tab-body') {
print '  </div>';




print $GLOBALS['div_body_head_end'];
printFooter();
}


