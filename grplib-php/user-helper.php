<?php

function getActivity() {
global $mysql;
$search_relationships_by_post = prepared('SELECT a.target AS pid, bm.recent_created_at FROM (SELECT pid, MAX(created_at) AS recent_created_at FROM posts GROUP BY pid) bm INNER JOIN relationships a ON bm.pid = a.target WHERE a.source = ?
UNION
SELECT IF(a.target = ?, a.source, a.target) AS pid, bm.recent_created_at FROM (SELECT pid, MAX(created_at) AS recent_created_at FROM posts GROUP BY pid) bm INNER JOIN friend_relationships a ON bm.pid = IF(a.target = ?, a.source, a.target) WHERE (a.source = ? OR a.target = ?)
ORDER BY recent_created_at DESC LIMIT 50 OFFSET ?', [$_SESSION['pid'], $_SESSION['pid'], $_SESSION['pid'], $_SESSION['pid'], $_SESSION['pid'], (!empty($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] : 0)]);
if($search_relationships_by_post->num_rows != 0) {
$posts = array();
while($row_relationship_posts = $search_relationships_by_post->fetch_assoc()) {
$person = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$row_relationship_posts['pid'].'"')->fetch_assoc();
$get_latest_post = $mysql->query('SELECT * FROM posts WHERE posts.pid = "'.$person['pid'].'" AND posts.hidden_resp != 1 OR posts.pid = "'.$person['pid'].'" AND posts.hidden_resp IS NULL ORDER BY posts.created_at DESC LIMIT 1');
if($get_latest_post->num_rows != 0) {
$posts[] = $get_latest_post->fetch_assoc(); }
}
} else {
$posts = false; }
return $posts;
}

function searchUser() {
global $mysql;
if(!empty($_GET['query'])) {
$query = addcslashes($_GET['query'], '%_');
$param[] = $query;
$param[] = $query;
	}
$param[] = (!empty($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] : 0);
return prepared('SELECT * FROM people WHERE CONCAT_WS(\'\', user_id, screen_name) '.(empty($_GET['query']) ? '= ""' : 'LIKE CONCAT(?, "%") OR CONCAT_WS(\'\', screen_name, user_id) LIKE CONCAT(?, "%")').' ORDER BY people.created_at DESC LIMIT 50 OFFSET ?', $param);
}

function getUpdates($pid) {
global $mysql;
return array(
'news' => $mysql->query('SELECT * FROM news WHERE news.to_pid = "'.$pid.'" AND news.has_read = "0" AND news.merged IS NULL ORDER BY news.news_id LIMIT 64')->num_rows,
'friend_requests' => $mysql->query('SELECT * FROM friend_requests WHERE friend_requests.recipient = "'.$pid.'" AND friend_requests.has_read = "0" AND friend_requests.finished = "0"')->num_rows,
'messages' => $mysql->query('select a.*, bm.conversation_id, bm.pid, bm.has_read from (select pid, has_read, conversation_id from messages where has_read != "1" and pid != "'.$pid.'") bm inner join conversations a on bm.conversation_id = a.conversation_id WHERE bm.pid != "'.$pid.'" AND a.sender = "'.$pid.'" OR a.recipient = "'.$pid.'" and bm.has_read = "0" OR bm.has_read IS NULL')->num_rows,
);
}

function getProfileComment($user, $profile) {
global $mysql;
if(!empty($profile)) { return htmlspecialchars($profile['comment']); }
$get_profile = $mysql->query('SELECT comment FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1');
if($get_profile->num_rows != 0) {
return $get_profile->fetch_assoc()['comment'];
	} else {
	return null;
	}
}

function findBlock($source, $target) {
if($source == $target) {
return false;
	}
global $mysql;
if(prepared('SELECT type FROM blacklist WHERE blacklist.source = ? AND blacklist.target = ? OR blacklist.source = ? AND blacklist.target = ?', [$source, $target, $target, $source])->num_rows != 0) {
return true;
	} else {
return false;
	}
}

function canUserView($user, $me) {
global $mysql;
if($me && findBlock($me, $user)) {
return true;
	}
return false;
}

function getProfile($user) {
global $mysql;
$search_profile = $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1');
if($search_profile->num_rows == 0) {
$createprofile = $mysql->query('INSERT INTO profiles(pid, platform_id) VALUES("'.$user['pid'].'", "'.$user['platform_id'].'")');
return $mysql->query('SELECT * FROM profiles WHERE profiles.pid = "'.$user['pid'].'" LIMIT 1')->fetch_assoc();
} else {
return $search_profile->fetch_assoc(); }
}

function checkFriendOK($user) {
if(getProfile($user)['allow_request'] == 0) {
return false;
	} else {
return true;
	}
}

