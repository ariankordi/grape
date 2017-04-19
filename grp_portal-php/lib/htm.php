<?php
if(!empty($_SESSION['pid'])) {
$lookup_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc(); }

function printHeader($is_act) {
global $pagetitle;
global $has_header_js;
if($is_act == true && $is_act === true) { $pagetitle = 'Grape::Account'; } elseif($is_act == 'err') { $pagetitle = 'Error'; }
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	print '<title>'.(isset($pagetitle) ? $pagetitle : 'grp.portal.page_title').'</title>
	';
	$GLOBALS['div_body_head'] = null; $GLOBALS['div_body_head_end'] = null;
} else {
	$GLOBALS['div_body_head'] = '
	<div id="body">'; $GLOBALS['div_body_head_end'] = '
	</div>
	';
	print '<!DOCTYPE html>


<html lang="en">
  <head>
    <meta charset="utf-8">
	<title>'.(isset($pagetitle) ? $pagetitle : 'grp.portal.page_title').'</title>
	';

if(strpos($_SERVER['HTTP_USER_AGENT'], 'miiverse') !== false) { $theme_css_file = '/css/portal-grp.css'; } else {
if(!empty($_COOKIE['grp_theme'])) {
if($_COOKIE['grp_theme'] == 'grape' || $_COOKIE['grp_theme'] == 'blueberry' ||  $_COOKIE['grp_theme'] == 'cherry' ||  $_COOKIE['grp_theme'] == 'orange') {
$theme_css_file = '/css/portal-grp_offdevice_'.htmlspecialchars($_COOKIE['grp_theme']).'.css'; } 
else { $theme_css_file = '/css/portal-grp_offdevice.css'; } } else { $theme_css_file = '/css/portal-grp_offdevice.css'; } }

    if(strpos($_SERVER['HTTP_USER_AGENT'], 'miiverse') !== false) {
	$theme_js_file = '/js/portal/complete.js'; } elseif($is_act && $is_act === true) { $theme_js_file = null; } else { $theme_js_file = '/js/portal/complete-emu.js'; }
	print '
	<link rel="stylesheet" type="text/css" href="'.$theme_css_file.'">';
	if(empty($has_theme_js)) { print '
	<script src="'.$theme_js_file.'"></script>'; } print "\n";
print '</head>
<body'.($is_act == true && $is_act === true ? ' id="help"' : null).'
';
if($is_act == false) {
if(!empty($_SESSION['pid'])) {
global $lookup_user;
       print 'data-hashed-pid="'.sha1($_SESSION['pid']).'"
	   ';
       print 'data-user-id="'.htmlspecialchars($lookup_user['user_id']).'"
	   ';
       print 'data-game-skill="0" data-follow-done="1" data-post-done="1" data-lang="en" data-country="us" data-post-done="1"
	   ';
       print 'data-profile-url="/users/'.htmlspecialchars($_SESSION['user_id']).'"
	   ';
	   } else {
	   print '
	   data-user-id="" 
	   data-is-first-post="1"';
} }

print '>


';    
	
}

}

function printFooter() {
global $pagetitle;
print '
    <a id="scroll-to-top" href="#" style="display:none"></a>
<div id="message-dialog-template"   class="window-page none">
  <div class="window">
    <h1 class="window-title">'.(isset($pagetitle) ? $pagetitle : 'grp.portal.page_title').'</h1>
    <div class="window-body"><div class="window-body-inner message">
      <p class="pre-line"></p>
    </div></div>
    <div class="window-bottom-buttons single-button">
      <a href="#" class="button ok-button">OK</a>
    </div>
  </div>
</div>

<div id="confirm-dialog-template"   class="window-page none">
  <div class="window">
    <h1 class="window-title">'.(isset($pagetitle) ? $pagetitle : 'grp.portal.page_title').'</h1>
    <div class="window-body"><div class="window-body-inner message">
      <p></p>
    </div></div>
    <div class="window-bottom-buttons">
      <a href="#" class="button cancel-button" data-sound="SE_WAVE_CANCEL">Cancel</a>
      <a href="#" class="button ok-button">OK</a>
    </div>
  </div>
</div>

<div id="parental-confirm-dialog-template"   class="window-page none">
  <div class="window">
    <h1 class="window-title">'.(isset($pagetitle) ? $pagetitle : 'grp.portal.page_title').'</h1>
    <div class="window-body">
      <div class="window-body-inner message">
        <p></p>
        <input type="password" controller="drc" minlength="4" maxlength="4" inputform="monospace" guidestring=" " class="parental_code textarea-line" name="parental_code" placeholder="Tap to enter the PIN." keyboard="pin">
      </div>
    </div>
    <div class="window-bottom-buttons">
      <a href="#" class="button cancel-button" data-sound="SE_WAVE_CANCEL">Back</a>
      <a href="#" class="button ok-button">OK</a>
    </div>
  </div>
</div>
<div id="capture-page"
     class="capture-page window-page none"
     data-modal-types="capture"
     data-is-template="1">
    <div class="capture-container">
        <div><img src="data:image/gif;base64,R0lGODlhEAAQAIAAAP%2F%2F%2FwAAACH5BAEAAAAALAAAAAAQABAAAAIOhI%2Bpy%2B0Po5y02ouzPgUAOw%3D%3D" class="capture"></div>
        <a href="#" class="olv-modal-close-button cancel-button accesskey-B" data-sound="SE_WAVE_CANCEL"><span>Back</span></a>
    </div>
</div>
';
if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') { print '
  </body>
</html>
'; }
}

