-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 13, 2024 at 07:03 AM
-- Server version: 10.6.15-MariaDB-1:10.6.15+maria~ubu2004
-- PHP Version: 8.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rv3`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_data`
--

CREATE TABLE `app_data` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `app_data` text NOT NULL,
  `title_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bans`
--

CREATE TABLE `bans` (
  `operator` int(12) NOT NULL,
  `reciever` int(12) NOT NULL,
  `operation_id` bigint(20) NOT NULL,
  `operation` int(8) NOT NULL DEFAULT 0 COMMENT '0 = restriction, 1 = tempban, 2 = permaban, 3 = deletion',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT current_timestamp(),
  `finished` int(11) DEFAULT 0,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `blacklist`
--

CREATE TABLE `blacklist` (
  `source` int(12) NOT NULL,
  `target` int(12) NOT NULL,
  `type` int(1) NOT NULL DEFAULT 0,
  `blacklist_id` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `communities`
--

CREATE TABLE `communities` (
  `olive_title_id` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `olive_community_id` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `community_id` int(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `type` int(2) NOT NULL,
  `min_perm` int(2) NOT NULL DEFAULT 0,
  `allowed_pids` text DEFAULT NULL,
  `hidden` int(2) DEFAULT NULL,
  `icon` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `banner` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `banner_3ds` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `comment` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `console_auth`
--

CREATE TABLE `console_auth` (
  `id` int(11) NOT NULL,
  `long_id` varchar(64) NOT NULL,
  `pid` varchar(128) NOT NULL,
  `user_id` varchar(128) NOT NULL,
  `theme` varchar(64) NOT NULL DEFAULT 'olive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `conversation_id` bigint(20) NOT NULL,
  `sender` int(12) NOT NULL,
  `recipient` int(12) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `email_confirmation`
--

CREATE TABLE `email_confirmation` (
  `pid` int(12) NOT NULL,
  `id` varchar(32) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `state` int(1) NOT NULL DEFAULT 0,
  `finished` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `empathies`
--

CREATE TABLE `empathies` (
  `id` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Post/reply/whateverID',
  `pid` int(12) NOT NULL COMMENT 'User''s PID',
  `empathy_id` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_from` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `feeling_id` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `settings_id` bigint(20) NOT NULL,
  `pid` int(12) NOT NULL,
  `community_id` int(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `friend_relationships`
--

CREATE TABLE `friend_relationships` (
  `relationship_id` bigint(20) NOT NULL,
  `source` int(12) NOT NULL,
  `target` int(12) NOT NULL,
  `is_me2me` int(2) NOT NULL DEFAULT 0,
  `updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `friend_requests`
--

CREATE TABLE `friend_requests` (
  `sender` int(12) NOT NULL,
  `recipient` int(12) NOT NULL,
  `news_id` bigint(20) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `has_read` int(8) NOT NULL DEFAULT 0,
  `finished` int(8) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `conversation_id` bigint(20) NOT NULL,
  `id` varchar(25) NOT NULL COMMENT 'urlsafe base64',
  `pid` int(12) NOT NULL,
  `screenshot` varchar(255) DEFAULT NULL,
  `feeling_id` int(1) NOT NULL DEFAULT 0,
  `platform_id` int(1) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_from` varchar(50) DEFAULT NULL,
  `is_spoiler` int(1) DEFAULT NULL,
  `has_read` int(8) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `from_pid` int(12) NOT NULL,
  `to_pid` int(12) NOT NULL,
  `news_id` bigint(20) NOT NULL,
  `id` varchar(50) DEFAULT NULL COMMENT 'Full URI',
  `news_context` int(8) NOT NULL,
  `merged` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `has_read` int(8) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `people`
--

CREATE TABLE `people` (
  `pid` int(12) NOT NULL,
  `user_id` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `screen_name` varchar(30) NOT NULL,
  `mii` varchar(130) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `mii_image` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `mii_hash` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `nnas_info` text DEFAULT NULL,
  `face` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `official_user` int(8) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `platform_id` int(8) NOT NULL DEFAULT 1,
  `created_from` varchar(50) DEFAULT NULL,
  `client_info` varchar(500) DEFAULT NULL,
  `device_id` decimal(12,0) DEFAULT NULL,
  `device_cert` varchar(384) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `privilege` int(8) NOT NULL DEFAULT 0 COMMENT '0 = normal, 1 = special, 2 = mod, 3 = admin, 4 = superadmin, 5 = dev (god)',
  `image_perm` int(8) DEFAULT 0,
  `status` int(8) NOT NULL DEFAULT 0 COMMENT '0 = ok, 1 = cannot comment, 2 = tempban, 3 = permaban, 4 = device ban, 5 = deleted',
  `empathy_restriction` int(8) DEFAULT NULL,
  `ban_status` int(8) DEFAULT 0,
  `comment` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `la` timestamp NOT NULL DEFAULT current_timestamp(),
  `las` int(1) NOT NULL DEFAULT 0,
  `lai` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `tid` int(11) NOT NULL,
  `id` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'urlsafe base64',
  `pid` int(12) NOT NULL,
  `_post_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'body',
  `screenshot` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `feeling_id` int(1) NOT NULL DEFAULT 0,
  `platform_id` int(1) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `url` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_from` varchar(50) DEFAULT NULL,
  `community_id` int(20) NOT NULL,
  `is_spoiler` varchar(1) NOT NULL DEFAULT '0',
  `is_autopost` int(1) NOT NULL DEFAULT 0,
  `is_special` int(1) DEFAULT 0,
  `topic_tag` varchar(128) DEFAULT NULL,
  `search_key` varchar(128) NOT NULL DEFAULT 'none',
  `search_key2` varchar(128) NOT NULL DEFAULT 'none',
  `search_key3` varchar(128) NOT NULL DEFAULT 'none',
  `search_key4` varchar(128) NOT NULL DEFAULT 'none',
  `search_key5` varchar(128) NOT NULL DEFAULT 'none',
  `is_ingame` int(1) NOT NULL DEFAULT 0,
  `can_show_ingame` int(1) NOT NULL DEFAULT 0,
  `is_hidden` int(1) DEFAULT 0,
  `hidden_resp` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `pid` int(12) NOT NULL,
  `last_updated` datetime NOT NULL DEFAULT current_timestamp(),
  `comment` text DEFAULT NULL,
  `birthday` varchar(255) DEFAULT NULL,
  `platform_id` int(8) DEFAULT 1,
  `country` varchar(255) DEFAULT NULL,
  `gender` varchar(2) DEFAULT NULL,
  `game_experience` varchar(8) DEFAULT '0',
  `favorite_screenshot` varchar(25) DEFAULT NULL,
  `empathy_optout` int(1) DEFAULT 0,
  `relationship_visibility` int(1) DEFAULT 1,
  `allow_request` int(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `relationships`
--

CREATE TABLE `relationships` (
  `relationship_id` bigint(20) NOT NULL,
  `source` int(12) NOT NULL,
  `target` int(12) NOT NULL,
  `is_me2me` int(2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `id` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `pid` int(12) NOT NULL,
  `reply_to_id` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `screenshot` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `feeling_id` int(1) NOT NULL DEFAULT 0,
  `platform_id` int(1) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `created_from` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `community_id` int(20) DEFAULT NULL,
  `is_spoiler` varchar(1) NOT NULL DEFAULT '0',
  `is_special` int(1) DEFAULT 0,
  `is_hidden` int(1) DEFAULT 0,
  `hidden_resp` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` bigint(20) NOT NULL,
  `source` int(12) NOT NULL,
  `subject` varchar(45) DEFAULT NULL,
  `type` int(8) NOT NULL DEFAULT 0,
  `reason` int(8) NOT NULL DEFAULT 0,
  `message` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `finished` int(8) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `restrictions`
--

CREATE TABLE `restrictions` (
  `operation_id` bigint(20) NOT NULL,
  `operator` int(12) NOT NULL,
  `id` varchar(25) DEFAULT NULL,
  `type` int(1) NOT NULL DEFAULT 0 COMMENT '0 for post, 1 for comment',
  `recipients` text DEFAULT NULL,
  `operation` int(1) NOT NULL DEFAULT 0 COMMENT '0 for empathy, 1 for reply'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `settings_title`
--

CREATE TABLE `settings_title` (
  `settings_id` bigint(20) NOT NULL,
  `pid` int(12) NOT NULL,
  `olive_title_id` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `value` int(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `settings_tutorial`
--

CREATE TABLE `settings_tutorial` (
  `pid` int(12) NOT NULL,
  `tutorial_id` bigint(20) NOT NULL,
  `updated` datetime DEFAULT current_timestamp(),
  `my_news` int(2) DEFAULT 0,
  `friend_messages` int(2) DEFAULT 0,
  `profile_setup` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `titles`
--

CREATE TABLE `titles` (
  `olive_title_id` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `olive_title_id_usa` varchar(20) NOT NULL,
  `olive_title_id_eur` varchar(20) NOT NULL,
  `olive_title_id_jpn` varchar(20) NOT NULL,
  `olive_community_id` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `unique_id` decimal(20,0) DEFAULT NULL,
  `icon` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `banner` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `banner_3ds` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `name` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `platform_id` varchar(8) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `platform_type` varchar(8) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `require_app_data` int(1) NOT NULL,
  `can_in_game_post` int(1) NOT NULL,
  `can_screenshot` int(1) NOT NULL DEFAULT 0,
  `is_recommended` int(1) NOT NULL DEFAULT 0,
  `hidden` int(8) DEFAULT NULL,
  `comment` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_data`
--
ALTER TABLE `app_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bans`
--
ALTER TABLE `bans`
  ADD PRIMARY KEY (`operation_id`),
  ADD KEY `bibfk1` (`operator`);

--
-- Indexes for table `blacklist`
--
ALTER TABLE `blacklist`
  ADD PRIMARY KEY (`blacklist_id`),
  ADD KEY `blibfk1` (`source`),
  ADD KEY `blibfk2` (`target`);

--
-- Indexes for table `communities`
--
ALTER TABLE `communities`
  ADD PRIMARY KEY (`community_id`),
  ADD KEY `olive_community2title` (`olive_title_id`);

--
-- Indexes for table `console_auth`
--
ALTER TABLE `console_auth`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`conversation_id`),
  ADD KEY `conibfk1` (`sender`),
  ADD KEY `conibfk2` (`recipient`);

--
-- Indexes for table `email_confirmation`
--
ALTER TABLE `email_confirmation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ecibfk1` (`pid`);

--
-- Indexes for table `empathies`
--
ALTER TABLE `empathies`
  ADD PRIMARY KEY (`empathy_id`),
  ADD KEY `pid_to_empathies` (`pid`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`settings_id`),
  ADD KEY `fibfk1` (`community_id`),
  ADD KEY `fibfk2` (`pid`);

--
-- Indexes for table `friend_relationships`
--
ALTER TABLE `friend_relationships`
  ADD PRIMARY KEY (`relationship_id`),
  ADD KEY `freibfk1` (`target`),
  ADD KEY `freibfk3` (`source`);

--
-- Indexes for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`news_id`),
  ADD KEY `freqibfk1` (`sender`),
  ADD KEY `freqibfk2` (`recipient`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pidmess1` (`pid`),
  ADD KEY `mibfk1` (`conversation_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`),
  ADD KEY `to_pid` (`to_pid`),
  ADD KEY `from_pid` (`from_pid`);

--
-- Indexes for table `people`
--
ALTER TABLE `people`
  ADD PRIMARY KEY (`pid`),
  ADD UNIQUE KEY `user_id_unique` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`tid`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `posts_ibfk_1` (`pid`),
  ADD KEY `posts_ibfk_2` (`community_id`),
  ADD KEY `tid` (`tid`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`pid`);

--
-- Indexes for table `relationships`
--
ALTER TABLE `relationships`
  ADD PRIMARY KEY (`relationship_id`),
  ADD KEY `target` (`target`),
  ADD KEY `source` (`source`) USING BTREE;

--
-- Indexes for table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `replies_ibfk_1` (`pid`),
  ADD KEY `replies_ibfk_2` (`reply_to_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `repibfk1` (`source`);

--
-- Indexes for table `restrictions`
--
ALTER TABLE `restrictions`
  ADD PRIMARY KEY (`operation_id`),
  ADD KEY `resibfk1` (`operator`);

--
-- Indexes for table `settings_title`
--
ALTER TABLE `settings_title`
  ADD PRIMARY KEY (`settings_id`),
  ADD KEY `stibfk1` (`olive_title_id`),
  ADD KEY `stibfk2` (`pid`);

--
-- Indexes for table `settings_tutorial`
--
ALTER TABLE `settings_tutorial`
  ADD PRIMARY KEY (`tutorial_id`),
  ADD KEY `pids` (`pid`);

--
-- Indexes for table `titles`
--
ALTER TABLE `titles`
  ADD PRIMARY KEY (`olive_title_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_data`
--
ALTER TABLE `app_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bans`
--
ALTER TABLE `bans`
  MODIFY `operation_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blacklist`
--
ALTER TABLE `blacklist`
  MODIFY `blacklist_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `communities`
--
ALTER TABLE `communities`
  MODIFY `community_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `console_auth`
--
ALTER TABLE `console_auth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `conversation_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `empathies`
--
ALTER TABLE `empathies`
  MODIFY `empathy_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `settings_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friend_relationships`
--
ALTER TABLE `friend_relationships`
  MODIFY `relationship_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `news_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `news_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `tid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `relationships`
--
ALTER TABLE `relationships`
  MODIFY `relationship_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restrictions`
--
ALTER TABLE `restrictions`
  MODIFY `operation_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings_title`
--
ALTER TABLE `settings_title`
  MODIFY `settings_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings_tutorial`
--
ALTER TABLE `settings_tutorial`
  MODIFY `tutorial_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bans`
--
ALTER TABLE `bans`
  ADD CONSTRAINT `bibfk1` FOREIGN KEY (`operator`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `blacklist`
--
ALTER TABLE `blacklist`
  ADD CONSTRAINT `blibfk1` FOREIGN KEY (`source`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `blibfk2` FOREIGN KEY (`target`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `communities`
--
ALTER TABLE `communities`
  ADD CONSTRAINT `cibfk1` FOREIGN KEY (`olive_title_id`) REFERENCES `titles` (`olive_title_id`);

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conibfk1` FOREIGN KEY (`sender`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `conibfk2` FOREIGN KEY (`recipient`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `email_confirmation`
--
ALTER TABLE `email_confirmation`
  ADD CONSTRAINT `ecibfk1` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `empathies`
--
ALTER TABLE `empathies`
  ADD CONSTRAINT `eibfk1` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fibfk1` FOREIGN KEY (`community_id`) REFERENCES `communities` (`community_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fibfk2` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `friend_relationships`
--
ALTER TABLE `friend_relationships`
  ADD CONSTRAINT `freibfk1` FOREIGN KEY (`target`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `freibfk3` FOREIGN KEY (`source`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD CONSTRAINT `freqibfk1` FOREIGN KEY (`sender`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `freqibfk2` FOREIGN KEY (`recipient`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `mibfk1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mibfk2` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `nibfk1` FOREIGN KEY (`from_pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `nibfk2` FOREIGN KEY (`to_pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `pibfk1` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pibfk2` FOREIGN KEY (`community_id`) REFERENCES `communities` (`community_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `pribfk1` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `relationships`
--
ALTER TABLE `relationships`
  ADD CONSTRAINT `ribfk1` FOREIGN KEY (`source`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ribfk2` FOREIGN KEY (`target`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `reibfk1` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reibfk2` FOREIGN KEY (`reply_to_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `repibfk1` FOREIGN KEY (`source`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restrictions`
--
ALTER TABLE `restrictions`
  ADD CONSTRAINT `resibfk1` FOREIGN KEY (`operator`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `settings_title`
--
ALTER TABLE `settings_title`
  ADD CONSTRAINT `stibfk1` FOREIGN KEY (`olive_title_id`) REFERENCES `titles` (`olive_title_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stibfk2` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`);

--
-- Constraints for table `settings_tutorial`
--
ALTER TABLE `settings_tutorial`
  ADD CONSTRAINT `stuibfk1` FOREIGN KEY (`pid`) REFERENCES `people` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
