<?php function grp_err($errno, $errstr, $errfile, $errline) { http_response_code(500);
if(!error_reporting()) {
header('Content-Type: text/plain; charset=UTF-8');
echo "500 Internal Server Error\nStack dump (send to webmaster):\n".strtr(base64_encode(json_encode(array(
'errno'=>$errno,'errstr'=>$errstr,'errfile'=>$errfile,'errline'=>$errline)))'+','.');
} else { ?>
<!doctype html>
<html><head>
<style>body{ font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; }</style>
<title>500 Internal Server Error</title></head>
<body>
<h2>Exception</h2>
<i>
<?php echo "Error in {$errfile} at line {$errline}:<br />
{$errstr} (Code: {$errno})"; ?>
</i>
</body></html>
<?php } exit(); }