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
if(!isset($_SERVER['X_AUTOPAGERIZE'])) {
# Community has been found
$community = $search_community->fetch_assoc();
$title = $mysql->query('SELECT * FROM titles WHERE titles.olive_title_id = "'.$mysql->real_escape_string($community['olive_title_id']).'"')->fetch_assoc();
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
if(postPermission($user, $community) == true) {
	print '
<form id="post-form" method="post" action="/posts" class="folded'.($user['official_user'] == '1' || $user['privilege'] >= 1 || $user['image_perm'] == '1' ? ' for-identified-user' : '').'">
  
  
  <input type="hidden" name="community_id" value="'.$community['community_id'].'">

  <div class="feeling-selector"><label class="symbol feeling-button feeling-button-normal checked"><input type="radio" name="feeling_id" value="0" checked=""><span class="symbol-label">normal</span></label><label class="symbol feeling-button feeling-button-happy"><input type="radio" name="feeling_id" value="1"><span class="symbol-label">happy</span></label><label class="symbol feeling-button feeling-button-like"><input type="radio" name="feeling_id" value="2"><span class="symbol-label">like</span></label><label class="symbol feeling-button feeling-button-surprised"><input type="radio" name="feeling_id" value="3"><span class="symbol-label">surprised</span></label><label class="symbol feeling-button feeling-button-frustrated"><input type="radio" name="feeling_id" value="4"><span class="symbol-label">frustrated</span></label><label class="symbol feeling-button feeling-button-puzzled"><input type="radio" name="feeling_id" value="5"><span class="symbol-label">puzzled</span></label>
  </div>


  <textarea name="body" class="textarea-text textarea" maxlength="1000" placeholder="Share your thoughts in a post to this community." data-open-folded-form="" data-required=""></textarea>
  ';
if($user['official_user'] == '1' || $user['privilege'] >= 1 || $user['image_perm'] == '1') {
print '
<input type="text" class="textarea-line url-form" name="url" placeholder="URL" maxlength="255">
<label class="file-button-container">
      <span class="input-label">Screenshot <span>JPEG/PNG/BMP</span></span>
      <input type="file" class="file-button" accept="image/jpeg">
      <input type="hidden" name="screenshot" value="">
    </label>
'; }
print '
  <label class="spoiler-button symbol">
    <input type="checkbox" id="is_spoiler" name="is_spoiler" value="1">
    Spoilers
  </label>
  
  
  <div class="form-buttons">
    <input type="submit" class="black-button post-button" value="Send">
  </div>
</form>
'; } }

print '<div class="body-content" id="community-post-list">

'; }
$community_get_posts = $mysql->query('SELECT * FROM posts WHERE posts.community_id = "'.$community['community_id'].'" AND posts.is_hidden != "1" ORDER BY posts.created_at DESC LIMIT 50'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET '.$mysql->real_escape_string($_GET['offset']) : '').'');
if(!$community_get_posts || $community_get_posts->num_rows == 0) {
print '<div class="no-content"><div>
        <p>This community doesn\'t have any posts yet.</p>
      </div></div>';
} else {
$mynewoffset = (!empty($_GET['offset']) ? strval($_GET['offset']) + 20 : '');
print '  <div class="list post-list js-post-list" data-next-page-url="'.($community_get_posts->num_rows > 49 ? '/titles/'.$community['olive_title_id'].'/'.$community['olive_community_id'].'?offset='.(!empty($_GET['offset']) ? $mynewoffset : 50) : '').'">


';
while($row_community_posts = $community_get_posts->fetch_assoc()) {	
print printPost($row_community_posts);
}
print '
</div>';
}
if(!isset($_SERVER['X_AUTOPAGERIZE'])) {
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

