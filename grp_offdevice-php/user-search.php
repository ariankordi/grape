<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';
$pagetitle = 'Search Users'; $bodyClass = 'search';
printHeader('old'); printMenu('old');

$search_users = $mysql->query('SELECT * FROM people WHERE CONCAT_WS(\'\', user_id, screen_name) '.(empty($_GET['query']) ? '= ""' : 'LIKE "'.$mysql->real_escape_string($_GET['query']).'%" OR CONCAT_WS(\'\', screen_name, user_id) LIKE "'.$mysql->real_escape_string($_GET['query']).'%"').' ORDER BY people.created_at DESC LIMIT 50'.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? ' OFFSET ""'.$mysql->real_escape_string($_GET['offset']).'"' : ''));
print '<div id="main-body">

  <h2 class="headline">Search Users</h2>
';
print '<form class="search" action="/users" method="GET"><!--
    --><input type="text" name="query" value="'.(empty($_GET['query']) ? null : htmlspecialchars($_GET['query'])).'" placeholder="Enter the ID or nickname of
 the user you want to find." minlength="1" maxlength="16"><!--
    --><input type="submit" value="q" title="Search">
  </form>
';
if(!$search_users || $search_users->num_rows == 0) {
print '<div class="search-user-content no-content search-content">
    <div class="search-content no-title-content">
      <p>"'.(empty($_GET['query']) ? null : htmlspecialchars($_GET['query'])).'" could not be found.<br>
Select Retry Search if you want to try again.</p>
    </div>
  </div>
  ';
} else {
print '<div class="search-user-content search-content">
      <p class="user-found note">Found "'.htmlspecialchars($_GET['query']).'".</p>
      <div class="list follow-list">
        <ul id="searched-user-list" class="list-content-with-icon-and-text arrow-list" data-next-page-url="'.($search_users->num_rows > 49 ? '/users?query='.htmlspecialchars(urlencode($_GET['query'])).'&offset='.(!empty($_GET['offset']) && is_numeric($_GET['offset']) ? 50 + $_GET['offset'] : 50) : null).'">
		';
require_once 'lib/htmUser.php';
while($user = $search_users->fetch_assoc()) {
userObject($user, true, true);
}
print '
        </ul>
      </div>
  </div>';
}

print '
</div>';
printFooter('old');