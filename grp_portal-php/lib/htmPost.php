<?php

function displayempathy($row, $post, $my) {
global $mysql;
if($my == true) { global $my_empathy_added; }
$empathies_person = $mysql->query('SELECT * FROM people WHERE people.pid = "'.($my == true ? $_SESSION['pid'] : $row['pid']).'" LIMIT 1')->fetch_assoc();
$empathies_person_mii = getMii($empathies_person, $post['feeling_id']);
print '<a href="/users/'.htmlspecialchars($empathies_person['user_id']).'" data-pjax="#body"  class="post-permalink-feeling-icon'.($my == true ? ' visitor' : '').'"'.($my == true ? 'style="'.($my_empathy_added == false ? 'display: none;' : '').'"' : '').'><img src="'.$empathies_person_mii['output'].'" class="user-icon"></a>';

}