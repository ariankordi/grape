<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

$search_title = $mysql->query('SELECT * FROM titles WHERE titles.olive_title_id = "'.$mysql->real_escape_string($_GET['title_id'] ?? 'a').'" AND titles.hidden != 1');

if(!$search_title) {
$pagetitle = 'Error'; print printHeader('old'); print printMenu('old'); print notFound('d', false); printFooter('old');  exit(); } elseif($search_title->num_rows == 0) { $pagetitle = 'Error'; print printHeader('old'); print printMenu('old'); print notFound('d', false); printFooter('old');  exit(); 
}

if(isset($_GET['community_id'])) {
# Display community
$search_community = $mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$mysql->real_escape_string($_GET['title_id']).'" AND communities.olive_community_id = "'.$mysql->real_escape_string($_GET['community_id']).'" AND communities.type != 5');
if(!$search_community) {
$pagetitle = 'Error'; print printHeader('old'); print printMenu('old'); print notFound('d', false); printFooter('old');  exit(); } elseif($search_community->num_rows == 0) { $pagetitle = 'Error'; print printHeader('old'); print printMenu('old'); print notFound('d', false); printFooter('old');  exit(); 
}
else {
require_once 'lib/htmCommunity.php';
require '../grplib-php/community-helper.php';
# Community has been found
$community = $search_community->fetch_assoc();
$title = $search_title->fetch_assoc();
$is_popular = !empty($_GET['mode']) && $_GET['mode'] == 'hot';
$title_href = '/titles/'.$community['olive_title_id'].'/'.$community['olive_community_id'];
if(!isset($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
$pagetitle = htmlspecialchars($title['name']);
$mnselect = 'community';
print printHeader('old');
print printMenu('old');
print '<div id="main-body">
';
print '<div id="page-title">'.$pagetitle.'</div>'.(!empty($community['banner_3ds']) ? '<div class="header-banner-container"><img src="'.htmlspecialchars($community['banner_3ds']).'"></div>' : '').'
<div id="community-content" class>
  <span class="icon-container"><img src="'.getIcon($community).'" class="icon"></span>';
  if(!empty($title['platform_id'])) {
print '  <span class="platform-tag"><img src="/img/platform-tag-'.($title['platform_id'] == 1 ? 'wiiu' : '3ds').'.png"></span>'; }
if(!empty($community['type']) && $community['type'] >= 1) {
  print '<span class="news-community-badge">'.($community['type'] == 2 ? 'Announcement Community' : 'Main Community').'</span>
'; }
print '  <span class="title">'.htmlspecialchars($community['name']).'</span>
  <span class="text">'.htmlspecialchars($community['description']).'</span>
  <div class="buttons-content">
  ';
if(!empty($_SESSION['pid'])) {
$find_community_favorite = $mysql->query('SELECT * FROM favorites WHERE favorites.pid = "'.$_SESSION['pid'].'" AND favorites.community_id = "'.$community['community_id'].'"');
print '	<button type="button" class="symbol button favorite-button'.($find_community_favorite->num_rows != 0 ? ' checked' : '').'" data-action-favorite="'.$title_href.'/favorite.json" data-action-unfavorite="'.$title_href.'/unfavorite.json"><span class="favorite-button-text">Favorite</span></button>'; }

if(!empty($_SESSION['pid'])) {
$search_settings = $mysql->query('SELECT * FROM settings_title WHERE settings_title.pid = "'.$_SESSION['pid'].'" AND settings_title.olive_title_id = "'.$title['olive_title_id'].'" LIMIT 1');
$pref_id = $search_settings->num_rows != 0 ? $search_settings->fetch_assoc()['value'] : 0;
} else {
$pref_id = 0;
	}

print '
	</div>
  ';
if($mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$community['olive_title_id'].'" AND communities.type != 5 ORDER BY created_at DESC LIMIT 2')->num_rows >= 2) {  print '<a href="/titles/'.$title['olive_title_id'].'" class="arrow-button"><span>Related Communities</span></a>'; }
  print '
</div>';

print '

  <div id="posts-filter-tab-container" class="select-tab2 ">
    <div id="posts-filter-container" class="posts-filter-tab symbol'.(!$is_popular ? ' selected ' : '').'">

          <a'.(!$is_popular ? ' selected' : '').' href="'.$title_href.'/new">All Posts</a>
      
    </div>

    <div id="hot-posts-filter-container" class="posts-filter-tab'.($is_popular ? ' selected' : '').'">
      <a'.($is_popular ? ' selected' : '').' href="'.$title_href.'/hot">Popular posts</a>
    </div>
  </div>
  ';

if(!empty($_SESSION['pid']) && !$is_popular) {
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
postForm($community, $user, false);
}
print '<div class="body-content" id="community-post-list" data-region="">

'; }
if($is_popular) {
$search_posts = searchPopular($community, $_GET['date'] ?? date('Y-m-d'), 50, $_GET['offset'] ?? 0, true);

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
	// back
	print '<a href="'.$title_href.'/hot?date='.date('Y-m-d', strtotime($back_query)).'" class="button back-button symbol"><span class="symbol-label">←</span></a>';
	}
  print '
    <a href="'.$title_href.'/hot?date='.htmlspecialchars($_GET['date'] ?? date('Y-m-d')).'" class="button selected">'.date('m/d/Y', $date - 86400).'</a>
	';
	$next_query = findPastPopular(0, $date, $community);
	if($next_query) {
	// next
	print '<a href="'.$title_href.'/hot?date='.date('Y-m-d', strtotime($next_query)).'" class="button next-button symbol"><span class="symbol-label">→</span></a>';
	}
	print '
  </div>
';
if(!$search_posts || $search_posts->num_rows == 0) {
print '<div class="no-content"><div>
        <p>There are no popular posts.</p>
      </div></div>';
} else {
print '  <div class="list post-list" data-next-page-url="'.($search_posts->num_rows > 49 ? $title_href.'?offset='.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? (!empty($_GET['offset']) ? strval($_GET['offset']) + 20 : '') : 50) : '').'">


';
while($row_community_posts = $search_posts->fetch_assoc()) {	
print printPost($row_community_posts);
}
print '
</div>';
		}
	} else {
$community_get_posts = $mysql->query('SELECT * FROM posts WHERE posts.community_id = "'.$community['community_id'].'" AND posts.is_hidden != "1" ORDER BY posts.created_at DESC LIMIT 50'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$mysql->real_escape_string($_GET['offset']) : '').'');
if(!$community_get_posts || $community_get_posts->num_rows == 0) {
print '<div class="no-content"><div>
        <p>This community doesn\'t have any posts yet.</p>
      </div></div>';
} else {
$mynewoffset = (!empty($_GET['offset']) ? strval($_GET['offset']) + 20 : '');
print '  <div class="list post-list" data-next-page-url="'.($community_get_posts->num_rows > 49 ? $title_href.'?offset='.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? $mynewoffset : 50) : '').'">


';
while($row_community_posts = $community_get_posts->fetch_assoc()) {	
print printPost($row_community_posts);
}
print '
</div>';
	}
}
if(!isset($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
print '
</div>

</div>';
printFooter('old');
} }


}
elseif(isset($_GET['title_id'])) {
require_once 'lib/htmCommunity.php';
# Display title	
# Succ(ess)
$row_title = $search_title->fetch_assoc();
$get_communities = $mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$row_title['olive_title_id'].'" AND communities.type != 5 ORDER BY created_at');
$pagetitle = htmlspecialchars($row_title['name']);
$mnselect = 'community';
print printHeader('old');
print printMenu('old');
print '
<div id="main-body">

<div id="page-title">'.$pagetitle.'</div>
';
if(!empty($row_title['banner_3ds'])) {
print '
<div class="header-banner-container"><img src="'.htmlspecialchars($row_title['banner_3ds']).'"></div>
'; }
print '
<ul class="list community-list">
  
';
while($row_communities = $get_communities->fetch_assoc()) {
print printTitle2($row_communities, $row_title);
}
print '

</ul>
      </div>
';
print printFooter('old');


}

else { $pagetitle = 'Error'; print printHeader('old'); print printMenu('old'); print notFound('d', false); printFooter('old');  exit(); }
