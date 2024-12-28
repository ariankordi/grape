<?php
header("Content-Type: application/xml");
exit(file_get_contents("lib/policy-list.xml"));
?>