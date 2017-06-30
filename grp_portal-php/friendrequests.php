<?php
require_once '../grplib-php/init.php';

# If user isn't logged in, then 403 them.
if(empty($_SESSION['pid'])) {
require 'lib/htm.php';
notLoggedIn(); exit();
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
  <li id="tab-header-my-news" class="tab-button'.(getNewsNotify() ? ' notify' : null).'"><a href="/news/my_news" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB" class="tab-icon-my-news"><span>Updates</span></a></li>
  <li id="tab-header-friend-request" class="tab-button selected"><a href="/news/friend_requests" data-pjax=".tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB" class="tab-icon-friend-request"><span>Friend Requests</span></a></li>
</menu>
';
}
print '
    <div class="tab-body">';

$find_user_friend_requests = $mysql->query('SELECT * FROM friend_requests WHERE friend_requests.recipient = "'.$_SESSION['pid'].'" AND friend_requests.finished = "0" ORDER BY news_id DESC LIMIT 100');

if($find_user_friend_requests->num_rows == 0) {
nocontentWindow('You don\'t have any friend requests.'); }
else {
// Found friend requests, display them here inside a div, an ul, close them then add the template.
print '<ul class="list-content-with-icon-and-text" id="friend-request-list-content">
';
while($friend_requests = $find_user_friend_requests->fetch_assoc()) {
print '<li>
';

$result_news_user_select = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$friend_requests['sender'].'"');
$row_news_user_select = $result_news_user_select->fetch_assoc();

$friend_request_mii = getMii($row_news_user_select, false);

print '<a href="/users/'.htmlspecialchars($row_news_user_select['user_id']).'" data-pjax="#body" class="icon-container'.($friend_request_mii['official'] == 1 ? ' official-user' : null).($friend_requests['has_read'] == 0 ? ' notify' : null).'"><img src="'.$friend_request_mii['output'].'" class="icon"></a>'."\n".'';
print '<div class="friend-request-buttons">
        <a href="#" class="button received-request-button" data-modal-open="#received-request-confirm-page" data-user-id="'.htmlspecialchars($row_news_user_select['user_id']).'" data-screen-name="'.htmlspecialchars($row_news_user_select['screen_name']).'"'.($friend_request_mii['official'] == 1 ? ' data-is-identified="1"' : null).' data-mii-face-url="'.$friend_request_mii['output'].'" data-pid="'.$row_news_user_select['pid'].'" data-body="'.$friend_requests['message'].'">View Friend Request</a>
        <span class="ok-message none"></span>
      </div>
<div class="body">
        <p class="title">';
		require_once '../grplib-php/user-helper.php';
		print '
          <span class="nick-name">'.htmlspecialchars($row_news_user_select['screen_name']).'</span>
        </p>
        <p class="text">'.getProfileComment($row_news_user_select, false).'</p>
      </div>';


print '
</li>';
$result_update = $mysql->query('UPDATE friend_requests SET friend_requests.has_read = "1" WHERE friend_requests.news_id = "'.$friend_requests['news_id'].'"');	
}
print '
</ul>';
print '<div id="received-request-confirm-page" class="friend-request-confirm-page window-page none" data-modal-types="confirm-relationship confirm-received-request" data-is-template="1">
  <div class="window user-window">
    <h1 class="window-title">Friend Request</h1>
    <div class="window-body"><div class="window-body-inner">
      <div class="user-container">
        <span class="icon-container"><img src="data:image/gif;base64,R0lGODlhEAAQAIAAAP%2F%2F%2FwAAACH5BAEAAAAALAAAAAAQABAAAAIOhI%2Bpy%2B0Po5y02ouzPgUAOw%3D%3D" class="icon"></span>
        <p class="nickname">
          <span class="screen-name"></span>
          <span class="id-name"></span>
        </p>
      </div>
      <div class="message"><p class="message-inner"></p></div>
    </div></div>
    <div class="window-bottom-buttons">
      <input type="button" class="cancel-button button" value="Reject" data-action="/users/friend_request.delete.json" data-sound="SE_WAVE_CANCEL">
      <input type="button" class="post-button button" value="Accept" data-action="/users/friend_request.accept.json" data-done-msgid="olv.portal.friend_request.successed_with" data-track-category="friendRequest" data-track-action="acceptFriendRequest">
    </div>
  </div>
</div>';
}
	
# End tab-content, body-content tab2-content.
print '</div>';

if($_SERVER['HTTP_X_PJAX_CONTAINER'] != '.tab-body') {
print '  </div>';




print $GLOBALS['div_body_head_end'];
printFooter();
}


