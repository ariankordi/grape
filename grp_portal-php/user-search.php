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
if(empty($_GET['query']) || !isset($_GET['query'])) {
$_GET['query'] = 'empty-grp-0000069420'; }

$sql_usersearch = "SELECT * FROM people WHERE CONCAT_WS('', user_id, screen_name) LIKE '".mysqli_real_escape_string($mysql, $_GET['query'])."%' ORDER BY people.pid DESC";
$result_usersearch = mysqli_query($mysql, $sql_usersearch);

if(mysqli_num_rows($result_usersearch) == 0 || $_GET['query'] == '' || $_GET['query'] == '0') {
if($_GET['query'] == 'empty-grp-0000069420') {
$no_content_message = '"" could not be found.<br>
Select Retry Search if you want to try again.';
} else {
$no_content_message = '"'.htmlspecialchars($_GET['query']).'" could not be found.<br>
Select Retry Search if you want to try again.';	}
include 'lib/no-content-window.php';
}

else {	
print '<div class="user-search-content">
    <p class="user-found user-found-message">Found: '.htmlspecialchars($_GET['query']).'    </p>
    <ul class="list-content-with-icon-and-text user-list" data-next-page-url="">';

while($row_usersearch = mysqli_fetch_assoc($result_usersearch)) {
$row_user_to_view = $row_usersearch;
$is_user_search = true;
include 'lib/userlist-li-template.php';
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

