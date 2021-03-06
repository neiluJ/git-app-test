-- phpMyAdmin SQL Dump
-- version 3.5.6
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Lun 08 Décembre 2014 à 14:17
-- Version du serveur: 5.5.40-0ubuntu0.14.04.1
-- Version de PHP: 5.5.9-1ubuntu4.5

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `forgery-new`
--

-- --------------------------------------------------------

--
-- Structure de la table `accesses`
--

CREATE TABLE `accesses` (
  `user_id` int(11) NOT NULL,
  `repository_id` int(11) NOT NULL,
  `readAccess` tinyint(1) NOT NULL DEFAULT '0',
  `writeAccess` tinyint(1) NOT NULL DEFAULT '0',
  `specialAccess` tinyint(1) NOT NULL DEFAULT '0',
  `adminAccess` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`repository_id`),
  KEY `repository_id` (`repository_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `accesses`
--

INSERT INTO `accesses` (`user_id`, `repository_id`, `readAccess`, `writeAccess`, `specialAccess`, `adminAccess`) VALUES
(1, 1, 1, 1, 1, 1),
(1, 2, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `acls_permissions`
--

CREATE TABLE `acls_permissions` (
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
-- Contenu de la table `acls_permissions`
--

INSERT INTO `acls_permissions` (`id`, `role`, `resource`, `type`, `permission`) VALUES
(2, 'guest', NULL, 'deny', 'view'),
(3, 'repo_create', 'repository', 'allow', 'create'),
(4, 'root', NULL, 'allow', NULL),
(5, 'staff', 'users', 'allow', NULL),
(6, 'staff', 'user', 'allow', 'edit'),
(7, 'staff', 'organizations', 'allow', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `acls_resources`
--

CREATE TABLE `acls_resources` (
  `resource` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`resource`),
  KEY `parent` (`parent`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `acls_resources`
--

INSERT INTO `acls_resources` (`resource`, `description`, `parent`, `sort`) VALUES
('organizations', 'Organizations resource', NULL, 0),
('repository', 'Repository resource', NULL, 0),
('user', 'User resource', NULL, 0),
('users', 'Users resource', NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `acls_roles`
--

CREATE TABLE `acls_roles` (
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
-- Contenu de la table `acls_roles`
--

INSERT INTO `acls_roles` (`role`, `description`, `parent`, `sort`, `default`) VALUES
('guest', 'Guest role', NULL, 0, 0),
('repo_create', 'Create and Delete repositories', NULL, 0, 0),
('root', 'Administrator', NULL, 3, 0),
('staff', 'Staff role', 'user', 2, 0),
('user', 'Logged in user role', 'guest', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `activities`
--

CREATE TABLE `activities` (
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
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentId` int(11) DEFAULT NULL,
  `thread` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `createdOn` datetime NOT NULL,
  `contents` text COLLATE utf8_unicode_ci NOT NULL,
  `authorName` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `authorEmail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `authorUrl` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parentId` (`parentId`),
  KEY `thread` (`thread`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `comments_threads`
--

CREATE TABLE `comments_threads` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `open` tinyint(1) NOT NULL DEFAULT '1',
  `comments` int(11) NOT NULL DEFAULT '0',
  `createdOn` datetime NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `commits`
--

CREATE TABLE `commits` (
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
-- Structure de la table `commits_refs`
--

CREATE TABLE `commits_refs` (
  `commitId` int(11) NOT NULL,
  `refId` int(11) NOT NULL,
  PRIMARY KEY (`commitId`,`refId`),
  KEY `refId` (`refId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `authorId` int(11) DEFAULT NULL,
  `authorName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `repositoryId` int(11) DEFAULT NULL,
  `repositoryName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `text` text COLLATE utf8_unicode_ci,
  `target` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `createdOn` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `channel` (`channel`),
  KEY `authorId` (`authorId`),
  KEY `repositoryId` (`repositoryId`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notifications_users`
--

CREATE TABLE `notifications_users` (
  `notificationId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `dateRead` datetime DEFAULT NULL,
  PRIMARY KEY (`notificationId`,`userId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `org_users`
--

CREATE TABLE `org_users` (
  `user_id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `added_by` int(11) NOT NULL,
  `reposWriteAccess` tinyint(1) NOT NULL DEFAULT '0',
  `reposAdminAccess` tinyint(1) NOT NULL DEFAULT '0',
  `membersAdminAccess` tinyint(1) NOT NULL DEFAULT '0',
  `adminAccess` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`organization_id`),
  KEY `organization_id` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pushes`
--

CREATE TABLE `pushes` (
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
-- Structure de la table `refs`
--

CREATE TABLE `refs` (
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
-- Structure de la table `repositories`
--

CREATE TABLE `repositories` (
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
-- Contenu de la table `repositories`
--

INSERT INTO `repositories` (`id`, `owner_id`, `type`, `public`, `parent_id`, `name`, `fullname`, `description`, `website`, `path`, `default_branch`, `created_at`, `last_commit_date`, `last_commit_hash`, `last_commit_author`, `last_commit_msg`, `watchers`, `forks`, `languages`) VALUES
(1, 1, 'repository', 0, NULL, 'gitolite-admin', 'admin/gitolite-admin', 'Gitolite Admin Repository', NULL, 'gitolite-admin.git', 'master', '2013-11-12 17:08:27', '2014-12-08 11:33:25', '9d4fd6ac3af6f8172a798b95dee79f768c19939e', 'git on gitmachine', 'gitolite setup -pk git.pub', 0, 0, NULL),
(2, 1, 'repository', 0, NULL, 'testing', 'admin/testing', 'Test Repository', NULL, 'testing.git', 'master', '2013-11-14 10:32:01', '2013-11-14 17:45:31', '95970220d59168120532866902b7d66b6168a97c', 'neiluJ', 'ohyeah', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `type`, `username`, `slug`, `password`, `http_password`, `email`, `date_registration`, `date_activation`, `hash`, `active`, `fullname`) VALUES
(1, 'user', 'admin', 'admin', 0xbf3cb2cf1f7fb33fea5cd70826984f1b5bf104e713f718f0a7b98fcbc2dab3c17eb9b414cdeb4d9b004c6ae8870a9767b6b403f5cdac9e4032c6e10b5a8992e14eec1d0e626eb6aa285822b7ab5bbba28c349a33590441bc5e2f584bb5139c9aeda5ce82752ad8c9948a71cf61c5706559434af1aa7c5792c6f233ceae9575c454f8fff6b2f302c112c28eef4e1ef076a1a57b97967a7d9f5e98f21dbf86a60da27a57a339a6a212c0dc811b690c93145fe90ecf54f77b27502e83aff15e79a663077809fde3ee3400e10a1846cf91c85e23ff1b49639ef444dd5362abc617cc1d04b94e29e897ea96a4131796fd16560359c25bc84a98943eb099eab2cafb1e, '$apr1$YITubfdf$0SH.ET4t2du5yhWzarUmk.', 'admin@nitronet.org', '2014-12-04 01:22:55', NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users_roles`
--

CREATE TABLE `users_roles` (
  `user_id` int(11) NOT NULL,
  `role` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`,`role`),
  KEY `role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `users_roles`
--

INSERT INTO `users_roles` (`user_id`, `role`) VALUES
(1, 'repo_create'),
(1, 'root'),
(1, 'staff'),
(1, 'user');

-- --------------------------------------------------------

--
-- Structure de la table `users_ssh_keys`
--

CREATE TABLE `users_ssh_keys` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `accesses`
--
ALTER TABLE `accesses`
  ADD CONSTRAINT `accesses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `accesses_ibfk_2` FOREIGN KEY (`repository_id`) REFERENCES `repositories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `acls_permissions`
--
ALTER TABLE `acls_permissions`
  ADD CONSTRAINT `acls_permissions_ibfk_1` FOREIGN KEY (`role`) REFERENCES `acls_roles` (`role`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `acls_permissions_ibfk_2` FOREIGN KEY (`resource`) REFERENCES `acls_resources` (`resource`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `acls_resources`
--
ALTER TABLE `acls_resources`
  ADD CONSTRAINT `acls_resources_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `acls_resources` (`resource`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `acls_roles`
--
ALTER TABLE `acls_roles`
  ADD CONSTRAINT `acls_roles_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `acls_roles` (`role`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `activities_ibfk_2` FOREIGN KEY (`repositoryId`) REFERENCES `repositories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `activities_ibfk_3` FOREIGN KEY (`targetId`) REFERENCES `repositories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`parentId`) REFERENCES `comments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`thread`) REFERENCES `comments_threads` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commits`
--
ALTER TABLE `commits`
  ADD CONSTRAINT `commits_ibfk_1` FOREIGN KEY (`pushId`) REFERENCES `pushes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commits_ibfk_2` FOREIGN KEY (`authorId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `commits_ibfk_3` FOREIGN KEY (`committerId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `commits_ibfk_4` FOREIGN KEY (`repositoryId`) REFERENCES `repositories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commits_refs`
--
ALTER TABLE `commits_refs`
  ADD CONSTRAINT `commits_refs_ibfk_1` FOREIGN KEY (`commitId`) REFERENCES `commits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commits_refs_ibfk_2` FOREIGN KEY (`refId`) REFERENCES `refs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`authorId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`repositoryId`) REFERENCES `repositories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `notifications_users`
--
ALTER TABLE `notifications_users`
  ADD CONSTRAINT `notifications_users_ibfk_1` FOREIGN KEY (`notificationId`) REFERENCES `notifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_users_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `org_users`
--
ALTER TABLE `org_users`
  ADD CONSTRAINT `org_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `org_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `org_users_ibfk_3` FOREIGN KEY (`organization_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `pushes`
--
ALTER TABLE `pushes`
  ADD CONSTRAINT `pushes_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pushes_ibfk_2` FOREIGN KEY (`repositoryId`) REFERENCES `repositories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `refs`
--
ALTER TABLE `refs`
  ADD CONSTRAINT `refs_ibfk_1` FOREIGN KEY (`repositoryId`) REFERENCES `repositories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `refs_ibfk_2` FOREIGN KEY (`pushId`) REFERENCES `pushes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `repositories`
--
ALTER TABLE `repositories`
  ADD CONSTRAINT `repositories_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `repositories_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `repositories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `users_roles`
--
ALTER TABLE `users_roles`
  ADD CONSTRAINT `users_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_roles_ibfk_2` FOREIGN KEY (`role`) REFERENCES `acls_roles` (`role`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `users_ssh_keys`
--
ALTER TABLE `users_ssh_keys`
  ADD CONSTRAINT `users_ssh_keys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;