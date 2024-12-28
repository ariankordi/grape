<?php
require_once '../grplib-php/init.php';
$pagetitle = "Notifications".($dev_server ? ' ('.CONFIG_SRV_ENV.')' : '');
require_once 'lib/htm.php';
require_once '../grplib-php/user-helper.php';
printHeader();
$find_user_news = $mysql->query('SELECT * FROM news WHERE news.to_pid = "'.$_SESSION["pid"].'" AND news.merged IS NULL ORDER BY news.created_at DESC LIMIT 65');
?>
<div id="body" class="news-page" data-region-id="2">
    <div class="title-header text-header">
        <div class="header-banner-container">
            <div class="header-banner">
                <p>Here you can see if you've gotten a response or reaction from other users.</p>
            </div>
        </div>
        <h1 class="info-content">
            <span class="icon-container"></span>
            <span class="title-container">
                <span class="title">Notifications</span>
            </span>
        </h1>
    </div>
    <div class="body-content">
        <div class="tab-body">
            <div class="tab2">
				<a id="tab-header" href="/news/my_news" data-pjax="1" class="selected"><span class="name">Notifications</span></a>
				<a id="tab-header" href="/news/dms" class="symbol" data-pjax="1"><span class="name">Messages</span></a>
                <a id="tab-header" href="#" class="symbol" data-pjax="1"><span class="name">Friends</span></a>
			</div>
            <?php
            $query = prepared('SELECT pid FROM settings_tutorial WHERE pid = ? AND my_news = 1 LIMIT 1', [$_SESSION["pid"]]);
            if(!$query || $query->num_rows == 0){
            ?>
            <div id="news-tutorial" class="tutorial-window">
                <p>New notifications are marked with an orange dot.</p>
                <a href="#" class="button tutorial-close-button" data-tutorial-name="my_news">Close</a>
            </div>
            <?php } 
            if($find_user_news->num_rows == 0) { ?>
            <center><p>No notifications.</p></center>
            <?php } else { 
            ?>
            <ul class="list-content-with-icon-and-text arrow-list" id="news-list-content">
            <?php
            $update = $mysql->query('UPDATE news SET news.has_read = "1" WHERE news.to_pid = "'.$_SESSION["pid"].'"');
            while($news = $find_user_news->fetch_assoc()) {
                // Types - 0, test, 1 admin message, 2, empathy, 3, comment empathy, 4, my comment, 5, poster comment, 6, follow
                $user = $mysql->query('SELECT * FROM people WHERE people.pid = "'.$news['from_pid'].'" LIMIT 1')->fetch_assoc();
                $usermii = getMii($user, false);
                $find_merged_news = $mysql->query('SELECT * FROM news WHERE news.merged = "'.$news['news_id'].'" ORDER BY news.created_at LIMIT 20');
                if($find_merged_news->num_rows != 0) {
                    $merged = $find_merged_news->fetch_all(MYSQLI_ASSOC); }
                // Prepare for the most ugly code ever.
                if($news['news_context'] == 2 || $news['news_context'] == 4 || $news['news_context'] == 5) {
                    $newsurl = '/posts/'.$news['id'];
                    $news_post1 = $mysql->query('SELECT * FROM posts WHERE posts.id = "'.$news['id'].'" LIMIT 1');
                    $news_post = ($news_post1->num_rows == 0 ? array('_post_type' => 'body', 'body' => 'not found') : $news_post1->fetch_assoc());
                    $news_body = ($news_post['_post_type'] == 'artwork' ? 'handwritten' : truncate($news_post['body'], 17)); }
                    if($news['news_context'] == 3) {
                    $newsurl = '/replies/'.$news['id'];
                    $news_post1 = $mysql->query('SELECT * FROM replies WHERE replies.id = "'.$news['id'].'" LIMIT 1');
                    $news_post = ($news_post1->num_rows == 0 ? array('body' => 'not found') : $news_post1->fetch_assoc());
                    $news_body = truncate($news_post['body'], 17); }
                    if($news['news_context'] == 6) {
                    $newsurl = '/users/'.htmlspecialchars($user['user_id']);
                    $get_follow_user = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.target = "'.$user['pid'].'"');
                    $has_user_follow = (isset($merged) && count($merged) >= 1 ? true : ($get_follow_user->num_rows != 0 ? true : false));
                    }
                    require_once '../grplib-php/user-helper.php';
                    if($news['news_context'] == 2) {
                    if(!isset($merged)) { $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b> gave <a href="/posts/'.$news['id'].'" class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; } else {
                    if(count($merged) == 1) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']);	
                    $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b> and <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b> gave <a href="/posts/'.$news['id'].'" class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }	
                    if(count($merged) == 2) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']);
                    $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b>, and <abclass="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</b> gave <a href="/posts/'.$news['id'].'" class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }	
                    if(count($merged) == 3) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);
                    $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</b>, and <abclass="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</b> gave <a href="/posts/'.$news['id'].'" class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }
                    if(count($merged) >= 4) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);  $subtr_news_curr = count($merged) - 3;
                    $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</b>, and '.$subtr_news_curr.' '.(count($merged) == 4 ? 'other person' : 'others').' gave <a href="/posts/'.$news['id'].'" class="link">your Post&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }		
                        }
                    }
                    if($news['news_context'] == 3) {
                    if(!isset($merged)) { $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b> gave <a href="/replies/'.$news['id'].'" class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; } else {
                    if(count($merged) == 1) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']);	
                    $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b> and <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b> gave <a href="/replies/'.$news['id'].'" class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }	
                    if(count($merged) == 2) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']);
                    $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b>, and <abclass="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</b> gave <a href="/replies/'.$news['id'].'" class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }	
                    if(count($merged) == 3) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);
                    $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</b>, and <abclass="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</b> gave <a href="/replies/'.$news['id'].'" class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }
                    if(count($merged) >= 4) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);  $subtr_news_curr = count($merged) - 3;
                    $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</b>, and '.$subtr_news_curr.' '.(count($merged) == 4 ? 'other person' : 'others').' gave <a href="/replies/'.$news['id'].'" class="link">your Comment&nbsp;('.htmlspecialchars($news_body).')</a> a Yeah.'; }		
                        }
                    }
                    
                    if($news['news_context'] == 4) {
                    if(!isset($merged)) { $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b> commented on <a href="/posts/'.$news['id'].'" class="link">your post&nbsp;('.htmlspecialchars($news_body).')</a>.'; } else {
                    if(count($merged) == 1) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']);	
                    $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b> and <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b> commented on <a href="/posts/'.$news['id'].'" class="link">your post&nbsp;('.htmlspecialchars($news_body).')</a>.'; }	
                    if(count($merged) == 2) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']);
                    $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b>, and <abclass="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</b> commented on <a href="/posts/'.$news['id'].'" class="link">your post&nbsp;('.htmlspecialchars($news_body).')</a>.'; }	
                    if(count($merged) == 3) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);
                    $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</b>, and <abclass="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</b> commented on <a href="/posts/'.$news['id'].'" class="link">your post&nbsp;('.htmlspecialchars($news_body).')</a>.'; }
                    if(count($merged) >= 4) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);  $subtr_news_curr = count($merged) - 3;
                    $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</b>, and '.$subtr_news_curr.' '.(count($merged) == 4 ? 'other person' : 'others').' commented on <a href="/posts/'.$news['id'].'" class="link">your post&nbsp;('.htmlspecialchars($news_body).')</a>.'; }		
                        }
                    }
                    
                    if($news['news_context'] == 5) {
                    if(!isset($merged)) { $body = '<b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b> commented on <a href="/posts/'.$news['id'].'" class="link">'.htmlspecialchars($user['screen_name']).'\'s post&nbsp;('.htmlspecialchars($news_body).')</a>.'; } else {
                    if(count($merged) >= 1) { }
                           }
                    }
                    
                    if($news['news_context'] == 6) {
                    if(!isset($merged)) { $body = 'Followed by <b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b>.'; } else {
                    if(count($merged) == 1) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']);	
                    $body = 'Followed by <b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b> and <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b>.'; }	
                    if(count($merged) == 2) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']);
                    $body = 'Followed by <b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b>, and <abclass="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</b>.'; }	
                    if(count($merged) == 3) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);
                    $body = 'Followed by <b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m3fpu['screen_name']).'</b>, and <abclass="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</b>.'; }
                    if(count($merged) >= 4) {
                    $m2fpu = infoFromPID($merged[0]['from_pid']); $m3fpu = infoFromPID($merged[1]['from_pid']); $m4fpu = infoFromPID($merged[2]['from_pid']);  $subtr_news_curr = count($merged) - 3;
                    $body = 'Followed by <b class="nick-name">'.htmlspecialchars($user['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m2fpu['screen_name']).'</b>, <abclass="nick-name">'.htmlspecialchars($m4fpu['screen_name']).'</b>, and '.$subtr_news_curr.' '.(count($merged) == 4 ? 'other person' : 'others').'.'; }		
                        }
                    }
                ?>
                <li>
                    <a href="/users/<?=htmlspecialchars($user['user_id'])?>" data-pjax="#body" class="icon-container">
                        <img src="<?=$usermii['output']?>" class="icon">
                    </a>
                    <a href="<?=$newsurl?>" data-pjax="#body" class="arrow-button scroll"></a>
                    <div class="body"><p class="text"><?=(empty($body) ? 'Sorry, not implemented.' : $body)?> <span class="timestamp"><?=$news["created_at"]?></span></p></div>
                </li>
            <?php } ?>
            </ul>
            <?php } ?>
        </div>
    </div>
</div>