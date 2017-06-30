<?php
	require_once '../grplib-php/init.php';
# If user isn't logged in, then 403 them.
if(empty($is_fav_own) && empty($_SESSION['pid'])) {
require 'lib/htm.php';
notLoggedIn(); exit();
}

if(!empty($is_fav_own)) {
if(!empty($_SESSION['pid']) && $_SESSION['pid'] == $is_fav_own) {
$user['pid'] = $_SESSION['pid'];
$pagetitle = loc('community', 'grp.portal.favorites_my');
} else {
$user = prepared('SELECT user_id, screen_name, pid FROM people WHERE people.pid = ?', [$is_fav_own])->fetch_assoc();
$pagetitle = sprintf(loc('community', 'grp.portal.favorites_other'), htmlspecialchars($user['screen_name'])); } }
else {
$pagetitle = loc('grp.portal.community');  }
   
    require_once 'lib/htm.php';
printHeader(false);
	printMenu();
	print $GLOBALS['div_body_head'];
print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';

if(!empty($is_fav_own)) {
$search_user_favorite_communities = prepared('SELECT * FROM favorites WHERE favorites.pid = ? ORDER BY favorites.created_at DESC', [$user['pid']]);
} else {
$search_user_favorite_communities = prepared('SELECT * FROM favorites WHERE favorites.pid = ? ORDER BY favorites.created_at DESC', [$_SESSION['pid']]);
}

print '<div class="body-content'.(empty($is_fav_own) ? ' tab2-content' : '').'" id="community-top">

';
if(empty($is_fav_own)) {
print '
  <menu class="tab-header tab-header-community">
    <li id="tab-header-favorite-community" class="tab-button selected"><a href="/communities/favorites" data-pjax="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"><span>'.loc('grp.portal.favorites').'</span></a></li>
    <li id="tab-header-played-post" class="tab-button disabled"><a class="disabled"><span>'.loc('grp.portal.played').'</span></a></li>
  </menu>
  
   <div class="tab-body">
'; }
   print '
    <div class="community-list">
	';
if($search_user_favorite_communities->num_rows == 0) {
if(!empty($is_fav_own)) {
noContentWindow(loc('community', 'grp.portal.no_favorites_other'));
} else {
noContentWindow(loc('community', 'grp.portal.no_favorites_my')); } }
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