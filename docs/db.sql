-- phpMyAdmin SQL Dump
-- version 3.5.6
-- http://www.phpmyadmin.net
--
-- Host: 192.168.210.12
-- Generation Time: Jan 24, 2014 at 01:53 PM
-- Server version: 5.1.71
-- PHP Version: 5.4.6-1ubuntu1.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `gitapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `accesses`
--

CREATE TABLE IF NOT EXISTS `accesses` (
  `user_id` int(11) NOT NULL,
  `repository_id` int(11) NOT NULL,
  `readAccess` tinyint(1) NOT NULL DEFAULT '0',
  `writeAccess` tinyint(1) NOT NULL DEFAULT '0',
  `specialAccess` tinyint(1) NOT NULL DEFAULT '0',
  `adminAccess` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`repository_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acls_permissions`
--

CREATE TABLE IF NOT EXISTS `acls_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `resource` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `permission` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role` (`role`),
  KEY `resource` (`resource`),
  KEY `role_resource` (`role`,`resource`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `acls_permissions`
--

INSERT INTO `acls_permissions` (`id`, `role`, `resource`, `type`, `permission`) VALUES
(2, 'guest', NULL, 'deny', 'view'),
(3, 'repo_create', 'repository', 'allow', 'create'),
(4, 'root', NULL, 'allow', NULL),
(5, 'staff', 'users', 'allow', NULL),
(6, 'staff', 'user', 'allow', 'edit'),
(7, 'staff', 'repository', 'allow', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acls_resources`
--

