<?php
	require_once '../grplib-php/init.php';
# If user isn't logged in, then 403 them.
if(empty($_SESSION["pid"])) {
require 'lib/htm.php';
notLoggedIn(); exit();
}

$pagetitle = loc('grp.portal.community');

   
    require_once 'lib/htm.php';
printHeader(false);
	printMenu();
	print $GLOBALS['div_body_head'];
print '<header id="header">
  
  <h1 id="page-title">'.$pagetitle.'</h1>

</header>';

$search_user_favorite_communities = $mysql->query('SELECT * FROM titles WHERE `hidden` = 0 ORDER BY created_at DESC');

print '<div class="body-content" id="community-top">

';
   print '
    <div class="community-list">
	';
if($search_user_favorite_communities->num_rows == 0) {
noContentWindow("No Communities?");
}
print '<ul class="list-content-with-icon-and-text arrow-list" id="community-top-content" data-next-page-url="">';
while($row_user_favorites = $search_user_favorite_communities->fetch_assoc()) {	
$row_get_community_from_cid = $mysql->query('SELECT * FROM communities WHERE communities.community_id = "'.$row_user_favorites['olive_community_id'].'"')->fetch_assoc();
require_once 'lib/htmCommunity.php';
print favoriteWithTitle($row_get_community_from_cid);

}
print '      </ul>
';
	print '
    </div>
	';
	
if(empty($is_fav_own)) { print '  </div>'; }
print '
</div>';

	print $GLOBALS['div_body_head_end'];
(!isset($_SERVER['HTTP_X_PJAX']) ? printFooter() : '');