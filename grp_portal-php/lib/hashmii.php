<?php

function get_hash_from_userid($user_id) {
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://3ds-us.olv.nintendo.net/users/'.$_POST['mii_hash'].'/blacklist.confirm');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSLCERT, '/usr/share/nginx/grp_portal/lib/cert.pem');
curl_setopt($ch, CURLOPT_SSLCERTPASSWD, 'alpine');
curl_setopt($ch, CURLOPT_HEADER, TRUE);
$extraHeaders[] = 'X-Nintendo-ParamPack: XFxc';
curl_setopt($ch, CURLOPT_HTTPHEADER, $extraHeaders);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);
$dom = new DOMDocument();
$res=$dom->loadHTML($response);
$xpath = new DomXPath($dom);
$img = 'user-icon';
$imgs = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $img ')]");
if ($imgs->length != 0) {
    $mii_hash_act = str_replace('_normal_face.png','',str_replace('http://mii-images.cdn.nintendo.net/','',$imgs[0]->getAttribute('src')));
    $mii_hash_success = 'OK';
	} else {
print null;
}
curl_close($ch);
}

