<?php
$grpmode = 1; require_once '../grplib-php/init.php';
require_once '../grp_act-php/lib/htm.php';

print printHeader();
print '<div class="alert alert-danger">
  You are required to sign in to view this page. Click the button below to log in or create an account.
</div>
<a class="btn btn-primary" href="/act/login'.(!empty($_GET['location']) ? '?location='.htmlspecialchars(urlencode($_GET['location'])) : '').'">Login</a>';
print printFooter();