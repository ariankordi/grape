<?php
    require_once '../grplib-php/init.php';
    require 'lib/olv-url-enc.php';
	print 'Generated URL is ';
	print olv_enc_post();
	print "!\n";

