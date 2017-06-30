-- phpMyAdmin SQL Dump
-- version 4.8.0-dev
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 03, 2017 at 08:53 AM
-- Server version: 5.7.18-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `grape`
--

-- --------------------------------------------------------


CREATE TABLE `bans` (
  `operator` int(12) NOT NULL,
  `reciever` int(12) NOT NULL,
  `operation_id` bigint(20) NOT NULL,
  `operation` int(8) NOT NULL DEFAULT '0' COMMENT '0 = restriction, 1 = tempban, 2 = permaban, 3 = deletion',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `finished` int(11) DEFAULT '0',
  `comment` text COLLATE utf8mb4_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `blacklist` (
  `source` int(12) NOT NULL,
  `target` int(12) NOT NULL,
  `type` int(1) NOT NULL DEFAULT '0',
  `blacklist_id` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `communities` (
  `olive_title_id` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `olive_community_id` varchar(20) CHARACTER SET latin1 NOT NULL,
  `community_id` int(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` int(2) NOT NULL,
  `min_perm` int(2) NOT NULL DEFAULT '0',
  `allowed_pids` text COLLATE utf8mb4_bin,
  `hidden` int(2) DEFAULT NULL,
  `icon` mediumtext CHARACTER SET latin1,
  `banner` mediumtext CHARACTER SET latin1,
  `banner_3ds` mediumtext CHARACTER SET latin1,
  `name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `description` text COLLATE utf8mb4_bin,
  `comment` text CHARACTER SET latin1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `conversations` (
  `conversation_id` bigint(20) NOT NULL,
  `sender` int(12) NOT NULL,
  `recipient` int(12) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `email_confirmation` (
  `pid` int(12) NOT NULL,
  `id` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_bin NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `state` int(1) NOT NULL DEFAULT '0',
  `finished` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `empathies` (
  `id` varchar(25) CHARACTER SET latin1 NOT NULL COMMENT 'Post/reply/whateverID',
  `pid` int(12) NOT NULL COMMENT 'User''s PID',
  `empathy_id` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_from` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `feeling_id` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `favorites` (
  `settings_id` bigint(20) NOT NULL,
  `pid` int(12) NOT NULL,
  `community_id` int(20) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `friend_relationships` (
  `relationship_id` bigint(20) NOT NULL,
  `source` int(12) NOT NULL,
  `target` int(12) NOT NULL,
  `is_me2me` int(2) NOT NULL DEFAULT '0',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `friend_requests` (
  `sender` int(12) NOT NULL,
  `recipient` int(12) NOT NULL,
  `news_id` bigint(20) NOT NULL,
  `message` text COLLATE utf8mb4_bin,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `has_read` int(8) NOT NULL DEFAULT '0',
  `finished` int(8) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `messages` (
  `conversation_id` bigint(20) NOT NULL,
  `id` varchar(25) COLLATE utf8mb4_bin NOT NULL COMMENT 'urlsafe base64',
  `pid` int(12) NOT NULL,
  `screenshot` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `feeling_id` int(1) NOT NULL DEFAULT '0',
  `platform_id` int(1) DEFAULT NULL,
  `body` text COLLATE utf8mb4_bin,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_from` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `is_spoiler` int(1) DEFAULT NULL,
  `has_read` int(8) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `news` (
  `from_pid` int(12) NOT NULL,
  `to_pid` int(12) NOT NULL,
  `news_id` bigint(20) NOT NULL,
  `id` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Full URI',
  `news_context` int(8) NOT NULL,
  `merged` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `has_read` int(8) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `people` (
  `pid` int(12) NOT NULL,
  `user_id` varchar(20) CHARACTER SET latin1 NOT NULL,
  `password` text CHARACTER SET latin1 NOT NULL,
  `screen_name` varchar(30) COLLATE utf8mb4_bin NOT NULL,
  `mii` varchar(130) CHARACTER SET latin1 DEFAULT NULL,
  `mii_image` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `mii_hash` varchar(15) CHARACTER SET latin1 DEFAULT NULL,
  `nnas_info` text COLLATE utf8mb4_bin,
  `face` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `official_user` int(8) DEFAULT NULL,
  `organization` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `platform_id` int(8) NOT NULL DEFAULT '1',
  `created_from` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `client_info` varchar(500) COLLATE utf8mb4_bin DEFAULT NULL,
  `device_id` decimal(12,0) DEFAULT NULL,
  `device_cert` varchar(384) CHARACTER SET latin1 DEFAULT NULL,
  `privilege` int(8) NOT NULL DEFAULT '0' COMMENT '0 = normal, 1 = special, 2 = mod, 3 = admin, 4 = superadmin, 5 = dev (god)',
  `image_perm` int(8) DEFAULT '0',
  `status` int(8) NOT NULL DEFAULT '0' COMMENT '0 = ok, 1 = cannot comment, 2 = tempban, 3 = permaban, 4 = device ban, 5 = deleted',
  `empathy_restriction` int(8) DEFAULT NULL,
  `ban_status` int(8) DEFAULT '0',
  `comment` text CHARACTER SET latin1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `posts` (
  `id` varchar(25) CHARACTER SET latin1 NOT NULL COMMENT 'urlsafe base64',
  `pid` int(12) NOT NULL,
  `_post_type` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT 'body',
  `screenshot` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `feeling_id` int(1) NOT NULL DEFAULT '0',
  `platform_id` int(1) DEFAULT NULL,
  `body` text COLLATE utf8mb4_bin,
  `url` text CHARACTER SET latin1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_from` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `community_id` int(20) NOT NULL,
  `is_spoiler` varchar(1) COLLATE utf8mb4_bin NOT NULL DEFAULT '0',
  `is_special` int(1) DEFAULT '0',
  `is_hidden` int(1) DEFAULT '0',
  `hidden_resp` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `profiles` (
  `pid` int(12) NOT NULL,
  `last_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` text COLLATE utf8mb4_bin,
  `birthday` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `platform_id` int(8) DEFAULT '1',
  `country` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `gender` varchar(2) COLLATE utf8mb4_bin DEFAULT NULL,
  `game_experience` varchar(8) COLLATE utf8mb4_bin DEFAULT '0',
  `favorite_screenshot` varchar(25) COLLATE utf8mb4_bin DEFAULT NULL,
  `empathy_optout` int(1) DEFAULT '0',
  `relationship_visibility` int(1) DEFAULT '1',
  `allow_request` int(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `relationships` (
  `relationship_id` bigint(20) NOT NULL,
  `source` int(12) NOT NULL,
  `target` int(12) NOT NULL,
  `is_me2me` int(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `replies` (
  `id` varchar(25) CHARACTER SET latin1 NOT NULL,
  `pid` int(12) NOT NULL,
  `reply_to_id` varchar(25) CHARACTER SET latin1 NOT NULL,
  `screenshot` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `feeling_id` int(1) NOT NULL DEFAULT '0',
  `platform_id` int(1) DEFAULT NULL,
  `body` text COLLATE utf8mb4_bin,
  `created_from` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `community_id` int(20) DEFAULT NULL,
  `is_spoiler` varchar(1) COLLATE utf8mb4_bin NOT NULL DEFAULT '0',
  `is_special` int(1) DEFAULT '0',
  `is_hidden` int(1) DEFAULT '0',
  `hidden_resp` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `reports` (
  `report_id` bigint(20) NOT NULL,
  `source` int(12) NOT NULL,
  `subject` varchar(45) COLLATE utf8mb4_bin DEFAULT NULL,
  `type` int(8) NOT NULL DEFAULT '0',
  `reason` int(8) NOT NULL DEFAULT '0',
  `message` text COLLATE utf8mb4_bin,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `finished` int(8) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `restrictions` (
  `operation_id` bigint(20) NOT NULL,
  `operator` int(12) NOT NULL,
  `id` varchar(25) COLLATE utf8mb4_bin DEFAULT NULL,
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '0 for post, 1 for comment',
  `recipients` text COLLATE utf8mb4_bin,
  `operation` int(1) NOT NULL DEFAULT '0' COMMENT '0 for empathy, 1 for reply'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `settings_title` (
  `settings_id` bigint(20) NOT NULL,
  `pid` int(12) NOT NULL,
  `olive_title_id` varchar(20) CHARACTER SET latin1 NOT NULL,
  `value` int(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `settings_tutorial` (
  `pid` int(12) NOT NULL,
  `tutorial_id` bigint(20) NOT NULL,
  `updated` datetime DEFAULT CURRENT_TIMESTAMP,
  `my_news` int(2) DEFAULT '0',
  `friend_messages` int(2) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `titles` (
  `olive_title_id` varchar(20) CHARACTER SET latin1 NOT NULL,
  `olive_community_id` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unique_id` decimal(20,0) DEFAULT NULL,
  `icon` mediumtext CHARACTER SET latin1,
  `banner` mediumtext CHARACTER SET latin1,
  `banner_3ds` mediumtext CHARACTER SET latin1,
  `name` text COLLATE utf8mb4_bin,
  `description` text COLLATE utf8mb4_bin,
  `platform_id` varchar(8) CHARACTER SET latin1 DEFAULT NULL,
  `platform_type` varchar(8) CHARACTER SET latin1 DEFAULT NULL,
  `hidden` int(8) DEFAULT NULL,
  `comment` text CHARACTER SET latin1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


ALTER TABLE `bans`
  ADD PRIMARY KEY (`operation_id`),
  ADD KEY `bibfk1` (`operator`);

ALTER TABLE `blacklist`
  ADD PRIMARY KEY (`blacklist_id`),
  ADD KEY `blibfk1` (`source`),
  ADD KEY `blibfk2` (`target`);

ALTER TABLE `communities`
  ADD PRIMARY KEY (`community_id`),
  ADD KEY `olive_community2title` (`olive_title_id`);

ALTER TABLE `conversations`
  ADD PRIMARY KEY (`conversation_id`),
  ADD KEY `conibfk1` (`sender`),
  ADD KEY `conibfk2` (`recipient`);

ALTER TABLE `email_confirmation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ecibfk1` (`pid`);

ALTER TABLE `empathies`
  ADD PRIMARY KEY (`empathy_id`),
  ADD KEY `pid_to_empathies` (`pid`);

ALTER TABLE `favorites`
  ADD PRIMARY KEY (`settings_id`),
  ADD KEY `fibfk1` (`community_id`),
  ADD KEY `fibfk2` (`pid`);

ALTER TABLE `friend_relationships`
  ADD PRIMARY KEY (`relationship_id`),
  ADD KEY `freibfk1` (`target`),
  ADD KEY `freibfk3` (`source`);

ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`news_id`),
  ADD KEY `freqibfk1` (`sender`),
  ADD KEY `freqibfk2` (`recipient`);

ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pidmess1` (`pid`),
  ADD KEY `mibfk1` (`conversation_id`);

ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`),
  ADD KEY `to_pid` (`to_pid`),
  ADD KEY `from_pid` (`from_pid`);

ALTER TABLE `people`
  ADD PRIMARY KEY (`pid`),
  ADD UNIQUE KEY `user_id_unique` (`user_id`);

ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posts_ibfk_1` (`pid`),
  ADD KEY `posts_ibfk_2` (`community_id`);

ALTER TABLE `profiles`
  ADD PRIMARY KEY (`pid`);

ALTER TABLE `relationships`
  ADD PRIMARY KEY (`relationship_id`),
  ADD KEY `target` (`target`),
  ADD KEY `source` (`source`) USING BTREE;

ALTER TABLE `replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `replies_ibfk_1` (`pid`),
  ADD KEY `replies_ibfk_2` (`reply_to_id`);

ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `repibfk1` (`source`);

ALTER TABLE `restrictions`
  ADD PRIMARY KEY (`operation_id`),
  ADD KEY `resibfk1` (`operator`);

ALTER TABLE `settings_title`
  ADD PRIMARY KEY (`settings_id`),
  ADD KEY `stibfk1` (`olive_title_id`),
  ADD KEY `stibfk2` (`pid`);

ALTER TABLE `settings_tutorial`
  ADD PRIMARY KEY (`tutorial_id`),
  ADD KEY `pids` (`pid`);

ALTER TABLE `titles`
  ADD PRIMARY KEY (`olive_title_id`);


ALTER TABLE `bans`
  MODIFY `operation_id` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `blacklist`
  MODIFY `blacklist_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
ALTER TABLE `communities`
  MODIFY `community_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
ALTER TABLE `conversations`
  MODIFY `conversation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
ALTER TABLE `empathies`
  MODIFY `empathy_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
ALTER TABLE `favorites`
  MODIFY `settings_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
ALTER TABLE `friend_relationships`
  MODIFY `relationship_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
ALTER TABLE `friend_requests`
  MODIFY `news_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
ALTER TABLE `news`
  MODIFY `news_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
ALTER TABLE `people`
  MODIFY `pid` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
ALTER TABLE `relationships`
  MODIFY `relationship_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
ALTER TABLE `reports`
  MODIFY `report_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
ALTER TABLE `restrictions`
  MODIFY `operation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
ALTER TABLE `settings_title`
  MODIFY `settings_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
ALTER TABLE `settings_tutorial`
  MODIFY `tutorial_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

ALTER TABLE `bans`
  ADD CONSTRAINT `bibfk1` FOREIGN KEY (`operator`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `blacklist`
  ADD CONSTRAINT `blibfk1` FOREIGN KEY (`source`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `blibfk2` FOREIGN KEY (`target`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `communities`
  ADD CONSTRAINT `cibfk1` FOREIGN KEY (`olive_title_id`) REFERENCES `titles` (`olive_title_id`);

ALTER TABLE `conversations`
  ADD CONSTRAINT `conibfk1` FOREIGN KEY (`sender`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `conibfk2` FOREIGN KEY (`recipient`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `email_confirmation`
  ADD CONSTRAINT `ecibfk1` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `empathies`
  ADD CONSTRAINT `eibfk1` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `favorites`
  ADD CONSTRAINT `fibfk1` FOREIGN KEY (`community_id`) REFERENCES `communities` (`community_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fibfk2` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `friend_relationships`
  ADD CONSTRAINT `freibfk1` FOREIGN KEY (`target`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `freibfk3` FOREIGN KEY (`source`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `friend_requests`
  ADD CONSTRAINT `freqibfk1` FOREIGN KEY (`sender`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `freqibfk2` FOREIGN KEY (`recipient`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `messages`
  ADD CONSTRAINT `mibfk1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mibfk2` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `news`
  ADD CONSTRAINT `nibfk1` FOREIGN KEY (`from_pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `nibfk2` FOREIGN KEY (`to_pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `posts`
  ADD CONSTRAINT `pibfk1` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pibfk2` FOREIGN KEY (`community_id`) REFERENCES `communities` (`community_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `profiles`
  ADD CONSTRAINT `pribfk1` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `relationships`
  ADD CONSTRAINT `ribfk1` FOREIGN KEY (`source`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ribfk2` FOREIGN KEY (`target`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `replies`
  ADD CONSTRAINT `reibfk1` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reibfk2` FOREIGN KEY (`reply_to_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `reports`
  ADD CONSTRAINT `repibfk1` FOREIGN KEY (`source`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `restrictions`
  ADD CONSTRAINT `resibfk1` FOREIGN KEY (`operator`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `settings_title`
  ADD CONSTRAINT `stibfk1` FOREIGN KEY (`olive_title_id`) REFERENCES `titles` (`olive_title_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stibfk2` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`);

ALTER TABLE `settings_tutorial`
  ADD CONSTRAINT `stuibfk1` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
