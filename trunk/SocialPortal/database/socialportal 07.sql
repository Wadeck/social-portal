-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 06, 2011 at 07:41 AM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `social_portal`
--
CREATE DATABASE `social_portal` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `social_portal`;

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE IF NOT EXISTS `activity` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `component` varchar(75) NOT NULL DEFAULT 'bidule',
  `type` varchar(75) DEFAULT NULL,
  `action` longtext NOT NULL,
  `content` longtext NOT NULL,
  `primary_link` varchar(150) NOT NULL DEFAULT 'NULL',
  `item_id` varchar(75) NOT NULL,
  `secondary_item_id` varchar(75) DEFAULT NULL,
  `date_recorded` datetime NOT NULL,
  `hide_sitewide` tinyint(1) DEFAULT NULL,
  `mptt_left` int(11) NOT NULL,
  `mptt_right` int(11) NOT NULL DEFAULT '4',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `activity`
--


-- --------------------------------------------------------

--
-- Table structure for table `activity_meta`
--

CREATE TABLE IF NOT EXISTS `activity_meta` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `activity_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `activity_meta`
--


-- --------------------------------------------------------

--
-- Table structure for table `forum`
--

CREATE TABLE IF NOT EXISTS `forum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` longtext NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `num_topics` int(11) NOT NULL,
  `num_posts` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `forum`
--


-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `initiator_user_id` bigint(20) NOT NULL,
  `friend_user_id` bigint(20) NOT NULL,
  `is_confirmed` tinyint(1) DEFAULT NULL,
  `is_limited` tinyint(1) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `friends`
--


-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `creator_id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` longtext NOT NULL,
  `status` varchar(10) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `groups`
--


-- --------------------------------------------------------

--
-- Table structure for table `groups_members`
--

CREATE TABLE IF NOT EXISTS `groups_members` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `inviter_id` bigint(20) NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `is_mod` tinyint(1) NOT NULL,
  `user_title` varchar(100) NOT NULL,
  `date_modified` datetime NOT NULL,
  `comments` longtext NOT NULL,
  `is_confirmed` tinyint(1) NOT NULL,
  `is_banned` tinyint(1) NOT NULL,
  `invite_sent` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `groups_members`
--


-- --------------------------------------------------------

--
-- Table structure for table `groups_meta`
--

CREATE TABLE IF NOT EXISTS `groups_meta` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `groups_meta`
--


-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `thread_id` bigint(20) NOT NULL,
  `sender_id` bigint(20) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` longtext NOT NULL,
  `date_sent` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `messages`
--


-- --------------------------------------------------------

--
-- Table structure for table `messages_notices`
--

CREATE TABLE IF NOT EXISTS `messages_notices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `subject` varchar(200) NOT NULL,
  `message` longtext NOT NULL,
  `date_sent` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `messages_notices`
--


-- --------------------------------------------------------

--
-- Table structure for table `messages_recipients`
--

CREATE TABLE IF NOT EXISTS `messages_recipients` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `thread_id` bigint(20) NOT NULL,
  `unread_count` int(11) NOT NULL,
  `sender_only` tinyint(1) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `messages_recipients`
--


-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

CREATE TABLE IF NOT EXISTS `meta` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `object_type` varchar(16) NOT NULL,
  `object_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `meta`
--


-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `secondary_item_id` bigint(20) DEFAULT NULL,
  `component_name` varchar(75) NOT NULL,
  `component_action` varchar(75) NOT NULL,
  `date_notified` datetime NOT NULL,
  `is_new` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `notifications`
--


-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `value` longtext NOT NULL,
  `autoload` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `options`
--


-- --------------------------------------------------------

--
-- Table structure for table `post__base`
--

CREATE TABLE IF NOT EXISTS `post__base` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `topic_id` bigint(20) DEFAULT NULL,
  `poster_id` bigint(20) DEFAULT NULL,
  `custom_type` smallint(6) NOT NULL,
  `time` datetime NOT NULL,
  `poster_ip` varchar(15) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  `position` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A8A02951F55203D` (`topic_id`),
  KEY `IDX_A8A02955BB66C05` (`poster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `post__base`
--


-- --------------------------------------------------------

--
-- Table structure for table `post_activity`
--

