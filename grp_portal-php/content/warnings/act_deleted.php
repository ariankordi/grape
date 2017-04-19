<?php
$warning_page_grp = true;
include_once '../../../grplib-php/init.php';
http_response_code(403);

$pagetitle = 'Warning';
$has_header_js = 'no';
include_once '../../lib/htm.php';
printHeader();

print $GLOBALS['div_body_head'];
print '<div class="window-page">
  <div class="window message-window with-button">
    <h1 class="window-title">'.$pagetitle.'</h1>
    <div class="window-body"><div class="window-body-inner message">
      <p>Account has been deleted.<br>This message has a high chance to appear in error, please log out and log back in.</p>
    </div></div>
    <div class="window-bottom-buttons single-button">
      <a href="/act/logout" type="button" class="exit-button button" data-sound="SE_WAVE_EXIT">Log Out</a>
    </div>
  </div>
</div>';
print $GLOBALS['div_body_head_end'];
printFooter();