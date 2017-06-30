<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';
$pagetitle = 'Search Communities'; $bodyClass = 'search';
printHeader('old'); printMenu('old');

$search = prepared('SELECT * FROM titles WHERE titles.name '.(empty($_GET['query']) ? '= ?' : 'LIKE CONCAT("%", ?, "%")').' ORDER BY titles.created_at DESC LIMIT 50', [!empty($_GET['query']) ? addcslashes($_GET['query'], '%_') : '']);

print '<div id="main-body">
  <h2 class="headline">Search Communities</h2>

  <form method="GET" action="/titles/search" class="search">
    <input type="text" name="query" placeholder="Mario, etc." minlength="2" maxlength="20"><input type="submit" value="q" title="Search">
  </form>
';
if(!$search || $search->num_rows == 0) {
print '<div class="search-content no-content">
      <p>No communities found for<br>
"'.htmlspecialchars($_GET['query'] ?? '').'". Please try again.</p>
  </div>
  ';
} else {
require_once '../grplib-php/community-helper.php';
require_once 'lib/htmCommunity.php';
print '  <div class="search-content">
      <p class="note">Communities found for "'.htmlspecialchars($_GET['query']).'".</p>
      <ul class="list community-list community-title-list">
';
while($titles = $search->fetch_assoc()) {
printTitle($titles);
}
print ' 
      </ul>
  </div>
'; }
print '      </div>';

printFooter('old');