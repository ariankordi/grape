<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

$pagetitle = 'Communities'; $mnselect = 'community';
print printHeader('old');
print printMenu('old');

require_once '../grplib-php/community-helper.php';
require_once 'lib/htmCommunity.php';
print '<div id="main-body">
';
print '
<div class="body-content" id="community-top">


  <div class="headline">
    <h2 class="headline-text">Communities</h2>
    <form method="GET" action="/titles/search" class="search">
      <input type="text" name="query" placeholder="Search Communities" minlength="2" maxlength="20"><input type="submit" value="q" title="Search">
    </form>
  </div>


  <div id="identified-user-banner">
    <a href="/identified_user_posts" data-pjax="#body" class="list-button us">
      <span class="title">Get the latest news here!</span>
      <span class="text">Posts from Verified Users</span>
    </a>
  </div>


  <div id="tab-wiiu-body" class="tab-body">
    
    

    <h3 class="label label-wiiu">
      New Communities
      
    </h3>

    <ul class="list community-list community-title-list">
';
$titles_show1 = $mysql->query('SELECT * FROM titles WHERE titles.platform_id IS NOT NULL ORDER BY titles.created_at DESC LIMIT 20');
while($titles_show = $titles_show1->fetch_assoc()) {
print printTitle($titles_show, ($mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$titles_show['olive_title_id'].'" AND communities.type != "4" LIMIT 2')->num_rows == 2 ? true : false));
}
print '

    </ul>
    
  </div>
  

  <h3 class="label">Special</h3>
  <ul class="list community-list community-title-list">

';
$titles_show2 = $mysql->query('SELECT * FROM titles WHERE titles.platform_id IS NULL ORDER BY titles.created_at DESC LIMIT 20');
while($titles_show3 = $titles_show2->fetch_assoc()) {
print printTitle($titles_show3, ($mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$titles_show['olive_title_id'].'" AND communities.type != "4" LIMIT 2')->num_rows == 2 ? true : false));
}
print '

  </ul>

</div>

      </div>';


print printFooter('old');
grpfinish($mysql);

