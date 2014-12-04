-- phpMyAdmin SQL Dump
-- version 4.1.13
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 04, 2014 at 01:23 AM
-- Server version: 5.5.40-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.5

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `forgery2`
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
  PRIMARY KEY (`user_id`,`repository_id`),
  KEY `repos_fk_id` (`repository_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `accesses`
--

INSERT INTO `accesses` (`user_id`, `repository_id`, `readAccess`, `writeAccess`, `specialAccess`, `adminAccess`) VALUES
(45, 3, 1, 1, 1, 1),
(45, 23, 1, 1, 1, 1),
(45, 25, 1, 1, 1, 1),
(47, 23, 1, 1, 1, 1),
(47, 47, 1, 1, 1, 1),
(47, 49, 1, 1, 1, 1),
(47, 53, 1, 1, 1, 1),
(48, 46, 1, 1, 1, 1),
(48, 54, 1, 1, 1, 1),
(54, 60, 1, 1, 1, 1),
(55, 61, 1, 1, 1, 1),
(55, 64, 1, 1, 1, 1),
(55, 66, 1, 1, 1, 1),
(56, 67, 1, 1, 1, 1),
(56, 68, 1, 1, 1, 1),
(56, 69, 1, 1, 1, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `acls_permissions`
--

INSERT INTO `acls_permissions` (`id`, `role`, `resource`, `type`, `permission`) VALUES
(2, 'guest', NULL, 'deny', 'view'),
(3, 'repo_create', 'repository', 'allow', 'create'),
(4, 'root', NULL, 'allow', NULL),
(5, 'staff', 'users', 'allow', NULL),
(6, 'staff', 'user', 'allow', 'edit'),
(7, 'staff', 'repository', 'allow', NULL),
(8, 'staff', 'organizations', 'allow', NULL);

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
('organization', 'Organization', NULL, 0),
('organizations', 'Organizations', NULL, 0),
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=50 ;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `userId`, `repositoryId`, `repositoryName`, `targetId`, `targetName`, `targetUrl`, `type`, `message`, `createdOn`) VALUES
(9, 45, 3, 'neiluJ2/Core', NULL, NULL, NULL, 'create', NULL, '2013-04-10 23:54:56'),
(10, 45, 23, 'neiluJ2/forgery', NULL, NULL, NULL, 'create', NULL, '2013-04-26 02:58:23'),
(11, 47, NULL, 'test/pour-le-fun', NULL, NULL, NULL, 'create', NULL, '2013-11-18 23:09:29'),
(12, 48, 46, 'boulet/forgery', NULL, NULL, NULL, 'create', NULL, '2013-11-19 17:05:45'),
(13, 47, 47, 'test/simon', NULL, NULL, NULL, 'create', NULL, '2013-11-20 00:00:16'),
(14, 47, NULL, 'test/forgery', NULL, NULL, NULL, 'create', NULL, '2013-11-21 09:08:40'),
(15, 47, 49, 'test/test-activity', NULL, NULL, NULL, 'create', NULL, '2013-11-25 16:24:19'),
(16, 47, NULL, 'test/forgery', 46, 'boulet/forgery', '/Repsoitory.action?name=boulet/forgery', 'fork', NULL, '2013-12-10 15:43:38'),
(17, 47, 53, 'test/abc-def', NULL, NULL, NULL, 'create', NULL, '2013-12-08 00:18:20'),
(18, 48, 54, 'boulet/test-activity', 49, 'test/test-activity', '/Repository.action?name=test%2Ftest-activity', 'fork', NULL, '2013-12-08 00:20:22'),
(19, 47, 23, 'neiluJ2/forgery', 23, 'neiluJ2/forgery', NULL, 'cmt-commit', '2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d', '2014-06-20 12:07:06'),
(20, 47, 23, 'neiluJ2/forgery', NULL, NULL, NULL, 'cmt-commit', 'af103f633980c32a8bb07b7aa04775d009752c56 This is the comment text...', '2014-06-20 15:11:44'),
(21, 47, 23, 'neiluJ2/forgery', NULL, NULL, NULL, 'cmt-commit', '2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d yet another comment', '2014-06-20 15:25:43'),
(22, 47, 23, 'neiluJ2/forgery', NULL, NULL, NULL, 'cmt-commit', '92c2ed1967c544ca6adea516fcae61e227665fa9 That&#39;s a nice commit', '2014-06-20 17:27:45'),
(23, 48, 23, 'neiluJ2/forgery', NULL, NULL, NULL, 'cmt-commit', '92c2ed1967c544ca6adea516fcae61e227665fa9 ouaip.', '2014-06-21 02:19:03'),
(24, 48, 23, 'neiluJ2/forgery', NULL, NULL, NULL, 'cmt-commit', '2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d truc de oOf.', '2014-06-21 02:21:23'),
(25, 47, NULL, 'zadzzadz/ohyeah', NULL, NULL, NULL, 'create', NULL, '2014-06-23 23:44:52'),
(26, 47, NULL, 'zadzzadz/test-name', NULL, NULL, NULL, 'create', NULL, '2014-06-24 00:01:20'),
(27, 47, NULL, 'zadzzadz/ohyeah', NULL, NULL, NULL, 'delete', NULL, '2014-06-24 00:06:22'),
(28, 47, NULL, 'zadzzadz/lfib', NULL, NULL, NULL, 'create', NULL, '2014-06-24 00:15:00'),
(29, 47, NULL, 'zadzzadz/lfib', NULL, NULL, NULL, 'delete', NULL, '2014-06-24 00:30:33'),
(30, 47, 60, 'zadzzadz/oh-yeah', NULL, NULL, NULL, 'create', NULL, '2014-06-24 23:38:22'),
(31, 47, 61, 'dsinp-web/sinoe', NULL, NULL, NULL, 'create', NULL, '2014-06-24 23:49:42'),
(32, 47, NULL, 'dsinp-web/ecom-backoffice', NULL, NULL, NULL, 'create', NULL, '2014-06-24 23:50:59'),
(33, 47, NULL, 'dsinp-web/ecommerce', NULL, NULL, NULL, 'create', NULL, '2014-06-24 23:51:51'),
(34, 47, 64, 'dsinp-web/enoma', NULL, NULL, NULL, 'create', NULL, '2014-06-24 23:52:27'),
(35, 47, NULL, 'dsinp-web/ecom-backoffice', NULL, NULL, NULL, 'delete', NULL, '2014-06-24 23:55:01'),
(36, 47, NULL, 'dsinp-web/ecommerce-backoffice', NULL, NULL, NULL, 'create', NULL, '2014-06-24 23:55:23'),
(37, 47, 66, 'dsinp-web/sinoe-backoffice', NULL, NULL, NULL, 'create', NULL, '2014-06-24 23:56:29'),
(38, 47, NULL, 'dsinp-web/ecommerce', NULL, NULL, NULL, 'delete', NULL, '2014-06-25 00:07:21'),
(39, 47, NULL, 'dsinp-web/ecommerce-backoffice', NULL, NULL, NULL, 'delete', NULL, '2014-06-25 00:07:46'),
(40, 47, 67, 'ecommerce/ecommerce', NULL, NULL, NULL, 'create', NULL, '2014-06-25 00:09:22'),
(41, 47, 68, 'ecommerce/backoffice', NULL, NULL, NULL, 'create', NULL, '2014-06-25 00:09:58'),
(42, 47, 69, 'ecommerce/batch-stats', NULL, NULL, NULL, 'create', NULL, '2014-06-25 00:12:15'),
(43, 47, NULL, 'test/pour-le-fun', NULL, NULL, NULL, 'delete', NULL, '2014-06-27 00:39:34'),
(44, 47, 25, 'gitolite-admin', NULL, NULL, NULL, 'cmt-commit', '4d630c0a3baeac52b01784269b76beecd6e7f527 a comment?', '2014-06-27 01:10:07'),
(45, 47, 23, 'neiluJ2/forgery', NULL, NULL, NULL, 'cmt-commit', '4a79f970e83219c577720b5ac08fa8ab222a286e tttttoooo', '2014-06-30 23:55:03'),
(46, 47, 23, 'neiluJ2/forgery', NULL, NULL, NULL, 'cmt-commit', '92c2ed1967c544ca6adea516fcae61e227665fa9 that&#39;s?', '2014-07-02 21:37:27'),
(47, 47, NULL, 'test/forgery', NULL, NULL, NULL, 'delete', NULL, '2014-07-08 00:28:54'),
(48, 47, 23, 'neiluJ2/forgery', NULL, NULL, NULL, 'cmt-commit', 'af103f633980c32a8bb07b7aa04775d009752c56 comment please??', '2014-12-04 00:12:41'),
(49, 47, 23, 'neiluJ2/forgery', NULL, NULL, NULL, 'cmt-commit', '4a79f970e83219c577720b5ac08fa8ab222a286e ilbzef\r\n', '2014-12-04 00:47:38');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentId` int(11) DEFAULT NULL,
  `thread` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `createdOn` datetime NOT NULL,
  `contents` text COLLATE utf8_unicode_ci NOT NULL,
  `authorName` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `authorEmail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `authorUrl` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authorId` int(11) DEFAULT NULL,
  `repositoryId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parentId` (`parentId`),
  KEY `thread` (`thread`),
  KEY `authorId` (`authorId`),
  KEY `repositoryId` (`repositoryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=47 ;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `parentId`, `thread`, `active`, `createdOn`, `contents`, `authorName`, `authorEmail`, `authorUrl`, `authorId`, `repositoryId`) VALUES
(1, NULL, 'homepage', 1, '2014-06-17 13:08:52', 'Ceci est un commentaire de test, sans parent.', 'neiluJ', 'julien@nitronet.org', 'http://nitronet.org', NULL, NULL),
(2, NULL, 'homepage', 1, '2014-06-17 14:48:47', 'another comment without parent', 'Joe', 'joe@nitronet.org', NULL, NULL, NULL),
(3, 1, 'homepage', 1, '2014-06-17 14:51:57', 'this is a reply to the first comment', 'Bar', 'bar@nitronet.org', NULL, NULL, NULL),
(4, 1, 'homepage', 1, '2014-06-17 16:00:58', 'another reply', 'joebar', 'joe@nitronet.org', NULL, NULL, NULL),
(5, 4, 'homepage', 1, '2014-06-17 16:01:29', 'The return of the reply', 'neiluJ', 'julien@nitronet.org', 'http://nitronet.org', NULL, NULL),
(17, NULL, 'homepage', 1, '2014-06-19 12:14:40', 'Posted comment \\o/', 'neiluJ', 'julien@nitronet.org', 'http://nitronet.org', NULL, NULL),
(18, NULL, 'homepage', 1, '2014-06-19 12:15:20', 'qscqscqscqsc', 'fzezef', 'julien@neiluj.pro', '', NULL, NULL),
(19, NULL, 'homepage', 1, '2014-06-19 13:34:19', 'fzaeiluafzb', 'dddd', 'efzzefzef@gmail.com', 'http://nitronet.org', NULL, NULL),
(20, 18, 'homepage', 1, '2014-06-19 13:36:08', 'RÃ©ponse ici', 'neiluJ', 'nitronet.org@gmail.com', '', NULL, NULL),
(21, NULL, 'homepage', 1, '2014-06-19 14:03:02', 'fzeefzzef', 'fzezef', 'nitronet.org@gmail.com', '', NULL, NULL),
(22, NULL, 'homepage', 1, '2014-06-19 16:01:09', 'et \r\nsi \r\nje \r\ntentais\r\nle\r\nmultilignes?', 'fzezef', 'nitronet.org@gmail.com', 'http://nitronet.org', NULL, NULL),
(23, NULL, 'commit-gitolite-admin-4d630c0a3baeac52b01784269b76beecd6e7f527', 1, '2014-06-19 17:23:34', 'Un petit commentaire de test', 'neiluJ', 'nitronet.org@gmail.com', '', NULL, NULL),
(24, NULL, 'commit-25-4d630c0a3baeac52b01784269b76beecd6e7f527', 1, '2014-06-19 17:29:18', 'Hello', 'neiluJ', 'nitronet.org@gmail.com', 'http://nitronet.org', NULL, NULL),
(25, NULL, 'commit-25-4d630c0a3baeac52b01784269b76beecd6e7f527', 1, '2014-06-19 17:40:24', 'go! go! go!', 'fzezef', 'efzzefzef@gmail.com', '', NULL, NULL),
(26, NULL, 'commit-23-2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d', 1, '2014-06-20 09:45:12', 'Oh Yeah', 'This is a comment', 'nitronet.org@gmail.com', '', NULL, NULL),
(29, NULL, 'commit-23-2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d', 1, '2014-06-20 10:35:08', 'efzze', 'test', 'test@example.com', NULL, 47, NULL),
(30, NULL, 'commit-23-2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d', 1, '2014-06-20 11:30:03', 'gzge', 'test', 'test@example.com', NULL, 47, 23),
(31, NULL, 'commit-23-2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d', 1, '2014-06-20 11:45:10', 'fqsdqsfqsf', 'test', 'test@example.com', NULL, 47, 23),
(32, NULL, 'commit-25-4d630c0a3baeac52b01784269b76beecd6e7f527', 1, '2014-06-20 11:46:06', 'wana comment?', 'test', 'test@example.com', NULL, 47, 25),
(33, NULL, 'commit-25-4d630c0a3baeac52b01784269b76beecd6e7f527', 1, '2014-06-20 11:55:53', 'COUCOU', 'test', 'test@example.com', NULL, 47, 25),
(34, NULL, 'commit-25-4d630c0a3baeac52b01784269b76beecd6e7f527', 1, '2014-06-20 11:58:33', 'fazazfazf', 'test', 'test@example.com', NULL, 47, 25),
(35, NULL, 'commit-23-2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d', 1, '2014-06-20 12:06:54', 'coucou', 'test', 'test@example.com', NULL, 47, 23),
(36, NULL, 'commit-23-2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d', 1, '2014-06-20 12:07:06', 'coucou', 'test', 'test@example.com', NULL, 47, 23),
(37, NULL, 'commit-23-af103f633980c32a8bb07b7aa04775d009752c56', 1, '2014-06-20 15:11:44', 'zadzda', 'test', 'test@example.com', NULL, 47, 23),
(38, NULL, 'commit-23-2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d', 1, '2014-06-20 15:25:43', 'yet another comment', 'test', 'test@example.com', NULL, 47, 23),
(39, NULL, 'commit-23-92c2ed1967c544ca6adea516fcae61e227665fa9', 1, '2014-06-20 17:27:45', 'That&#39;s a nice commit', 'test', 'test@example.com', NULL, 47, 23),
(40, NULL, 'commit-23-92c2ed1967c544ca6adea516fcae61e227665fa9', 1, '2014-06-21 02:19:03', 'ouaip.', 'boulet', 'boulet@nitronet.org', NULL, 48, 23),
(41, NULL, 'commit-23-2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d', 1, '2014-06-21 02:21:23', 'truc de oOf.', 'boulet', 'boulet@nitronet.org', NULL, 48, 23),
(42, NULL, 'commit-25-4d630c0a3baeac52b01784269b76beecd6e7f527', 1, '2014-06-27 01:10:07', 'a comment?', 'test', 'test@example.com', NULL, 47, 25),
(43, NULL, 'commit-23-4a79f970e83219c577720b5ac08fa8ab222a286e', 1, '2014-06-30 23:55:03', 'tttttoooo', 'test', 'test@example.com', NULL, 47, 23),
(44, NULL, 'commit-23-92c2ed1967c544ca6adea516fcae61e227665fa9', 1, '2014-07-02 21:37:27', 'that&#39;s?', 'test', 'test@example.com', NULL, 47, 23),
(45, NULL, 'commit-23-af103f633980c32a8bb07b7aa04775d009752c56', 1, '2014-12-04 00:12:41', 'comment please??', 'test', 'test@example.com', NULL, 47, 23),
(46, NULL, 'commit-23-4a79f970e83219c577720b5ac08fa8ab222a286e', 1, '2014-12-04 00:47:38', 'ilbzef\r\n', 'test', 'test@example.com', NULL, 47, 23);

-- --------------------------------------------------------

--
-- Table structure for table `comments_threads`
--

CREATE TABLE IF NOT EXISTS `comments_threads` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `open` tinyint(1) NOT NULL DEFAULT '1',
  `comments` int(11) NOT NULL DEFAULT '0',
  `createdOn` datetime NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `comments_threads`
--

INSERT INTO `comments_threads` (`name`, `open`, `comments`, `createdOn`) VALUES
('commit-23-2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d', 1, 8, '2014-06-20 09:45:12'),
('commit-23-4a79f970e83219c577720b5ac08fa8ab222a286e', 1, 2, '2014-06-30 23:55:03'),
('commit-23-92c2ed1967c544ca6adea516fcae61e227665fa9', 1, 3, '2014-06-20 17:27:45'),
('commit-23-af103f633980c32a8bb07b7aa04775d009752c56', 1, 2, '2014-06-20 15:11:44'),
('commit-25-4d630c0a3baeac52b01784269b76beecd6e7f527', 1, 6, '2014-06-19 17:29:18'),
('commit-gitolite-admin-4d630c0a3baeac52b01784269b76beecd6e7f527', 1, 1, '2014-06-19 17:23:34'),
('homepage', 1, 11, '2014-06-17 13:08:09');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1161 ;

--
-- Dumping data for table `commits`
--

INSERT INTO `commits` (`id`, `hash`, `repositoryId`, `pushId`, `authorName`, `authorEmail`, `authorDate`, `authorId`, `committerName`, `committerEmail`, `committerDate`, `committerId`, `message`, `indexDate`) VALUES
(965, '777ffd15548e724a3b5d5323f717a18eb7b8da9c', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-17 15:17:42', 45, 0, 0, '2013-10-17 15:17:42', 45, 'initial import\n', '2013-12-02 11:00:55'),
(966, '2c0e11cd29d7d08ee96bd16e81a555484f66da85', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-20 21:30:58', 45, 0, 0, '2013-10-20 21:30:58', 45, 'new viewHelper use', '2013-12-02 11:00:55'),
(967, '7ea3dcbe276a3f1a7d576c4920c59e7d619a1b16', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-10-21 16:00:23', NULL, 0, 0, '2013-10-21 16:00:23', NULL, '...', '2013-12-02 11:00:55'),
(968, 'c3ef96fd24a56c4303ecc94909de0927ee2c6749', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-10-21 16:51:19', NULL, 0, 0, '2013-10-21 16:51:19', NULL, '...', '2013-12-02 11:00:55'),
(969, '17cdd8f2254da00eebcc26f658ba0ce0bbad06ee', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-10-21 17:32:23', NULL, 0, 0, '2013-10-21 17:32:23', NULL, '...', '2013-12-02 11:00:55'),
(970, 'ae30f912291e83d14ef09f4edd8dfe2960685653', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-10-22 16:01:21', NULL, 0, 0, '2013-10-22 16:01:21', NULL, 'powah!', '2013-12-02 11:00:55'),
(971, '17e98e753ba7757cfd5179b5b6f86149b441b808', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-10-22 16:29:19', NULL, 0, 0, '2013-10-22 16:29:19', NULL, 'powah!', '2013-12-02 11:00:55'),
(972, 'a86c682a4688c915bb58aef484158fdee30736f6', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-22 20:31:43', 45, 0, 0, '2013-10-22 20:31:43', 45, 'better display', '2013-12-02 11:00:55'),
(973, '1c7b9a93279d6226cf31df8b580af1e66fcbc98c', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-22 20:32:01', 45, 0, 0, '2013-10-22 20:32:01', 45, 'better display', '2013-12-02 11:00:55'),
(974, '6b357f964748e14344ad3b67c736e3778d1a15ff', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-22 21:27:54', 45, 0, 0, '2013-10-22 21:27:54', 45, 'various fixes', '2013-12-02 11:00:55'),
(975, 'c8526fcc2b756f80223c9713ee770993cfc64b6d', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-22 21:57:12', 45, 0, 0, '2013-10-22 21:57:12', 45, 'fixes BlobRaw ', '2013-12-02 11:00:55'),
(976, '6853b8b032ba0024ab6865d48a853dd6dc180d90', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-10-24 16:35:13', NULL, 0, 0, '2013-10-24 16:35:13', NULL, 'better angular (still buggy)', '2013-12-02 11:00:55'),
(977, 'bed4f0ead5b7049ca4e369f4082170e6a883c176', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-24 21:58:02', 45, 0, 0, '2013-10-24 21:58:02', 45, 'rebuild navigation', '2013-12-02 11:00:55'),
(978, '68ef92a366c6930f425ac83749169239c19bfd4c', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-24 22:04:46', 45, 0, 0, '2013-10-24 22:04:46', 45, 'rebuild navigation', '2013-12-02 11:00:55'),
(979, 'aa4501b1c4fd0d97d20f648304cc23f14f1d2d6a', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-25 00:09:37', 45, 0, 0, '2013-10-25 00:09:37', 45, 'synchro commits', '2013-12-02 11:00:55'),
(980, '923a5661b00caa9c5e94980160534b2b225b84d7', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-25 00:27:38', 45, 0, 0, '2013-10-25 00:27:38', 45, 'commits ordered', '2013-12-02 11:00:55'),
(981, 'b48fba01210909dca6effcd66399331633cfe585', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-25 01:12:54', 45, 0, 0, '2013-10-25 01:12:54', 45, 'commits browsing', '2013-12-02 11:00:55'),
(982, 'bce9693c8f20d0a25a1df1321b1eee9fcf4e203d', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-10-25 13:19:22', NULL, 0, 0, '2013-10-25 13:19:22', NULL, 'added commits cache (+md5.js)\nadded language detection', '2013-12-02 11:00:55'),
(983, '838aff08d96e755a9047476e54c3a2ee9497cf0a', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-10-25 14:35:41', NULL, 0, 0, '2013-10-25 14:35:41', NULL, 'fixed blob display', '2013-12-02 11:00:55'),
(984, '20b9fd3fda2db35971b87fd41c58aa1e47927cd6', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-10-25 16:14:43', NULL, 0, 0, '2013-10-25 16:14:43', NULL, 'commit view!', '2013-12-02 11:00:55'),
(985, '7f1c3a70a68dbb576b27123144faaa45dbd73514', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-10-25 16:22:29', NULL, 0, 0, '2013-10-25 16:22:29', NULL, 'fixed some commit views', '2013-12-02 11:00:55'),
(986, 'cf46655dcb3ff941ec3d9188d6b6c0919ec63826', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-27 18:33:46', 45, 0, 0, '2013-10-27 18:33:46', 45, 'fixed display ', '2013-12-02 11:00:55'),
(987, '70fd6715041b5383f9ab47254e054ea75ae27211', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-27 19:32:39', 45, 0, 0, '2013-10-27 19:32:39', 45, 'fixed display', '2013-12-02 11:00:55'),
(988, '5f9cb9fae7e54ad74f39dd5d23784ae8ff281fcf', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-27 22:11:14', 45, 0, 0, '2013-10-27 22:11:14', 45, 'fixed blob/api\nadded compare/diff \n', '2013-12-02 11:00:55'),
(989, '4d2849adaa1d82be097c1e437675ee1d5149a454', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-27 23:07:46', 45, 0, 0, '2013-10-27 23:07:46', 45, 'added buggy pushstate', '2013-12-02 11:00:55'),
(990, '3cfb11458fa1daee74d2d5d119e01f6ef455d1f1', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-10-28 17:32:55', NULL, 0, 0, '2013-10-28 17:32:55', NULL, 'better angular (services) \nneedfix commit view (not changing currentCommit)\nneedfix html5 pushState support\nneedfix compare broken\nneedfix compare direct link', '2013-12-02 11:00:55'),
(991, 'c8d89599709e59a2be86620927f6d41de230613f', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-29 00:18:36', 45, 0, 0, '2013-10-29 00:18:36', 45, 'tinyfix', '2013-12-02 11:00:55'),
(992, '5e2df7713bd02f5e321b79f900759684995c1756', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-10-29 09:49:32', NULL, 0, 0, '2013-10-29 09:49:32', NULL, 'fixed compare broken\nfixed compare direct link\nneedfix commit view (not changing currentCommit)\nneedfix html5 pushState support\n', '2013-12-02 11:00:55'),
(993, '6c5875cf277c637b62ce3f36825904da11545dc0', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-10-29 16:43:46', NULL, 0, 0, '2013-10-29 16:43:46', NULL, 'added pushState support (needfix/buggy)\nadded Branches/Tags view (need angularization)\n', '2013-12-02 11:00:55'),
(994, '4d9bb554385d0d5f95dca4904a1a1752a233fb16', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-10-29 17:01:59', NULL, 0, 0, '2013-10-29 17:01:59', NULL, 'added compare button', '2013-12-02 11:00:55'),
(995, '1427fa0af864814a1ba0ff345b4a82123e9f3516', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-29 21:05:34', 45, 0, 0, '2013-10-29 21:05:34', 45, 'fixed ui performances\nfixed commit navigation', '2013-12-02 11:00:55'),
(996, '798e784476cb4609e42941f19444955b1e210cc2', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-29 22:46:31', 45, 0, 0, '2013-10-29 22:46:31', 45, 'fixed html5 pushState ', '2013-12-02 11:00:55'),
(997, '9ed791bc7b47513dd4493e3a478da0f65978d31d', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-10-30 00:10:23', 45, 0, 0, '2013-10-30 00:10:23', 45, 'fixed commit view', '2013-12-02 11:00:55'),
(998, '1ddfba68f1e92292ce4b5019f69c4f75e71fd279', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-11-03 21:53:03', 45, 0, 0, '2013-11-03 21:53:03', 45, 'fixed commit view: display also remotes branches \nadded users support', '2013-12-02 11:00:55'),
(999, 'ecf247c9adcf09e70c5be23c940a908cab21600c', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-11-03 23:04:26', 45, 0, 0, '2013-11-03 23:04:26', 45, 'continued users page', '2013-12-02 11:00:55'),
(1000, 'e71ab0613a809417c86f11f79a245b09db882c11', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-11-04 23:40:19', 45, 0, 0, '2013-11-04 23:40:19', 45, '...', '2013-12-02 11:00:55'),
(1001, '98c72263f48762db3c06b93b4497151a0435188c', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-05 16:41:27', NULL, 0, 0, '2013-11-05 16:41:27', NULL, 'still working on users', '2013-12-02 11:00:55'),
(1002, '19dd6a0e90e9fbd9c88d92ddd3cb91e68deff99c', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-11-07 22:44:04', 45, 0, 0, '2013-11-07 22:44:04', 45, 'added escaping in templates\nsynched repositories with database\nimproved user integration\nfixed default branch is now configurable\nfixed clone url is now configurable ', '2013-12-02 11:00:55'),
(1003, '5e251e12f6a87b90a343f8c71770169b4d05b259', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-11-07 22:47:01', 45, 0, 0, '2013-11-07 22:47:01', 45, 'removed Finder dependency', '2013-12-02 11:00:55'),
(1004, 'af103f633980c32a8bb07b7aa04775d009752c56', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-08 17:21:21', NULL, 0, 0, '2013-11-08 17:21:21', NULL, 'added docs etc..', '2013-12-02 11:00:55'),
(1005, '4a79f970e83219c577720b5ac08fa8ab222a286e', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-12 12:47:18', NULL, 0, 0, '2013-11-12 12:47:18', NULL, 'fixed composer.json', '2013-12-02 11:00:55'),
(1006, '06ea80868a037d3712d48d6a23771b703329a19a', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-12 13:28:07', NULL, 0, 0, '2013-11-12 13:28:07', NULL, 'working on commands', '2013-12-02 11:00:55'),
(1007, 'a33a263606657ea9eae705c2d801a63b463506af', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-12 13:37:37', NULL, 0, 0, '2013-11-12 13:37:37', NULL, 'changed hook', '2013-12-02 11:00:55'),
(1008, 'a1ab5f8decc0c364fe85c77455459acb0146927f', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-11-12 14:53:38', 45, 0, 0, '2013-11-12 14:53:38', 45, 'Forgery administration\n', '2013-12-02 11:00:55'),
(1009, 'f3f60f3a3f0a4c61629eddf3d394c274f6b35b60', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-12 17:10:32', NULL, 0, 0, '2013-11-12 17:10:32', NULL, 'working on server-side\nadded "special repos" handling\n', '2013-12-02 11:00:55'),
(1010, '702c4c08fc180d6c3b5cd2f89de206ab812c5fd2', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-11-12 17:13:49', 45, 0, 0, '2013-11-12 17:13:49', 45, 'oops\n', '2013-12-02 11:00:55'),
(1011, '9fd1b76ff971ec1994dcfc1cefaa28e90187afaf', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-11-12 20:13:39', 45, 0, 0, '2013-11-12 20:13:39', 45, 'enhanced repository response time', '2013-12-02 11:00:55'),
(1012, '5c631a164c57dc342ddb60d00e245759555dbea7', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-11-12 22:11:35', 45, 0, 0, '2013-11-12 22:11:35', 45, 'enhanced repository response time', '2013-12-02 11:00:56'),
(1013, 'd26cbf76913b63fbd3c1b016b8c5abb3cf661366', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-13 16:17:00', NULL, 0, 0, '2013-11-13 16:17:00', NULL, 'removed config from repository', '2013-12-02 11:00:56'),
(1014, '8dc5e060bce13723b30160f951380b9d628ce52c', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-11-13 16:22:13', 45, 0, 0, '2013-11-13 16:22:13', 45, 'added gitignore\n', '2013-12-02 11:00:56'),
(1015, '1b770039cf1ca610f788c298f9f52f68b063b417', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-11-13 16:22:38', 45, 0, 0, '2013-11-13 16:22:38', 45, 'added gitignore\n', '2013-12-02 11:00:56'),
(1016, '72436ea37273dfcc1937150603363218013dea69', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-13 16:26:33', NULL, 0, 0, '2013-11-13 16:26:33', NULL, 'removed config from repository', '2013-12-02 11:00:56'),
(1017, '01b4ab858c55a84bc0c8a88aba5a0993d08558fd', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-13 16:27:19', NULL, 0, 0, '2013-11-13 16:27:19', NULL, 'removed config from repository', '2013-12-02 11:00:56'),
(1018, '3abe4b27b338055650c7a06a77d24fec19278a11', 23, 27, 'neiluJ', 'julien@nitronet.org', '2013-11-13 16:29:03', 45, 0, 0, '2013-11-13 16:29:03', 45, 'gnak\n', '2013-12-02 11:00:56'),
(1019, 'b7800b42a99c2b1dea26fb183d9884b3c9a9bebc', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-14 09:12:00', NULL, 0, 0, '2013-11-14 09:12:00', NULL, 'temp tests', '2013-12-02 11:00:56'),
(1020, '92c2ed1967c544ca6adea516fcae61e227665fa9', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-14 16:10:27', NULL, 0, 0, '2013-11-14 16:10:27', NULL, 'try to fix hooks...', '2013-12-02 11:00:56'),
(1021, '3ebe764099861a4b812a3b056d9ba582ed0f5cd8', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-14 16:14:26', NULL, 0, 0, '2013-11-14 16:14:26', NULL, 'try to fix hooks...', '2013-12-02 11:00:56'),
(1022, '39c62872b57d030f3731649b1ec02ab2425da377', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-14 17:25:44', NULL, 0, 0, '2013-11-14 17:25:44', NULL, 'try to fix hooks...', '2013-12-02 11:00:56'),
(1023, '187a58aa9d8c2c396fcc6204519355baacf69efa', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-14 17:27:51', NULL, 0, 0, '2013-11-14 17:27:51', NULL, 'try to fix hooks...', '2013-12-02 11:00:56'),
(1024, 'f02d8a836ad880af927ede9bc25f4b61a3ae8830', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-14 17:30:21', NULL, 0, 0, '2013-11-14 17:30:21', NULL, 'try to fix hooks...', '2013-12-02 11:00:56'),
(1025, 'f5e9084d1cdb0235c2dbf8a1fc75666d2978390d', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-14 17:34:15', NULL, 0, 0, '2013-11-14 17:34:15', NULL, 'try to fix hooks...', '2013-12-02 11:00:56'),
(1026, 'd87b9e393ce1a2a175d44d4a06a567d2266b3b96', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-14 17:36:24', NULL, 0, 0, '2013-11-14 17:36:24', NULL, 'try to fix hooks...', '2013-12-02 11:00:56'),
(1027, 'b7ac0a7595d7fc8b1ed314513ecc6e24c5a8fee8', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-14 17:42:42', NULL, 0, 0, '2013-11-14 17:42:42', NULL, 'try to fix hooks...', '2013-12-02 11:00:56'),
(1028, '2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d', 23, 27, 'neiluj', 'neiluj@baltringue', '2013-11-14 17:44:37', NULL, 0, 0, '2013-11-14 17:44:37', NULL, 'try to fix hooks...', '2013-12-02 11:00:56'),
(1029, 'cd5d3c1ea7cb09521462e8759ffb5b31d1eb3621', 25, 28, 'git on gitmachine', 'git@gitmachine', '2013-11-13 16:41:55', NULL, 0, 0, '2013-11-13 16:41:55', NULL, 'gitolite setup -pk neiluJ.pub\n', '2013-11-25 09:35:11'),
(1030, '3964bc40e39f82b866cbaa44fb9a1f688dfbf5be', 25, 28, 'neiluJ', 'julien@nitronet.org', '2013-11-13 16:48:23', 45, 0, 0, '2013-11-13 16:48:23', 45, 'foooo\n', '2013-11-25 09:35:11'),
(1031, '642b2e6ee065a76548bd23db4417aea525139b03', 25, 28, 'neiluJ', 'julien@nitronet.org', '2013-11-14 17:10:43', 45, 0, 0, '2013-11-14 17:10:43', 45, 'added testing\n', '2013-11-25 09:35:12'),
(1032, 'ee29429a12a19f189b637dd56825a798dce0e9df', 25, 28, 'neiluJ', 'julien@nitronet.org', '2013-11-14 17:50:08', 45, 0, 0, '2013-11-14 17:50:08', 45, 'testing\n', '2013-11-25 09:35:12'),
(1033, 'e6a0d397c7f31472ac1f61ffb9a5891d7a87d0b3', 25, 28, 'neiluJ', 'julien@nitronet.org', '2013-11-14 17:51:15', 45, 0, 0, '2013-11-14 17:51:15', 45, 'testing again\n', '2013-11-25 09:35:12'),
(1034, '56f0bfd01c7f2e630177b3280e23369e8e3bd850', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-16 23:55:56', NULL, 0, 0, '2013-11-16 23:55:56', 47, 'added new ssh-key\n', '2013-11-25 09:35:12'),
(1035, '6ef6f400e82e2e498b1bad47892dc31c33d412ab', 25, 28, 'neiluJ', 'julien@nitronet.org', '2013-11-19 16:01:29', 45, 0, 0, '2013-11-19 16:01:29', 45, 'added forgery\n', '2013-11-25 09:35:12'),
(1036, '363cd470628db1ddaa80839beb0b705cfa8eec38', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-17 16:33:04', NULL, 0, 0, '2013-11-17 16:33:04', NULL, 'Merge branch ''master'' of localhost:gitolite-admin\n', '2013-11-25 09:35:12'),
(1037, '8c5b63f9883bc4a84b85bc861e19e0c991998ea3', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-17 17:22:50', NULL, 0, 0, '2013-11-17 17:22:50', 47, 'added new ssh-key\n', '2013-11-25 09:35:12'),
(1038, '86aefd75c29fd082447d737cfda24aaacc21d7fd', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-17 17:25:53', NULL, 0, 0, '2013-11-17 17:25:53', 47, 'removed ssh-key\n', '2013-11-25 09:35:12'),
(1039, '8ba20b6633d085150854fb326d9781045f743764', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-17 17:30:44', NULL, 0, 0, '2013-11-17 17:30:44', 47, 'added new ssh-key\n', '2013-11-25 09:35:12'),
(1040, 'ab9f217140f86ca07d89763a744d2d36c9bfa1e1', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-17 17:33:31', NULL, 0, 0, '2013-11-17 17:33:31', 47, 'removed ssh-key\n', '2013-11-25 09:35:12'),
(1041, '54c2463b8aee8de5e4b82446100613b4cadf1b8f', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-17 17:48:29', NULL, 0, 0, '2013-11-17 17:48:29', 47, 'added new ssh-key\n', '2013-11-25 09:35:13'),
(1042, 'ddff8b99cf4691255990133828799c8c34631cd8', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-17 17:52:03', NULL, 0, 0, '2013-11-17 17:52:03', 47, 'removed ssh-key\n', '2013-11-25 09:35:13'),
(1043, 'bb6c2d71172f22078933cc0040db199987c455bf', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-17 23:44:59', NULL, 0, 0, '2013-11-17 23:44:59', 47, 'created repository test/testeuh\n', '2013-11-25 09:35:13'),
(1044, '8bde61b20d076ec464be650e4d16b6554fc90b69', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-18 16:19:54', NULL, 0, 0, '2013-11-18 16:19:54', 47, 'created repository test/test-again\n', '2013-11-25 09:35:13'),
(1045, '7f700ef241f313d7cdda8882635de482853e6942', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-18 17:53:39', NULL, 0, 0, '2013-11-18 17:53:39', 47, 'created repository test/test-again\n', '2013-11-25 09:35:13'),
(1046, 'eb42c35a8f38d410fa044e4952e38d840c457152', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-18 22:09:29', NULL, 0, 0, '2013-11-18 22:09:29', 47, 'created repository test/pour-le-fun\n', '2013-11-25 09:35:13'),
(1047, '09d502c32cd69612aad5c7a58a5643db9a48682a', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-19 00:47:58', NULL, 0, 0, '2013-11-19 00:47:58', 47, 'added access\n', '2013-11-25 09:35:13'),
(1048, 'a0b2dad22e6207a1bfd9738c42560279df22037e', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-19 00:51:12', NULL, 0, 0, '2013-11-19 00:51:12', 47, 'edited priviledges\n', '2013-11-25 09:35:13'),
(1049, '9d55268596baa1e40a89951b5678e2ccc94fabb3', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-19 00:52:07', NULL, 0, 0, '2013-11-19 00:52:07', 47, 'removed access\n', '2013-11-25 09:35:13'),
(1050, '16c333adb7a580df4dc81b17c1206a96be63534d', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-19 16:05:45', NULL, 0, 0, '2013-11-19 16:05:45', 48, 'created fork of neiluJ2/forgery to boulet/forgery\n', '2013-11-25 09:35:13'),
(1051, 'aed709dedb7f1b13b98dd77ee7925075937a9f25', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-19 18:44:59', NULL, 0, 0, '2013-11-19 18:44:59', 47, 'removed repository test/testeuh\n', '2013-11-25 09:35:14'),
(1052, 'c8a08744189b17aa3e6ccfeafae82b3f8f5c2bd0', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-19 23:00:16', NULL, 0, 0, '2013-11-19 23:00:16', 47, 'created repository test/simon\n', '2013-11-25 09:35:14'),
(1053, '7f023a288d5272d793039fcf5a87b1c38d893f0a', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-19 23:02:12', NULL, 0, 0, '2013-11-19 23:02:12', 48, 'created fork of test/simon to boulet/simon\n', '2013-11-25 09:35:14'),
(1054, '52919bab74a45e1ee85c9c5e125a86a2321e4dce', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-19 23:05:33', NULL, 0, 0, '2013-11-19 23:05:33', 48, 'removed repository boulet/simon\n', '2013-11-25 09:35:14'),
(1055, 'cbfcd05e5ac5d1fa6fd182a9abb576366d782ca2', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-21 02:44:24', NULL, 0, 0, '2013-11-21 02:44:24', 47, 'edited priviledges\n', '2013-11-25 09:35:14'),
(1056, '335bb05339a7bdef5d2d07a04208b3520feee0e8', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-21 08:08:40', NULL, 0, 0, '2013-11-21 08:08:40', 47, 'created fork of neiluJ2/forgery to test/forgery\n', '2013-11-25 09:35:14'),
(1057, 'e787e7c037e1b9247464e2a377d5fdc606162b5e', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-23 08:46:32', NULL, 0, 0, '2013-11-23 08:46:32', 47, 'removed repository test/test-again\n', '2013-11-25 09:35:14'),
(1058, '0ba82991fa172a257d7988a620e797e5f015e55e', 25, 28, 'forgery', 'forgery@nitronet.org', '2013-11-25 08:35:05', NULL, 0, 0, '2013-11-25 08:35:05', 47, 'edited priviledges\n', '2013-11-25 09:35:14'),
(1060, '7e020cad03d6da075171f6e5f0e178e8236171ee', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-14 21:21:57', 45, 0, 0, '2013-11-14 21:21:57', 45, 'added .htaccess', '2013-11-25 09:45:16'),
(1061, '5c68f351b64fa4d29b8117d4eacc46a3f39d960a', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-15 08:56:52', NULL, 0, 0, '2013-11-15 08:56:52', NULL, 'added fwk/form dependency', '2013-11-25 09:45:16'),
(1062, 'e7ef85e56563e2a0dea3e9805d43c35536c60fc1', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-15 16:24:51', NULL, 0, 0, '2013-11-15 16:24:51', NULL, 'added user stuff', '2013-11-25 09:45:16'),
(1063, '4bce1a2d570d85765cbe99b82a883bb770e45854', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-17 18:05:06', 45, 0, 0, '2013-11-17 18:05:06', 45, 'added User add\nadded Logout\nfixed Form Renderer layouts (needfix for non-element errors)\nfixed usermenu', '2013-11-25 09:45:16'),
(1064, '7853bed1b81b44b80299fded5e6b012d5883d14e', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-17 18:09:25', 45, 0, 0, '2013-11-17 18:09:25', 45, 'fixed User display', '2013-11-25 09:45:16'),
(1065, '27ac4e5262aa43f7bdfd81364a91e9693a563c7a', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-17 22:31:13', 45, 0, 0, '2013-11-17 22:31:13', 45, 'added bootstrap-theme\nstarted user profile', '2013-11-25 09:45:16'),
(1066, 'fb501fe46a22175d7ff8d1094c93e78524567ccc', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-17 22:56:20', 45, 0, 0, '2013-11-17 22:56:20', 45, 'stoopid cosmetic change', '2013-11-25 09:45:17'),
(1067, '8f26d9dd024401533e48c909349300142604c1ea', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-18 16:08:41', NULL, 0, 0, '2013-11-18 16:08:41', NULL, 'added better profile\nadded profile settings\nfixed some forms/filters', '2013-11-25 09:45:17'),
(1068, '1cd20b5fb87a4ee8ea32b196539d938e1c390c64', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-18 16:23:55', NULL, 0, 0, '2013-11-18 16:23:55', NULL, 'added handling of htpasswd for http git access', '2013-11-25 09:45:17'),
(1069, 'b550e349e6eb6bbb47bf93b710aa1d94fa3f05df', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-18 16:25:29', NULL, 0, 0, '2013-11-18 16:25:29', NULL, 'added ''daemon'' username restriction', '2013-11-25 09:45:17'),
(1070, '31dc07bdfa56e58b2ff1912d4c29b7f07b205cbb', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-18 16:39:59', NULL, 0, 0, '2013-11-18 16:39:59', NULL, 'fixed header/footer', '2013-11-25 09:45:17'),
(1071, '8bc6851f4702df343b146756a86ec047a1e890d3', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-18 16:49:40', NULL, 0, 0, '2013-11-18 16:49:40', NULL, 'fixed header/footer', '2013-11-25 09:45:17'),
(1072, 'ba1d8b27f49a1779b0ad8183f60bc9d86e779588', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-19 00:35:57', 45, 0, 0, '2013-11-19 00:35:57', 45, 'added missing confs', '2013-11-25 09:45:17'),
(1073, 'df170eb9f9fb63ffa39b1cb943b83ba5dc0f39d5', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-19 16:17:12', NULL, 0, 0, '2013-11-19 16:17:12', NULL, 'added Events and synchronization', '2013-11-25 09:45:17'),
(1074, 'c79083a32119af37f48681da580d2854b32aba77', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-19 16:20:23', NULL, 0, 0, '2013-11-19 16:20:23', NULL, 'oops', '2013-11-25 09:45:17'),
(1075, '4fca322df5361c9d09fb3b64a06a4b9bfde2e052', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-19 19:59:06', 45, 0, 0, '2013-11-19 19:59:06', 45, 'added transactions', '2013-11-25 09:45:17'),
(1076, '9a71fbd1c6a38dd27aec2376ed8311371b35568d', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-19 20:29:12', 45, 0, 0, '2013-11-19 20:29:12', 45, 'fixed oops :/', '2013-11-25 09:45:17'),
(1077, '6a246c8e38296c405107d664e5234c95434196ca', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-19 20:34:33', 45, 0, 0, '2013-11-19 20:34:33', 45, 'fixed oops :/', '2013-11-25 09:45:18'),
(1078, 'ec76584b5d4e455883d484ad689ec3e2adecb8af', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-20 08:56:06', NULL, 0, 0, '2013-11-20 08:56:06', NULL, 'test lock workdir', '2013-11-25 09:45:18'),
(1079, '05181492fe54917d25a1ca91607c505113e3f56d', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-20 09:43:02', NULL, 0, 0, '2013-11-20 09:43:02', NULL, 'added Logger', '2013-11-25 09:45:18'),
(1080, '38d3f74ee6e48f3903e1401868099c2dc80f651b', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-20 09:49:38', NULL, 0, 0, '2013-11-20 09:49:38', NULL, 'stupid me', '2013-11-25 09:45:18'),
(1081, '592ece83fadef7d120e94a2b6e9d3e41efd2f8f8', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-20 10:14:54', NULL, 0, 0, '2013-11-20 10:14:54', NULL, 'better log format', '2013-11-25 09:45:18'),
(1082, '338c303eb5d4c852b39356454e63748203c5b3bf', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-20 10:17:09', NULL, 0, 0, '2013-11-20 10:17:09', NULL, 'better log format', '2013-11-25 09:45:18'),
(1083, '0035b59a28e73f411f77fe7061a1be730e9351ac', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-20 10:18:58', NULL, 0, 0, '2013-11-20 10:18:58', NULL, 'fixed push options', '2013-11-25 09:45:18'),
(1084, '61af4725a5743427c55998ab275862d8a0cdf39f', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-20 16:10:04', NULL, 0, 0, '2013-11-20 16:10:04', NULL, 'added repository creation', '2013-11-25 09:45:18'),
(1085, '51104137048d609b02005f54ec650a4519cce13d', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-20 17:02:48', NULL, 0, 0, '2013-11-20 17:02:48', NULL, 'added empty_repository handling', '2013-11-25 09:45:18'),
(1086, '2d53ec40df43692b756b36974387a64bf8a81807', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-20 23:20:05', 45, 0, 0, '2013-11-20 23:20:05', 45, 'fixed git.executable', '2013-11-25 09:45:18'),
(1087, '3be82868ae4224b35d7e3d5aee5ae0e2c83cd189', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-20 23:21:08', 45, 0, 0, '2013-11-20 23:21:08', 45, 'fixed git.executable', '2013-11-25 09:45:18'),
(1088, '4cc7e2ba739bb3b87d45cf2ed499d4c41c2d4cc0', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-20 23:23:48', 45, 0, 0, '2013-11-20 23:23:48', 45, 'fixed git.executable', '2013-11-25 09:45:19'),
(1089, 'bc01020a317f81bcd09bdcca5be3cab199400bb6', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 01:07:39', 45, 0, 0, '2013-11-21 01:07:39', 45, 'fixed empty repository detection...', '2013-11-25 09:45:19'),
(1090, '7336502fdf1700b7f0b5806c4d85b0d377b45790', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 01:15:34', 45, 0, 0, '2013-11-21 01:15:34', 45, 'fixed git/forgery user mixup', '2013-11-25 09:45:19'),
(1091, '7e9d093be03386cafebbb25e7270e1226f424889', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-21 14:18:41', NULL, 0, 0, '2013-11-21 14:18:41', NULL, 'added clone url (ssh/http) buttons/sync/config', '2013-11-25 09:45:19'),
(1092, '0b4e26db3b5e67c4fc1bb30d111be700b2790c78', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-21 14:19:16', NULL, 0, 0, '2013-11-21 14:19:16', NULL, 'added clone url (ssh/http) buttons/sync + config', '2013-11-25 09:45:19'),
(1093, '95b1b4b172f92f329e5436f185d36fb9ae2d3e10', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-21 14:27:24', NULL, 0, 0, '2013-11-21 14:27:24', NULL, 'added ini-dist to ini syntax highlighting', '2013-11-25 09:45:19'),
(1094, '20706b3f1c51053050c85ccaa77353ce104df063', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-21 16:56:49', NULL, 0, 0, '2013-11-21 16:56:49', NULL, 'fixed .htaccess to allow http-backend', '2013-11-25 09:45:19'),
(1095, '151e72338ead7e655f38139ab483e19a4d9c4966', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-21 17:14:07', NULL, 0, 0, '2013-11-21 17:14:07', NULL, 'added repository edition management', '2013-11-25 09:45:19'),
(1096, 'c1cd5752a919e5874fa5759076584892a889d311', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 20:39:21', 45, 0, 0, '2013-11-21 20:39:21', 45, 'temp commit ...', '2013-11-25 09:45:19'),
(1097, 'e472e00e8cda928833cd7f9b45760087ae50fa26', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 21:10:43', 45, 0, 0, '2013-11-21 21:10:43', 45, 'try forks', '2013-11-25 09:45:19'),
(1098, 'f98f39b0f8c006efa358555b0104c9ac15b825a6', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 21:11:00', 45, 0, 0, '2013-11-21 21:11:00', 45, 'try forks', '2013-11-25 09:45:19'),
(1099, '58d0ea41d50557840550edf8caee47b052221425', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 21:13:25', 45, 0, 0, '2013-11-21 21:13:25', 45, 'stoopid', '2013-11-25 09:45:20'),
(1100, '375ca9949915a3e92daf19c48c132b12223a6b84', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 21:14:36', 45, 0, 0, '2013-11-21 21:14:36', 45, 'stoopid', '2013-11-25 09:45:20'),
(1101, 'f6611e24e8241f8b3f478592de0cef704a61b7df', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 21:15:28', 45, 0, 0, '2013-11-21 21:15:28', 45, 'fixed cp', '2013-11-25 09:45:20'),
(1102, '2be67786e6a6b2a5edc215e75d83e6c1a1779701', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 21:17:06', 45, 0, 0, '2013-11-21 21:17:06', 45, 'fixed cp', '2013-11-25 09:45:20'),
(1103, '0f55afeba50db410b6ea0a72989a65ce47f4ad20', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 21:38:01', 45, 0, 0, '2013-11-21 21:38:01', 45, 'try other fork method', '2013-11-25 09:45:20'),
(1104, '7ac033c481589d6a9012594b4b487b64adb7fb2f', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 21:46:04', 45, 0, 0, '2013-11-21 21:46:04', 45, 'try other fork method', '2013-11-25 09:45:20'),
(1105, '554af763f7faba41af221f1e4fde815734f8877f', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 21:50:37', 45, 0, 0, '2013-11-21 21:50:37', 45, 'stoopid', '2013-11-25 09:45:20'),
(1106, '20ade567736e72ac0cfa5accdc9ea454f9859833', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 21:58:31', 45, 0, 0, '2013-11-21 21:58:31', 45, 'fork still on the way', '2013-11-25 09:45:20'),
(1107, '33c6f8ad53696866fcac3d94b76a85d74323b172', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 22:06:10', 45, 0, 0, '2013-11-21 22:06:10', 45, 'fixed forking\nfixed php error when calling getForkForm', '2013-11-25 09:45:20'),
(1108, '78430d088ee2533623f346d883a44d1618cefc40', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 22:28:05', 45, 0, 0, '2013-11-21 22:28:05', 45, 'fixed prevent fork of empty repos', '2013-11-25 09:45:20'),
(1109, '45c9cc2b6e148e7d2759766d234fb6030ab9ada0', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 22:40:52', 45, 0, 0, '2013-11-21 22:40:52', 45, 'fixed fork UI', '2013-11-25 09:45:20'),
(1110, '956dfe5df3a6c1c36ea9bd74519c3346d2d15e5b', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 22:43:21', 45, 0, 0, '2013-11-21 22:43:21', 45, 'fixed error handling', '2013-11-25 09:45:21'),
(1111, '398c6cd1524d2ff04ce0879c946ecb1b8b5eb130', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-21 22:45:42', 45, 0, 0, '2013-11-21 22:45:42', 45, 'fixed error handling', '2013-11-25 09:45:21'),
(1112, '790b365f2d540868abaabbda2fe5dcd5fbbfc0e2', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-22 11:06:36', NULL, 0, 0, '2013-11-22 11:06:36', NULL, 'added repository delete', '2013-11-25 09:45:21'),
(1113, '55061cd26bdb96dec16adf6c75095fe43c413e25', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-22 14:54:49', NULL, 0, 0, '2013-11-22 14:54:49', NULL, 'added repository settings\nadded repository website handling', '2013-11-25 09:45:21'),
(1114, '88141ac8935a70cac06a4d1586608b2e4919257b', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-22 15:09:42', NULL, 0, 0, '2013-11-22 15:09:42', NULL, 'cosmetic changes', '2013-11-25 09:45:21'),
(1115, '125eba3337dcc7ad9c1752e4f1ec125faf59f17e', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-24 22:08:19', 45, 0, 0, '2013-11-24 22:08:19', 45, 'added public url', '2013-11-25 09:45:21'),
(1116, '346f1a5b7d5cda0bf1573b0fdd97d9ed7f15e37a', 23, 30, 'Julien', 'julien@nitronet.org', '2013-11-24 22:08:34', 45, 0, 0, '2013-11-24 22:08:34', 45, 'Merge branch ''master'' of github.com:neiluJ/git-app-test\n', '2013-11-25 09:45:21'),
(1117, '4ef557338adf1cd622d79afb85b6a7f9e7b59317', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-24 22:16:33', 45, 0, 0, '2013-11-24 22:16:33', 45, 'added public url', '2013-11-25 09:45:21'),
(1118, '732b0f1fbc6d4c7fcb141268657bb05dd5acdbe4', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-24 23:09:09', 45, 0, 0, '2013-11-24 23:09:09', 45, 'fixed htaccess', '2013-11-25 09:45:21'),
(1119, 'bee8d8d5390e88365e3cada4b86ab6709b2b0ba7', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-25 17:00:06', NULL, 0, 0, '2013-11-25 17:00:06', NULL, 'started roles/acls controls', '2013-11-25 09:45:21'),
(1120, '597b3704189a86b626c9daf9363dc3d7f611cd95', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-25 23:27:19', 45, 0, 0, '2013-11-25 23:27:19', 45, 'fixed acls', '2013-11-25 09:45:22'),
(1121, 'ddc82dbfc305725378107edfa86f7d97ea6feb71', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-26 10:54:06', NULL, 0, 0, '2013-11-26 10:54:06', NULL, 'updated acls...', '2013-11-25 09:45:22'),
(1122, '5f86c6bba3409494a55c11faaa6c6c4a03290130', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-26 15:40:15', NULL, 0, 0, '2013-11-26 15:40:15', NULL, 'added roles in users management', '2013-11-25 09:45:22'),
(1123, '558eae295615871a11eed2db3aa2c782656559de', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-11-28 23:39:22', 45, 0, 0, '2013-11-28 23:39:22', 45, 'started commits indexer ', '2013-11-25 09:45:22'),
(1124, '2957b8b5e057fbc30ff77f107093903e5f423fae', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-11-29 16:10:33', NULL, 0, 0, '2013-11-29 16:10:33', NULL, 'continued commit index', '2013-11-25 09:45:22'),
(1125, 'c3aa3f2cef665c539acd13126b1eefa48d7dd5a6', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-12-02 15:46:19', NULL, 0, 0, '2013-12-02 15:46:19', NULL, 'fixed indexation (for now)\nadded search bar + autocomplete', '2013-11-25 09:45:22'),
(1126, 'b5d28598861e653d1ad513c4aff8f6e48181460f', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-12-02 16:38:55', NULL, 0, 0, '2013-12-02 16:38:55', NULL, 'added user-acls to autocomplete (needfix)', '2013-11-25 09:45:22'),
(1127, '07d2343d0fd79c0d82df9bced31eb72ab3826a20', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-12-02 16:42:48', NULL, 0, 0, '2013-12-02 16:42:48', NULL, 'fixed query for acls :/', '2013-11-25 09:45:22'),
(1128, '1b11f014c37a723e16e88810b7a756fc256d70b9', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-12-02 22:37:07', 45, 0, 0, '2013-12-02 22:37:07', 45, 'removed useless data in sql', '2013-11-25 09:45:22'),
(1129, '9ae775c60c32a4ba5d5ad6e2f4587f940e5d85df', 23, 30, 'neiluJ', 'julien@nitronet.org', '2013-12-02 23:12:58', 45, 0, 0, '2013-12-02 23:12:58', 45, 'new pretty loader', '2013-11-25 09:45:22'),
(1130, '21e37b33eb560e6e323411ff1f98b571ccff11ad', 23, 30, 'neiluj', 'neiluj@baltringue', '2013-12-03 09:26:47', NULL, 0, 0, '2013-12-03 09:26:47', NULL, 'fixed loader\ntrying to fix index bug', '2013-11-25 09:45:22'),
(1131, '8a302931d7d702ffb8b4c9057d5cb67f8daff874', 25, 31, 'forgery', 'forgery@nitronet.org', '2013-11-25 08:49:25', NULL, 0, 0, '2013-11-25 08:49:25', 47, 'removed access\n', '2013-11-25 09:49:32'),
(1132, '7493c539968740e713f91a56b10e6592bc093366', 23, 32, 'neiluj', 'neiluj@baltringue', '2013-12-03 09:46:40', NULL, 0, 0, '2013-12-03 09:46:40', NULL, 'working on post-update', '2013-11-25 10:04:40'),
(1133, 'ae150265b49d574c968fb30dae6648d1bbed77c9', 23, 33, 'neiluj', 'neiluj@baltringue', '2013-12-03 10:00:20', NULL, 0, 0, '2013-12-03 10:00:20', NULL, 'working on post-update', '2013-11-25 10:18:17'),
(1134, 'addcf81fdd5fac1368eac3c5e3ca5238efaeb9aa', 23, 34, 'neiluj', 'neiluj@baltringue', '2013-12-03 10:15:54', NULL, 0, 0, '2013-12-03 10:15:54', NULL, 'working on post-update', '2013-11-25 10:34:21'),
(1135, 'bc7134b5877076c68ff584e9f006ff3e3fd94c9e', 23, 35, 'neiluj', 'neiluj@baltringue', '2013-12-03 15:03:38', NULL, 0, 0, '2013-12-03 15:03:38', NULL, 'added user activity stream (needfix: display contributions/pagination/full history)', '2013-11-25 15:22:17'),
(1136, '24aa512f62816d8614d4892f8a48c502108ba254', 23, 36, 'neiluj', 'neiluj@baltringue', '2013-12-03 15:30:31', NULL, 0, 0, '2013-12-03 15:30:31', NULL, 'try repo activity', '2013-11-25 15:56:28'),
(1137, 'd2dde390e18d4a9ca064f7045553f394b8b83f4d', 25, 37, 'forgery', 'forgery@nitronet.org', '2013-11-25 15:24:20', NULL, 0, 0, '2013-11-25 15:24:20', 47, 'created repository test/test-activity\n', '2013-11-25 16:24:28'),
(1138, '9b95d1422401ba92b7f079a62791113279927a4e', 49, 38, 'neiluJ', 'julien@nitronet.org', '2013-12-03 16:07:41', 45, 0, 0, '2013-12-03 16:07:41', 45, 'ajout de test\n', '2013-11-25 16:25:32'),
(1139, '006932456c63615f025f5d0dab3045c48a1b5b93', 49, 39, 'neiluJ', 'julien@nitronet.org', '2013-12-03 16:08:25', 45, 0, 0, '2013-12-03 16:08:25', 45, 'ajout de test 2\n', '2013-11-25 16:26:16'),
(1140, '12fdd520d64a2afef7988f8e233e9aa88fd218d3', 47, 40, 'neiluJ', 'julien@nitronet.org', '2013-11-22 15:29:00', 45, 0, 0, '2013-11-22 15:29:00', 45, 'test\n', '2013-11-25 16:34:52'),
(1141, 'f1fe530f80ff263f3d05c749e00e801f786fb550', 47, 40, 'neiluJ', 'julien@nitronet.org', '2013-12-03 16:17:01', 45, 0, 0, '2013-12-03 16:17:01', 45, 'yoooo\n', '2013-11-25 16:34:52'),
(1142, '4a6dfceee5a605e415f541593eb6f233cc45184e', 47, 41, 'neiluJ', 'julien@nitronet.org', '2013-12-03 16:44:10', 45, 0, 0, '2013-12-03 16:44:10', 45, 'feoabazf\n', '2013-11-25 17:02:00'),
(1143, '3ddabf5b0120cc71685c0f4093666cad673542d4', 23, 42, 'neiluj', 'neiluj@baltringue', '2013-12-03 16:32:00', NULL, 0, 0, '2013-12-03 16:32:00', NULL, 'added anonymous handling', '2013-11-25 17:08:38'),
(1144, '15d87ad1172764d9003628e26350b8773d19e019', 23, 42, 'neiluj', 'neiluj@baltringue', '2013-12-03 16:40:42', NULL, 0, 0, '2013-12-03 16:40:42', NULL, 'added anonymous handling', '2013-11-25 17:08:38'),
(1145, '16c2b8ad5dbcb7f7e04f492910a66b0d9c5f8112', 23, 42, 'neiluj', 'neiluj@baltringue', '2013-12-03 16:42:30', NULL, 0, 0, '2013-12-03 16:42:30', NULL, 'added anonymous handling', '2013-11-25 17:08:38'),
(1146, '1726b98313c834b65049d26e70d1f66139853e4d', 23, 43, 'neiluJ', 'julien@nitronet.org', '2013-12-03 21:37:44', 45, 0, 0, '2013-12-03 21:37:44', 45, 'try handling tags', '2013-12-07 22:24:46'),
(1147, 'fc65144bd7e59bda467d63fad131f0fe249d1b84', 23, 43, 'neiluJ', 'julien@nitronet.org', '2013-12-03 21:39:09', 45, 0, 0, '2013-12-03 21:39:09', 45, 'try handling tags', '2013-12-07 22:24:46'),
(1148, '4c2abaa3c3071acd2ae4e4ba59087b823624c6da', 23, 43, 'neiluJ', 'julien@nitronet.org', '2013-12-03 21:53:36', 45, 0, 0, '2013-12-03 21:53:36', 45, 'try handling tags', '2013-12-07 22:24:46'),
(1149, 'fecba363b52d78108b1edf4ca0495637714557b6', 23, 43, 'neiluJ', 'julien@nitronet.org', '2013-12-03 22:23:29', 45, 0, 0, '2013-12-03 22:23:29', 45, 'fixed activity (needfix: --all)', '2013-12-07 22:24:46'),
(1150, 'b40729351c288cd6e43d4c46e2c7f4f476b45bd3', 23, 43, 'Julien', 'neiluJ@users.noreply.github.com', '2013-12-06 13:41:00', NULL, 0, 0, '2013-12-06 13:41:00', NULL, 'Create README.md', '2013-12-07 22:24:47'),
(1151, '429849b7516556ecdfd818a9952cd40f8509f4b5', 23, 43, 'Julien', 'neiluJ@users.noreply.github.com', '2013-12-06 15:17:32', NULL, 0, 0, '2013-12-06 15:17:32', NULL, 'Update README.md', '2013-12-07 22:24:47'),
(1152, '65d8a50c5dc3838e7c7380a364d81aab5440206a', 25, 44, 'forgery', 'forgery@nitronet.org', '2013-12-07 23:12:45', NULL, 0, 0, '2013-12-07 23:12:45', 47, 'created repository test/abc-def\n', '2013-12-08 00:12:52'),
(1153, '2d513b986398e5c45c745f088fdff6b84051b4d8', 25, 45, 'forgery', 'forgery@nitronet.org', '2013-12-07 23:16:12', NULL, 0, 0, '2013-12-07 23:16:12', 47, 'created repository test/abc-def\n', '2013-12-08 00:16:18'),
(1154, '22a48121003c47b352307ea7a54899518553da2a', 25, 46, 'forgery', 'forgery@nitronet.org', '2013-12-07 23:17:56', NULL, 0, 0, '2013-12-07 23:17:56', 47, 'created repository test/abc-def\n', '2013-12-08 00:18:02'),
(1155, 'ef2ee11227681843a9e38d0a5b3563eea6ca10ee', 25, 47, 'forgery', 'forgery@nitronet.org', '2013-12-07 23:18:20', NULL, 0, 0, '2013-12-07 23:18:20', 47, 'created repository test/abc-def\n', '2013-12-08 00:18:26'),
(1156, '5e9d8b5784ca9267c28992e73210fe130ebb0e9f', 25, 48, 'forgery', 'forgery@nitronet.org', '2013-12-07 23:20:23', NULL, 0, 0, '2013-12-07 23:20:23', 48, 'created fork of test/test-activity to boulet/test-activity\n', '2013-12-08 00:20:29'),
(1157, '1a95691a3670b2b204f8babb10bbdfc8c09f5a01', 23, 49, 'neiluj', 'neiluj@baltringue', '2013-12-10 14:57:51', NULL, 0, 0, '2013-12-10 14:57:51', NULL, 'added static activity', '2013-12-08 01:03:27'),
(1158, 'eb47bb55014060cec1834ff3f15d77b7a530c83d', 23, 49, 'neiluj', 'neiluj@baltringue', '2013-12-10 15:02:23', NULL, 0, 0, '2013-12-10 15:02:23', NULL, 'added static activity (debug)', '2013-12-08 01:03:27'),
(1159, 'ac4175d1c236d69d21adceb57a7105f38c366697', 23, 49, 'neiluj', 'neiluj@baltringue', '2013-12-10 15:04:40', NULL, 0, 0, '2013-12-10 15:04:40', NULL, 'added static activity (debug)', '2013-12-08 01:03:27'),
(1160, '83e6cb4569fce98e3b5e40308182b92b6e7dc43a', 23, 49, 'neiluj', 'neiluj@baltringue', '2013-12-10 15:50:18', NULL, 0, 0, '2013-12-10 15:50:18', NULL, 'added static activity database docs', '2013-12-08 01:03:27');

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

--
-- Dumping data for table `commits_refs`
--

INSERT INTO `commits_refs` (`commitId`, `refId`) VALUES
(965, 142),
(966, 142),
(967, 142),
(968, 142),
(969, 142),
(970, 142),
(971, 142),
(972, 142),
(973, 142),
(974, 142),
(975, 142),
(976, 142),
(977, 142),
(978, 142),
(979, 142),
(980, 142),
(981, 142),
(982, 142),
(983, 142),
(984, 142),
(985, 142),
(986, 142),
(987, 142),
(988, 142),
(989, 142),
(990, 142),
(991, 142),
(992, 142),
(993, 142),
(994, 142),
(995, 142),
(996, 142),
(997, 142),
(998, 142),
(999, 142),
(1000, 142),
(1001, 142),
(1002, 142),
(1003, 142),
(1004, 142),
(1005, 142),
(1006, 142),
(1007, 142),
(1008, 142),
(1009, 142),
(1010, 142),
(1011, 142),
(1012, 142),
(1013, 142),
(1014, 142),
(1015, 142),
(1016, 142),
(1017, 142),
(1018, 142),
(1019, 142),
(1020, 142),
(1021, 142),
(1022, 142),
(1023, 142),
(1024, 142),
(1025, 142),
(1026, 142),
(1027, 142),
(1028, 142),
(1060, 142),
(1061, 142),
(1062, 142),
(1063, 142),
(1064, 142),
(1065, 142),
(1066, 142),
(1067, 142),
(1068, 142),
(1069, 142),
(1070, 142),
(1071, 142),
(1072, 142),
(1073, 142),
(1074, 142),
(1075, 142),
(1076, 142),
(1077, 142),
(1078, 142),
(1079, 142),
(1080, 142),
(1081, 142),
(1082, 142),
(1083, 142),
(1084, 142),
(1085, 142),
(1086, 142),
(1087, 142),
(1088, 142),
(1089, 142),
(1090, 142),
(1091, 142),
(1092, 142),
(1093, 142),
(1094, 142),
(1095, 142),
(1096, 142),
(1097, 142),
(1098, 142),
(1099, 142),
(1100, 142),
(1101, 142),
(1102, 142),
(1103, 142),
(1104, 142),
(1105, 142),
(1106, 142),
(1107, 142),
(1108, 142),
(1109, 142),
(1110, 142),
(1111, 142),
(1112, 142),
(1113, 142),
(1114, 142),
(1115, 142),
(1116, 142),
(1117, 142),
(1118, 142),
(1119, 142),
(1120, 142),
(1121, 142),
(1122, 142),
(1123, 142),
(1124, 142),
(1125, 142),
(1126, 142),
(1127, 142),
(1128, 142),
(1129, 142),
(1130, 142),
(1132, 142),
(1133, 142),
(1134, 142),
(1135, 142),
(1136, 142),
(1143, 142),
(1144, 142),
(1145, 142),
(1146, 142),
(1147, 142),
(1148, 142),
(1149, 142),
(1150, 142),
(1151, 142),
(1157, 142),
(1158, 142),
(1159, 142),
(1160, 142),
(1029, 143),
(1030, 143),
(1031, 143),
(1032, 143),
(1033, 143),
(1034, 143),
(1035, 143),
(1036, 143),
(1037, 143),
(1038, 143),
(1039, 143),
(1040, 143),
(1041, 143),
(1042, 143),
(1043, 143),
(1044, 143),
(1045, 143),
(1046, 143),
(1047, 143),
(1048, 143),
(1049, 143),
(1050, 143),
(1051, 143),
(1052, 143),
(1053, 143),
(1054, 143),
(1055, 143),
(1056, 143),
(1057, 143),
(1058, 143),
(1131, 143),
(1137, 143),
(1152, 143),
(1153, 143),
(1154, 143),
(1155, 143),
(1156, 143),
(1138, 144),
(1139, 144),
(1140, 145),
(1141, 145),
(1142, 145);

-- --------------------------------------------------------

--
-- Table structure for table `org_users`
--

CREATE TABLE IF NOT EXISTS `org_users` (
  `organization_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `added_by` int(11) NOT NULL,
  `reposWriteAccess` tinyint(1) NOT NULL DEFAULT '0',
  `reposAdminAccess` tinyint(1) NOT NULL DEFAULT '0',
  `membersAdminAccess` tinyint(1) NOT NULL DEFAULT '0',
  `adminAccess` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`organization_id`,`user_id`),
  KEY `added_by` (`added_by`),
  KEY `users_id_fk` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `org_users`
--

INSERT INTO `org_users` (`organization_id`, `user_id`, `added_by`, `reposWriteAccess`, `reposAdminAccess`, `membersAdminAccess`, `adminAccess`) VALUES
(54, 47, 47, 1, 1, 1, 1),
(55, 45, 47, 1, 1, 0, 1),
(55, 47, 47, 1, 1, 1, 1),
(55, 57, 47, 0, 1, 0, 0),
(56, 47, 47, 1, 1, 1, 1),
(58, 47, 47, 1, 1, 1, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=50 ;

--
-- Dumping data for table `pushes`
--

INSERT INTO `pushes` (`id`, `userId`, `username`, `repositoryId`, `createdOn`) VALUES
(27, 45, 'neiluJ2', 23, '2013-12-02 11:00:55'),
(28, 45, 'neiluJ2', 25, '2013-11-25 09:35:11'),
(30, 45, 'neiluJ2', 23, '2013-11-25 09:45:16'),
(31, 45, 'neiluJ2', 25, '2013-11-25 09:49:31'),
(32, 45, 'neiluJ2', 23, '2013-11-25 10:04:40'),
(33, 45, 'neiluJ2', 23, '2013-11-25 10:18:17'),
(34, 47, 'test', 23, '2013-11-25 10:34:20'),
(35, 47, 'test', 23, '2013-11-25 15:22:17'),
(36, 47, 'test', 23, '2013-11-25 15:56:28'),
(37, NULL, NULL, 25, '2013-11-25 16:24:28'),
(38, 47, 'test', 49, '2013-11-25 16:25:32'),
(39, 47, 'test', 49, '2013-11-25 16:26:16'),
(40, NULL, NULL, 47, '2013-11-25 16:34:52'),
(41, NULL, NULL, 47, '2013-11-25 17:01:59'),
(42, 47, 'test', 23, '2013-11-25 17:08:38'),
(43, 47, 'test', 23, '2013-12-07 22:24:46'),
(44, NULL, 'forgery', 25, '2013-12-08 00:12:52'),
(45, NULL, 'forgery', 25, '2013-12-08 00:16:17'),
(46, NULL, 'forgery', 25, '2013-12-08 00:18:02'),
(47, NULL, 'forgery', 25, '2013-12-08 00:18:26'),
(48, NULL, 'forgery', 25, '2013-12-08 00:20:29'),
(49, 47, 'test', 23, '2013-12-08 01:03:26');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=146 ;

--
-- Dumping data for table `refs`
--

INSERT INTO `refs` (`id`, `name`, `type`, `fullname`, `repositoryId`, `pushId`, `createdOn`, `commitHash`) VALUES
(142, 'master', 'branch', 'refs/heads/master', 23, 32, '2013-12-02 11:00:55', '777ffd15548e724a3b5d5323f717a18eb7b8da9c'),
(143, 'master', 'branch', 'refs/heads/master', 25, 31, '2013-11-25 09:35:11', 'cd5d3c1ea7cb09521462e8759ffb5b31d1eb3621'),
(144, 'master', 'branch', 'refs/heads/master', 49, 38, '2013-11-25 16:25:32', '9b95d1422401ba92b7f079a62791113279927a4e'),
(145, 'master', 'branch', 'refs/heads/master', 47, 40, '2013-11-25 16:34:52', '12fdd520d64a2afef7988f8e233e9aa88fd218d3');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=70 ;

--
-- Dumping data for table `repositories`
--

INSERT INTO `repositories` (`id`, `owner_id`, `type`, `public`, `parent_id`, `name`, `fullname`, `description`, `website`, `path`, `default_branch`, `created_at`, `last_commit_date`, `last_commit_hash`, `last_commit_author`, `last_commit_msg`, `watchers`, `forks`, `languages`) VALUES
(3, 45, 'private', 1, NULL, 'Core', 'neiluJ2/Core', 'ohyeahPwhat', NULL, 'neiluJ2/Core.git', 'master', '2013-04-10 23:54:56', NULL, NULL, NULL, NULL, 0, 0, NULL),
(23, 45, 'public', 1, NULL, 'forgery', 'neiluJ2/forgery', 'Forgery main repository', 'http://nitronet.org/forgery', 'neiluJ2/forgery.git', 'master', '2013-04-26 02:58:23', '2013-12-10 15:50:18', '83e6cb4569fce98e3b5e40308182b92b6e7dc43a', 'neiluj', 'added static activity database docs', 0, 0, NULL),
(25, NULL, 'repository', 0, NULL, 'gitolite-admin', 'gitolite-admin', 'Gitolite Admin Repository', NULL, 'gitolite-admin.git', 'master', '2013-11-12 17:08:27', '2013-12-07 23:20:23', '5e9d8b5784ca9267c28992e73210fe130ebb0e9f', 'forgery', 'created fork of test/test-activity to boulet/test-...', 0, 0, NULL),
(26, NULL, 'repository', 0, NULL, 'testing', 'testing', 'Test Repository', NULL, 'testing.git', 'master', '2013-11-14 10:32:01', '2013-11-14 17:45:31', '95970220d59168120532866902b7d66b6168a97c', 'neiluJ', 'ohyeah', 0, 0, NULL),
(46, 48, 'fork', 1, 23, 'forgery', 'boulet/forgery', 'Forgery main repository', NULL, 'boulet/forgery.git', 'master', '2013-11-19 17:05:45', '2013-11-20 10:18:58', '0035b59a28e73f411f77fe7061a1be730e9351ac', 'neiluj', 'fixed push options', 0, 0, NULL),
(47, 47, 'repository', 1, NULL, 'simon', 'test/simon', 'test', NULL, 'test/simon.git', 'master', '2013-11-20 00:00:16', '2013-12-03 16:44:10', '4a6dfceee5a605e415f541593eb6f233cc45184e', 'neiluJ', 'feoabazf', 0, 0, NULL),
(49, 47, 'repository', 1, NULL, 'test-activity', 'test/test-activity', 'Repository de test d&#39;activitÃ©', NULL, 'test/test-activity.git', 'master', '2013-11-25 16:24:19', '2013-12-03 16:08:25', '006932456c63615f025f5d0dab3045c48a1b5b93', 'neiluJ', 'ajout de test 2', 0, 0, NULL),
(53, 47, 'repository', 1, NULL, 'abc-def', 'test/abc-def', 'Repository de test activitÃ© statique', NULL, 'test/abc-def.git', 'master', '2013-12-08 00:18:20', NULL, NULL, NULL, NULL, 0, 0, NULL),
(54, 48, 'fork', 1, 49, 'test-activity', 'boulet/test-activity', 'Repository de test d&#39;activitÃ©', NULL, 'boulet/test-activity.git', 'master', '2013-12-08 00:20:22', '2013-12-03 16:08:25', '006932456c63615f025f5d0dab3045c48a1b5b93', 'neiluJ', 'ajout de test 2', 0, 0, NULL),
(60, 54, 'repository', 1, NULL, 'oh-yeah', 'zadzzadz/oh-yeah', '', NULL, 'zadzzadz/oh-yeah.git', 'master', '2014-06-24 23:38:22', NULL, NULL, NULL, NULL, 0, 0, NULL),
(61, 55, 'repository', 0, NULL, 'sinoe', 'dsinp-web/sinoe', 'SiNoÃ‰ - Simplifions Nos Ã‰changes', NULL, 'dsinp-web/sinoe.git', 'master', '2014-06-24 23:49:42', NULL, NULL, NULL, NULL, 0, 0, NULL),
(64, 55, 'repository', 0, NULL, 'enoma', 'dsinp-web/enoma', 'Ã‰NoMa - Ã‰laborons Nos MarchÃ©s', NULL, 'dsinp-web/enoma.git', 'master', '2014-06-24 23:52:27', NULL, NULL, NULL, NULL, 0, 0, NULL),
(66, 55, 'repository', 0, NULL, 'sinoe-backoffice', 'dsinp-web/sinoe-backoffice', 'SiNoÃ‰ - Back Office SiNoE', NULL, 'dsinp-web/sinoe-backoffice.git', 'master', '2014-06-24 23:56:29', NULL, NULL, NULL, NULL, 0, 0, NULL),
(67, 56, 'repository', 0, NULL, 'ecommerce', 'ecommerce/ecommerce', 'eCommerce - Application Java', 'http://www.ugap.fr', 'ecommerce/ecommerce.git', 'master', '2014-06-25 00:09:22', NULL, NULL, NULL, NULL, 0, 0, NULL),
(68, 56, 'repository', 0, NULL, 'backoffice', 'ecommerce/backoffice', 'eCommerce - BackOffice (WordPress)', NULL, 'ecommerce/backoffice.git', 'master', '2014-06-25 00:09:58', NULL, NULL, NULL, NULL, 0, 0, NULL),
(69, 56, 'repository', 0, NULL, 'batch-stats', 'ecommerce/batch-stats', 'eCommerce v3 - Script PHP pour Espace client > Etats Comptables', NULL, 'ecommerce/batch-stats.git', 'master', '2014-06-25 00:12:15', NULL, NULL, NULL, NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `username` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `password` blob,
  `http_password` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_registration` datetime NOT NULL,
  `date_activation` datetime DEFAULT NULL,
  `hash` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `fullname` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `email` (`email`),
  KEY `active` (`active`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=60 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `type`, `username`, `slug`, `password`, `http_password`, `email`, `date_registration`, `date_activation`, `hash`, `active`, `fullname`) VALUES
(45, 'user', 'neiluJ2', 'neiluJ2', 0x4212262fc9b03ad1cdfb9f5a9452220ed6d652f158903599b55ffdfa555c79b38d3d31616c85fd3fd59c819f0777c9c9953ddf617f22b79f8f1ec410fb0ef75f6f7a5034ecc632c782fed64b065d7ffad238221bbaf98896903757912528e7ef57acb636d637fa46641c3739d32bfd088be1b7902af7837db9e161d4394145b3b8362161434abeb46c56fbb92df583e4d9ede15c6632f1ce422e28179a67790089da50713a107b3d18a278e7038e89b73fb6511873ab98750558400a138fc576b0e48f5013eef1ed23d1d3511ed6ae43a67e0604b4cc9fa566a9b242836093dc878b6326571863ce80d12e68d854d13f5593020610091b52a9edee00e5db7adc, NULL, 'julien@nitronet.org', '2013-02-04 00:00:26', NULL, NULL, 1, 'Julien Ballestracci'),
(46, 'user', 'pwet', 'pwet', '', NULL, 'pwet@nitronet.org', '2013-11-05 14:08:31', NULL, NULL, 1, 'PwetPwet Enbois'),
(47, 'user', 'test', 'test', 0xe7d565cf687ee3ee4e5eb7dbcb7f653c2fc5cb870aedec8d3226dd0487bbee55567178d68d649d0f0bba88ef4bfb79876a354f9e3f66ca1edb0e62eddb046fbc847ecb1e47793b839ae63adfc635a3e4ee29a13388847acfa9018839fb96e68cba0d2641016893c5befb695271f37f335bce6c6d11028498590c161f96dde51eb9e368df52dac7d97c87bbdc66948053703bc0d3dc7ada2ccd47766e11baeaf95b03596673a6584c394082db14856f11f15ef9680300ff710c6ae25b2577c2e06a26b98d9f669228fe8f16cd236275cdd3ade5cd3022922328ff9222c6dba4885b7bc1797b3587ed2c4b8186daeb6cba960a2970593c8418e4f472d8b99c2003, '$apr1$yP54OaSW$njpnqmYG8oGceVMTJDxhu0', 'test@example.com', '2013-11-15 17:28:46', NULL, NULL, 1, 'Testeur Testeur'),
(48, 'user', 'boulet', 'boulet', 0xc7d0371612836f4c9417700600fcd61311c02de04d33489444449ce0d1220eb58cf5cc9e182bc682a4c2cb8b0f0b8e7617d7f3c344999951df211328e82f26c5811f7348733e73e9fd7b36ff7dafa53f3b6857c4cc16d86fbdaabc45dbc69db16aceafadbebd8185c4f9ed4916b14497a6a6010e1ee23c66fd79bdf580961c776c5be16cf455f3119f60c42f224d6504047e84afa50675b7c1c06dc8477450586b45e6d4b4ba1375f4768c5f1172f3b0a7d67c260d2bc9bd40d775296e1c3929262a6d83993821c9b1c8449f23a74250b03df12b9baa55f0c9435e31571a8fc9cade5eb1de4ce50b38f479fab2994f27d440957741226575ba9819c9f801902a, '$apr1$04yS.ZZo$TYiYbhFFmsazrAa752Ack1', 'boulet@nitronet.org', '2013-11-18 17:23:06', NULL, NULL, 1, 'Joe Boulet'),
(49, 'user', 'rolified', 'rolified', 0xbdc20b2465c530d65f77568dc88d39721d379320f0f7c9db2c81b469d23c1b5a88a68ee5443f439eb7463d0567dcb143b3995607e32350eb74faa43d846d71d91d5234dee07c257870ef162d4a48366c134f1773a7dfb5d9a3e69cd3491d081947699c585efc72fecd983bd1edff0ecc19a0651b87785fbd8c8e17d573f8989ef98c6cbc4d9bb62edbd48a09da7a767d07b5ac064505d885bffbf05bf223b4cbfb2948cd9659341dd6e6c1cfef5c89bd7bbaf86a3f4976d73369936bd1d607c9ee10280d56b78566c2b8644d878f42bb3cd85f70257db3d92cbbc4b85ba3708b0039607b26e2f606dd3cafd365f3d94a93b5a3d0ade32664e5adacec9d57b38b, '$apr1$7qvf7J/B$I7x1bZ9fPgyMcfo0bzh0s/', 'role@nitronet.org', '2013-11-26 16:19:21', NULL, NULL, 1, NULL),
(51, 'user', 'dza', 'dza', 0x35db56e3e2fa16e036f4b2710b6a4e675ce5e09f844d52c8cc633d3d7ba305b5d198248b3c886f1a48a9854f53dae36aaab79cc18eecdaaabd7ac4b3f1114895c23ae61b22eb3a2b568995535cf209c7a20fc7e4ebb9817395b8b3133fc40a495a3656f26debe45911725b3cafa32bd3c8e6e11a42cc18392f31e1f27920af8e2b7e4b66a4e9d7267510b21fa47ce68c5cb79ab920fcb3366bbb2489594a0ac67b91fea050870ee0f4ccbcd7c68213cba67dcada1cb2eddfae34afb2ac87bef27150040049db909720d353a03ff3e649e4403f528548f5cf8cdc462b6a93db365228f62bc5feb5c78943003aacebb4ad9fb8c889d36e79b7be22435c76256994, '$apr1$F0CoS8oR$TfwTbm6Jsfk4vITxT40w6/', 'dza@nitronet.org', '2013-11-26 16:39:33', NULL, NULL, 1, NULL),
(52, 'user', 'JBallestracci', 'jballestracci', 0x61b98600601830d571431b68a4ea6e0606362816fe0c4b937a715ccf068c46f20cdb18386754b0aea2635973c5a4e50d536671c19dde613bfb4160af59251e2bc33da2a513f7b7aec59404a555411d6fd4e35a70330450a5819cf68ef68df13b6af6c230300437825e2346bf68135ad136005ab7cda328523aa541f0b8f1fab7524b8f5ec1c39d3072138f9f215babf3edf0bf088296c6fdf090022ec8bef11fc5a5c3385e57f7f0ac75c9610a571e5ced56bafb3b26fb17332b00df15a4d331ac5d1756c2136016c5ae8cb00c8571d87c6c85099708786163d6df5c77d23b17d47b2a73e36e5754ca2e2025d3b016f82758ccd25fc9741bb4817dd57ec266eb, '$apr1$b/YLJXkZ$E2Y0CxF2ixJ21z4BQUWdL/', 'jballestracci@ugap.fr', '2014-01-24 13:16:45', NULL, NULL, 1, NULL),
(54, 'organization', 'zadzzadz', 'zadzzadz', NULL, NULL, 'joe@org.dev', '2014-06-23 20:04:45', NULL, NULL, 1, 'Organization Description'),
(55, 'organization', 'dsinp-web', 'dsinp-web', NULL, NULL, 'dsinp@ugap.dev', '2014-06-24 23:43:52', NULL, NULL, 1, 'IngÃ©nierie Nouveaux-Projets - PÃ´le Web'),
(56, 'organization', 'ecommerce', 'ecommerce', NULL, NULL, 'contact@ugap.dev', '2014-06-25 00:07:11', NULL, NULL, 1, 'eCommerce - DSI-NP-WEB'),
(57, 'user', 'test2', 'test2', 0xadaa4cc4c253713e475a8170d64d07e447e62cdabb3dce58455f97c7a909e5c3b8294d6c42c53108340f786a26df211340128370b3a28de97c524c3c844ad11fbc4707ea6e3a7d3ef89d1d6ceee65273630f4b277caa9f632cc30495f1d8180f8e7b088046dd28280703f0a3bad8a64e9e7728384ecb4491b8ee96a989beef7e087e2cda2082bb4ceebca268720b4983959f67b3c63d9e0b33f58e1ca1a125e824f492b69025c6b3311c2bd1dbd1b1746277cfbc1196d0eb4f63dd566f916344e18e7d098f9978343745caa30ecf3f0a7efcb559d146f4d0f1b24f35e18fe7118044a1bc7348214e227e494c0c97447e7aa3d38bcbe140eb275776083797ca72, '$apr1$6aF1/LAv$17lq88mMf28kNl8n6Jl4O0', 'test@nitronet.org', '2014-06-26 22:54:07', NULL, NULL, 1, NULL),
(58, 'organization', 'pwweeet', 'pwweeet', NULL, NULL, NULL, '2014-06-27 00:44:49', NULL, NULL, 1, 'The Pwet Org'),
(59, 'user', 'admin', 'admin', 0xbf3cb2cf1f7fb33fea5cd70826984f1b5bf104e713f718f0a7b98fcbc2dab3c17eb9b414cdeb4d9b004c6ae8870a9767b6b403f5cdac9e4032c6e10b5a8992e14eec1d0e626eb6aa285822b7ab5bbba28c349a33590441bc5e2f584bb5139c9aeda5ce82752ad8c9948a71cf61c5706559434af1aa7c5792c6f233ceae9575c454f8fff6b2f302c112c28eef4e1ef076a1a57b97967a7d9f5e98f21dbf86a60da27a57a339a6a212c0dc811b690c93145fe90ecf54f77b27502e83aff15e79a663077809fde3ee3400e10a1846cf91c85e23ff1b49639ef444dd5362abc617cc1d04b94e29e897ea96a4131796fd16560359c25bc84a98943eb099eab2cafb1e, '$apr1$YITubfdf$0SH.ET4t2du5yhWzarUmk.', 'admin@nitronet.org', '2014-12-04 01:22:55', NULL, NULL, 1, NULL);

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
(47, 'repo_create'),
(48, 'repo_create'),
(51, 'repo_create'),
(52, 'repo_create'),
(57, 'repo_create'),
(59, 'repo_create'),
(47, 'root'),
(59, 'root'),
(47, 'staff'),
(51, 'staff'),
(52, 'staff'),
(59, 'staff'),
(45, 'user'),
(47, 'user'),
(48, 'user'),
(51, 'user'),
(52, 'user'),
(57, 'user'),
(59, 'user');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21 ;

--
-- Dumping data for table `users_ssh_keys`
--

INSERT INTO `users_ssh_keys` (`id`, `user_id`, `title`, `contents`, `hash`, `created_on`) VALUES
(13, 45, 'homy', 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCgy+FOMWscmMa5Htray1vKXVeUg87gwpOxr8eardH+3Q05nJGC3RRJuqH6Iw8MWdVO57nccRv1uul3fZPn/YI9XaLubPNjWVLxEMkR3gAm88RYCvY2w/HGWUmfM3/MUBIL61fKPyB1e4con4m/Y8YrCtXsu2Lg2L38F7yCSMpEeL7Fj3rRYqc41YZvYMCVbYig/KdrqlasnmMVFSExARCBHaOy7+L73VpFR8qsrna58TUEa5WVoDdJLlacHQX/1snCQVSBlX47kmKxoILhRXILIvzExJJt6Us4S1f7vYCoii357mx8NlKKbCXzrFmVKPamAMcRBcp6fS0/zxANRi11 neiluj@homer', '64881002ec2f71f96292c5d27f01fd1a', '2013-04-07 23:29:29'),
(18, 47, 'default', 'ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEArhOxg5cJCaMtZyVfcDPccoJkc9tf4F19wDVijIPyBZPupnr2N1dF2Jqx2zPko6mdt2rHVzv1fKwS/CTKEdd4DXY1v74viWFQROwSx3ykwQC4+xXliAMXtP0kKK+AwG4WjLQY/qUwQ47PXDLfkCCDhDIbQNfRdcBO3ZEu9swnKckJi3TgEGFiUb7BhGqLnzNi4OkmZ/B7pHy2z7UdTZTI9LgpuDSIua9VnTmlZl6/avkBCIKIZb1n59zhGbUt2L4Vrv4Bj+LDSCq94RuT91FKKaaPgCAGgVPjji4DUMfbBb4CgoJqE0PUjRHEsjE8DXOHeciKZ/5jXwbAjyUOVvQ6Ew== test@example.com', '1f04d00ee6af240fa208a6dd7f0b356b', '2013-11-17 00:55:56'),
(20, 47, 'default2', 'ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAdQEArhOxg5cJCaMtZyVfcDPccoJkc9tf4F19wDVijIPyBZPupnr2N1dF2Jqx2zPko6mdt2rHVzv1fKwS/CTKEdd4DXY1v74viWFQROwSx3ykwQC4+xXliAMXtP0kKK+AwG4WjLQY/qUwQ47PXDLfkCCDhDIbQNfRdcBO3ZEu9swnKckJi3TgEGFiUb7BhGqLnzNi4OkmZ/B7pHy2z7UdTZTI9LgpuDSIua9VnTmlZl6/avkBCIKIZb1n59zhGbUt2L4Vrv4Bj+LDSCq94RuT91FKKaaPgCAGgVPjji4DUMfbBb4CgoJqE0PUjRHEsjE8DXOHeciKZ/5jXwbAjyUOVvQ6Ew== test@example.com', 'ac7e54f9782a9ef9246276412a6053a0', '2013-11-17 17:30:52');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accesses`
--
ALTER TABLE `accesses`
  ADD CONSTRAINT `users_id_fk1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `repos_fk_id` FOREIGN KEY (`repository_id`) REFERENCES `repositories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`parentId`) REFERENCES `comments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`thread`) REFERENCES `comments_threads` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`authorId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_4` FOREIGN KEY (`repositoryId`) REFERENCES `repositories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `org_users`
--
ALTER TABLE `org_users`
  ADD CONSTRAINT `addedby_id_fk` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `org_id_fk` FOREIGN KEY (`organization_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `parent_repo_fk` FOREIGN KEY (`parent_id`) REFERENCES `repositories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `repositories_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;