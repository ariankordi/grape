<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {

$pagetitle = loc('grp.portal.community'); $mnselect = 'community';
printHeader('old'); printMenu('old');

require_once '../grplib-php/community-helper.php';
require_once 'lib/htmCommunity.php';
$titles_show = prepared('SELECT * FROM titles WHERE titles.platform_id IS NOT NULL AND titles.hidden != 1 ORDER BY titles.created_at DESC LIMIT 30 OFFSET ?', [!empty($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] : 0]);
$num_titles = $mysql->query('SELECT COUNT(olive_title_id) AS ct FROM titles WHERE titles.platform_id IS NOT NULL')->fetch_assoc()['ct'];
print '<div id="main-body">
<div class="body-content" id="community-top">
    <h2 class="headline">
      '.loc('grp.portal.community').'
      
    </h2>
    <h3 class="label label-wiiu">
      '.sprintf(loc('community', 'grp.portal.titles_headline'), $num_titles).'
        
    </h3>
	';
}
print '    <ul class="list community-list community-title-list" data-next-page-url="'.($titles_show->num_rows > 29 ? '?offset='.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? (!empty($_GET['offset']) ? strval($_GET['offset']) + 30 : '') : 30) : '').'">
';
while($titles_show1 = $titles_show->fetch_assoc()) {
printTitle($titles_show1, ($mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$titles_show1['olive_title_id'].'" AND communities.type != "4" LIMIT 2')->num_rows == 2));
}
print '
    </ul>
';
if(empty($_SERVER['HTTP_X_AUTOPAGERIZE'])) {
print '</div>


      </div>';

printFooter('old');
}