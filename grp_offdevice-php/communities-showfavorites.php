<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';
if(empty($_SESSION['pid'])) {
noLogin(); grpfinish($mysql); exit(); }

if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {

$pagetitle = loc('community', 'grp.portal.favorites_my'); $mnselect = 'community';
printHeader('old'); printMenu('old');

require_once '../grplib-php/community-helper.php';
require_once 'lib/htmCommunity.php';
print '<div id="main-body">
';

print '<div class="body-content" id="community-top">
    <h2 class="headline">
      '.loc('grp.portal.community').'
      
    </h2>
    <h3 class="label">'.$pagetitle.'</h3>
';
}
$search_favorites = prepared('SELECT * FROM favorites WHERE favorites.pid = ? ORDER BY created_at DESC LIMIT 30 OFFSET ?', [$_SESSION['pid'], (!empty($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] : 0)]);
if($search_favorites->num_rows != 0) {
print '    <ul class="list community-list" data-next-page-url="'.($search_favorites->num_rows > 29 ? '?offset='.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? (!empty($_GET['offset']) ? strval($_GET['offset']) + 30 : '') : 30) : '').'">
';

while($favorites = $search_favorites->fetch_assoc()) {
$fav_comm = $mysql->query('SELECT * FROM communities WHERE communities.community_id = "'.$favorites['community_id'].'"')->fetch_assoc();
printCommunity($fav_comm);
}

print '
    </ul>
	'; } else {
noContentWindow(loc('community', 'grp.portal.no_favorites_my'));
	}
if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
print '</div>';

print '
</div>';
printFooter('old');
}