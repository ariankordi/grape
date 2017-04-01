<?php
	include 'lib/sql-connect.php';
if(empty($is_fav_own)) {
# If user isn't logged in, then 403 them.
if(empty($_SESSION['pid'])) {
if(isset($grp_config_server_type) && $grp_config_server_type == 'prod') {
include 'communities.php';
exit();
}
else {
if(isset($_SERVER['HTTP_X_PJAX'])) {
header('Content-Type: application/json; charset=UTF-8');
header("HTTP/1.1 401 Unauthorized");
			print '{"success":0,"errors":[{"message":"You have been logged out.\nPlease log back in.","error_code":1510110}],"code":"401"}';
			exit ("\n");
}
else {
header('Content-Type: text/plain; charset=UTF-8');
header("HTTP/1.1 403 Forbidden");
exit("403 Forbidden\n");
} } }
}

if(!empty($is_fav_own)) {
$row_userfavorites_user = mysqli_fetch_assoc(mysqli_query($link, 'SELECT user_id, screen_name, pid FROM people WHERE people.pid = "'.mysqli_real_escape_string($link, $is_fav_own).'"'));
$pagetitle = htmlspecialchars($row_userfavorites_user['screen_name'])."'s Favorite Communities"; }
else {
$pagetitle = (isset($grp_config_server_type) && $grp_config_server_type == 'dev' && isset($grp_config_server_env) ? 'Communities ('.$grp_config_server_env.')' : 'Communities' );  }
   
    include 'lib/header.php';
	include 'lib/user-menu.php';
	print $div_body_head;
print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';

if(!empty($is_fav_own)) {
$search_user_favorite_communities = mysqli_query($link, 'SELECT * FROM favorites WHERE favorites.pid = "'.$row_userfavorites_user['pid'].'" ORDER BY favorites.created_at DESC');
} else {
$search_user_favorite_communities = mysqli_query($link, 'SELECT * FROM favorites WHERE favorites.pid = "'.$_SESSION['pid'].'" ORDER BY favorites.created_at DESC');
}

print '<div class="body-content'.(empty($is_fav_own) ? ' tab2-content' : '').'" id="community-top">

';
if(empty($is_fav_own)) {
print '
  <menu class="tab-header tab-header-community">
    <li id="tab-header-favorite-community" class="tab-button selected"><a href="/communities/favorites" data-pjax="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"><span>Favorites</span></a></li>
    <li id="tab-header-played-post" class="tab-button disabled"><a class="disabled"><span>Software Used</span></a></li>
  </menu>
  
   <div class="tab-body">
'; }
   print '
    <div class="community-list">
	';
if(mysqli_num_rows($search_user_favorite_communities) == 0) {
if(!empty($is_fav_own)) {
$no_content_message = 'No favorite communities added yet.';
} else {
$no_content_message = "Tap the ☆ button on a community's page to have it show up as a favorite community here."; }
include 'lib/no-content-window.php'; }
else {
print '<ul class="list-content-with-icon-and-text arrow-list" id="community-top-content" data-next-page-url="">';
while($row_user_favorites = mysqli_fetch_assoc($search_user_favorite_communities)) {	
$row_get_community_from_cid = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM communities WHERE communities.community_id = "'.$row_user_favorites['community_id'].'"'));
require_once 'lib/community-template.php';
print favoriteWithTitle($row_get_community_from_cid);

}
print '      </ul>
';
 }
	print '
    </div>
	';
	
if(empty($is_fav_own)) { print '  </div>'; }
print '
</div>';

	print $div_body_head_end;
(!isset($_SERVER['HTTP_X_PJAX']) ? include 'lib/footer.php' : '');
?>