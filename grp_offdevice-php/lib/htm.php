<?php
   bindtextdomain('miitoo', '../l10n/');
   bindtextdomain('community', '../l10n/');

function printHeader($mode) {
global $pagetitle;
global $bodyClass;
global $bodyID;
print '<!DOCTYPE html>
    <html lang="en"><head><meta http-equiv="Content-Type" content="text/html">
    <meta charset="utf-8">
    <title>'.(!empty($pagetitle) ? $pagetitle : 'grp.offdevice.page_title').'</title>
    <meta http-equiv="content-style-type" content="text/css">
    <meta http-equiv="content-script-type" content="text/javascript">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    ';
# Put checks here soon for themes and such, please!
print '
    <link rel="stylesheet" type="text/css" href="/css/offdevice'.(!empty($mode) && $mode == 'old' ? '-old' : '').'.css">';
global $nnecnojs;
if(!isset($nnecnojs)) { print '
<script src="/js/offdevice/complete-old.js"></script>'; } print '<style type="text/css"></style>
  <style type="text/css">* {}</style><style type="text/css">* {}</style></head>
';
print '  <body '.(!empty($bodyID) ? 'id='.$bodyID.' ' : '').(!empty($_SESSION['pid']) ? ' data-token="" data-hashed-pid="'.sha1($_SESSION['pid']).'" data-user-id="'.htmlspecialchars($_SESSION['user_id']).'" data-game-skill="0" data-follow-done="1" data-post-done="1" data-enable-user-recommendation="1"' : 'class="guest-top guest" data-token=""').'>';
print '    
    <div id="wrapper">
      

      
      

';      
	}

function truncate($text, $chars) {
$truncate_post_bodyp1 = mb_substr(($text), 0, $chars);
return (mb_strlen($text) >= $chars + 1 ? $truncate_post_bodyp1.'...' : $truncate_post_bodyp1);
}

	
function printMenu($mode) {
global $mnselect;
print '<div id="sub-body">
        <menu id="'.(isset($mode) && $mode == 'old' && empty($_SESSION['pid']) ? '' : 'global-menu').'">
          <li id="global-menu-logo"><h1>'.(!isset($mode) || $mode != 'old' ? '<a href="/">' : '').'<img src="'.(isset($mode) && $mode == 'old' ? '/img/menu-logo.png' : 'https://d13ph7xrk1ee39.cloudfront.net/img/menu-logo.png').'" alt="Miiverse" width="'.(!isset($mode) || $mode != 'old' ? '165' : '200').'" height="'.(!isset($mode) || $mode != 'old' ? '30' : '55').'">'.(!isset($mode) || $mode != 'old' ? '</a>' : '').'</h1></li>
		  ';
if(!empty($_SESSION['pid'])) {
global $mysql;
$miia = $mysql->query('SELECT face, mii_hash, mii, user_id, official_user FROM people WHERE people.pid = "'.$_SESSION['pid'].'" LIMIT 1')->fetch_assoc();
$mii = getMii($miia, false);
print (!isset($mode) || $mode != 'old' ? '<li id="global-menu-list">
            <ul>' : '' ).'
          <li id="global-menu-mymenu"'.(isset($mnselect) && $mnselect == 'users' ? ' class="selected"' : '').'><a href="/users/'.htmlspecialchars($miia['user_id']).'"><span class="icon-container'.($mii['official'] ? ' official-user' : '').'"><img src="'.$mii['output'].'" alt="'.loc('grp.portal.my_page').'"></span><span>'.loc('grp.portal.my_page').'</span></a></li>
          <li id="global-menu-feed"'.(isset($mnselect) && $mnselect == 'feed' ? ' class="selected"' : '').'><a href="/activity" class="symbol"><span>'.loc('grp.portal.activity').'</span></a></li>
          <li id="global-menu-community"'.(isset($mnselect) && $mnselect == 'community' ? ' class="selected"' : '').'><a href="/" class="symbol"><span>'.loc('grp.portal.community').'</span></a></li>
          <li id="global-menu-news"'.(isset($mnselect) && $mnselect == 'news' ? ' class="selected"' : '').'><a href="/news/my_news" class="symbol">'.(isset($mode) && $mode == 'old' ? '<span>'.loc('grp.portal.news').'</span>' : '').'<span class="badge" style="display: none;">0</span></a></li>
		  '.(!isset($mode) || $mode != 'old' ? '<li id="global-menu-my-menu"><button class="symbol js-open-global-my-menu open-global-my-menu"></button>
<menu id="global-my-menu" class="invisible none">
                  <li><a href="/settings/profile" class="symbol my-menu-profile-setting"><span>Profile Settings</span></a></li>

                  <li>
                    <form action="/act/logout" method="get" id="my-menu-logout" class="symbol">
                      <input type="hidden" name="location" value="'.htmlspecialchars($_SERVER['REQUEST_URI']).'">
                      <input type="submit" value="Sign out">
                    </form>
                  </li>
                </menu>
              </li>
		</ul>
		  </li>' : '');
} else {
print '<li id="global-menu-login">
            <form id="login_form" action="/act/login" method="get">
              <input type="hidden" name="location" value="'.htmlspecialchars($_SERVER['REQUEST_URI']).'">
              <input type="submit" class="black-button" value="Sign in">
            </form>
</li>';	  }
print '
        </menu>
      </div>';	
	
}

function notFound($text, $htext) {
http_response_code(404);
print '<div id="main-body">
';
if($htext == true) { $nftext = $text; }
else { switch($text) {
case 'posts':
$nftext = 'The post could not be found.'; break;
case 'replies':
$nftext = 'The comment could not be found.'; break;
case 'users':
$nftext = 'The user could not be found.'; break;
default:
$nftext = 'The page could not be found.'; break;
}	}
print '
<div class="no-content track-error" data-track-error="404">
  <div>
    <p>'.$nftext.'</p>
  </div>
</div>

</div>';
}

function noContentWindow($message) {
print '<div class="no-content"><div>
    <p>'.$message.'</p>
  </div></div>';
}

function printFooter($mode) {
global $version;
print '
      
      <div id="footer">
';
if(isset($mode) && $mode == 'old') {
print '<div id="sidebar">
</div>'; }
if(!isset($mode) || $mode != 'old') {
print '        <div id="footer-inner">


          <div class="link-container">
';            
}
global $dev_server;
print '            <p id="copyright">grape'.($dev_server == true ? '/'.$version.' (offdevice)' : '').'</p>
          </div>
</div> ';
if(!isset($mode) || $mode != 'old') { print '
      </div>
    </div>
  

<div style="clear: both;"></div>'; }
print '</body></html>';
	
	
}

function noLogin() {
header('Content-Type: text/plain');
print "403 Forbidden\n";
}


