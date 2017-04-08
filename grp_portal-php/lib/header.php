<?php
if (empty($pagetitle)) {
	$pagetitle = 'grp.portal.page_title';
}
if (empty($body_id)) {
	$body_id = 'grp';
}
if (empty($has_header_js)) {
	$has_header_js = '1';
}

if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest" && $body_id !='help') {
	print '<title>';
	print $pagetitle;
	print '</title>
	';
	$div_body_head = '';
	$div_body_head_end = '';
}
}


else {
	$div_body_head = '<div id="body">';
	$div_body_head_end = '</div>';
	print '<!DOCTYPE html>';
	print '

<html lang="en">
  <head>
    <meta charset="utf-8">
	<title>';
    print $pagetitle;
	print '</title>';

if(!empty($_COOKIE['grp_theme'])) {
if($_COOKIE['grp_theme'] == 'grape' || $_COOKIE['grp_theme'] == 'blueberry' ||  $_COOKIE['grp_theme'] == 'cherry' ||  $_COOKIE['grp_theme'] == 'orange') {
$theme_css_file = '/css/portal-grp_offdevice_'.htmlspecialchars($_COOKIE['grp_theme']).'.css'; } 
else {
$theme_css_file = '/css/portal-grp_offdevice.css'; } } else {
$theme_css_file = '/css/portal-grp_offdevice.css'; }

    if (strpos( $_SERVER['HTTP_USER_AGENT'], 'miiverse') !== false) {
    if (strpos( $has_header_js, 'no') !== false) {
	print ('<link rel="stylesheet" type="text/css" href="/css/portal-grp.css">' );
		}
		else {
				print ('<link rel="stylesheet" type="text/css" href="/css/portal-grp.css"> <script src="/js/portal/complete.js"></script>' );
		}
	}
      else {
    if (strpos( $has_header_js, 'no') !== false) {
	print '<link rel="stylesheet" type="text/css" href="'.$theme_css_file.'">';
		}
		else {
				print ('<link rel="stylesheet" type="text/css" href="'.$theme_css_file.'"> <script src="/js/portal/complete-emu.js"></script>' );
		}
	  }
print '</head>';
print '  <body id="';
	print $body_id;
	print '" ';
    if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true) {
       print 'data-hashed-pid="' . sha1($_SESSION['pid']) . '"';
       print 'data-user-id="' . htmlspecialchars($_SESSION['user_id']) . '"';
       print 'data-game-skill="0" data-follow-done="1" data-post-done="1" data-lang="en" data-country="us" data-post-done="1"';
       print 'data-profile-url="/users/' . $_SESSION['user_id'] . '"';
	   } else { 
	   print 'data-user-id="" data-is-first-post="1"';
	   }

print '	   >


';    
}
?>