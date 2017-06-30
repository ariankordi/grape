<?php function grp_err($errno, $errstr, $errfile, $errline) { 
http_response_code(500);
if(!error_reporting()) {
header('Content-Type: text/plain');
echo "500 Internal Server Error\n";
} else { ?>
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html"><title>500 Internal Server Error</title></head>
<body>
<h1>500 Internal Server Error</h1>
<p>Sorry, something went wrong.<br><br>A team of highly trained monkeys has been dispatched to deal with this situation.</p>
If you see them, give them this error as text:<br>
<pre>    <?=json_encode(array($errno, $errstr, $errfile, $errline))?></pre>

</body></html>
<?php } exit(); }