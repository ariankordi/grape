<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

if(isset($_GET['community_id'])) {
# Display community
$search_community = $mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$mysql->real_escape_string($_GET['title_id']).'" AND communities.olive_community_id = "'.$mysql->real_escape_string($_GET['community_id']).'" AND communities.type != "5"');
if(!$search_community) {
$pagetitle = 'Error'; print printHeader('old'); print printMenu('old'); print notFound('d', false); printFooter('old'); grpfinish($mysql); exit(); } elseif($search_community->num_rows == 0) { $pagetitle = 'Error'; print printHeader('old'); print printMenu('old'); print notFound('d', false); printFooter('old'); grpfinish($mysql); exit(); 
}
else {
require_once 'lib/htmCommunity.php';
require '../grplib-php/community-helper.php';
# Community has been found
$community = $search_community->fetch_assoc();
$title = $mysql->query('SELECT * FROM titles WHERE titles.olive_title_id = "'.$mysql->real_escape_string($community['olive_title_id']).'"')->fetch_assoc();
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
print '  <span class="platform-tag"><img src="https://i.imgur.com/'.($title['platform_id'] == 1 ? 'nZkp8NW' : 'VaXHOg6').'.png"></span>'; }
if(!empty($community['type']) && $community['type'] >= 1) {
  print '<span class="news-community-badge">'.($community['type'] == 2 ? 'Announcement Community' : 'Main Community').'</span>
'; }
print '  <span class="title">'.htmlspecialchars($community['name']).'</span>
  <span class="text">'.htmlspecialchars($community['description']).'</span>
  <div class="buttons-content">
  ';
#if(!empty($_SESSION['pid'])) {
# put favorite/unfavorite checks here
#print '	<button type="button" class="symbol button favorite-button" data-action-favorite="/titles/'.$community['olive_title_id'].'/'.$community['olive_community_id'].'/favorite.json" data-action-unfavorite="/titles/'.$community['olive_title_id'].'/'.$community['olive_community_id'].'/unfavorite.json"><span class="favorite-button-text">Favorite</span></button>'; }
print '
	</div>
  ';
if($mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$community['olive_title_id'].'" ORDER BY created_at DESC LIMIT 2')->num_rows >= 2) {  print '<a href="/titles/'.$title['olive_title_id'].'" class="arrow-button"><span>Related Communities</span></a>'; }
  print '
</div>';

print '

  <div id="posts-filter-tab-container" class="select-tab2 ">
    <div id="posts-filter-container" class="posts-filter-tab symbol selected ">

          <a selected href="/titles/'.$community['olive_title_id'].'/'.$community['olive_community_id'].'/new">All Posts</a>
      
    </div>

    <div id="hot-posts-filter-container" class="posts-filter-tab disabled">
      <a>Popular posts</a>
    </div>
  </div>
  ';

if(!empty($_SESSION['pid'])) {
$user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
postForm($community, $user, false);
}

print '<div class="body-content" id="community-post-list" data-region="">

'; }
$community_get_posts = $mysql->query('SELECT * FROM posts WHERE posts.community_id = "'.$community['community_id'].'" AND posts.is_hidden != "1" ORDER BY posts.created_at DESC LIMIT 50'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$mysql->real_escape_string($_GET['offset']) : '').'');
if(!$community_get_posts || $community_get_posts->num_rows == 0) {
print '<div class="no-content"><div>
        <p>This community doesn\'t have any posts yet.</p>
      </div></div>';
} else {
$mynewoffset = (!empty($_GET['offset']) ? strval($_GET['offset']) + 20 : '');
print '  <div class="list post-list" data-next-page-url="'.($community_get_posts->num_rows > 49 ? '/titles/'.$community['olive_title_id'].'/'.$community['olive_community_id'].'?offset='.(!empty($_GET['offset']) ? $mynewoffset : 50) : '').'">


';
while($row_community_posts = $community_get_posts->fetch_assoc()) {	
print printPost($row_community_posts);
}
print '
</div>';
}
if(!isset($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
print '
</div>

</div>';
printFooter('old');
} }


} elseif(isset($_GET['title_id'])) {
require_once 'lib/htmCommunity.php';
# Display title	
$search_title = $mysql->query('SELECT * FROM titles WHERE titles.olive_title_id = "'.$mysql->real_escape_string($_GET['title_id']).'"');
if(!$search_title) {
$pagetitle = 'Error'; print printHeader('old'); print printMenu('old'); print notFound('d', false); printFooter('old'); grpfinish($mysql); exit(); } elseif($search_title->num_rows == 0) { $pagetitle = 'Error'; print printHeader('old'); print printMenu('old'); print notFound('d', false); printFooter('old'); grpfinish($mysql); exit(); 
}
else {
# Succ(ess)
$row_title = $search_title->fetch_assoc();
$get_communities = $mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$row_title['olive_title_id'].'" AND communities.type != "5" ORDER BY created_at');
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


}

else { $pagetitle = 'Error'; print printHeader('old'); print printMenu('old'); print notFound('d', false); printFooter('old'); grpfinish($mysql); exit(); }

