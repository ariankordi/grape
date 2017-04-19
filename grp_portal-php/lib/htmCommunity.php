<?php

function getIcon($row) {
if(empty($row['icon']) || strlen($row['icon']) <= 1) { return 'https://miiverse.nintendo.net/img/title-icon-default.png'; } else {
return htmlspecialchars($row['icon']); }
}

function printTitle($row) {
global $mysql;
			print '<li id="community-'.$row['olive_community_id'].'" class="">
			<span class="icon-container"><img src="'.getIcon($row).'" class="icon"></span>';
	if($mysql->query('SELECT * FROM communities WHERE communities.olive_title_id = "'.$row['olive_title_id'].'" AND communities.type != "5"')->num_rows >= 2) {				
		print '
		<a href="/titles/'.$row['olive_title_id'].'" data-pjax="#body" class="list-button button">Related Communities</a>
		';
	}
     print '<a href="/titles/'.$row['olive_title_id'].'/'.$row['olive_community_id'].'" data-pjax="#body" class="scroll to-community-button"></a>
   <div class="body">
     <div class="body-content">
        <span class="community-name title">' . htmlspecialchars($row['name']) . '</span>
		
		';
if(!empty($row['platform_type'])) {
if($row['platform_type'] == '1' && $row['platform_id'] == '1') { $platformIDtext = 'Wii U Games'; }
elseif($row['platform_type'] == '1' && $row['platform_id'] != '1') { $platformIDtext = '3DS Games'; } 
elseif($row['platform_type'] == '2') { $platformIDtext = '3DS Games'; } 
elseif($row['platform_type'] == '3') { $platformIDtext = 'Virtual Console'; } 
else { $platformIDtext = 'Others'; } }
	
	if(!empty($row['platform_id'])) {
print '<span class="platform-tag platform-tag-'.($row['platform_id'] == 1 ? 'wiiu' : '3ds').'"></span>
<span class="text">'.$platformIDtext.'</span>
';
	}
	
print '
  </div>
 </div>
</li>
';
}

function favoriteWithTitle($row_community) {
global $mysql;
$row_get_titles_from_cid = $mysql->query('SELECT * FROM titles WHERE titles.olive_title_id = "'.$row_community['olive_title_id'].'"')->fetch_assoc();
if(!empty($row_get_titles_from_cid['platform_id'])) {
if($row_get_titles_from_cid['platform_id'] == '0') { $platform_id_text = "3ds"; }
if($row_get_titles_from_cid['platform_id'] == '1') { $platform_id_text = "wiiu"; }
if($row_get_titles_from_cid['platform_id'] == '2') { $platform_id_text = "3ds"; }
}
return '
<li id="community-'.$row_community['olive_community_id'].'" class="">
  <span class="icon-container"><img src="'.getIcon($row_community).'" class="icon"></span>
  <a href="/titles/'.htmlspecialchars($row_community['olive_title_id']).'/'.htmlspecialchars($row_community['olive_community_id']).'" data-pjax="#body" class="scroll arrow-button"></a>
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
$row_get_titles_from_cid = $mysql->query('SELECT * FROM titles WHERE titles.olive_title_id = "'.$row_community['olive_title_id'].'"')->fetch_assoc();
if(!empty($row_get_titles_from_cid['platform_id'])) {
if($row_get_titles_from_cid['platform_id'] == '0') { $platform_id_text = "3ds"; }
if($row_get_titles_from_cid['platform_id'] == '1') { $platform_id_text = "wiiu"; }
if($row_get_titles_from_cid['platform_id'] == '2') { $platform_id_text = "3ds"; }
}
return '<li class="favorite-community">
      <a href="/titles/'.htmlspecialchars($row_community['olive_title_id']).'/'.htmlspecialchars($row_community['olive_community_id']).'" data-pjax="#body"><span class="icon-container"><img class="icon" src="'.getIcon($row_community).'"></span></a>'.      (isset($platform_id_text) ? '<span class="platform-tag platform-tag-'.$platform_id_text.'"></span>' : '').'
    </li>';	
} }