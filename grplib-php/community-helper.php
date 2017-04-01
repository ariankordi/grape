<?php
require_once('init.php');

function communitiesShow($mysql, $sqlSuffix) {
$result_get_communities = $mysql->query('SELECT * FROM titles ORDER BY titles.created_at DESC'.(isset($sqlSuffix) ? $sqlSuffix : 'LIMIT 20'));
if(!$result_get_communities) {
$result = array(
'success' => 0,
'mysql_error' => $result_get_communities->errno );
return $result;
grpfinish(); exit(); }
if($result_get_communities->num_rows == 0) {
$result = array(
'success' => 1,
'communities' => 'null' );
return $result;
grpfinish(); exit(); }
$result = array(
'success' => 1,
'communities' => array() );
while($row_get_communities = $result_get_communities->fetch_assoc()) {
$result['communities'][] = array(
'olive_title_id' => $row_get_communities['olive_title_id'],
'olive_community_id' => $row_get_communities['olive_community_id'],
'unique_id' => $row_get_communities['unique_id'],
'created_at' => $row_get_communities['created_at'],
'icon' => $row_get_communities['icon'],
'banner' => $row_get_communities['banner'],
'banner_3ds' => $row_get_communities['banner_3ds'],
'name' => $row_get_communities['name'],
'description' => $row_get_communities['description'],
'platform_id' => $row_get_communities['platform_id'],
'platform_type' => $row_get_communities['platform_type']
);
	}
return $result;
grpfinish(); exit();
}

function communityInfo($mysql, $community_id) {
$result_get_communities = $mysql->query('SELECT * FROM communities WHERE community_id = "'.$mysql->real_escape_string($community_id).'" ORDER BY titles.created_at DESC');
if(!$result_get_communities) {
$result = array(
'success' => 0,
'mysql_error' => $result_get_communities->errno );
return $result;
grpfinish(); exit(); }
if($result_get_communities->num_rows == 0) {
$result = array(
'success' => 1,
'communities' => 'null' );
return $result;
grpfinish(); exit(); }
$result = array(
'success' => 1,
'communities' => array() );
$row_get_titles = $mysql->query('SELECT * FROM titles WHERE olive_title_id = "'.$mysql->real_escape_string($community_id).'" ORDER BY titles.created_at DESC')->fetch_assoc();
$row_get_communities = $result_get_communities->fetch_assoc()
$result['communities'][] = array(
'olive_title_id' => $row_get_communities['olive_title_id'],
'olive_community_id' => $row_get_communities['olive_community_id'],
'created_at' => $row_get_communities['created_at'],
'icon' => $row_get_communities['icon'],
'banner' => $row_get_communities['banner'],
'banner_3ds' => $row_get_communities['banner_3ds'],
'name' => $row_get_communities['name'],
'description' => $row_get_communities['description']
);
return $result;
grpfinish(); exit();
}
}

function communityPosts($mysql, $community_id, $sqlSuffix) {
$result_get_posts = $mysql->query('SELECT * FROM posts WHERE posts.community_id = "'.$mysql->real_escape_string($community_id).'" AND posts.is_hidden = "0" ORDER BY posts.created_at DESC '.(isset($sqlSuffix) ? $sqlSuffix : 'LIMIT 50'));
if(!$result_get_posts) {
$result = array(
'success' => 0,
'mysql_error' => $result_get_posts->errno );
return $result;
grpfinish(); exit(); }
if($result_get_posts->num_rows == 0) {
$result = array(
'success' => 1,
'posts' => 'null' );
return $result;
grpfinish(); exit(); }
$result = array(
'success' => 1,
'posts' => array() );
while($row_get_posts = $result_get_posts->fetch_assoc()) {
$result['posts'][] = array(
'id' => $row_get_posts['id'],
'pid' => $row_get_posts['pid'],
'_post_type' => $row_get_posts['_post_type'],
'screenshot' => $row_get_posts['screenshot'],
'feeling_id' => $row_get_posts['feeling_id'],
'platform_id' => $row_get_posts['platform_id'],
'body' => $row_get_posts['body'],
'url' => $row_get_posts['url'],
'created_at' => $row_get_posts['created_at'],
'community_id' => $row_get_posts['community_id'],
'is_spoiler' => $row_get_posts['is_spoiler'],
'is_hidden' => $row_get_posts['is_hidden'],
'hidden_resp' => $row_get_posts['hidden_resp']
);
	}
return $result;
grpfinish(); exit();

}

print_r(communityPosts($mysql, 1, 'LIMIT 10'));

?>