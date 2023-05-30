<?php
require_once(dirname(__FILE__) . '/../grplib-php/AltoRouter.php');

$router = new AltoRouter();

$router->addRoutes(array(
    ['GET|POST', '/titles/show', 'redirect.php', 'Titles-show'],
    ['GET|POST', '/titles/[a:title_id]', 'titles.php', 'Titles'],
    ['GET|POST', '/titles/[a:title_id]/[a:community_id]', 'titles.php', 'Titles_Community'],
    ['GET|POST', '/titles/[a:title_id]/[a:community_id]/new', 'titles.php', 'Titles_Community_New'],
    ['GET|POST', '/titles/[a:title_id]/[a:community_id]/[a:mode]', 'titles.php', 'Titles_Community_Mode'],
    ['GET|POST', '/titles/[a:title_id]/[a:community_id]/favorite.json', 'communities-createfavorite.php', 'Titles_Community_Favorite'],
    ['GET|POST', '/titles/[a:title_id]/[a:community_id]/unfavorite.json', 'communities-createfavorite.php', 'Titles_Community_Unfavorite'],
    ['GET|POST', '/settings/titles/[a:title_id]', 'title_settings.php', 'Settings_Titles'],
    ['GET|POST', '/act/[a:pg]', 'act.php', 'Act'],
    ['GET|POST', '/act/', 'act.php', 'Act_Index'],
    ['GET|POST', '/theme-set', 'theme-set.php', 'Theme_Set'],
    ['GET|POST', '/settings/played_title_ids', 'my/played.php', 'Settings_Played_Titles'],
    ['GET|POST', '/', 'root.php', 'Root'],
    ['GET|POST', '/my/latest_following_related_profile_posts', 'my/follow-rel-posts.php', 'My_Following_Related_Profile_Posts'],
    ['GET|POST', '/my_blacklist', 'my_blacklist.php', 'My_Blacklist'],
    ['GET|POST', '/settings/profile_post.unset.json', 'profile-post-unset.php', 'Settings_Profile_Post_Unset'],
    ['GET|POST', '/check_update.json', 'check_update.php', 'Check_Update'],
    ['GET|POST', '/settings/tutorial_post', 'tutorial_post.php', 'Settings_Tutorial_Post'],
    ['GET|POST', '/friend_messages', 'messages.php', 'Friend_Messages'],
    ['GET|POST', '/news/my_news', 'news.php', 'News_My_News'],
    ['GET|POST', '/news/friend_requests', 'friendrequests.php', 'News_Friend_Requests'],
    ['GET|POST', '/users/friend_request.accept.json', 'friend_request.php', 'Users_Friend_Request_Accept'],
    ['GET|POST', '/users/friend_request.cancel.json', 'friend_request.php', 'Users_Friend_Request_Cancel'],
    ['GET|POST', '/users/friend_request.delete.json', 'friend_request.php', 'Users_Friend_Request_Delete'],
    ['GET|POST', '/users/breakup.json', 'friend_request.php', 'Users_Breakup'],
    ['GET|POST', '/users', 'user-search.php', 'Users'],
    ['GET|POST', '/users/show', 'user-show.php', 'Users_Show2'],
    ['GET|POST', '/warning/deleted_account', 'content/warnings/act_deleted.php', 'Warning_Deleted_Account'],
    ['GET|POST', '/warning/readonly', 'content/warnings/readonly.php', 'Warning_Readonly'],
    ['GET|POST', '/communities', 'communities.php', 'Communities'],
    ['GET|POST', '/communities/favorites', 'communities-showfavorites.php', 'Communities_Favorites'],
    ['GET|POST', '/communities/categories', 'communities-categories.php', 'Communities_Categories'],
    ['GET|POST', '/identified_user_posts', 'identified_user_posts.php', 'Identified_User_Posts'],
    ['GET|POST', '/guest_menu', 'guest_menu.php', 'Guest_Menu'],
    ['GET|POST', '/my_menu', 'my_menu.php', 'My_Menu'],
    ['GET|POST', '/users/[*:user_id]/check_can_post.json', 'generate_success.json', 'Users_Check_Can_Post'],
    ['GET|POST', '/admin/titles_create', 'create_title.php', 'Admin_Titles_Create'],
    ['GET|POST', '/admin/communities_create', 'create_community.php', 'Admin_Communities_Create'],
    ['GET|POST', '/settings/profile', 'profile_settings.php', 'Settings_Profile'],
    ['GET|POST', '/settings/account', 'account_settings.php', 'Settings_Account'],
    ['GET|POST', '/posts', 'post-create.php', 'Posts_Create'],
    ['GET|POST', '/help_and_guide', 'help_and_guide.php', 'Help_And_Guide'],
    ['GET|POST', '/special/redesign_announcement', 'content/special/redesign.php', 'Special_Redesign_Announcement'],
    ['GET|POST', '/users/@me', 'profile-me.php', 'Users_Profile_Me'],
    ['GET|POST', '/posts/[a:id]', 'posts.php', 'Posts'],
    ['GET|POST', '/posts/[a:id]/empathies.delete', 'posts.php', 'Posts_Empathies_Delete'],
    ['GET|POST', '/posts/[a:id]/[a:mode]', 'posts.php', 'Posts_Mode'],
    ['GET|POST', '/posts/[a:id]/screenshot.set_profile_post', 'posts.php', 'Posts_Set_Profile_Post'],
    ['GET|POST', '/replies/[a:id]', 'replies.php', 'Replies'],
    ['GET|POST', '/replies/[a:id]/empathies.delete', 'replies.php', 'Replies_Empathies_Delete'],
    ['GET|POST', '/replies/[a:id]/[a:mode]', 'replies.php', 'Replies_Mode'],
    ['GET|POST', '/replies/[a:id]/empathies.delete', 'replies.php', 'Replies_Empathies'],
    ['GET|POST', '/users/[*:user_id]', 'users.php', 'Users_Show'],
    ['GET|POST', '/users/[*:user_id].follow.json', 'users.php', 'Users_Follow'],
    ['GET|POST', '/users/[*:user_id].unfollow.json', 'users.php', 'Users_Unfollow'],
    ['GET|POST', '/users/[*:user_id].blacklist.create.json', 'blacklist.php', 'Users_Blacklist_Create'],
    // TODO FIX THE WAY THIS WORKS!!!!
    ['GET|POST', '/users/[*:user_id].blacklist.delete.json', 'blacklist.php', 'Users_Blacklist_Delete'],
    ['GET|POST', '/users/[*:user_id]/friend_request.create.json', 'friend_request.php', 'Users_Friend_Request_Create'],
    ['GET|POST', '/users/[*:user_id]/[a:mode]', 'users.php', 'Users_Mode'],
    ['GET|POST', '/friend_messages/[*:user_id]', 'messages.php', 'Friend_Messages_User'],
    ['GET|POST', '/create_title', 'create_title.php', 'Title_Create'],

		['GET', '/js/[*]', 'application/javascript', 'Static-JS'],
		['GET', '/css/[*]', 'text/css', 'Static-CSS'],
		['GET', '/img/[*]', '', 'Static-IMG'],
));

// Match the current request
$match = $router->match();
//error_log(print_r($match, true));
if($match) {
    // hack in order to serve static js/css/img for DEVELOPMENT PURPOSES!!!!!!
    // usually the htaccess would be used in its place
    if(substr($match['name'], 0, 6) === 'Static') {
        $fname = '../Static' . $_SERVER['REQUEST_URI'];
        $mime = $match['target'];
        if(empty($mime)) {
            $mime = mime_content_type($fname);
        }
        header('Content-Type: ' . $mime);
        readfile($fname);
        exit();
    }
    $_GET = array_merge($_GET, $match['params']);
    //error_log('chdir to ' . dirname($match['target']));
    // not using chdir causes a lot of pages to not work
    chdir(dirname($match['target']));
    require $match['target'];
} else {
    //require '404.php';
    http_response_code(404);
    echo 'not found';
}
