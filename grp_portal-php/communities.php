<?php
//Communities screen
	require_once '../grplib-php/init.php';
$pagetitle = (isset($grp_config_server_type) && $grp_config_server_type == 'dev' && isset($grp_config_server_env) ? 'Communities ('.$grp_config_server_env.')' : 'Communities' );
    
    require_once 'lib/htm.php';
printHeader(false);
	printMenu();
	print $GLOBALS['div_body_head'];
if(!isset($_COOKIE['grp_theme']) || empty($_COOKIE['grp_theme'])) { $_COOKIE['grp_theme'] = 'olive'; }
print '<header id="header">
  
  <h1 id="page-title" class="left">'.$pagetitle.'</h1>
  
  <div class="region dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" data-sound="SE_WAVE_BALLOON_OPEN" href="#">Theme: '.(isset($_COOKIE['grp_theme']) ? htmlspecialchars($_COOKIE['grp_theme']) : 'olive').'</a>
    <ul class="dropdown-menu">
      <li>
        <a href="/theme-set?theme=olive" class="button checkbox-button'.(!isset($_COOKIE['grp_theme']) || $_COOKIE['grp_theme'] == 'olive' ? ' selected' : '').'" data-sound="SE_WAVE_TOGGLE_CHECK">olive</a>
      </li>
      <li>
        <a href="/theme-set?theme=grape" class="button checkbox-button'.(isset($_COOKIE['grp_theme']) && $_COOKIE['grp_theme'] == 'grape' ? ' selected' : '').'" data-sound="SE_WAVE_TOGGLE_CHECK">grape</a>
      </li>
      <li>
        <a href="/theme-set?theme=blueberry" class="button checkbox-button'.(isset($_COOKIE['grp_theme']) && $_COOKIE['grp_theme'] == 'blueberry' ? ' selected' : '').'" data-sound="SE_WAVE_TOGGLE_CHECK">blueberry</a>
      </li>
      <li>
        <a href="/theme-set?theme=cherry" class="button checkbox-button'.(isset($_COOKIE['grp_theme']) && $_COOKIE['grp_theme'] == 'cherry' ? ' selected' : '').'" data-sound="SE_WAVE_TOGGLE_CHECK">cherry</a>
      </li>
      <li>
        <a href="/theme-set?theme=orange" class="button checkbox-button'.(isset($_COOKIE['grp_theme']) && $_COOKIE['grp_theme'] == 'orange' ? ' selected' : '').'" data-sound="SE_WAVE_TOGGLE_CHECK">orange</a>
      </li>
      
    </ul>
  </div>

';
if(!empty($_SESSION['pid'])) {
print '<a id="header-favorites-button" href="/communities/favorites" data-pjax="#body">Favorite Communities</a>'; }
print '
</header>

<div class="body-content" id="community-top">

';

if($mysql->query('SELECT * FROM titles LIMIT 1')->num_rows == 0) {
nocontentWindow('No communities have been created yet.');
(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
printFooter(); grpfinish($mysql); exit(); }

require_once 'lib/htmCommunity.php';

$get_platformtitles = $mysql->query('SELECT * FROM titles WHERE titles.platform_id IS NOT NULL ORDER BY titles.created_at DESC');
print '<div class="community-list">
';
# Official user banner
print '<div class="banner-container">
      <a href="/identified_user_posts" data-pjax="#body" class="button identified-user-button">
        <span class="title">Get the latest news here!</span>
        <span class="text">Posts from Verified Users</span>
      </a>
    </div>';
	
	print '
		<div class="headline headline-wiiu">
			<h2>New Communities</h2>
			</div>
			<ul class="list-content-with-icon-column" id="community-top-content">
			
			';
			
        while($platformtitles = $get_platformtitles->fetch_assoc()) {
printTitle($platformtitles);
        }
	print '
	</ul>';
$get_specialtitles = $mysql->query('SELECT * FROM titles WHERE titles.platform_id IS NULL ORDER BY titles.created_at DESC LIMIT 6');
if($get_specialtitles->num_rows != 0) {
print '<h2 class="headline headline-special">Special</h2>
<ul class="list-content-with-icon-column" id="community-top-content">
';

        while($specialtitles = $get_specialtitles->fetch_assoc()) {
        printTitle($specialtitles);
        }

print '    </ul>
            </div>';
#print '<span type="button" class="fixed-bottom-button search-button" id="community-search">Search<input name="query" class="title-search-query" minlength="2" maxlength="20" inputform="monospace" guidestring="Search Communities" data-pjax="#body"></span>
#';
print '
			 </div>';
	
	print $GLOBALS['div_body_head_end'];
    }
(empty($_SERVER['HTTP_X_PJAX']) ? printFooter() : '');
grpfinish($mysql);