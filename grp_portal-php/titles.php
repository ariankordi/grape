<?php
//Communities screen
$body_id = 'main';
require_once '../grplib-php/init.php';

# Community listing.
if(isset($_GET['community_id'])) {
$community_uid = mysqli_real_escape_string($mysql, $_GET['community_id']);
$title_uid = mysqli_real_escape_string($mysql, $_GET['title_id']);
$sql_comm = 'SELECT * FROM communities WHERE communities.olive_community_id = "' . htmlspecialchars($community_uid) . '" AND communities.olive_title_id ="' . $title_uid . '"';
$result_comm = mysqli_query($mysql, $sql_comm);
$row_comm = mysqli_fetch_assoc($result_comm);


$sql_title = 'SELECT * FROM titles WHERE titles.olive_title_id = "' . htmlspecialchars($title_uid) . '"';
$result_title = mysqli_query($mysql, $sql_title);
$row_title = mysqli_fetch_assoc($result_title);

if(!$result_comm)
{
http_response_code(500);
$pagetitle = ('Error');
	require_once 'lib/htm.php';
printHeader(false);
	printMenu();
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="500">';
$no_content_message = ( 'Server error.' );
include 'lib/no-content-window.php';
print $GLOBALS['div_body_head_end'];
}
else
{	
if(isset($row_comm['type'])) {
$commtype = strval($row_comm['type']); }
else { $commtype = 0; }
    if(mysqli_num_rows($result_comm) == 0 || $commtype == 5)
    {
(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
$pagetitle = ('Communities');
	require_once 'lib/htm.php';
printHeader(false);
	printMenu();
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="community-404">';
$no_content_message = ( 'Community could not be found.' );
include 'lib/no-content-window.php';
print $GLOBALS['div_body_head_end'];
    }
    else
    {

# Success, show community. 
$pagetitle = htmlspecialchars($row_title['name']);
if(!isset($_SERVER['HTTP_X_PJAX_CONTAINER']) || $_SERVER['HTTP_X_PJAX_CONTAINER'] != '#community-tab-body') {
	require_once 'lib/htm.php';
printHeader(false);
	printMenu();

	if(empty($_SESSION['signed_in'])) {
	$if_can_user_post = ' disabled'; } else {
$row_my_poster1 = 'SELECT * FROM people WHERE people.pid = "' . $_SESSION['pid'] . '"';
$result_my_poster1 = mysqli_query($mysql, $row_my_poster1);
$row_my_poster1 = mysqli_fetch_assoc($result_my_poster1); 

    if(isset($row_current_peopleban)) {
	$if_can_user_post = ' disabled';		
	}
	
    elseif(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && $row_comm['min_perm'] <= $row_my_poster1['privilege']) {
	$if_can_user_post = ''; }
	elseif(in_array($_SESSION['pid'], explode(', ', $row_comm['allowed_pids']))) {
	$if_can_user_post = ''; }
else {
	$if_can_user_post = ' disabled'; }	
	}
    print $GLOBALS['div_body_head'];
	print '
<header id="header">
<a id="header-post-button"' . $if_can_user_post . ' class="header-button' . $if_can_user_post . ' none"'.($if_can_user_post == '' ? 'href="#"' : '').' data-modal-open="#add-post-page">Post</a>';
$sql_communityscroll = 'SELECT * FROM communities WHERE communities.olive_title_id = "' . htmlspecialchars($row_title['olive_title_id']) . '" AND type != "5"';
$result_communityscroll = mysqli_query($mysql, $sql_communityscroll);
$communityscroll_amt = mysqli_num_rows($result_communityscroll);
if(($communityscroll_amt) >= 2) {
print '<a id="header-communities-button" href="/titles/' . htmlspecialchars($row_title['olive_title_id']) . '" data-pjax="#body">Related Communities</a>';
$banner_container_type_button = ' with-top-button';
}
else { 
$banner_container_type_button = null;
 }
print '<h1 id="page-title">' . $pagetitle . '</h1>
</header>
<div class="body-content" id="community-post-list">';
# If the community has a banner, display it.
if($row_comm['banner']) {
print '<div class="header-banner-container"><img src="' . htmlspecialchars($row_comm['banner']) . '" class="header-banner' . $banner_container_type_button . '"></div>
<div class="community-info info-content with-header-banner">';
}
else {
	print '<div class="community-info info-content">';
}
print '<span class="icon-container"><img src="';
if(!$row_comm['icon']) {
# Replace this?
$title_icon_default = 'https://miiverse.nintendo.net/img/title-icon-default.png';	
print $title_icon_default;
}
else {
	print htmlspecialchars($row_comm['icon']);
}
# Put favorite & title settings icon here at some point?
print '" class="icon">
</span>
';
if(isset($row_title['platform_id'])) {
if(($row_title['platform_id']) == 2) {
print '<span class="platform-tag platform-tag-3ds"></span>'; }
if(($row_title['platform_id']) == 1) {
print '<span class="platform-tag platform-tag-wiiu"></span>'; }
}

if(!empty($_SESSION['pid'])) {
$has_community_favorited = mysqli_num_rows(mysqli_query($mysql, 'SELECT * FROM favorites WHERE favorites.pid = "'.$_SESSION['pid'].'" AND favorites.community_id = "'.$row_comm['community_id'].'"'));
print '<a href="#" class="favorite-button favorite-button-mini button'.($has_community_favorited != 0 ? ' checked' : '').'" data-action-favorite="/titles/'.$row_comm['olive_title_id'].'/'.$row_comm['olive_community_id'].'/favorite.json" data-action-unfavorite="/titles/'.$row_comm['olive_title_id'].'/'.$row_comm['olive_community_id'].'/unfavorite.json" data-sound="SE_WAVE_CHECKBOX_'.($has_community_favorited != 0 ? 'UN' : '').'CHECK" data-community-id="'.$row_comm['olive_community_id'].'" data-url-id="" data-track-label="community" data-title-id="'.$row_comm['olive_title_id'].'" data-track-action="cancelFavorite" data-track-category="favorite"></a>';
}

    if(($row_comm['type']) == '1') {
	print '<span class="news-community-badge">Main Community</span>
	';
	}
	if(($row_comm['type']) == '2') {
	print '<span class="news-community-badge">Announcement Community</span>
	';
	}
	print '<span class="title">' . htmlspecialchars($row_comm['name']) . '</span>
	<span class="text">' . htmlspecialchars($row_comm['description']) . '
</span>';


print '
</div>';
# Community info, etc, is done. 
# Place title settings RIGHT HERE when implemented.

# This is the hot/new posts selector.
print '<menu class="tab-header">
    <li id="tab-header-post" class="tab-button selected" data-show-post-button="1">
        <a href="/titles/'.$row_comm['olive_title_id'].'/'.$row_comm['olive_community_id'].'/new" data-pjax-replace="1" data-pjax="#community-tab-body" data-pjax-cache-container="#body" data-sound="SE_WAVE_SELECT_TAB"><span class="new-post">All Posts</span></a>
    </li>
<li id="tab-header-hot-post" class="tab-button disabled"><a class="disabled"><span>Popular posts</span></a></li>
    
  </menu>';

# This is where community tab body and post list is. 2 div conc.
print '<div id="community-tab-body" class="tab-body">';
}
# Post list.
        $sql_post = 'SELECT * FROM posts WHERE posts.community_id = "' . $row_comm['community_id'] . '" AND posts.is_hidden = "0" ORDER BY posts.created_at DESC LIMIT 50';
        #$sql_post = 'SELECT * FROM posts LEFT JOIN people ON posts.pid = people.pid WHERE posts.community_id = "' . $row_comm['community_id'] . '" ORDER BY posts.created_at DESC';
        $result_post = mysqli_query($mysql, $sql_post);
        if(!$result_post)
        {
$no_content_message = ( "Server error." );
include 'lib/no-content-window.php';
        }
        else
        {
			
            if(mysqli_num_rows($result_post) == 0)
            {
	print '<div class="js-post-list post-list" data-next-page-url="">';
$no_content_message = ( "This community doesn't have any posts yet." );
include 'lib/no-content-window.php';
print '</div>';
            }
            else
            {
							
if(isset($_GET['offset']) && is_numeric($_GET['offset']) && strlen($_GET['offset']) >= 1) {

	    $sql_post_offset = 'SELECT * FROM posts WHERE posts.community_id = "' . $row_comm['community_id'] . '" AND posts.is_hidden = "0" ORDER BY posts.created_at DESC LIMIT 50 OFFSET '.mysqli_real_escape_string($mysql, $_GET['offset']).'';
        $result_post_offset = mysqli_query($mysql, $sql_post_offset);
	
if(mysqli_num_rows($result_post_offset) > 49) {
$what_is_my_new_offset1 = mysqli_real_escape_string($mysql, $_GET['offset']) + 50;
$what_is_my_new_offset = '/titles/'.$row_comm['olive_title_id'].'/'.$row_comm['olive_community_id'].'?offset='.$what_is_my_new_offset1.'';
}
else {
$what_is_my_new_offset = ''; }	

	
				print '<div class="js-post-list post-list" data-next-page-url="'.$what_is_my_new_offset.'">';
                while($row_post_offset = mysqli_fetch_assoc($result_post_offset))
                {
	$sql_post_user = 'SELECT * FROM people WHERE people.pid = "' . $row_post_offset['pid'] . '"';
	$result_post_user = mysqli_query($mysql, $sql_post_user);
	$row_post_user = mysqli_fetch_assoc($result_post_user);
	
	$sql_post_replies = 'SELECT * FROM replies WHERE replies.reply_to_id = "' . $row_post_offset['id'] . '" AND replies.is_hidden != "1"';
	$result_post_replies = mysqli_query($mysql, $sql_post_replies);
	$row_post_replies = mysqli_fetch_assoc($result_post_replies);
	
	$sql_post_recent_replies = 'SELECT * FROM replies WHERE replies.reply_to_id = "' . $row_post_offset['id'] . '" AND replies.is_hidden != "1" AND replies.is_spoiler != "1" AND replies.pid !="'.$row_post_offset['pid'].'" ORDER BY replies.created_at DESC LIMIT 1';
	$result_post_recent_replies = mysqli_query($mysql, $sql_post_recent_replies);
	
	$sql_post_empathies = 'SELECT * FROM empathies WHERE empathies.id = "' . $row_post_offset['id'] . '"';
	$result_post_empathies = mysqli_query($mysql, $sql_post_empathies);
	$row_post_empathies = mysqli_fetch_assoc($result_post_empathies);
	
$template_creator_pid = $row_post_user['pid'];
$template_creator_user_id = htmlspecialchars($row_post_user['user_id']);
$template_creator_screen_name = $row_post_user['screen_name'];
$template_creator_mii_hash = htmlspecialchars($row_post_user['mii_hash']);
$template_creator_user_face = htmlspecialchars($row_post_user['user_face']);
$template_creator_official_user = $row_post_user['official_user'];
$template_post_id = $row_post_offset['id'];
$template_post_pid = $row_post_offset['pid'];
$template_post_type = $row_post_offset['_post_type'];
$template_post_body = htmlspecialchars($row_post_offset['body']);
$template_post_url = htmlspecialchars($row_post_offset['url']);
$template_post_is_hidden = $row_post_offset['is_hidden'];
$template_post_screenshot = $row_post_offset['screenshot'];
$template_post_created_at = $row_post_offset['created_at'];
$template_post_spoiler = htmlspecialchars($row_post_offset['is_spoiler']);
$template_post_feeling_id = htmlspecialchars($row_post_offset['feeling_id']);

$template_result_post_empathies = $result_post_empathies;
$template_result_post_replies = $result_post_replies;
include 'lib/postlist-post-template.php';
				
				
				}
			print '</div>';		
exit();
			}
				
				
				$sql_get_community_postamt = 'SELECT * FROM posts WHERE posts.community_id = "'.$row_comm['community_id'].'"';
                $result_get_community_postamt = mysqli_query($mysql, $sql_get_community_postamt);
				if(mysqli_num_rows($result_get_community_postamt) >= 50) {
				$do_i_have_offset = '/titles/'.$row_comm['olive_title_id'].'/'.$row_comm['olive_community_id'].'?offset=50';
				} else {
				$do_i_have_offset = ''; }
	
				print '<div class="js-post-list post-list" data-next-page-url="'.$do_i_have_offset.'">';
                while($row_post = mysqli_fetch_assoc($result_post))
                {
	$sql_post_user = 'SELECT * FROM people WHERE people.pid = "' . $row_post['pid'] . '"';
	$result_post_user = mysqli_query($mysql, $sql_post_user);
	$row_post_user = mysqli_fetch_assoc($result_post_user);
	
	$sql_post_replies = 'SELECT * FROM replies WHERE replies.reply_to_id = "' . $row_post['id'] . '" AND replies.is_hidden != "1"';
	$result_post_replies = mysqli_query($mysql, $sql_post_replies);
	$row_post_replies = mysqli_fetch_assoc($result_post_replies);
	
	$sql_post_recent_replies = 'SELECT * FROM replies WHERE replies.reply_to_id = "' . $row_post['id'] . '"  AND replies.is_hidden != "1" AND replies.is_spoiler != "1" AND replies.pid !="'.$row_post['pid'].'" ORDER BY replies.created_at DESC LIMIT 1';
	$result_post_recent_replies = mysqli_query($mysql, $sql_post_recent_replies);
	
	$sql_post_empathies = 'SELECT * FROM empathies WHERE empathies.id = "' . $row_post['id'] . '"';
	$result_post_empathies = mysqli_query($mysql, $sql_post_empathies);
	$row_post_empathies = mysqli_fetch_assoc($result_post_empathies);
	
$template_creator_pid = $row_post_user['pid'];
$template_creator_user_id = htmlspecialchars($row_post_user['user_id']);
$template_creator_screen_name = $row_post_user['screen_name'];
$template_creator_mii_hash = htmlspecialchars($row_post_user['mii_hash']);
$template_creator_user_face = htmlspecialchars($row_post_user['user_face']);
$template_creator_official_user = $row_post_user['official_user'];
$template_post_id = $row_post['id'];
$template_post_pid = $row_post['pid'];
$template_post_type = $row_post['_post_type'];
$template_post_body = htmlspecialchars($row_post['body']);
$template_post_url = htmlspecialchars($row_post['url']);
$template_post_is_hidden = $row_post['is_hidden'];
$template_post_screenshot = $row_post['screenshot'];
$template_post_created_at = $row_post['created_at'];
$template_post_spoiler = htmlspecialchars($row_post['is_spoiler']);
$template_post_feeling_id = htmlspecialchars($row_post['feeling_id']);

$template_result_post_empathies = $result_post_empathies;
$template_result_post_replies = $result_post_replies;
include 'lib/postlist-post-template.php';
				
				
				}
			print '</div>
 </div>
  </div>
  ';	
        }
    }	
if(!isset($_SERVER['HTTP_X_PJAX_CONTAINER']) || $_SERVER['HTTP_X_PJAX_CONTAINER'] != '#community-tab-body') {

# Post form.
print '<div id="add-post-page" class="add-post-page '.(strval($lookup_user['image_perm']) >= 1 ? 'official-user-post ' : '');

print 'none " data-modal-types="add-entry add-post require-body preview-body" data-is-template="1">
<header class="add-post-page-header">
';
print '<h1 class="page-title">Post to ' . htmlspecialchars($row_comm['name']) . '</h1>
	</header>
	'; 
	print '<form method="post" action="/posts" id="posts-form">
	    <input type="hidden" name="community_id" value="' . htmlspecialchars($row_comm['community_id']) . '">';
    print '<div ';
		 if(isset($_SESSION['pid'])) {
	 if(strval($lookup_user['image_perm']) >= 1) {
	  print 'style="position: absolute; left: 200px; top: 100px;" ';
	 }
	 }
	 print 'class="add-post-page-content">
	';
    if(isset($_SESSION['signed_in'])) {
	if($lookup_user['mii_hash']) {
	print '<div class="feeling-selector expression">
  <img src="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_normal_face.png" class="icon">
  <ul class="buttons"><li class="checked"><input type="radio" name="feeling_id" value="0" class="feeling-button-normal" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_normal_face.png" checked="" data-sound="SE_WAVE_MII_FACE_00"></li><li><input type="radio" name="feeling_id" value="1" class="feeling-button-happy" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_happy_face.png" data-sound="SE_WAVE_MII_FACE_01"></li><li><input type="radio" name="feeling_id" value="2" class="feeling-button-like" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_like_face.png" data-sound="SE_WAVE_MII_FACE_02"></li><li><input type="radio" name="feeling_id" value="3" class="feeling-button-surprised" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_surprised_face.png" data-sound="SE_WAVE_MII_FACE_03"></li><li><input type="radio" name="feeling_id" value="4" class="feeling-button-frustrated" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_frustrated_face.png" data-sound="SE_WAVE_MII_FACE_04"></li><li><input type="radio" name="feeling_id" value="5" class="feeling-button-puzzled" data-mii-face-url="https://mii-secure.cdn.nintendo.net/' . htmlspecialchars($lookup_user['mii_hash']) . '_puzzled_face.png" data-sound="SE_WAVE_MII_FACE_05"></li>  </ul>
</div>';
	}
	if(isset($lookup_user['user_face'])) {
	if($lookup_user['user_face']) {	
	print '<div class="feeling-selector expression">
  <img src="' . htmlspecialchars($lookup_user['user_face']) . '" class="icon">
  
</div>';
	}
   }
  }
	print '<div class="textarea-container textarea-with-menu active-text">
        
          <menu class="textarea-menu">
            <li><label class="textarea-menu-text checked">
                <input type="radio" name="_post_type" value="body" checked="" data-sound="">
            </label></li>
            <li><label class="textarea-menu-memo">
              <input type="radio" name="_post_type" value="painting" data-sound="">
            </label></li>
          </menu>
        
        <textarea name="body" class="textarea-text" value="" maxlength="1000" placeholder="Share your thoughts in a post to this community."></textarea>
        <div class="textarea-memo trigger" data-sound=""><div class="textarea-memo-preview"></div><input type="hidden" name="painting"></div>
      </div>';
	 if(isset($lookup_user['privilege'])) {
	 if(strval($lookup_user['image_perm']) >= 1) {
	 print '<input type="text" class="textarea-line url-form" name="url" placeholder="URL" maxlength="255">';
	 }
	 if(strval($lookup_user['image_perm']) >= 1) {
	 print '<input type="text" class="textarea-line url-form" name="screenshot" placeholder="Screenshot URL" maxlength="255">';
	 }
	}
	
	 
	 print '<input type="button" class="olv-modal-close-button fixed-bottom-button left" value="Cancel" data-sound="SE_WAVE_CANCEL">
	 <input type="submit" class="post-button fixed-bottom-button" value="Post" data-track-category="post" data-track-action="sendPost" data-track-label="default" data-community-id="' . htmlspecialchars($row_comm['olive_community_id']) . '" data-title-id="' . htmlspecialchars($row_comm['olive_title_id']) . '" data-post-content-type="text">
</form>
<label class="spoiler-button checkbox-button">
        Spoilers
        <input type="checkbox" name="is_spoiler" value="1">
      </label>';
print '

</div>
</div>

';
print $GLOBALS['div_body_head_end'];
	}
}
	# End of community listing.
	}
}

# Start of title listing.
else {
$title_uid = (isset($_GET['title_id']) ? mysqli_real_escape_string($mysql, $_GET['title_id']) : '');
$sql0 = 'SELECT * FROM titles WHERE titles.olive_title_id = "' . $title_uid . '"';
$result0 = mysqli_query($mysql, $sql0);
$row0 = mysqli_fetch_assoc($result0);

if(!$result0)
// DB error.
{
http_response_code(500);
$pagetitle = ('Error');
require_once 'lib/htm.php';
printHeader(false);
printMenu();
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="500">';
$no_content_message = ( 'Server error.' );
include 'lib/no-content-window.php';
include 'lib/no-content-window.php';
}
else
// Title doesn't exist.
{
    if(mysqli_num_rows($result0) == 0)
    {
(isset($_SERVER['HTTP_X_PJAX'])? '' : http_response_code(404));
$pagetitle = ('Error');
require_once 'lib/htm.php';
printHeader(false);
printMenu();
print $GLOBALS['div_body_head'];
print '<header id="header">
<h1 id="page-title" class="left">' . $pagetitle . '</h1>
</header>';
print '<div class="body-content track-error" data-track-error="404">';
$no_content_message = ( 'The screen could not be displayed.' );
include 'lib/no-content-window.php';
    }
    else
    {
// Yes communities ; print start.
       $pagetitle = htmlspecialchars($row0['name']);
	   require_once 'lib/htm.php';
printHeader(false);
printMenu();
       print $GLOBALS['div_body_head'];
	   print '<header id="header">
<h1 id="page-title">' . $pagetitle . '</h1>
</header>';
        print '<div class="body-content" id="community-top">
		';
		if(!strlen($row0['banner']) < 2) {
		print '<div class="header-banner-container"><img src="' . htmlspecialchars($row0['banner']) . '" class="header-banner"></div>';
		}
		
print '<div class="community-list">';
		print '<ul class="list-content-with-icon-and-text arrow-list">
			
			';
			
$sql1 = 'SELECT * FROM communities WHERE communities.olive_title_id = "' . $title_uid . '"';
$result1 = mysqli_query($mysql, $sql1);
                while($row1 = mysqli_fetch_assoc($result1))
                {
			if(strval($row1['type']) == 5) {
		    print null; }
			else {
			if (empty($row1['icon'])) {
			  $row1['icon'] = 'https://miiverse.nintendo.net/img/title-icon-default.png';
		   }
                        print '<li id="community-' . htmlspecialchars($row1['olive_community_id']) . '" class="';
                        if(($row1['type']) == '1' || ($row1['type']) == '2') {
						print 'with-news-community-badge';
						}
		                print '">';
						print '<span class="icon-container"><img src="' . htmlspecialchars($row1['icon']) . '" class="icon"></span>
						';
						print '<a href="/titles/' . htmlspecialchars($row0['olive_title_id']) . '/' . htmlspecialchars($row1['olive_community_id']) . '" data-pjax="#body" class="scroll arrow-button"></a>
						';
						print '<div class="body">';
						print '<div class="body-content">
						';
						if(($row1['type']) == '1') {
						print '<span class="news-community-badge">Main Community</span>
						';
						}
						if(($row1['type']) == '2') {
						print '<span class="news-community-badge">Announcement Community</span>
						';
						}
						print '<span class="community-name title">' . htmlspecialchars($row1['name']) . '</span>
						';
						print '<span class="text">' . htmlspecialchars($row0['name']) . '</span>
						
						';
            }
            }
        }
print '
   </div>
  </div>
 </div>
</li>
';
print '</ul>';
print 
'</div>
</div>';
print $GLOBALS['div_body_head_end'];
    }
	}
(empty($_SERVER['HTTP_X_PJAX']) ? printFooter() : '');
