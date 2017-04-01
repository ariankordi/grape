<?php
require 'config.php';

function connectSQL($server, $user, $pw, $name) {
$mysql = new mysqli($server, $user, $pw, $name);
$mysql->set_charset('utf8mb4');

if($mysql->connect_errno){
http_response_code(502); die(); } 
$mysql->query('SET time_zone = "-4:00"');
date_default_timezone_set('America/New_York');
return $mysql;
}
function initAll() {
global $grp_config_database_server; global $grp_config_database_user; global $g$
$mysql = connectSQL($grp_config_database_server, $grp_config_database_user, $gr$
return $mysql;
}
function grpfinish($mysql) {
$mysql->close();
} 

?>