<?php
include 'lib/sql-connect.php';
$pagetitle = 'Posts from Verified Users';
include 'lib/header.php';
include 'lib/user-menu.php';

print $div_body_head;
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

$sql_identified_users_select = 'select a.*, bm.recent_created_at from (select pid, max(created_at) as recent_created_at from posts group by pid) bm inner join people a on bm.pid = a.pid WHERE a.official_user = "1" ORDER BY recent_created_at DESC';
$result_identified_users_select = mysqli_query($link, $sql_identified_users_select);	

print ' 	<ul class="list-content-with-icon-and-text js-post-list post-list test-identified-post-list" data-next-page-url="">';
if(mysqli_num_rows($result_identified_users_select) == 0) {
print null; } else {
while($row_identified_users_select = mysqli_fetch_assoc($result_identified_users_select)) {
print '<li class="scroll">'."\n".'';
$sql_identified_users_posts = 'SELECT * FROM grape.posts WHERE posts.pid = "'.$row_identified_users_select['pid'].'" AND posts.is_hidden != "1" AND posts.is_spoiler != "1" ORDER BY posts.created_at DESC LIMIT 1';
$result_identified_users_posts = mysqli_query($link, $sql_identified_users_posts);
$row_identified_users_posts = mysqli_fetch_assoc($result_identified_users_posts);

$row_temp_current_post = $row_identified_users_posts;
$sql_act_people_posts_replies = 'SELECT * FROM grape.replies WHERE replies.reply_to_id = "'.$row_identified_users_posts['id'].'" AND replies.is_hidden != "1"';
$result_act_people_posts_replies = mysqli_query($link, $sql_act_people_posts_replies);
$sql_act_people_posts_empathies = 'SELECT * FROM grape.empathies WHERE empathies.id = "'.$row_identified_users_posts['id'].'"';
$result_act_people_posts_empathies = mysqli_query($link, $sql_act_people_posts_empathies);

$result_temp_current_post_replies = $result_act_people_posts_replies;
$result_temp_current_post_empathies = $result_act_people_posts_empathies;
$row_temp_current_post_user = $row_identified_users_select;
$is_identified_user_post = '1';

include 'lib/userpage-post-template.php';
print ''."\n".'</li>';

       } 			
}
print '      </ul>
    </div>
  </div>
</div>';


print '<div id="unfollow-confirm-page" class="unfollow-confirm-page window-page none" data-modal-types="confirm-relationship confirm-unfollow" data-is-template="1">
  <div class="window user-window">
    <h1 class="window-title">Unfollow</h1>
    <div class="window-body"><div class="window-body-inner">
      <div class="user-container">
        <span class="icon-container"><img src="data:image/gif;base64,R0lGODlhEAAQAIAAAP%2F%2F%2FwAAACH5BAEAAAAALAAAAAAQABAAAAIOhI%2Bpy%2B0Po5y02ouzPgUAOw%3D%3D" class="icon"></span>
        <p class="nickname">
          <span class="screen-name"></span>
          <span class="id-name"></span>
        </p>
      </div>
      <div class="message">
        <p>Stop following this user?</p>
      </div>
    </div></div>
    <div class="window-bottom-buttons">
      <input type="button" class="cancel-button button" value="Back" data-sound="SE_WAVE_CANCEL">
      <input type="button" class="post-button button" value="Yes" data-done-msgid="olv.portal.unfollow_succeeded_to">
    </div>
  </div>
</div>';
print $div_body_head_end;
include 'lib/footer.php';

?>