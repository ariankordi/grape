<?php
	require_once '../grplib-php/init.php';
if(empty($is_fav_own)) {
# If user isn't logged in, then 403 them.
if(empty($_SESSION['pid'])) {
if(isset($grp_config_server_type) && $grp_config_server_type == 'prod') {
include_once 'communities.php';
grpfinish($mysql); exit();
}
else {
require 'lib/htm.php';
notLoggedIn(); grpfinish($mysql); exit();
} }
}

if(!empty($is_fav_own)) {
$row_userfavorites_user = mysqli_fetch_assoc(mysqli_query($mysql, 'SELECT user_id, screen_name, pid FROM people WHERE people.pid = "'.mysqli_real_escape_string($mysql, $is_fav_own).'"'));
$pagetitle = htmlspecialchars($row_userfavorites_user['screen_name'])."'s Favorite Communities"; }
else {
$pagetitle = (isset($grp_config_server_type) && $grp_config_server_type == 'dev' && isset($grp_config_server_env) ? 'Communities ('.$grp_config_server_env.')' : 'Communities' );  }
   
    require_once 'lib/htm.php';
printHeader(false);
	printMenu();
	print $GLOBALS['div_body_head'];
print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';

if(!empty($is_fav_own)) {
$search_user_favorite_communities = $mysql->query('SELECT * FROM favorites WHERE favorites.pid = "'.$row_userfavorites_user['pid'].'" ORDER BY favorites.created_at DESC');
} else {
$search_user_favorite_communities = $mysql->query('SELECT * FROM favorites WHERE favorites.pid = "'.$_SESSION['pid'].'" ORDER BY favorites.created_at DESC');
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
if($search_user_favorite_communities->num_rows == 0) {
if(!empty($is_fav_own)) {
noContentWindow('No favorite communities added yet.');
} else {
noContentWindow('Tap the ☆ button on a community\'s page to have it show up as a favorite community here.'); } }
else {
print '<ul class="list-content-with-icon-and-text arrow-list" id="community-top-content" data-next-page-url="">';
while($row_user_favorites = $search_user_favorite_communities->fetch_assoc()) {	
$row_get_community_from_cid = $mysql->query('SELECT * FROM communities WHERE communities.community_id = "'.$row_user_favorites['community_id'].'"')->fetch_assoc();
require_once 'lib/htmCommunity.php';
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

	print $GLOBALS['div_body_head_end'];
(!isset($_SERVER['HTTP_X_PJAX']) ? printFooter() : '');
