<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

$search_title = $mysql->query('SELECT * FROM titles WHERE titles.olive_title_id = "'.$mysql->real_escape_string($_GET['title_id'] ?? 'a').'" AND titles.hidden != 1 LIMIT 1');

if(!$search_title) {
include '404.php';  exit(); }
elseif($search_title->num_rows == 0) {
include_once '404.php';  exit(); }

# Community listing.
if(isset($_GET['community_id'], $_GET['title_id'])) {

if(!empty($_GET['mode']) && $_GET['mode'] != 'hot') {
include '404.php';  exit(); }

$search_community = $mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$mysql->real_escape_string($_GET['title_id']).'" AND communities.olive_community_id = "'.$mysql->real_escape_string($_GET['community_id']).'" LIMIT 1');

$pagetitle = 'Communities';
function noCommErr() {
(empty($_SERVER['HTTP_X_REQUESTED_WITH']) ? http_response_code(404) : null); printHeader(false); printMenu(); print $GLOBALS['div_body_head']; print "\n".'<header id="header">
<h1 id="page-title" class="left">Communities</h1>
</header>
'; print '<div class="body-content track-error" data-track-error="community-404">';
noContentWindow('Community could not be found.'); print $GLOBALS['div_body_head_end']; printFooter();
}

if(!$search_community) {
include '404.php';  exit(); }
elseif($search_community->num_rows == 0) {
noCommErr();  exit(); }
$community = $search_community->fetch_assoc();
if($community['type'] == 5) {
noCommErr();  exit(); }
$title = $search_title->fetch_assoc();

# Success, show community.
require_once '../grplib-php/community-helper.php';
require_once 'lib/htmCommunity.php';
// Does ?date match a regex?
if((!empty($_GET['mode']) && $_GET['mode'] == 'hot' && !empty($_GET['date'])) && !preg_match('/^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/', $_GET['date'])) {
include '404.php';  exit(); }

$not_offset = !((!empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) || (!empty($_SERVER['HTTP_X_PJAX_CONTAINER']) && $_SERVER['HTTP_X_PJAX_CONTAINER'] == '#community-tab-body'));
$title_href = '/titles/'.$community['olive_title_id'].'/'.$community['olive_community_id'];
if($not_offset) {
$pagetitle = htmlspecialchars($title['name']);
printHeader(false); printMenu();

$user_permission = empty($_SESSION['pid']) || !postPermission($user, $community);

    print $GLOBALS['div_body_head'];
	print '
<header id="header">
<a id="header-post-button"'.($user_permission ? ' disabled' : null).' class="header-button'.($user_permission ? ' disabled' : null).' none"'.($user_permission ? '' : ' href="#"').' data-modal-open="#add-post-page">Post</a>';
$communities_search_others = $mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$title['olive_title_id'].'" AND communities.type != 5');
if($communities_search_others->num_rows >= 2) {
print '<a id="header-communities-button" href="/titles/'.$title['olive_title_id'].'" data-pjax="#body">Related Communities</a>'; }

print '<h1 id="page-title">'.$pagetitle.'</h1>
</header>
<div class="body-content" id="community-post-list">
';
# If the community has a banner, display it.
if(!empty($community['banner'])) {
print '<div class="header-banner-container"><img src="'.htmlspecialchars($community['banner']).'" class="header-banner'.($communities_search_others->num_rows >= 2 ? ' with-top-button' : null).'"></div>'; }
print '<div class="community-info info-content'.(!empty($community['banner']) ?  ' with-header-banner' : null).'">
<span class="icon-container"><img src="'.getIcon($community).'" class="icon">
</span>
';

if(!empty($title['platform_id'])) {
print '<span class="platform-tag platform-tag-'.($title['platform_id'] == 1 ? 'wiiu' : '3ds').'"></span>';
}

if(!empty($_SESSION['pid'])) {
print '
  <a href="#" data-modal-open="#title-settings-page" class="button setting-button" data-sound="SE_WAVE_OK_SUB"></a>
';
$community_favorite_rows = $mysql->query('SELECT * FROM favorites WHERE favorites.pid = "'.$_SESSION['pid'].'" AND favorites.community_id = "'.$community['community_id'].'"')->num_rows;
print '  <a href="#" class="favorite-button favorite-button-mini button'.($community_favorite_rows != 0 ? ' checked' : '').'" data-action-favorite="/titles/'.$community['olive_title_id'].'/'.$community['olive_community_id'].'/favorite.json" data-action-unfavorite="/titles/'.$community['olive_title_id'].'/'.$community['olive_community_id'].'/unfavorite.json" data-sound="SE_WAVE_CHECKBOX_'.($community_favorite_rows != 0 ? 'UN' : '').'CHECK" data-community-id="'.$community['olive_community_id'].'" data-url-id="" data-track-label="community" data-title-id="'.$community['olive_title_id'].'" data-track-action="cancelFavorite" data-track-category="favorite"></a>';
}
  if($community['type'] >= 1) {
  print '<span class="news-community-badge">'.($community['type'] == 2 ? 'Announcement Community' : 'Main Community').'</span>'; }

	print '<span class="title">'.htmlspecialchars($community['name']).'</span>
	<span class="text">'.htmlspecialchars($community['description']).'
</span>';

print '
</div>';
# Community info, etc, is done. 
# Place title settings RIGHT HERE when implemented.

# This is the hot/new posts selector.

// Add disabled to hot-post if announcement community:
// <li id="tab-header-hot-post" class="tab-button disabled"><a class="disabled"><span>Popular posts</span></a></li>
// \/
print '<menu class="tab-header">
    <li id="tab-header-post" class="tab-button'.(!empty($_GET['mode']) && $_GET['mode'] == 'hot' ? '' : ' selected').'" data-show-post-button="1">
        <a href="'.$title_href.'/new" data-pjax-replace="1" data-pjax="#community-tab-body" data-pjax-cache-container="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="new-post">All Posts</span></a>
    </li>
