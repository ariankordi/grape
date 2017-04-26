<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';
if(empty($_SESSION['pid'])) {
noLogin(); grpfinish($mysql); exit(); }

$pagetitle = 'Favorite Communities'; $mnselect = 'community';
printHeader('old'); printMenu('old');

require_once '../grplib-php/community-helper.php';
require_once 'lib/htmCommunity.php';
print '<div id="main-body">
';

print '<div class="body-content" id="community-top" data-region="">
    <h2 class="headline">
      Communities
      
    </h2>
    <h3 class="label">Favorite Communities</h3>
';
$search_favorites = $mysql->query('SELECT * FROM favorites WHERE favorites.pid = "'.$_SESSION['pid'].'" ORDER BY created_at DESC');
if($search_favorites->num_rows != 0) {
print '    <ul class="list community-list" data-next-page-url="">
';

while($favorites = $search_favorites->fetch_assoc()) {
$fav_comm = $mysql->query('SELECT * FROM communities WHERE communities.community_id = "'.$favorites['community_id'].'"')->fetch_assoc();
printCommunity($fav_comm);
}

print '
    </ul>
	'; } else {
noContentWindow('Tap the ☆ button on a community\'s page to have it show up as a favorite community here.');
	}
print '</div>';

print '
</div>';
printFooter('old');