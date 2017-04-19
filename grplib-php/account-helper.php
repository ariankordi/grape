<?php

function actLoginCheck($user_id, $password) {
global $mysql;
$search_user = $mysql->query('SELECT * FROM people WHERE people.user_id = "'.$user_id.'" LIMIT 1');
      if($search_user->num_rows == 0) {
return 'none'; }
$user = $search_user->fetch_assoc();
		if($user['ban_status'] >= 4) {
return 'ban'; }

$parts = explode('$', $user['user_pass']);
if(crypt($password, sprintf('$%s$%s$%s$', $parts[1], $parts[2], $parts[3])) != $user['user_pass']) {
if(password_hash($_POST['password'],PASSWORD_BCRYPT,['salt'=>'zvHy85=EZLaw8?5ct!Ov9YEiP(Gi)itI']) != $user['user_pass']) {
return 'fail'; } }

return $user;

}