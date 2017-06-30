<?php
require_once '../grplib-php/init.php';

require_once 'lib/htm.php';
$pagetitle = loc('grp.portal.community');
printHeader(false); printMenu();
print $GLOBALS['div_body_head'];
print '
<header id="header">
';
require_once '../grplib-php/community-helper.php';
require_once 'lib/htmCommunity.php';
if(!empty($_SESSION['pid'])) {
favButton();
}
$pg_no = (!empty($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 0);
$search_communities = prepared('SELECT * FROM titles WHERE (titles.hidden != 1 OR titles.hidden IS NULL) AND titles.platform_id IS NOT NULL ORDER BY titles.created_at DESC LIMIT 20 OFFSET ?', [$pg_no * 20]);
$num_titles = $mysql->query('SELECT COUNT(olive_title_id) AS ct FROM titles WHERE titles.platform_id IS NOT NULL')->fetch_assoc()['ct'];

print '  
  <h1 id="page-title" class="left">'.$pagetitle.'</h1>

</header>


<div class="body-content" id="community-top">
  <div class="community-list category-list">
    <div class="headline headline-wiiu">
      <h2>'.sprintf(loc('community', 'grp.portal.titles_headline'), $num_titles).'</h2>
      
    </div>
    
    <ul class="list-content-with-icon-column" id="community-top-content" data-next-page-url="">
        ';
	if($search_communities->num_rows == 0) {
	noContentWindow(loc('community', 'grp.portal.no_community'));
	} else {
while($communities = $search_communities->fetch_assoc()) {
printTitle($communities);
		}
	}
print '
    </ul>
        


';
if($num_titles > 19 && $search_communities->num_rows != 0) {
$pages = ceil($num_titles / 20);
print '<div class="pager-button">
';
	if($pg_no != 0) {
	print '<a href="?page='.($pg_no - 1).'" class="back-button symbol" data-pjax="#body"><span class="symbol-label">←</span></a>';
	}
for($i = 0; $i < $pages; $i++) {
     print '<a href="?page='.$i.'" class="button'.($pg_no == $i ? ' selected' : '').'" data-pjax="#body">'.($i+ 1).'</a>
	 ';
}
	if($pg_no != ($i - 1)) {
	print '<a href="?page='.($pg_no + 1).'" class="next-button symbol scroll" data-pjax="#body"><span class="symbol-label">→</span></a>';
	}
print '</div>';

/*
print '<div class="pager-button">
  <a href="?page=0" class="button selected" data-pjax="#body">1</a>
  <a href="?page=1" class="button" data-pjax="#body">2</a>
  <a href="?page=2" class="button" data-pjax="#body">3</a>
  <a href="?page=3" class="button" data-pjax="#body">4</a>
  <a href="?page=4" class="button" data-pjax="#body">5</a><span class="pager-ellipsis">…</span>
  <a href="?page=1" class="next-button symbol scroll" data-pjax="#body"><span class="symbol-label">→</span></a>
</div>';
*/
}
print '
  </div>
</div>

    ';
print $GLOBALS['div_body_head_end'];
printFooter();