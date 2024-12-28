<?php
if (!function_exists('str_contains')) {
  function str_contains($haystack, $needle) {
      return $needle !== '' && mb_strpos($haystack, $needle) !== false;
  }
}
   bindtextdomain('miitoo', '../l10n/');
   bindtextdomain('community', '../l10n/');
   $sunshine = "olive";
if(isset($_SERVER["HTTP_X_NINTENDO_SERVICETOKEN"])){
  global $mysql;
  global $sunshine;
  global $_COOKIE;
  $serviceToken = bin2hex(base64_decode($_SERVER['HTTP_X_NINTENDO_SERVICETOKEN']));
  $sessionId = substr($serviceToken, 0, 64);
  $stmt = $mysql->prepare("SELECT * FROM `console_auth` WHERE `long_id` = ?");
  $stmt->bind_param("s", $sessionId);
  $stmt->execute();
  $res = $stmt->get_result();
  if($res->num_rows == 0){
    if($_SERVER['REQUEST_URI'] != "/act/login" AND $_SERVER['REQUEST_URI'] != "/act/create"){
      //exit("<script>wiiuErrorViewer.openByCodeAndMessage(1270010, 'You are not whitelisted. Sorry.');wiiuBrowser.closeApplication();</script>");
      header("Location: /act/login");
      exit("You need to login.<br><a href='/act/login'>Click here if you're not redirected.</a>");
    }
  } else {
    $row = $res->fetch_assoc();
    $_SESSION["signed_in"] = true;
    $_SESSION["user_id"] = $row["user_id"];
    $_SESSION["pid"] = $row["pid"];
    $pid = $row["pid"];
    $_COOKIE["grp_theme"] = $row["theme"];
    $pids = array("1741588700", "1738295343", "1738406070");
    //exit("<script>wiiuBrowser.closeApplication();</script>Youre Mom");
    if(!in_array($_SESSION["pid"], $pids)){
    if($_SERVER["HTTP_ACCEPT"] == "*/*" && $_SERVER["HTTP_X_REQUESTED_WITH"] !== "XMLHttpRequest"){
      exit("<script>setTimeout(function(){wiiuBrowser.closeApplication();},1000);</script>Youre Mom");
    }
    }
    if(rand(1,1000) == 500){
      //exit("<script>wiiuErrorViewer.openByCodeAndMessage(4201337, 'Out of sheer luck (bad luck), you have encountered this error message. I will now disrupt your Miiverse browsing experience.\\n\\nMuahahahahaha!');wiiuBrowser.closeApplication();</script>");
    }
    if(isset($_SERVER["HTTP_ACCEPT"]) && strpos($_SERVER["HTTP_ACCEPT"], "webp") !== false && $_SERVER["HTTP_HOST"] == "rvqcportal.rverse.club" && !in_array($_SESSION["pid"], $pids)){
      //exit("<script>wiiuBrowser.endStartUp();</script>gggggghfgdhsjlgrsggggggggggggggggggggggg youre mom");
      if($_SERVER["REQUEST_URI"] !== "/warning/readonly"){
        if(pleasebanme($_SESSION["pid"], "accessing portal when not supposed to LOLZERS!")){
          header("Location: /warning/readonly");
          exit();
        } else {
          header("Location: /warning/readonly");
          exit();
        }
      }
      }
  }
} else {
  if(!isset($_SESSION["num"])){
    $_SESSION["num"] = 1;
  } else {
    $_SESSION["num"] = $_SESSION["num"] + 1;
  }
  if($_SESSION["num"] > 3){
    if($_SESSION["num"] == 3){
      sendHook2('Type: '.$_SERVER["HTTP_HOST"]);
    }
    exit("I think you're making a grave mistake.");
  }
  exit("Please don't mess with this site, activity is logged.");
} 
function printHeader($is_act) {
global $pagetitle;
global $has_header_js;
if(!empty($_SESSION["pid"])) {
global $mysql;
$lookup_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION["pid"].'" LIMIT 1')->fetch_assoc(); }
if($is_act == true && $is_act === true) { $pagetitle = 'rverse::Account'; } elseif($is_act == 'err' && empty($pagetitle)) { $pagetitle = loc('grp.portal.error'); }
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
    <script>
    setTimeout(function(){wiiuBrowser.lockUserOperation(false);wiiuBrowser.showLoadingIcon(false);},3000);
    </script>
    <script>
  if(typeof wiiuBrowser == undefined){
    window.location.href = "https://rv3api.rverse.club";
  }
  </script>
	<title>'.(isset($pagetitle) ? $pagetitle : 'grp.portal.page_title').'</title>
	';
  if($_SERVER["REQUEST_URI"] != "/welcome/"){
    print '
    <script>if (typeof wiiuBrowser !== "undefined" && typeof wiiuBrowser.endStartUp !== "undefined") {
      wiiuBrowser.endStartUp();
      wiiuSound.playSoundByName("BGM_OLV_MAIN", 3);
      setTimeout(function() {
          wiiuSound.playSoundByName("BGM_OLV_MAIN_LOOP_NOWAIT", 3);
      },90000);
    }</script>';
  }
  $serviceToken = bin2hex(base64_decode($_SERVER['HTTP_X_NINTENDO_SERVICETOKEN']));
  $sessionId = substr($serviceToken, 0, 64);
  $stmt = $mysql->prepare("SELECT * FROM `console_auth` WHERE `long_id` = ?");
  $stmt->bind_param("s", $sessionId);
  $stmt->execute();
  $res = $stmt->get_result();
  if($res->num_rows == 0){
    if($_SERVER['REQUEST_URI'] != "/act/login" AND $_SERVER['REQUEST_URI'] != "/act/create"){
      //exit("<script>wiiuErrorViewer.openByCodeAndMessage(1270010, 'You are not whitelisted. Sorry.');wiiuBrowser.closeApplication();</script>");
      header("Location: /act/login");
      exit("You need to login.<br><a href='/act/login'>Click here if you're not redirected.</a>");
    }
  }
  $row = $res->fetch_assoc();
