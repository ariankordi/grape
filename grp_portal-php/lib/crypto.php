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
  


?>