CREATE TABLE IF NOT EXISTS `post_activity` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `content` longtext NOT NULL,
  `postBase_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_139980FB6FCBE53B` (`postBase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `post_activity`
--


-- --------------------------------------------------------

--
-- Table structure for table `post_freetext`
--

CREATE TABLE IF NOT EXISTS `post_freetext` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `content` longtext NOT NULL,
  `postBase_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A49820F96FCBE53B` (`postBase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `post_freetext`
--


-- --------------------------------------------------------

--
-- Table structure for table `post_story`
--

CREATE TABLE IF NOT EXISTS `post_story` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `content` longtext NOT NULL,
  `automaticThoughts` longtext NOT NULL,
  `alternativeThoughts` longtext NOT NULL,
  `realisticThoughts` longtext NOT NULL,
  `postBase_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7C4D88D76FCBE53B` (`postBase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `post_story`
--


-- --------------------------------------------------------

--
-- Table structure for table `post_strategy`
--

CREATE TABLE IF NOT EXISTS `post_strategy` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `content` longtext NOT NULL,
  `postBase_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_ABABCC4C6FCBE53B` (`postBase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `post_strategy`
--


-- --------------------------------------------------------

--
-- Table structure for table `profile_data`
--

CREATE TABLE IF NOT EXISTS `profile_data` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `value` longtext NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `profile_data`
--


-- --------------------------------------------------------

--
-- Table structure for table `profile_fields`
--

CREATE TABLE IF NOT EXISTS `profile_fields` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) NOT NULL,
  `parent_id` bigint(20) NOT NULL,
  `type` varchar(150) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` longtext NOT NULL,
  `is_required` tinyint(1) NOT NULL,
  `is_default_option` tinyint(1) NOT NULL,
  `field_order` bigint(20) NOT NULL,
  `option_order` bigint(20) NOT NULL,
  `order_by` varchar(15) NOT NULL,
  `can_delete` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `profile_fields`
--


-- --------------------------------------------------------

--
-- Table structure for table `profile_groups`
--

CREATE TABLE IF NOT EXISTS `profile_groups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` longtext NOT NULL,
  `can_delete` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `profile_groups`
--


-- --------------------------------------------------------

--
-- Table structure for table `term`
--

CREATE TABLE IF NOT EXISTS `term` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `term`
--


-- --------------------------------------------------------

--
-- Table structure for table `term_relation`
--

CREATE TABLE IF NOT EXISTS `term_relation` (
  `object_id` bigint(20) NOT NULL,
  `term_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `term_order` int(11) NOT NULL,
  PRIMARY KEY (`object_id`,`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `term_relation`
--


-- --------------------------------------------------------

--
-- Table structure for table `topic__base`
--

CREATE TABLE IF NOT EXISTS `topic__base` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) DEFAULT NULL,
  `lastposter_id` bigint(20) DEFAULT NULL,
  `poster_id` bigint(20) DEFAULT NULL,
  `custom_type` smallint(6) NOT NULL,
  `custom_id` bigint(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `start_time` datetime NOT NULL,
  `time` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  `is_open` tinyint(1) NOT NULL,
  `is_sticky` tinyint(1) NOT NULL,
  `num_posts` bigint(20) NOT NULL,
  `tag_count` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_26A2221829CCBAD0` (`forum_id`),
  KEY `IDX_26A22218D176CFB` (`lastposter_id`),
  KEY `IDX_26A222185BB66C05` (`poster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `topic__base`
--


-- --------------------------------------------------------

--
-- Table structure for table `topic_activity`
--

CREATE TABLE IF NOT EXISTS `topic_activity` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `content` longtext NOT NULL,
  `topicBase_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5A45845CFCEE3286` (`topicBase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `topic_activity`
--


-- --------------------------------------------------------

--
-- Table structure for table `topic_freetext`
--

CREATE TABLE IF NOT EXISTS `topic_freetext` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `content` longtext NOT NULL,
  `topicBase_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_ED44245EFCEE3286` (`topicBase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `topic_freetext`
--


-- --------------------------------------------------------

--
-- Table structure for table `topic_story`
--

CREATE TABLE IF NOT EXISTS `topic_story` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `story_content` longtext NOT NULL,
  `automatic_thoughts` longtext NOT NULL,
  `alternative_thoughts` longtext NOT NULL,
  `realistic_thoughts` longtext NOT NULL,
  `ps` longtext NOT NULL,
  `topicBase_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5065A85AFCEE3286` (`topicBase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `topic_story`
--


-- --------------------------------------------------------

--
-- Table structure for table `topic_strategy`
--

CREATE TABLE IF NOT EXISTS `topic_strategy` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `description` longtext NOT NULL,
  `topicBase_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E277C8EBFCEE3286` (`topicBase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `topic_strategy`
--


-- --------------------------------------------------------

--
-- Table structure for table `topic_strategy_item`
--

CREATE TABLE IF NOT EXISTS `topic_strategy_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `topic_id` bigint(20) DEFAULT NULL,
  `content` varchar(100) NOT NULL,
  `num_vote` int(11) NOT NULL,
  `creation_time` datetime NOT NULL,
  `last_vote_time` datetime NOT NULL,
  `author` bigint(20) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_93358A991F55203D` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `topic_strategy_item`
--


-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `login` varchar(60) NOT NULL,
  `password` varchar(64) NOT NULL,
  `email` varchar(100) NOT NULL,
  `registered` datetime NOT NULL,
  `activation_key` varchar(60) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `user`
--


-- --------------------------------------------------------

--
-- Table structure for table `user_meta`
--

CREATE TABLE IF NOT EXISTS `user_meta` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `user_meta`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `post__base`
--
ALTER TABLE `post__base`
  ADD CONSTRAINT `post__base_ibfk_2` FOREIGN KEY (`poster_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `post__base_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topic__base` (`id`);

--
-- Constraints for table `post_activity`
--
ALTER TABLE `post_activity`
  ADD CONSTRAINT `post_activity_ibfk_1` FOREIGN KEY (`postBase_id`) REFERENCES `post__base` (`id`);

--
-- Constraints for table `post_freetext`
--
ALTER TABLE `post_freetext`
  ADD CONSTRAINT `post_freetext_ibfk_1` FOREIGN KEY (`postBase_id`) REFERENCES `post__base` (`id`);

--
-- Constraints for table `post_story`
--
ALTER TABLE `post_story`
  ADD CONSTRAINT `post_story_ibfk_1` FOREIGN KEY (`postBase_id`) REFERENCES `post__base` (`id`);

--
-- Constraints for table `post_strategy`
--
ALTER TABLE `post_strategy`
  ADD CONSTRAINT `post_strategy_ibfk_1` FOREIGN KEY (`postBase_id`) REFERENCES `post__base` (`id`);

--
-- Constraints for table `topic__base`
--
ALTER TABLE `topic__base`
  ADD CONSTRAINT `topic__base_ibfk_3` FOREIGN KEY (`poster_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `topic__base_ibfk_1` FOREIGN KEY (`forum_id`) REFERENCES `forum` (`id`),
  ADD CONSTRAINT `topic__base_ibfk_2` FOREIGN KEY (`lastposter_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `topic_activity`
--
ALTER TABLE `topic_activity`
  ADD CONSTRAINT `topic_activity_ibfk_1` FOREIGN KEY (`topicBase_id`) REFERENCES `topic__base` (`id`);

--
-- Constraints for table `topic_freetext`
--
ALTER TABLE `topic_freetext`
  ADD CONSTRAINT `topic_freetext_ibfk_1` FOREIGN KEY (`topicBase_id`) REFERENCES `topic__base` (`id`);

--
-- Constraints for table `topic_story`
--
ALTER TABLE `topic_story`
  ADD CONSTRAINT `topic_story_ibfk_1` FOREIGN KEY (`topicBase_id`) REFERENCES `topic__base` (`id`);

--
-- Constraints for table `topic_strategy`
--
ALTER TABLE `topic_strategy`
  ADD CONSTRAINT `topic_strategy_ibfk_1` FOREIGN KEY (`topicBase_id`) REFERENCES `topic__base` (`id`);

--
-- Constraints for table `topic_strategy_item`
--
ALTER TABLE `topic_strategy_item`
  ADD CONSTRAINT `topic_strategy_item_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topic_strategy` (`id`);
