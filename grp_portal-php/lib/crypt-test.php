<?php
require_once '../../grplib-php/init.php';
require_once 'crypto.php';

header('Content-Type: text/plain; charset=UTF-8');
print "Heyo\n";
if(empty($_SESSION['pid'])) {
print "Log the fuck in thanks\n"; exit(); }
if(empty($_SESSION['password'])) {
print "Log the fuck back in something bad happened thanks\n"; exit(); }

print "Let's generate some tokens!\n";
print "Your new identity token is:\n\n";
$decryptedToken = gen_identity($grp_config_server_env, $_SESSION['pid'], $_SESSION['user_id'], $_SESSION['password']);
print $decryptedToken;
print "\n\nSweet, now let's encrypt it!!!!\n";
print base64_encode(encrypt_identity($grp_config_pubkey, $decryptedToken));
print "\n\nAMAZING!!!!!!!!\nNow let's draw individual values!!!11\n";
print "The PID from that was ".GetTokenPart($decryptedToken, 'u', '\d+').", the user ID being ".GetTokenPart($decryptedToken, 'a', '[0-9a-zA-Z\\-\\_\\.]{6,20}').", and the double-hashed password being ".GetTokenPart($decryptedToken, 'c', '[0-9a-z]{1,32}')."!!!!!\n\n";
print "The only thing to really do at this point would be to decrypt it, so..\n\n";
print decrypt_identity($grp_config_privkey, encrypt_identity($grp_config_pubkey, gen_identity($grp_config_server_env, $_SESSION['pid'], $_SESSION['user_id'], $_SESSION['password'])));
print "\n\n\nNice.\nOkay, that's enough for now, see you next time!\n";
