<?php

function favoriteWithTitle($row_community) {
global $mysql;
$row_get_titles_from_cid = mysqli_fetch_assoc(mysqli_query($mysql, 'SELECT * FROM titles WHERE titles.olive_title_id = "'.$row_community['olive_title_id'].'"'));
if(!empty($row_get_titles_from_cid['platform_id'])) {
if($row_get_titles_from_cid['platform_id'] == '0') { $platform_id_text = "3ds"; }
if($row_get_titles_from_cid['platform_id'] == '1') { $platform_id_text = "wiiu"; }
if($row_get_titles_from_cid['platform_id'] == '2') { $platform_id_text = "3ds"; }
}
return '
<li id="community-'.$row_community['olive_community_id'].'" class="">
  <span class="icon-container"><img src="'.(!empty($row_community['icon']) ? htmlspecialchars($row_community['icon']) : 'https://miiverse.nintendo.net/img/title-icon-default.png').'" class="icon"></span>
  <a href="/titles/' . htmlspecialchars($row_community['olive_title_id']) . '/' . htmlspecialchars($row_community['olive_community_id']) . '" data-pjax="#body" class="scroll arrow-button"></a>
  <div class="body">
    <div class="body-content">      <span class="community-name title">'.htmlspecialchars($row_community['name']).'</span>
'.
	(isset($platform_id_text) ? '<span class="platform-tag platform-tag-'.$platform_id_text.'"></span>' : '') .'

	
      <span class="text">'.htmlspecialchars($row_get_titles_from_cid['name']).'</span>
      
      
    </div>
  </div>
</li>

';
}

function favoriteWithIcon($row_community, $existence) {
if($existence == false) {
return '<li class="favorite-community empty">
      <span class="icon-container"></span>
    </li>';
} else {
global $mysql;
$row_get_titles_from_cid = mysqli_fetch_assoc(mysqli_query($mysql, 'SELECT * FROM titles WHERE titles.olive_title_id = "'.$row_community['olive_title_id'].'"'));
if(!empty($row_get_titles_from_cid['platform_id'])) {
if($row_get_titles_from_cid['platform_id'] == '0') { $platform_id_text = "3ds"; }
if($row_get_titles_from_cid['platform_id'] == '1') { $platform_id_text = "wiiu"; }
if($row_get_titles_from_cid['platform_id'] == '2') { $platform_id_text = "3ds"; }
}
return '<li class="favorite-community">
      <a href="/titles/' . htmlspecialchars($row_community['olive_title_id']) . '/' . htmlspecialchars($row_community['olive_community_id']) . '" data-pjax="#body"><span class="icon-container"><img class="icon" src="'.(!empty($row_community['icon']) ? htmlspecialchars($row_community['icon']) : 'https://miiverse.nintendo.net/img/title-icon-default.png').'"></span></a>'.      (isset($platform_id_text) ? '<span class="platform-tag platform-tag-'.$platform_id_text.'"></span>' : '').'
    </li>';	
} }

?>