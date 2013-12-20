-- phpMyAdmin SQL Dump
-- version 3.5.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 29, 2013 at 04:43 PM
-- Server version: 5.5.32-0ubuntu0.12.10.1
-- PHP Version: 5.4.6-1ubuntu1.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `forgery`
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

--
-- Dumping data for table `accesses`
--

INSERT INTO `accesses` (`user_id`, `repository_id`, `readAccess`, `writeAccess`, `specialAccess`, `adminAccess`) VALUES
(45, 3, 1, 1, 1, 1),
(45, 23, 1, 1, 1, 1),
(45, 25, 1, 1, 1, 1),
(45, 45, 1, 0, 0, 0),
(46, 23, 1, 1, 0, 0),
(47, 23, 1, 1, 1, 0),
(47, 42, 1, 1, 1, 1),
(47, 44, 1, 1, 1, 1),
(47, 45, 1, 1, 1, 1),
(47, 47, 1, 1, 1, 1),
(47, 48, 1, 1, 1, 1),
(48, 46, 1, 1, 1, 1),
(48, 48, 1, 1, 1, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `acls_permissions`
--

INSERT INTO `acls_permissions` (`id`, `role`, `resource`, `type`, `permission`) VALUES
(2, 'guest', NULL, 'deny', 'view'),
(3, 'repo_create', 'repository', 'allow', 'create'),
(4, 'root', NULL, 'allow', NULL),
(5, 'staff', 'users', 'allow', NULL),
(6, 'staff', 'user', 'allow', 'edit');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=24 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=76 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=49 ;

--
-- Dumping data for table `repositories`
--

INSERT INTO `repositories` (`id`, `owner_id`, `type`, `public`, `parent_id`, `name`, `fullname`, `description`, `website`, `path`, `default_branch`, `created_at`, `last_commit_date`, `last_commit_hash`, `last_commit_author`, `last_commit_msg`, `watchers`, `forks`, `languages`) VALUES
(3, 45, 'private', 1, 23, 'Core', 'neiluJ2/Core', 'ohyeah', NULL, 'neiluJ2/Core.git', 'master', '2013-04-10 23:54:56', NULL, NULL, NULL, NULL, 0, 0, NULL),
(23, 45, 'public', 1, NULL, 'forgery', 'neiluJ2/forgery', 'Forgery main repository', NULL, 'neiluJ2/forgery.git', 'master', '2013-04-26 02:58:23', '2013-11-14 17:44:37', '2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d', 'neiluj', 'try to fix hooks...', 0, 0, NULL),
(25, NULL, 'repository', 0, NULL, 'gitolite-admin', 'gitolite-admin', 'Gitolite Admin Repository', NULL, 'gitolite-admin.git', 'master', '2013-11-12 17:08:27', '2013-11-23 08:46:32', 'e787e7c037e1b9247464e2a377d5fdc606162b5e', 'forgery', 'removed repository test/test-again', 0, 0, NULL),
(26, NULL, 'repository', 0, NULL, 'testing', 'testing', 'Test Repository', NULL, 'testing.git', 'master', '2013-11-14 10:32:01', '2013-11-14 17:45:31', '95970220d59168120532866902b7d66b6168a97c', 'neiluJ', 'ohyeah', 0, 0, NULL),
(45, 47, 'repository', 0, NULL, 'pour-le-fun', 'test/pour-le-fun', 'Repository pour le fun', 'http://nitronet.org', 'test/pour-le-fun.git', 'master', '2013-11-18 23:09:29', '2013-11-21 16:43:51', '7c736546ae9d9c91896f17c9072941fc3437ddb1', 'neiluJ', 'test no prefix', 0, 0, NULL),
(46, 48, 'fork', 0, 23, 'forgery', 'boulet/forgery', 'Forgery main repository', NULL, 'boulet/forgery.git', 'master', '2013-11-19 17:05:45', '2013-11-20 10:18:58', '0035b59a28e73f411f77fe7061a1be730e9351ac', 'neiluj', 'fixed push options', 0, 0, NULL),
(47, 47, 'repository', 1, NULL, 'simon', 'test/simon', 'test', NULL, 'test/simon.git', 'master', '2013-11-20 00:00:16', '2013-11-22 15:29:00', '12fdd520d64a2afef7988f8e233e9aa88fd218d3', 'neiluJ', 'test', 0, 0, NULL),
(48, 47, 'fork', 1, 23, 'forgery', 'test/forgery', 'Forgery main repository', NULL, 'test/forgery.git', 'master', '2013-11-21 09:08:40', '2013-11-20 10:18:58', '0035b59a28e73f411f77fe7061a1be730e9351ac', 'neiluj', 'fixed push options', 0, 0, NULL);

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
  KEY `active` (`active`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `slug`, `password`, `http_password`, `email`, `date_registration`, `date_activation`, `hash`, `active`, `fullname`) VALUES
(45, 'neiluJ2', 'neiluJ2', 0x4212262fc9b03ad1cdfb9f5a9452220ed6d652f158903599b55ffdfa555c79b38d3d31616c85fd3fd59c819f0777c9c9953ddf617f22b79f8f1ec410fb0ef75f6f7a5034ecc632c782fed64b065d7ffad238221bbaf98896903757912528e7ef57acb636d637fa46641c3739d32bfd088be1b7902af7837db9e161d4394145b3b8362161434abeb46c56fbb92df583e4d9ede15c6632f1ce422e28179a67790089da50713a107b3d18a278e7038e89b73fb6511873ab98750558400a138fc576b0e48f5013eef1ed23d1d3511ed6ae43a67e0604b4cc9fa566a9b242836093dc878b6326571863ce80d12e68d854d13f5593020610091b52a9edee00e5db7adc, NULL, 'julien@nitronet.org', '2013-02-04 00:00:26', NULL, NULL, 1, 'Julien Ballestracci'),
(46, 'pwet', 'pwet', '', NULL, 'pwet@nitronet.org', '2013-11-05 14:08:31', NULL, NULL, 1, 'PwetPwet Enbois'),
(47, 'test', 'test', 0xe7d565cf687ee3ee4e5eb7dbcb7f653c2fc5cb870aedec8d3226dd0487bbee55567178d68d649d0f0bba88ef4bfb79876a354f9e3f66ca1edb0e62eddb046fbc847ecb1e47793b839ae63adfc635a3e4ee29a13388847acfa9018839fb96e68cba0d2641016893c5befb695271f37f335bce6c6d11028498590c161f96dde51eb9e368df52dac7d97c87bbdc66948053703bc0d3dc7ada2ccd47766e11baeaf95b03596673a6584c394082db14856f11f15ef9680300ff710c6ae25b2577c2e06a26b98d9f669228fe8f16cd236275cdd3ade5cd3022922328ff9222c6dba4885b7bc1797b3587ed2c4b8186daeb6cba960a2970593c8418e4f472d8b99c2003, '$apr1$yP54OaSW$njpnqmYG8oGceVMTJDxhu0', 'test@example.com', '2013-11-15 17:28:46', NULL, NULL, 1, 'Testeur Testeur'),
(48, 'boulet', 'boulet', 0xc7d0371612836f4c9417700600fcd61311c02de04d33489444449ce0d1220eb58cf5cc9e182bc682a4c2cb8b0f0b8e7617d7f3c344999951df211328e82f26c5811f7348733e73e9fd7b36ff7dafa53f3b6857c4cc16d86fbdaabc45dbc69db16aceafadbebd8185c4f9ed4916b14497a6a6010e1ee23c66fd79bdf580961c776c5be16cf455f3119f60c42f224d6504047e84afa50675b7c1c06dc8477450586b45e6d4b4ba1375f4768c5f1172f3b0a7d67c260d2bc9bd40d775296e1c3929262a6d83993821c9b1c8449f23a74250b03df12b9baa55f0c9435e31571a8fc9cade5eb1de4ce50b38f479fab2994f27d440957741226575ba9819c9f801902a, '$apr1$04yS.ZZo$TYiYbhFFmsazrAa752Ack1', 'boulet@nitronet.org', '2013-11-18 17:23:06', NULL, NULL, 1, NULL),
(49, 'rolified', 'rolified', 0xbdc20b2465c530d65f77568dc88d39721d379320f0f7c9db2c81b469d23c1b5a88a68ee5443f439eb7463d0567dcb143b3995607e32350eb74faa43d846d71d91d5234dee07c257870ef162d4a48366c134f1773a7dfb5d9a3e69cd3491d081947699c585efc72fecd983bd1edff0ecc19a0651b87785fbd8c8e17d573f8989ef98c6cbc4d9bb62edbd48a09da7a767d07b5ac064505d885bffbf05bf223b4cbfb2948cd9659341dd6e6c1cfef5c89bd7bbaf86a3f4976d73369936bd1d607c9ee10280d56b78566c2b8644d878f42bb3cd85f70257db3d92cbbc4b85ba3708b0039607b26e2f606dd3cafd365f3d94a93b5a3d0ade32664e5adacec9d57b38b, '$apr1$7qvf7J/B$I7x1bZ9fPgyMcfo0bzh0s/', 'role@nitronet.org', '2013-11-26 16:19:21', NULL, NULL, 1, NULL),
(51, 'dza', 'dza', 0x35db56e3e2fa16e036f4b2710b6a4e675ce5e09f844d52c8cc633d3d7ba305b5d198248b3c886f1a48a9854f53dae36aaab79cc18eecdaaabd7ac4b3f1114895c23ae61b22eb3a2b568995535cf209c7a20fc7e4ebb9817395b8b3133fc40a495a3656f26debe45911725b3cafa32bd3c8e6e11a42cc18392f31e1f27920af8e2b7e4b66a4e9d7267510b21fa47ce68c5cb79ab920fcb3366bbb2489594a0ac67b91fea050870ee0f4ccbcd7c68213cba67dcada1cb2eddfae34afb2ac87bef27150040049db909720d353a03ff3e649e4403f528548f5cf8cdc462b6a93db365228f62bc5feb5c78943003aacebb4ad9fb8c889d36e79b7be22435c76256994, '$apr1$F0CoS8oR$TfwTbm6Jsfk4vITxT40w6/', 'dza@nitronet.org', '2013-11-26 16:39:33', NULL, NULL, 1, NULL);

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
(51, 'repo_create'),
(47, 'staff'),
(51, 'staff'),
(45, 'user'),
(47, 'user'),
(48, 'user'),
(51, 'user');

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
-- Constraints for table `commits`
--
ALTER TABLE `commits`
  ADD CONSTRAINT `commits_ibfk_4` FOREIGN KEY (`repositoryId`) REFERENCES `repositories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commits_ibfk_1` FOREIGN KEY (`pushId`) REFERENCES `pushes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commits_ibfk_2` FOREIGN KEY (`authorId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `commits_ibfk_3` FOREIGN KEY (`committerId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `commits_refs`
--
ALTER TABLE `commits_refs`
  ADD CONSTRAINT `commits_refs_ibfk_2` FOREIGN KEY (`refId`) REFERENCES `refs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commits_refs_ibfk_1` FOREIGN KEY (`commitId`) REFERENCES `commits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `refs_ibfk_2` FOREIGN KEY (`pushId`) REFERENCES `pushes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `refs_ibfk_1` FOREIGN KEY (`repositoryId`) REFERENCES `repositories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_3` FOREIGN KEY (`targetId`) REFERENCES `repositories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `activities_ibfk_2` FOREIGN KEY (`repositoryId`) REFERENCES `repositories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
