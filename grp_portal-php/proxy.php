<?php
if(str_contains($_GET["url"], "i.imgur.com") || str_contains($_GET["url"], "https://") || str_contains($_GET["url"], "http://")){
    exit();
}
$url = $_GET["url"];
$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_USERAGENT, $agent);
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt_array($ch,array(
    CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0',
    CURLOPT_ENCODING=>'gzip, deflate',
    CURLOPT_HTTPHEADER=>array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
    ),
));
$content = curl_exec($ch);
if(!$content){
    exit("failed to fetch content");
}
header("Content-Type: image/png");
exit($content);
?>