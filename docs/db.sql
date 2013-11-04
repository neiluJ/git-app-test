-- phpMyAdmin SQL Dump
-- version 3.4.8
-- http://www.phpmyadmin.net
--
-- Client: 127.0.0.1
-- Généré le : Mar 05 Novembre 2013 à 00:38
-- Version du serveur: 5.5.34
-- Version de PHP: 5.3.10-1ubuntu3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données: `forgery`
--

-- --------------------------------------------------------

--
-- Structure de la table `accesses`
--

CREATE TABLE IF NOT EXISTS `accesses` (
  `user_id` int(11) NOT NULL,
  `repository_id` int(11) NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `write` tinyint(1) NOT NULL DEFAULT '0',
  `special` tinyint(1) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`repository_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `acls_permissions`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Contenu de la table `acls_permissions`
--

INSERT INTO `acls_permissions` (`id`, `role`, `resource`, `type`, `permission`) VALUES
(2, 'user', NULL, 'allow', 'view');

-- --------------------------------------------------------

--
-- Structure de la table `acls_resources`
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

-- --------------------------------------------------------

--
-- Structure de la table `acls_roles`
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
-- Contenu de la table `acls_roles`
--

INSERT INTO `acls_roles` (`role`, `description`, `parent`, `sort`, `default`) VALUES
('email_not_verified', 'tells a user haven''t verified his email address yet', NULL, 99, 1),
('guest', 'Guest role', NULL, 0, 0),
('root', 'Administrator', NULL, 3, 0),
('staff', 'Staff role', 'user', 2, 0),
('user', 'Logged in user role', 'guest', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `repositories`
--

CREATE TABLE IF NOT EXISTS `repositories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'repository',
  `public` tinyint(1) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=25 ;

--
-- Contenu de la table `repositories`
--

INSERT INTO `repositories` (`id`, `owner_id`, `type`, `public`, `parent_id`, `name`, `description`, `path`, `created_at`, `last_commit_date`, `last_commit_hash`, `last_commit_author`, `last_commit_msg`, `watchers`, `forks`, `languages`) VALUES
(3, 45, 'private', 1, NULL, 'Core', 'ohyeah', 'neiluJ2/Core.git', '2013-04-10 23:54:56', NULL, NULL, NULL, NULL, 0, 0, NULL),
(23, 45, 'public', 1, NULL, 'forgery', 'Forgery main repository', 'neiluJ2/forgery.git', '2013-04-26 02:58:23', NULL, NULL, NULL, NULL, 0, 0, NULL),
(24, 45, 'repository', 1, NULL, 'symfony', 'Symfony fork', 'neiluJ2/symfony.git', '2013-05-02 00:37:10', NULL, NULL, NULL, NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `password` blob NOT NULL,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=46 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `username`, `slug`, `password`, `email`, `date_registration`, `date_activation`, `hash`, `active`, `fullname`) VALUES
(45, 'neiluJ2', 'neiluJ2', 0x4212262fc9b03ad1cdfb9f5a9452220ed6d652f158903599b55ffdfa555c79b38d3d31616c85fd3fd59c819f0777c9c9953ddf617f22b79f8f1ec410fb0ef75f6f7a5034ecc632c782fed64b065d7ffad238221bbaf98896903757912528e7ef57acb636d637fa46641c3739d32bfd088be1b7902af7837db9e161d4394145b3b8362161434abeb46c56fbb92df583e4d9ede15c6632f1ce422e28179a67790089da50713a107b3d18a278e7038e89b73fb6511873ab98750558400a138fc576b0e48f5013eef1ed23d1d3511ed6ae43a67e0604b4cc9fa566a9b242836093dc878b6326571863ce80d12e68d854d13f5593020610091b52a9edee00e5db7adc, 'julien@nitronet.org', '2013-02-04 00:00:26', NULL, NULL, 1, 'Julien Ballestracci');

-- --------------------------------------------------------

--
-- Structure de la table `users_roles`
--

CREATE TABLE IF NOT EXISTS `users_roles` (
  `user_id` int(11) NOT NULL,
  `role` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`,`role`),
  KEY `role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `users_roles`
--

INSERT INTO `users_roles` (`user_id`, `role`) VALUES
(45, 'email_not_verified'),
(45, 'user');

-- --------------------------------------------------------

--
-- Structure de la table `users_ssh_keys`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;

--
-- Contenu de la table `users_ssh_keys`
--

INSERT INTO `users_ssh_keys` (`id`, `user_id`, `title`, `contents`, `hash`, `created_on`) VALUES
(13, 45, 'homy', 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCgy+FOMWscmMa5Htray1vKXVeUg87gwpOxr8eardH+3Q05nJGC3RRJuqH6Iw8MWdVO57nccRv1uul3fZPn/YI9XaLubPNjWVLxEMkR3gAm88RYCvY2w/HGWUmfM3/MUBIL61fKPyB1e4con4m/Y8YrCtXsu2Lg2L38F7yCSMpEeL7Fj3rRYqc41YZvYMCVbYig/KdrqlasnmMVFSExARCBHaOy7+L73VpFR8qsrna58TUEa5WVoDdJLlacHQX/1snCQVSBlX47kmKxoILhRXILIvzExJJt6Us4S1f7vYCoii357mx8NlKKbCXzrFmVKPamAMcRBcp6fS0/zxANRi11 neiluj@homer', '64881002ec2f71f96292c5d27f01fd1a', '2013-04-07 23:29:29');

--
-- Contraintes pour les tables exportées
--

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
-- Contraintes pour la table `repositories`
--
ALTER TABLE `repositories`
  ADD CONSTRAINT `repositories_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `repositories_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `repositories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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
