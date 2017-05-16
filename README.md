I don't know how to use Git.

# grape #
A Miiverse clone, named which due to Miiverse's original codename being 'olive'.
Heavy work in progress.
There is a lot of naive programming in here, I'm new to PHP and it'll get better soon.

# grp_portal #
This is portal, or the Wii U mode.
# grp_offdevice #
The offdevice version. This was written when I knew more PHP then when I had written portal. It's way better written, and I'm trying to rewrite portal now.
# grp_act #
The account login and profile system shared across all of these.

# grplib #
Some shared libraries to be used across all of these. None of these are seen by the user.

# How to install??? #
First, use nginx, since that's the best web server software. I'm kidding, but it's really good.
Set up however many servers you want. **You must have separate servers for each realm and cannot have them in directories of your site.**
If you want to set up portal, make its root grp\_portal-php, and the same for offdevice.
Make a database in MySQL, then make a new config.php from the template and connect to your DB.
Run db\_create.sql in whatever you use as an interface to MySQL, and it'll work.

# Requires #
grape requires:
* pecl-xml (dnf install php-pecl-xml)
* curl (dnf install php-curl)
* intl (dnf install php-intl)
* pear-mail (wip)

# Rewrites (nginx) #
These are required, you cannot use the .php files directly or you might get a 404 and JS won't work.