CREATE TABLE IF NOT EXISTS `acls_resources` (
  `resource` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`resource`),
  KEY `parent` (`parent`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `acls_resources`
--

INSERT INTO `acls_resources` (`resource`, `description`, `parent`, `sort`) VALUES
('repository', 'Repository resource', NULL, 0),
('user', 'User resource', NULL, 0),
('users', 'Users resource', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `acls_roles`
--

CREATE TABLE IF NOT EXISTS `acls_roles` (
  `role` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort` int(2) NOT NULL DEFAULT '0',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role`),
  KEY `parent` (`parent`),
  KEY `sort` (`sort`),
  KEY `default` (`default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `acls_roles`
--

INSERT INTO `acls_roles` (`role`, `description`, `parent`, `sort`, `default`) VALUES
('guest', 'Guest role', NULL, 0, 0),
('repo_create', 'Create and Delete repositories', NULL, 0, 0),
('root', 'Administrator', NULL, 3, 0),
('staff', 'Staff role', 'user', 2, 0),
('user', 'Logged in user role', 'guest', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE IF NOT EXISTS `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `repositoryId` int(11) DEFAULT NULL,
  `repositoryName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `targetId` int(11) DEFAULT NULL,
  `targetName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `targetUrl` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `createdOn` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `repositoryId` (`repositoryId`),
  KEY `targetId` (`targetId`),
  KEY `createdOn` (`createdOn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `commits`
--

CREATE TABLE IF NOT EXISTS `commits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `repositoryId` int(11) NOT NULL,
  `pushId` int(11) NOT NULL,
  `authorName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authorEmail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `authorDate` datetime NOT NULL,
  `authorId` int(11) DEFAULT NULL,
  `committerName` int(11) DEFAULT NULL,
  `committerEmail` int(11) NOT NULL,
  `committerDate` datetime NOT NULL,
  `committerId` int(11) DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `indexDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`,`repositoryId`),
  KEY `pushId` (`pushId`),
  KEY `authorId` (`authorId`),
  KEY `committerId` (`committerId`),
  KEY `repositoryId` (`repositoryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `commits_refs`
--

CREATE TABLE IF NOT EXISTS `commits_refs` (
  `commitId` int(11) NOT NULL,
  `refId` int(11) NOT NULL,
  PRIMARY KEY (`commitId`,`refId`),
  KEY `refId` (`refId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `pushes`
--

CREATE TABLE IF NOT EXISTS `pushes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `repositoryId` int(11) NOT NULL,
  `createdOn` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `repositoryId` (`repositoryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refs`
--

CREATE TABLE IF NOT EXISTS `refs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `fullname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `repositoryId` int(11) NOT NULL,
  `pushId` int(11) NOT NULL,
  `createdOn` datetime NOT NULL,
  `commitHash` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `repositoryId` (`repositoryId`),
  KEY `pushId` (`pushId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repositories`
--

CREATE TABLE IF NOT EXISTS `repositories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'repository',
  `public` tinyint(1) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `fullname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `default_branch` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'master',
  `created_at` datetime NOT NULL,
  `last_commit_date` datetime DEFAULT NULL,
  `last_commit_hash` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_commit_author` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_commit_msg` text COLLATE utf8_unicode_ci,
  `watchers` int(11) NOT NULL DEFAULT '0',
  `forks` int(11) NOT NULL DEFAULT '0',
  `languages` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `owner_id_2` (`owner_id`,`name`),
  KEY `parent_id` (`parent_id`),
  KEY `owner_id` (`owner_id`),
  KEY `last_commit` (`last_commit_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `repositories`
--

INSERT INTO `repositories` (`id`, `owner_id`, `type`, `public`, `parent_id`, `name`, `fullname`, `description`, `website`, `path`, `default_branch`, `created_at`, `last_commit_date`, `last_commit_hash`, `last_commit_author`, `last_commit_msg`, `watchers`, `forks`, `languages`) VALUES
(1, NULL, 'repository', 0, NULL, 'gitolite-admin', 'gitolite-admin', 'Gitolite Admin Repository', NULL, 'gitolite-admin.git', 'master', NOW(), NULL, NULL, NULL, NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `password` blob NOT NULL,
  `http_password` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_registration` datetime NOT NULL,
  `date_activation` datetime DEFAULT NULL,
  `hash` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `fullname` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `email` (`email`),
  KEY `active` (`active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `slug`, `password`, `http_password`, `email`, `date_registration`, `date_activation`, `hash`, `active`, `fullname`) VALUES
(1, 'user', 'admin', 'admin', 0xf2b73e13710499d51afb6946adc41c1450c25149ae9500a678c468df532f6126c7eb3daaab141381fadad05e95128f699b30779a179728421726141a9f671aa265fee13ff488f0b2eda9d4897401e5d6725990f018523909e86393f4eeeacceb6e058af6b8cda6e7b7905dc893976ac2b83296ed0a1538b1d28d1f3b4b02be1df7abe7f40e39f2641631c37e2d5a52ae70b5c1b730721632f2b11db4ac6cb24972e72d65f2f9b0c4a0f0efe44c275616a7d46e18ef1cc312601465d0267701963b20ffae0f69c75ebfd28a652de5a387c40cbaed338ac6bbba098948240715a5ef46643294e188ced7a655773d232f6057c1bf4d9d3eab4775187baa11fccf84, '$apr1$.od95WXr$LQsYopoRTjum8Cdbei3P3.', 'admin@nitronet.org', '2014-01-24 13:18:15', NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_roles`
--

CREATE TABLE IF NOT EXISTS `users_roles` (
  `user_id` int(11) NOT NULL,
  `role` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`,`role`),
  KEY `role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users_roles`
--

INSERT INTO `users_roles` (`user_id`, `role`) VALUES
(1, 'repo_create'),
(1, 'root'),
(1, 'staff'),
(1, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `users_ssh_keys`
--

CREATE TABLE IF NOT EXISTS `users_ssh_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `contents` text COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_2` (`user_id`,`title`),
  UNIQUE KEY `user_id_3` (`user_id`,`hash`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `acls_permissions`
--
ALTER TABLE `acls_permissions`
  ADD CONSTRAINT `acls_permissions_ibfk_1` FOREIGN KEY (`role`) REFERENCES `acls_roles` (`role`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `acls_permissions_ibfk_2` FOREIGN KEY (`resource`) REFERENCES `acls_resources` (`resource`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `acls_resources`
--
ALTER TABLE `acls_resources`
  ADD CONSTRAINT `acls_resources_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `acls_resources` (`resource`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `acls_roles`
--
ALTER TABLE `acls_roles`
  ADD CONSTRAINT `acls_roles_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `acls_roles` (`role`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `activities_ibfk_2` FOREIGN KEY (`repositoryId`) REFERENCES `repositories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `activities_ibfk_3` FOREIGN KEY (`targetId`) REFERENCES `repositories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `commits`
--
ALTER TABLE `commits`
  ADD CONSTRAINT `commits_ibfk_1` FOREIGN KEY (`pushId`) REFERENCES `pushes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commits_ibfk_2` FOREIGN KEY (`authorId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `commits_ibfk_3` FOREIGN KEY (`committerId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `commits_ibfk_4` FOREIGN KEY (`repositoryId`) REFERENCES `repositories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `commits_refs`
--
ALTER TABLE `commits_refs`
  ADD CONSTRAINT `commits_refs_ibfk_1` FOREIGN KEY (`commitId`) REFERENCES `commits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commits_refs_ibfk_2` FOREIGN KEY (`refId`) REFERENCES `refs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pushes`
--
ALTER TABLE `pushes`
  ADD CONSTRAINT `pushes_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pushes_ibfk_2` FOREIGN KEY (`repositoryId`) REFERENCES `repositories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `refs`
--
ALTER TABLE `refs`
  ADD CONSTRAINT `refs_ibfk_1` FOREIGN KEY (`repositoryId`) REFERENCES `repositories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `refs_ibfk_2` FOREIGN KEY (`pushId`) REFERENCES `pushes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `repositories`
--
ALTER TABLE `repositories`
  ADD CONSTRAINT `repositories_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `repositories_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `repositories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `users_roles`
--
ALTER TABLE `users_roles`
  ADD CONSTRAINT `users_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_roles_ibfk_2` FOREIGN KEY (`role`) REFERENCES `acls_roles` (`role`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_ssh_keys`
--
ALTER TABLE `users_ssh_keys`
  ADD CONSTRAINT `users_ssh_keys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
