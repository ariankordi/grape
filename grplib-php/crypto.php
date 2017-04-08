<?php

function gen_identity ($server, $pid, $user_id, $passwd) {
if(!isset($server) && strlen($server) != 2) {
$server = 'X1'; }
$my_identity_wip = "\\"."z"."\\"."1"."\\"."u"."\\".$pid."\\"."a"."\\".$user_id."\\"."s"."\\".$server."\\"."v"."\\"."0100"."\\"."c"."\\".sha1($passwd)."\\"."e"."\\"."00BD"."\\"."t"."\\".time();
$my_identity_final = $my_identity_wip."\\"."h"."\\".sha1($my_identity_wip);

return $my_identity_final;
}

function encrypt_identity ($key, $identity) {
$key = openssl_pkey_get_public($key);
$encrypt = openssl_public_encrypt($identity, $encrypted, $key);

return $encrypted;	
}

function decrypt_identity ($key, $identity_crypt) {
$key = openssl_pkey_get_private($key);
$decrypt = openssl_private_decrypt($identity_crypt, $decrypted, $key);

return $decrypted;
}
# Nintendo
  function GetTokenPart($decryptedToken, $id, $capture)
  {
      if(preg_match('#\\\\' . $id . '\\\\(' . $capture . ')#', $decryptedToken, $matches) == 1)
          return $matches[1];
      
      return FALSE;
  }
  
  function initToken($decryptedToken) {
global $mysql;
if(!preg_match('#^(.+)\\\\h\\\\([0-9a-z]{40})$#i', $decryptedToken)) {
return false; }
global $grp_config_server_env;
if(!empty($grp_config_server_env) && GetTokenPart($decryptedToken, 's', '[A-Za-z]\d') != $grp_config_server_env) {
return false; }
if(strlen(GetTokenPart($decryptedToken, 'u', '\d+')) == 10) {
$account_sql = 'SELECT * FROM people WHERE people.pid = "'.GetTokenPart($decryptedToken, 'u', '\d+').'" AND people.user_id = "'.GetTokenPart($decryptedToken, 'a', '[0-9a-zA-Z\\-\\_\\.]{6,20}').'" AND people.ban_status != 5';
$account_res = $mysql->query($account_sql);
if($account_res->num_rows == 0) {
return false; }
else {
global $identity_login;
$identity_login = $account_res->fetch_assoc();
return $identity_login;
}

   }
else {
return false; }

          
}


?>
