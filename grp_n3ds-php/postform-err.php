<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';

$pagetitle = 'Error'; $bodyClass = 'error-page';
printHeader(); topHeader($pagetitle);

print '<div class="body-content">
  <div class="no-content-window"><div class="window">
';
if(!empty($_GET['errors'])) { $err_msgd = base64_decode($_GET['errors']); $err_msg = json_decode($err_msgd);
if($err_msgd && $err_msg) {
foreach($err_msg as &$err_msgr) {
print '    <p>
      Error Code: '.(!empty($err_msgr->error_code) ? (is_numeric($err_msgr->error_code) && strlen($err_msgr->error_code) == 7 ? substr($err_msgr->error_code,0,3).' - '.substr($err_msgr->error_code,3,4) : htmlspecialchars($err_msgr->error_code)) : null).'
    </p>
    <p>
	  '.(!empty($err_msgr->message) ? htmlspecialchars($err_msgr->message) : null).'
    </p>
	';
} } }
print '    <div class="window-bottom-buttons">
      <button class="back-button button">Back</button>
    </div>
  </div></div>
</div>';

printFooter();