function printMenu() {
if(empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
	if(!empty($_SESSION['pid'])) {
global $lookup_user;
	print '<menu id="global-menu">
      <li id="global-menu-mymenu"><a href="/users/'.htmlspecialchars($lookup_user['user_id']).'" data-pjax="#body" data-sound="SE_WAVE_MENU"><span class="mii-icon"><img src="'.getMii($lookup_user, false)['output'].'" alt="User Menu"></span><span>User Page</span></a></li>
      <li id="global-menu-feed"><a href="/" data-pjax="#body" data-sound="SE_WAVE_MENU">Activity Feed</a></li>
      <li id="global-menu-community"><a href="/communities" data-pjax="#body" data-sound="SE_WAVE_MENU">Communities</a></li>
      <li id="global-menu-message"><a href="/friend_messages" data-pjax="#body" data-sound="SE_WAVE_MENU">Messages<span class="badge" style="display: none;">0</span></a></li>
      <li id="global-menu-news"><a href="/news/my_news" data-pjax="#body" data-sound="SE_WAVE_MENU">Notifications<span class="badge" style="display: none;">0</span></a></li>
      <li id="global-menu-exit"><a href="#" role="button" data-sound="SE_WAVE_EXIT">Close</a></li>
      <li id="global-menu-back" class="none"><a href="#" role="button" class="accesskey-B" data-sound="SE_WAVE_BACK">Back</a></li>
    </menu>
'; } else {
	print '
    <menu id="global-menu">
      <li id="global-menu-mymenu"><a href="/guest_menu" data-pjax="#body" data-sound="SE_WAVE_MENU"><span class="mii-icon"><img src="/img/mii/img_unknown_MiiIcon.png" alt="Guest Menu"></span><span>Guest Menu</span></a></li>
      <li id="global-menu-feed"><a href="javascript:alert(\'An account is required to use this feature. Create one in Guest Menu.\');" data-pjax="#body" data-sound="SE_WAVE_MENU">Activity Feed</a></li>
      <li id="global-menu-community"><a href="/communities" data-pjax="#body" data-sound="SE_WAVE_MENU">Communities</a></li>
      <li id="global-menu-message"><a href="javascript:alert(\'An account is required to use this feature. Create one in Guest Menu.\');" data-pjax="#body" data-sound="SE_WAVE_MENU">Messages<span class="badge" style="display: none;">0</span></a></li>
      <li id="global-menu-news"><a href="javascript:alert(\'An account is required to use this feature. Create one in Guest Menu.\');" data-pjax="#body" data-sound="SE_WAVE_MENU">Notifications<span class="badge" style="display: none;">0</span></a></li>
      <li id="global-menu-exit"><a href="#" role="button" data-sound="SE_WAVE_EXIT">Close</a></li>
      <li id="global-menu-back" class="none"><a href="#" role="button" class="accesskey-B" data-sound="SE_WAVE_BACK">Back</a></li>
    </menu>
';
	}
} }

function actTemplate($subheader, $location, $content) {
printHeader(true);
print '	<div id="body">
<header id="header">
  
  <h1 id="page-title">Grape::Account</h1>

</header>

<div class="help-left-button">

  <a href="'.$location.'" class="guide-exit-button exit-button index" data-sound="SE_WAVE_BACK">Cancel</a>
</div>
<h2 id="sub-header" class="guide-sub-header">'.$subheader.'</h2>
<div id="guide" class="help-content"><style>.btn_001 { 
margin:0 30px 35px 20px; float:left; 
display:block; width:355px; height:60px; line-height:60px; text-align:center; margin:auto; font-size:26px; color:#323232; text-decoration:none; 
    background:-webkit-gradient(linear, left top, left bottom, from(#ffffff), color-stop(0.5, #ffffff), color-stop(0.8, #f6f6f6), color-stop(0.96, #f5f5f5), to(#bbbbbb));
  border: 0;
  margin: 0;
    border-radius:50px; box-shadow:0 3px 10px 0 #555555; text-align:center; margin:10px; padding:auto; text-decoration:none; cursor:pointer; }
.textbox{ background:#ffffff; border:2px #747474 solid; border-radius:10px; color:#828282; box-shadow: 0 2px 6px 1px #aaaaaa inset; }</style>
'.$content.'
    </div>
	';
printFooter();
}

function nocontentWindow($message) {
print '<div class="no-content-window"><div class="window">
        <p>'.$message.'</p>
      </div></div>'; }

function generalError($code, $message) {
(empty($_SERVER['HTTP_X_REQUESTED_WITH']) ? http_response_code($code) : null);
printHeader('err');
printMenu();

print $GLOBALS['div_body_head'];
print '
<header id="header">
<h1 id="page-title" class="left">Error</h1>
</header>';
print '<div class="body-content track-error" data-track-error="'.$code.'">';
noContentWindow((!empty($message) ? $message : 'The screen could not be displayed.'));
print $GLOBALS['div_body_head_end'];
printFooter();
}
	  
function actError($error, $subheader, $location) {
actTemplate($subheader, $location, '<div class="num3">
  <h2>Errors</h2>
  <p>Error Code: '.substr($error['code'],0,3).'-'.substr($error['code'],3,4).'</p><p>'.$error['message'].'</p>
</div>');
}

function notLoggedIn() {
if(isset($_SERVER['HTTP_X_PJAX'])) {
header('Content-Type: application/json; charset=UTF-8');
http_response_code(401);
exit(json_encode(array('success' => 0, 'errors' => [array('message' => 'You have been logged out.
Please log back in.', 'error_code' => 1510110)], 'code' => 401)));
}
else {
header('Content-Type: text/plain; charset=UTF-8');
http_response_code(403);
exit('403 Forbidden');
} 
}