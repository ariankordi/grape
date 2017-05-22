<?php
require_once '../grplib-php/init.php';
require_once 'lib/htm.php';
$pagetitle = 'Error';
print printHeader('old'); print printMenu('old'); print notFound('d', false); printFooter('old'); grpfinish($mysql); exit();