<li id="tab-header-hot-post" class="tab-button'.(!empty($_GET['mode']) && $_GET['mode'] == 'hot' ? ' selected' : '').'"><a href="'.$title_href.'/hot" data-pjax="#community-tab-body" data-pjax-cache-container="#body" data-pjax-replace="1" data-sound="SE_WAVE_SELECT_TAB"><span>Popular posts</span></a></li>
    
  </menu>';
// Title settings

if(!empty($_SESSION['pid'])) {
$search_settings = $mysql->query('SELECT * FROM settings_title WHERE settings_title.pid = "'.$_SESSION['pid'].'" AND settings_title.olive_title_id = "'.$title['olive_title_id'].'" LIMIT 1');
require_once 'lib/htmTemplates.php';
$pref_id = $search_settings->num_rows != 0 ? $search_settings->fetch_assoc()['value'] : 0;
titleSettingsPages($title, $pref_id);
} else {
$pref_id = 0;
	}
	
# This is where community tab body and post list is. 2 div conc.
print '<div id="community-tab-body" class="tab-body">
';
}
if(!empty($_GET['mode']) && $_GET['mode'] == 'hot') {
$search_posts = searchPopular($community, $_GET['date'] ?? date('Y-m-d'), 50, $_GET['offset'] ?? 0, true);
# Popular Posts! Do special function which is a passthrough to mysqli::result.
if(!empty($_GET['date'])) {
$date = strtotime($_GET['date']);	
} else {
$date = time();
}
print '
  <div class="pager-button date-pager">
  ';
    $back_query = findPastPopular(1, $date, $community);
	if($back_query) {
	// back, as in posts in the future
	print '<a href="'.$title_href.'/hot?date='.date('Y-m-d', strtotime($back_query)).'" class="button back-button" data-pjax="#community-tab-body" data-pjax-cache-container="#body" data-pjax-replace="1"><span class="symbol-label">←</span></a>';
	}
  print '
    <a href="'.$title_href.'/hot?date='.date('Y-m-d', $date).'" class="button selected" data-pjax="#community-tab-body" data-pjax-cache-container="#body" data-pjax-replace="1">'.date('m/d/Y', $date - 86400).'</a>
	';
	$next_query = findPastPopular(0, $date, $community);
	if($next_query) {
	// next, as in posts from the past
	print '<a href="'.$title_href.'/hot?date='.date('Y-m-d', strtotime($next_query)).'" class="button next-button" data-pjax="#community-tab-body" data-pjax-cache-container="#body" data-pjax-replace="1"><span class="symbol-label">→</span></a>';
	}
	print '
  </div>
';
    if(!$search_posts || $search_posts->num_rows == 0) {
	print '<div class="js-post-list post-list">';
noContentWindow('There are no popular posts.');
print '</div>'; } else {
print '<div class="js-post-list post-list" data-next-page-url="'.($search_posts->num_rows > 49 ? $title_href.'?offset='.(!empty($_GET['offset']) ? (!empty($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] + 50 : '') : 50) : '').'">';
while($post = $search_posts->fetch_assoc()) {
printPost($post, false, false, false);
}
print '</div>
 </div>
  </div>
  ';	
        }
}
else {
# Post list
$get_posts = $mysql->query('SELECT * FROM posts WHERE posts.community_id = "'.$community['community_id'].'" AND posts.is_hidden != "1" ORDER BY posts.created_at DESC LIMIT 50'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$mysql->real_escape_string($_GET['offset']) : null));			
    if(!$get_posts || $get_posts->num_rows == 0) {
	print '<div class="js-post-list post-list">';
noContentWindow('This community doesn\'t have any posts yet.');
print '</div>'; } else {
print '<div class="js-post-list post-list" data-next-page-url="'.($get_posts->num_rows > 49 ? $title_href.'?offset='.(!empty($_GET['offset']) ? (!empty($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] + 50 : '') : 50) : '').'">';
while($post = $get_posts->fetch_assoc()) {
printPost($post, false, false, false);
}
print '</div>
 </div>
  </div>
  ';	
        }
}
if($not_offset) {
# Post form
if(!empty($_SESSION['pid']) && (empty($_GET['mode']) || $_GET['mode'] != 'hot')) {
postForm('posts', $community, $user); }
print '

</div>

';
print $GLOBALS['div_body_head_end'];
	# End of community listing.
(empty($_SERVER['HTTP_X_PJAX']) ? printFooter() : '');
} }
elseif(isset($_GET['title_id'])) {
# Start of title listing.

$title = $search_title->fetch_assoc();
// Yes communities ; print start.
$pagetitle = htmlspecialchars($title['name']);
printHeader(false); printMenu(); 
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title">'.$pagetitle.'</h1>
</header>';
print '<div class="body-content" id="community-top">
		';
require_once 'lib/htmCommunity.php';
if(!empty($title['banner'])) {
print '<div class="header-banner-container"><img src="'.htmlspecialchars($title['banner']).'" class="header-banner"></div>'; }
		
print '<div class="community-list">';
		print '<ul class="list-content-with-icon-and-text arrow-list">
			
			';
$search_communities = $mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$title['olive_title_id'].'" ORDER BY communities.created_at');
                while($community = $search_communities->fetch_assoc()) {
printCommunityforTitle($community, $title);
            }
print '
   </div>
  </div>
 </div>
</li>
';
print '</ul>';
print 
'</div>
</div>';
print $GLOBALS['div_body_head_end'];
(empty($_SERVER['HTTP_X_PJAX']) ? printFooter() : '');
}

else {
include '404.php';	 exit();
}