$sunshine = $row["theme"];
//$sunshine = "orange"; //I wonder what this is for!!!!
//exit($sunshine.' <script>setTimeout(function(){ location.reload(); },2000);</script><p>The site is currently being worked on! This page will automatically refresh.</p>');
$theme_css_file = '/css/portal-grp.css';
if(!empty($sunshine)) {
if($sunshine == 'grape' || $sunshine == 'blueberry' ||  $sunshine == 'cherry' ||  $sunshine == 'orange') {
$theme_css_file = '/css/portal-grp_offdevice_'.htmlspecialchars($sunshine).'.css'; 
} else if($sunshine == 'olive') {
  $theme_css_file = '/css/portal-grp.css'; 
}
}
    if(strpos($_SERVER['HTTP_USER_AGENT'], 'miiverse') !== false) {
	$theme_js_file = '/js/portal/complete.js'; } elseif($is_act && $is_act === true) { $theme_js_file = null; } else { $theme_js_file = '/js/portal/complete-emu.js'; }
  $theme_js_file = '/js/portal/complete.js';
	print '
	<link rel="stylesheet" type="text/css" href="'.$theme_css_file.'">';
	if(empty($has_theme_js)) { print '
	<script src="'.$theme_js_file.'"></script>'; } print "\n";
print '</head>
<body'.($is_act == true && $is_act === true ? ' id="help"' : null).'
';
if($is_act == false) {
if(!empty($_SESSION["pid"])) {
       print 'data-hashed-pid="'.sha1($_SESSION["pid"]).'"
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
// I wonder what this is for!!!! print '><video src="/lel.mp4" width="1" height="1" style="display: none;" autoplay loop></video>
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

function truncate($text, $chars) {
$truncate_post_bodyp1 = mb_substr(($text), 0, $chars);
return (mb_strlen($text) >= $chars + 1 ? $truncate_post_bodyp1.'...' : $truncate_post_bodyp1);
}

function printMenu() {
if(empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {

	if(!empty($_SESSION["pid"])) {
global $mysql;
$lookup_user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION["pid"].'" LIMIT 1')->fetch_assoc();
	print '<menu id="global-menu">
      <li id="global-menu-mymenu"><a href="/users/'.htmlspecialchars($lookup_user['user_id']).'" data-pjax="#body" data-sound="SE_WAVE_MENU"><span class="mii-icon"><img src="'.getMii($lookup_user, false)['output'].'" alt="'.loc('grp.portal.my_page').'"></span><span>'.loc('grp.portal.my_page').'</span></a></li>
      <li id="global-menu-feed"><a href="/" data-pjax="#body" data-sound="SE_WAVE_MENU">'.loc('grp.portal.activity').'</a></li>
      <li id="global-menu-community"><a href="/communities" data-pjax="#body" data-sound="SE_WAVE_MENU">'.loc('grp.portal.community').'</a></li>
      <li id="global-menu-message"><a href="/friend_messages" data-pjax="#body" data-sound="SE_WAVE_MENU">'.loc('grp.portal.message').'<span class="badge" style="display: none;">0</span></a></li>
      <li id="global-menu-news"><a href="/news/my_news" data-pjax="#body" data-sound="SE_WAVE_MENU">'.loc('grp.portal.news').'<span class="badge" style="display: none;">0</span></a></li>
      <li id="global-menu-exit"><a href="#" role="button" data-sound="SE_WAVE_EXIT">'.loc('grp.portal.exit').'</a></li>
      <li id="global-menu-back" class="none"><a href="#" role="button" class="accesskey-B" data-sound="SE_WAVE_BACK">'.loc('grp.portal.back').'</a></li>
    </menu>
'; } else {
	print '
    <menu id="global-menu">
      <li id="global-menu-mymenu"><a href="/guest_menu" data-pjax="#body" data-sound="SE_WAVE_MENU"><span class="mii-icon"><img src="/img/mii/img_unknown_MiiIcon.png" alt="'.loc('grp.portal.my_menu_for_guest').'"></span><span>'.loc('grp.portal.my_menu_for_guest').'</span></a></li>
      <li id="global-menu-feed"><a href="javascript:alert(\'An account is required to use this feature. Create one in Guest Menu.\');" data-pjax="#body" data-sound="SE_WAVE_MENU">Activity Feed</a></li>
      <li id="global-menu-community"><a href="/communities" data-pjax="#body" data-sound="SE_WAVE_MENU">'.loc('grp.portal.community').'</a></li>
      <li id="global-menu-message"><a href="javascript:alert(\'An account is required to use this feature. Create one in Guest Menu.\');" data-pjax="#body" data-sound="SE_WAVE_MENU">'.loc('grp.portal.message').'<span class="badge" style="display: none;">0</span></a></li>
      <li id="global-menu-news"><a href="javascript:alert(\'An account is required to use this feature. Create one in Guest Menu.\');" data-pjax="#body" data-sound="SE_WAVE_MENU">'.loc('grp.portal.news').'<span class="badge" style="display: none;">0</span></a></li>
      <li id="global-menu-exit"><a href="#" role="button" data-sound="SE_WAVE_EXIT">'.loc('grp.portal.exit').'</a></li>
      <li id="global-menu-back" class="none"><a href="#" role="button" class="accesskey-B" data-sound="SE_WAVE_BACK">'.loc('grp.portal.back').'</a></li>
    </menu>
';
	}
} }

function actTemplate($subheader, $location, $content) {
printHeader(true);
print '	<div id="body">
<header id="header">
  
  <h1 id="page-title">rverse::Account</h1>

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
global $pagetitle;
if(empty($pagetitle)) {
$pagetitle = loc('grp.portal.error');
}
printHeader('err');
printMenu();
print $GLOBALS['div_body_head']; print "\n".'<header id="header">
<h1 id="page-title" class="left">'.$pagetitle.'</h1>
</header>';
print '<div class="body-content track-error" data-track-error="'.$code.'">';
noContentWindow((!empty($message) ? $message : loc('grp.portal.error_general'))); print $GLOBALS['div_body_head_end']; printFooter();
}

function plainErr($code, $message) {
http_response_code(!empty($code) ? $code : 403);
header('Content-Type: text/plain');
print !empty($message) ? $message."\n" : "403 Forbidden\n";
}

function notLoggedIn() {
if(isset($_SERVER['HTTP_X_PJAX'])) {
header('Content-Type: application/json');
http_response_code(401);
exit(json_encode(array('success' => 0, 'errors' => [array('message' => 'You have been logged out.
Please log back in.', 'error_code' => 1510110)], 'code' => 401)));
}
else {
plainErr(403, '403 Forbidden');
	} 
}