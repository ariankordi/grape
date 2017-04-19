<?php
function genURL() {
# beginning 3 letters; dev = AYED/018101, prod = AYMH/018307
# we will be using our own beginning; AYYG/018606
$magic_url = pack ("C*", 0x01, 0x86, 0x06);
# this is a padding + a number
$urlenv_url = pack ("C*", 0x00, 0x00);
# another magic number
$magic2_url = pack ("C*", 0x41);
# final unique number
$unique_url = openssl_random_pseudo_bytes(10);
# end result
$b64url_data = str_replace(array('+','/','='),array('-','_',''),base64_encode("$magic_url$urlenv_url$magic2_url$unique_url"));

#$result_url = urlsafe_b64encode($results_url);
return $b64url_data;
}

function newURLgen() {
# beginning 3 letters; dev = AYED/018101, prod = AYMH/018307
# we will be using our own beginning; AYYG/018606
$magic_url = pack ("C*", 0x01, 0x86, 0x06, 0x00, 0x00, 0x41);
# add count of all objects
$unique_url = openssl_random_pseudo_bytes(10);
$semifinal = "$magic_url$unique_url";
global $mysql;
$num_objects = $mysql->query('SELECT COUNT(id) FROM posts')->fetch_assoc()['COUNT(id)'] + $mysql->query('SELECT COUNT(id) FROM replies')->fetch_assoc()['COUNT(id)'] + $mysql->query('SELECT COUNT(id) FROM messages')->fetch_assoc()['COUNT'] + $mysql->query('SELECT COUNT(pid) FROM people')->fetch_assoc()['COUNT(pid)'];
# end result
$all = decbin(bindec($semifinal) + $num_objects);

return str_replace(array('+','/','='),array('-','_',''),base64_encode($all));
}

function getPostID($post_id) {
$decode_post_id = bin2hex(base64_decode($post_id));
return substr($decode_post_id, 0, 4).'-'.substr($decode_post_id, 4, 8).'-'.substr($decode_post_id, 8, 12).'-'.substr($decode_post_id, 12, 16);	
}