Portal: 

	rewrite ^/titles/([A-Za-z0-9_-]+)$ /titles.php?title_id=$1;
	rewrite ^/titles/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)$ /titles.php?title_id=$1&community_id=$2;
	rewrite ^/titles/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)/new$ /titles.php?title_id=$1&community_id=$2;
	rewrite ^/titles/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)$ /titles.php?title_id=$1&community_id=$2&mode=$3;
	rewrite ^/titles/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)/favorite.json$ /communities-createfavorite.php?olive_community_id=$2 last;
	rewrite ^/titles/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)/unfavorite.json$ /communities-createfavorite.php?olive_community_id=$2&delete last;
	rewrite ^/settings/titles/([A-Za-z0-9_-]+)$ /title_settings.php?title_id=$1 last;
	
	rewrite ^/act/([^/'"]+)$ /act.php?pg=$1 last;
	rewrite ^/act/$ /act.php?pg=index last;
	
	rewrite ^/theme-set$ /theme-set.php last;
	rewrite ^/settings/played_title_ids$ /my/played.php last;
	rewrite ^/$ /root.php last;
	rewrite ^/my/latest_following_related_profile_posts$ /my/follow-rel-posts.php last;
	rewrite ^/settings/profile_post.unset.json$ /profile-post-unset.php last;
	rewrite ^/check_update.json$ /check_update.php last;
	rewrite ^/settings/tutorial_post$ /tutorial_post.php last;
	rewrite ^/friend_messages$ /messages.php last;
	rewrite ^/news/my_news$ /news.php last;
	rewrite ^/news/friend_requests$ /friendrequests.php last;
	rewrite ^/users/friend_request.accept.json$ /friend_request.php last;
	rewrite ^/users/friend_request.cancel.json$ /friend_request.php?cancel last;
	rewrite ^/users/friend_request.delete.json$ /friend_request.php?delete last;
	rewrite ^/users/breakup.json$ /friend_request.php?breakup last;
	rewrite ^/users$ /user-search.php last;
	rewrite ^/users/show$ /user-show.php last;
	rewrite ^/warning/deleted_account$ /content/warnings/act_deleted.php last;
	rewrite ^/warning/readonly$ /content/warnings/readonly.php last;
	rewrite ^/communities$ /communities.php last;
	rewrite ^/communities/favorites$ /communities-showfavorites.php last;
	rewrite ^/identified_user_posts$ /identified_user_posts.php last;
	rewrite ^/guest_menu$ /guest_menu.php last;
	rewrite ^/my_menu$ /my_menu.php last;
	rewrite ^/admin/titles_create$ /create_title.php last;
	rewrite ^/admin/communities_create$ /create_community.php last;
	rewrite ^/settings/profile /profile_settings.php last;
	rewrite ^/settings/account /account_settings.php last;
	rewrite ^/posts$ /post-create.php last;
	rewrite ^/help_and_guide$ /help_and_guide.php last;
	rewrite ^/special/redesign_announcement$ /content/special/redesign.php last;
	rewrite ^/users/@me$ /profile-me.php last;

	rewrite ^/posts/([A-Za-z0-9-_]+)$ /posts.php?id=$1 last;
	rewrite ^/posts/([A-Za-z0-9-_]+)/([A-Za-z0-9-_]+)$ /posts.php?id=$1&mode=$2 last;
	rewrite ^/posts/([A-Za-z0-9-_]+).([A-Za-z0-9-_]+)$ /posts.php?id=$1&mode=$2 last;
	rewrite ^/posts/([A-Za-z0-9-_]+)/screenshot.set_profile_post$ /posts.php?id=$1&mode=screenshot.set_profile_post last;
	rewrite ^/replies/([A-Za-z0-9-_]+)$ /replies.php?id=$1 last;
	rewrite ^/replies/([A-Za-z0-9-_]+)/([A-Za-z0-9-_]+)$ /replies.php?id=$1&mode=$2 last;
	rewrite ^/replies/([A-Za-z0-9-_]+).([A-Za-z0-9-_]+)$ /replies.php?id=$1&mode=$2 last;
	rewrite ^/replies/([A-Za-z0-9-_]+)/empathies.delete$ /replies.php?id=$1&mode=empathies last;
	rewrite ^/users/([^/'"]+)$ /users.php?user_id=$1 last;
	rewrite ^/users/([^/'"]+).follow.json$ /users.php?user_id=$1&mode=follow last;
	rewrite ^/users/([^/'"]+).unfollow.json$ /users.php?user_id=$1&mode=unfollow last;
	rewrite ^/users/([^/'"]+)/friend_request.create.json$ /friend_request.php?create&user_id=$1 last;
	rewrite ^/users/([^/'"]+)/([^/'"]+)$ /users.php?user_id=$1&mode=$2 last;
	rewrite ^/friend_messages/([^/'"]+)$ /messages.php?user_id=$1 last;
	
Offdevice:

	rewrite ^/titles/([A-Za-z0-9_-]+)$ /titles.php?title_id=$1;
	rewrite ^/titles/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)$ /titles.php?title_id=$1&community_id=$2;
	rewrite ^/titles/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)/new$ /titles.php?title_id=$1&community_id=$2;
	rewrite ^/titles/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)$ /titles.php?title_id=$1&community_id=$2&mode=$3;
	rewrite ^/titles/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)/favorite.json$ /communities-createfavorite.php?olive_community_id=$2 last;
	rewrite ^/titles/([A-Za-z0-9_-]+)/([A-Za-z0-9_-]+)/unfavorite.json$ /communities-createfavorite.php?olive_community_id=$2&delete last;
	rewrite ^/act/([^/'"]+)$ /act.php?pg=$1 last;
	rewrite ^/act/$ /act.php?pg=index last;
	
	rewrite ^/communities$ /communities.php last;
	rewrite ^/$ /communities.php last;
	rewrite ^/communities/favorites$ /communities-showfavorites.php last;
	rewrite ^/posts$ /post-create.php last;
	rewrite ^/users/@me$ /profile-me.php last;
	rewrite ^/users$ /user-search.php last;
	rewrite ^/check_update.json$ /check_update.php last;
	rewrite ^/news/my_news$ /news.php last;
	rewrite ^/activity$ /activity.php last;
	rewrite ^/my_menu$ /my_menu.php last;
	ewrite ^/identified_user_posts$ /identified_user_posts.php last;
	rewrite ^/settings/profile_post.unset.json$ /profile-post-unset.php last;
	rewrite ^/settings/profile$ /profile_settings.php last;
	rewrite ^/settings/account /account_settings.php last;
	
	rewrite ^/posts/([A-Za-z0-9-_]+)$ /posts.php?id=$1 last;
	rewrite ^/posts/([A-Za-z0-9-_]+)/([A-Za-z0-9-_]+)$ /posts.php?id=$1&mode=$2 last;
	rewrite ^/posts/([A-Za-z0-9-_]+).([A-Za-z0-9-_]+)$ /posts.php?id=$1&mode=$2 last;
	rewrite ^/posts/([A-Za-z0-9-_]+)/screenshot.set_profile_post$ /posts.php?id=$1&mode=screenshot.set_profile_post last;
	rewrite ^/posts/([A-Za-z0-9_-]+)/empathies.delete$ /posts.php?id=$1&mode=empathies last;
	rewrite ^/replies/([A-Za-z0-9-_]+)$ /replies.php?id=$1 last;
	rewrite ^/replies/([A-Za-z0-9-_]+)/([A-Za-z0-9-_]+)$ /replies.php?id=$1&mode=$2 last;
	rewrite ^/replies/([A-Za-z0-9-_]+).([A-Za-z0-9-_]+)$ /replies.php?id=$1&mode=$2 last;
	rewrite ^/replies/([A-Za-z0-9-_]+)/empathies.delete$ /replies.php?id=$1&mode=empathies last;
	rewrite ^/users/([^/'"]+)$ /users.php?user_id=$1 last;
	rewrite ^/users/([^/'"]+)/([^/'"]+)$ /users.php?user_id=$1&mode=$2 last;
	rewrite ^/users/([^/'"]+).follow.json$ /users.php?user_id=$1&mode=follow last;
	rewrite ^/users/([^/'"]+).unfollow.json$ /users.php?user_id=$1&mode=unfollow last;
	
	rewrite ^/act/([A-Za-z0-9_-]+)$ /act.php?pg=$1 last;
	rewrite ^/act/$ /act.php?pg=index last;

	
# Anything else? #
Not much yet, thanks for asking.

Copyright (C) Arian Kordi 2017, styling and ideas copyright Nintendo, please don't cease and desist me.