<?php
//Communities screen
	include 'lib/sql-connect.php';
$pagetitle = (isset($grp_config_server_type) && $grp_config_server_type == 'dev' && isset($grp_config_server_env) ? 'Communities ('.$grp_config_server_env.')' : 'Communities' );
    
    include 'lib/header.php';
	include 'lib/user-menu.php';
	print $div_body_head;
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

<div class="body-content" id="community-top">';

// After 'ul class list community-list'
$sql = "SELECT * FROM grape.titles ORDER BY olive_title_id LIMIT 1";
$result = mysqli_query($link, $sql);
if(!$result)
{
// Error connecting with SQL.
$no_content_message = ( 'Server error.' );
    include 'lib/no-content-window.php';
	http_response_code(500);
}
else
{
// No titles.
    if(mysqli_num_rows($result) == 0)
    {
$no_content_message = ( 'No communities have been created yet.' );
    include 'lib/no-content-window.php';
	// I don't know if this should be present.
	#(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
    }
    else
    {
// Yes titles ; organize and print start.
		$sql_platformtitles = 'SELECT * FROM grape.titles WHERE titles.platform_id IS NOT NULL ORDER BY titles.created_at DESC';
		$result_platformtitles = mysqli_query($link, $sql_platformtitles);
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
			
        while($row_platformtitles = mysqli_fetch_assoc($result_platformtitles))
        {
// Define rows & print titles.
           if (empty($row_platformtitles['icon'])) {
			  $row_platformtitles['icon'] = 'https://miiverse.nintendo.net/img/title-icon-default.png';
		   }
			print '<li id="community-' . htmlspecialchars($row_platformtitles['olive_community_id']) . '" class="">
			<span class="icon-container"><img src="' . htmlspecialchars($row_platformtitles['icon']) . '" class="icon"></span>';
    $sql_communityscroll = 'SELECT * FROM grape.communities WHERE communities.olive_title_id = "' . htmlspecialchars($row_platformtitles['olive_title_id']) . '"';
	$result_communityscroll = mysqli_query($link, $sql_communityscroll);
	$communityscroll_amt = mysqli_num_rows($result_communityscroll);
	if(($communityscroll_amt) >= 2) {				
		print '<a href="/titles/' . htmlspecialchars($row_platformtitles['olive_title_id']) . '" data-pjax="#body" class="list-button button">Related Communities</a>	';
	}
     print '<a href="/titles/' . htmlspecialchars($row_platformtitles['olive_title_id']) . '/' . htmlspecialchars($row_platformtitles['olive_community_id']) . '" data-pjax="#body" class="scroll to-community-button"></a>
   <div class="body">
     <div class="body-content">
        <span class="community-name title">' . htmlspecialchars($row_platformtitles['name']) . '</span>
		
		';

// Define platform IDs and platform types.

$platform_type_plain = $row_platformtitles['platform_type'];
$platform_id_plain = $row_platformtitles['platform_id'];

if($platform_id_plain == '0') {
$platform_id_text = "3ds"; }
if($platform_id_plain == '1') {
$platform_id_text = "wiiu"; }
if($platform_id_plain == '2') {
$platform_id_text = "3ds"; }

    if($platform_type_plain == '0') {
    $platform_type_text = "1st NUP Test Platform";
    }
    if($platform_type_plain == '1') {
    $platform_type_text = "Wii U Games";
    }
	if($platform_type_plain == '2') {
    $platform_type_text = "3DS Games";
    }
    if($platform_type_plain == '3') {
    $platform_type_text = "Virtual Console";
    }
    if($platform_type_plain == '4') {
    $platform_type_text = "Others";
    }
	
	if(!empty($row_platformtitles['platform_id'])) {
print '<span class="platform-tag platform-tag-' . $platform_id_text . '"></span>';

print '<span class="text">' . $platform_type_text . '</span>';
	}
	
print '
  </div>
 </div>
</li>
';
        }
	print '</ul>';

		$sql_specialtitles = 'SELECT * FROM grape.titles WHERE titles.platform_id IS NULL ORDER BY titles.created_at DESC LIMIT 2';
		$result_specialtitles = mysqli_query($link, $sql_specialtitles);
		
if(mysqli_num_rows($result_specialtitles) == 0) {
} else {
print '<h2 class="headline headline-special">Special</h2>
<ul class="list-content-with-icon-column" id="community-top-content">
';

        while($row_specialtitles = mysqli_fetch_assoc($result_specialtitles))
        {
// Define rows & print titles.
           if (empty($row_specialtitles['icon'])) {
			  $row_specialtitles['icon'] = 'https://miiverse.nintendo.net/img/title-icon-default.png';
		   }
			print '<li id="community-' . htmlspecialchars($row_specialtitles['olive_community_id']) . '" class="">
			<span class="icon-container"><img src="' . htmlspecialchars($row_specialtitles['icon']) . '" class="icon"></span>';
    $sql_communityscroll = 'SELECT * FROM grape.communities WHERE communities.olive_title_id = "' . htmlspecialchars($row_specialtitles['olive_title_id']) . '" AND type != "5"';
	$result_communityscroll = mysqli_query($link, $sql_communityscroll);
	$communityscroll_amt = mysqli_num_rows($result_communityscroll);
	if(($communityscroll_amt) >= 2) {				
		print '<a href="/titles/' . htmlspecialchars($row_specialtitles['olive_title_id']) . '" data-pjax="#body" class="list-button button">Related Communities</a>	';
	}
     print '<a href="/titles/' . htmlspecialchars($row_specialtitles['olive_title_id']) . '/' . htmlspecialchars($row_specialtitles['olive_community_id']) . '" data-pjax="#body" class="scroll to-community-button"></a>
   <div class="body">
     <div class="body-content">
        <span class="community-name title">' . htmlspecialchars($row_specialtitles['name']) . '</span>
		
		';

// Define platform IDs and platform types.

$platform_type_plain = $row_specialtitles['platform_type'];
$platform_id_plain = $row_specialtitles['platform_id'];

if($platform_id_plain == '0') {
$platform_id_text = "3ds"; }
if($platform_id_plain == '1') {
$platform_id_text = "wiiu"; }
if($platform_id_plain == '2') {
$platform_id_text = "3ds"; }

    if($platform_type_plain == '0') {
    $platform_type_text = "1st NUP Test Platform";
    }
    if($platform_type_plain == '1') {
    $platform_type_text = "Wii U Games";
    }
	if($platform_type_plain == '2') {
    $platform_type_text = "3DS Games";
    }
    if($platform_type_plain == '3') {
    $platform_type_text = "Virtual Console";
    }
    if($platform_type_plain == '4') {
    $platform_type_text = "Others";
    }
	
	if(!empty($row_specialtitles['platform_id'])) {
print '<span class="platform-tag platform-tag-' . $platform_id_text . '"></span>';

print '<span class="text">' . $platform_type_text . '</span>';
	}
	
print '
  </div>
 </div>
</li>
';
        }

print '    </ul>
            </div>';
#print '<span type="button" class="fixed-bottom-button search-button" id="community-search">Search<input name="query" class="title-search-query" minlength="2" maxlength="20" inputform="monospace" guidestring="Search Communities" data-pjax="#body"></span>
#';
print '
			 </div>';

}
	
	print $div_body_head_end;
	}
    }
(empty($_SERVER['HTTP_X_PJAX']) ? include 'lib/footer.php' : '');
?>