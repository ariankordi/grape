<?php
require_once '../grplib-php/init.php';

$pagetitle = 'Search Users';

require_once 'lib/htm.php';
printHeader(false);
printMenu();

print $GLOBALS['div_body_head'];
print '<header id="header">'."\n".'
  <h1 id="page-title">'.$pagetitle.'</h1>'."\n".'
</header>'."\n".'';
print '<div class="body-content" id="user-search-list-page">'."\n".'';

# Search for user here

require_once '../grplib-php/user-helper.php';
$search_user = searchUser();

if($search_user->num_rows == 0 || empty($_GET['query'])) {
noContentWindow('"'.htmlspecialchars($_GET['query'] ?? '').'" could not be found.<br>
Select Retry Search if you want to try again.');
}

else {	
print '<div class="user-search-content">
    <p class="user-found user-found-message">Found: '.htmlspecialchars($_GET['query']).'    </p>
    <ul class="list-content-with-icon-and-text user-list" data-next-page-url="">';
require_once 'lib/htmUser.php';
while($users = $search_user->fetch_assoc()) {
userObject($users, false, true, 'search');
   }

print '
	</ul>
  </div>';

}

	
# Retry Search button, appears in all new searches
print '<span type="button" class="fixed-bottom-button search-button">Retry Search<input name="query" class="user-search-query" minlength="1" maxlength="16" inputform="monospace" guidestring="Enter the ID or nickname of
the user you want to find." data-pjax="#body"></span>';
# End of document; first end body-content
print '</div>'."\n".'';
print $GLOBALS['div_body_head_end'];
printFooter();