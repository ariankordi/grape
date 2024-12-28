<?php
if($_SERVER["REQUEST_URI"] == "/title/show"){
  header("Location: /communities");
  exit();
}
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

$pagetitle = loc('grp.portal.community'); $mnselect = 'community';
print printHeader('old');
print printMenu('old');

require_once '../grplib-php/community-helper.php';
require_once 'lib/htmCommunity.php';
print '<div id="main-body">
';
print '
<div class="body-content" id="community-top">


  <div class="headline">
    <h2 class="headline-text">'.$pagetitle.'</h2>
    <form method="GET" action="/titles/search" class="search">
      <input type="text" name="query" placeholder="'.loc('grp.portal.search_title').'" minlength="2" maxlength="20"><input type="submit" value="q" title="'.loc('grp.portal.search').'">
    </form>
  </div>
';
if(!empty($_SESSION['pid'])) {
$popular_rows = $mysql->query("SELECT communities.olive_title_id, communities.olive_community_id, name, icon FROM communities JOIN posts ON communities.olive_community_id = posts.community_id AND posts.created_at > NOW() - INTERVAL 24 HOUR AND posts.is_hidden = 0 WHERE communities.hidden = 0 GROUP BY communities.olive_community_id ORDER BY COUNT(posts.id) DESC LIMIT 5");
$search_favorite_communitities = $mysql->query('SELECT * FROM favorites WHERE favorites.pid = "'.$_SESSION['pid'].'" ORDER BY created_at DESC LIMIT 5');
if($search_favorite_communitities->num_rows != 0) {
print '<h3 class="label">'.loc('community', 'grp.portal.favorites_my').'</h3>
<ul class="list community-list">
';
while($favorites = $search_favorite_communitities->fetch_assoc()) {
$fav_comm = $mysql->query('SELECT * FROM communities WHERE communities.community_id = "'.$favorites['community_id'].'"')->fetch_assoc();
printCommunity($fav_comm);
}
print '
  </ul>
<div class="buttons-content">
      <a href="/communities/favorites" class="button">'.loc('grp.portal.show_more').'</a>
    </div>';

} }
?>
  <div id="identified-user-banner">
    <a href="/identified_user_posts" data-pjax="#body" class="list-button us">
      <span class="title"><?=loc('grp.portal.identified_user_banner-title')?></span>
      <span class="text"><?=loc('grp.portal.identified_user_banner-text')?></span>
    </a>
  </div>


  <div id="tab-wiiu-body" class="tab-body">
    <?php if($popular_rows->num_rows != 0){ ?>
  <h3 class="label label-wiiu"><img src="/img/hot-icon-wiiu.png" class="hot-icon">Spotlight</h3>
  <ul class="icon-list">
    <?php while($community = $popular_rows->fetch_assoc()){ ?>
      <li>
        <a href="/titles/<?=$community['olive_title_id']?>/<?=$community['olive_community_id']?>" data-pjax="#body">
          <span class="icon-container"><img src="<?=getIcon($community)?>" class="icon"></span>
        </a>
      </li>
      <?php } ?>
    </ul>
      <?php } ?>
    <h3 class="label label-wiiu"><?=loc('grp.portal.community_headline')?></h3>

    <ul class="list community-list community-title-list">
<?php
$titles_show1 = $mysql->query('SELECT * FROM titles WHERE titles.platform_id IS NOT NULL AND titles.hidden != 1 ORDER BY titles.created_at DESC LIMIT 20');
while($titles_show = $titles_show1->fetch_assoc()) {
print printTitle($titles_show);
}
print '

    </ul>

  <div class="buttons-content">
      <a href="/communities/categories/wiiu_all" class="button">'.loc('grp.portal.show_more').'</a>
    </div>
	
  </div>
  

  <h3 class="label">'.loc('grp.portal.special_headline').'</h3>
  <ul class="list community-list community-title-list">

';
$titles_show2 = $mysql->query('SELECT * FROM titles WHERE titles.platform_id IS NULL AND titles.hidden != 1 ORDER BY titles.created_at DESC LIMIT 20');
while($titles_show3 = $titles_show2->fetch_assoc()) {
print printTitle($titles_show3, ($mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$titles_show['olive_title_id'].'" AND communities.type != "4" LIMIT 2')->num_rows == 2 ? true : false));
}
print '

  </ul>

</div>

      </div>';


print printFooter('old');