function sendNews($from, $to, $type, $subject) {
global $mysql;
if($type == 2 || $type == 3 ? $mysql->query('SELECT empathy_optout FROM profiles WHERE pid = "'.$to.'" AND empathy_optout = 1 LIMIT 1')->num_rows == 0 : true) {
	// If the user gave the same type of notification 8 seconds ago, then don't send this.
	$check_fastnews = $mysql->query('SELECT news.to_pid, news.created_at FROM news WHERE news.from_pid = "'.$from.'" AND news.to_pid = "'.$to.'" AND news.news_context = "'.$type.'" AND news.created_at > NOW() - 8 ORDER BY news.created_at DESC');
    if($check_fastnews->num_rows == 0) {
    $check_ownusernews = $mysql->query('SELECT * FROM news WHERE news.from_pid = "'.$from.'" AND news.to_pid = "'.$to.'" AND news.news_context = "'.$type.'"'.($type != 6 ? ' AND news.id = "'.$subject.'"' : '').' AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
	$check_mergedusernews = $mysql->query('SELECT * FROM news WHERE news.from_pid = "'.$from.'" AND news.to_pid = "'.$to.'" AND news.news_context = "'.$type.'"'.($type != 6 ? ' AND news.id = "'.$subject.'"' : '').' AND news.merged IS NOT NULL AND news.created_at > NOW() - 7200 ORDER BY news.created_at DESC');
 if($check_mergedusernews->num_rows != 0) {
	$result_update_mergedusernewsagain = $mysql->query('UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$check_mergedusernews->fetch_assoc()['merged'].'"');	
	} elseif($check_ownusernews->num_rows != 0) {
	$result_update_ownusernewsagain = $mysql->query('UPDATE news SET has_read = "0", created_at = CURRENT_TIMESTAMP WHERE news.news_id = "'.$check_ownusernews->fetch_assoc()['news_id'].'"'); }
else {
$result_update_newsmergesearch = $mysql->query('SELECT * FROM news WHERE news.to_pid = "'.$to.'"'.($type != 6 ? ' AND news.id = "'.$subject.'"' : '').' AND news.created_at > NOW() - 7200 AND news.news_context = "'.$type.'" ORDER BY news.created_at DESC');	
if($result_update_newsmergesearch->num_rows != 0) {
$row_update_newsmergesearch = $result_update_newsmergesearch->fetch_assoc();
$result_newscreatemerge = $mysql->query('INSERT INTO grape.news(from_pid, to_pid,'.($type != 6 ? ' id,' : '').' merged, news_context, has_read) VALUES ("'.$from.'", "'.$to.'",'.($type != 6 ? ' "'.$subject.'",' : '').' "'.$row_update_newsmergesearch['news_id'].'", "'.$type.'", "0")');

$result_update_newsformerge = $mysql->query('UPDATE news SET has_read = "0", created_at = NOW() WHERE news.news_id = "'.$row_update_newsmergesearch['news_id'].'"');
		}
else {
        $result_newscreate = $mysql->query('INSERT INTO grape.news(from_pid, to_pid,'.($type != 6 ? ' id,' : '').' news_context, has_read) VALUES ("'.$from.'", "'.$to.'",'.($type != 6 ? ' "'.$subject.'",' : '').' "'.$type.'", "0")'); 	
	} } 
    } }
return true;
}

function userIDtoPID($user_id) {
global $mysql;
$query = $mysql->query('SELECT pid FROM people WHERE people.user_id = "'.$user_id.'" LIMIT 1');
if(!$query || $query->num_rows == 0) { return false; }
return $query->fetch_assoc()['pid'];
}

function infoFromPID($pid) {
global $mysql;
return $mysql->query('SELECT user_id, screen_name FROM people WHERE people.pid = "'.$pid.'" LIMIT 1')->fetch_assoc();
}

function getNewsNotify() {
global $mysql;
if($mysql->query('SELECT * FROM news WHERE news.to_pid = "'.$_SESSION['pid'].'" AND news.has_read = "0" AND news.merged IS NULL LIMIT 1')->num_rows == 1) { return true; } else { return false; }
}

function getMessageNotify() {
global $mysql;
if($mysql->query('SELECT * FROM friend_requests WHERE friend_requests.recipient = "'.$_SESSION['pid'].'" AND friend_requests.finished = "0" AND friend_requests.has_read = "0" LIMIT 1')->num_rows == 1) { return true; } else { return false; }
}

function getFriendRelationship($source, $target) {
global $mysql;
$search_relationship = $mysql->query('SELECT * FROM friend_relationships WHERE friend_relationships.source = "'.$source.'" AND friend_relationships.target = "'.$target.'" OR friend_relationships.source = "'.$target.'" AND friend_relationships.target = "'.$source.'"');
if(!$search_relationship || $search_relationship->num_rows == 0) { return false; }
$relationship = $search_relationship->fetch_assoc();
$search_conversation = $mysql->query('SELECT * FROM conversations WHERE conversations.sender = "'.$source.'" AND conversations.recipient = "'.$target.'" OR conversations.sender = "'.$target.'" AND conversations.recipient = "'.$source.'"');
if($search_conversation->num_rows != 0) { $conversation = $search_conversation->fetch_assoc(); }

return array(
'relationship_id' => $relationship['relationship_id'],
'updated' => $relationship['updated'],
'conversation_id' => ($search_conversation->num_rows != 0 ? $conversation['conversation_id'] : false),
'conversation_createdat' => ($search_conversation->num_rows != 0 ? $conversation['created_at'] : false)
);
}

function profileRelationshipVisible($me, $user, $requirement) {
if($requirement == 1) {
return true;
	}
elseif($requirement == 3) {
	if($me == $user) {
	return true;
		}
	else {
	return false;
		}
	}
elseif($requirement == 2) {
	if($me == $user) {
	return true;
		}
		else {
		if(getFriendRelationship($me, $user)) {
		return true;
			} else {
		return false;
			}
		}
	}
else {
return true;
	}
}