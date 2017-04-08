<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';
if(empty($_SESSION['pid'])) {
# You are not logged in page
noLogin(); grpfinish($mysql); exit();
}
$pagetitle = 'Notifications';
$mnselect = 'news';
printHeader('old'); printMenu('old');
$search_news = $mysql->query('SELECT * FROM grape.news WHERE news.to_pid = "'.$_SESSION['pid'].'" AND news.merged IS NULL ORDER BY news.created_at DESC LIMIT 65');
print '<div id="main-body">



  
  

<div class="main-column">
  <div class="post-list-outline">

    <h2 class="headline">Updates</h2>
	
	
';
if($search_news->num_rows == 0) {
print '<div id="updates-no-content" class="no-content"><div>
    <p>No updates.</p>
  </div></div>';
} else {
# Found news
require_once 'lib/htmUser.php';
print '<div class="list news-list">
';

while($news_row = $search_news->fetch_assoc()) {
printNews($news_row);
$mysql->query('UPDATE grape.news SET news.has_read = "1" WHERE news.news_id = "'.$news_row['news_id'].'"');
}
print '

</div>';

}


print '  </div>
</div>

</div>';

printFooter('old');
grpfinish($mysql);