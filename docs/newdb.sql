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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=773 ;

--
-- Dumping data for table `commits`
--

INSERT INTO `commits` (`id`, `hash`, `repositoryId`, `pushId`, `authorName`, `authorEmail`, `authorDate`, `authorId`, `committerName`, `committerEmail`, `committerDate`, `committerId`, `message`, `indexDate`) VALUES
(709, '777ffd15548e724a3b5d5323f717a18eb7b8da9c', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-17 15:17:42', NULL, 0, 0, '2013-10-17 15:17:42', NULL, 'initial import\n', '2013-11-29 16:31:03'),
(710, '2c0e11cd29d7d08ee96bd16e81a555484f66da85', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-20 21:30:58', NULL, 0, 0, '2013-10-20 21:30:58', NULL, 'new viewHelper use', '2013-11-29 16:31:03'),
(711, '7ea3dcbe276a3f1a7d576c4920c59e7d619a1b16', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-10-21 16:00:23', NULL, 0, 0, '2013-10-21 16:00:23', NULL, '...', '2013-11-29 16:31:03'),
(712, 'c3ef96fd24a56c4303ecc94909de0927ee2c6749', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-10-21 16:51:19', NULL, 0, 0, '2013-10-21 16:51:19', NULL, '...', '2013-11-29 16:31:03'),
(713, '17cdd8f2254da00eebcc26f658ba0ce0bbad06ee', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-10-21 17:32:23', NULL, 0, 0, '2013-10-21 17:32:23', NULL, '...', '2013-11-29 16:31:03'),
(714, 'ae30f912291e83d14ef09f4edd8dfe2960685653', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-10-22 16:01:21', NULL, 0, 0, '2013-10-22 16:01:21', NULL, 'powah!', '2013-11-29 16:31:03'),
(715, '17e98e753ba7757cfd5179b5b6f86149b441b808', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-10-22 16:29:19', NULL, 0, 0, '2013-10-22 16:29:19', NULL, 'powah!', '2013-11-29 16:31:03'),
(716, 'a86c682a4688c915bb58aef484158fdee30736f6', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-22 20:31:43', NULL, 0, 0, '2013-10-22 20:31:43', NULL, 'better display', '2013-11-29 16:31:04'),
(717, '1c7b9a93279d6226cf31df8b580af1e66fcbc98c', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-22 20:32:01', NULL, 0, 0, '2013-10-22 20:32:01', NULL, 'better display', '2013-11-29 16:31:04'),
(718, '6b357f964748e14344ad3b67c736e3778d1a15ff', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-22 21:27:54', NULL, 0, 0, '2013-10-22 21:27:54', NULL, 'various fixes', '2013-11-29 16:31:04'),
(719, 'c8526fcc2b756f80223c9713ee770993cfc64b6d', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-22 21:57:12', NULL, 0, 0, '2013-10-22 21:57:12', NULL, 'fixes BlobRaw ', '2013-11-29 16:31:04'),
(720, '6853b8b032ba0024ab6865d48a853dd6dc180d90', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-10-24 16:35:13', NULL, 0, 0, '2013-10-24 16:35:13', NULL, 'better angular (still buggy)', '2013-11-29 16:31:04'),
(721, 'bed4f0ead5b7049ca4e369f4082170e6a883c176', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-24 21:58:02', NULL, 0, 0, '2013-10-24 21:58:02', NULL, 'rebuild navigation', '2013-11-29 16:31:04'),
(722, '68ef92a366c6930f425ac83749169239c19bfd4c', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-24 22:04:46', NULL, 0, 0, '2013-10-24 22:04:46', NULL, 'rebuild navigation', '2013-11-29 16:31:04'),
(723, 'aa4501b1c4fd0d97d20f648304cc23f14f1d2d6a', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-25 00:09:37', NULL, 0, 0, '2013-10-25 00:09:37', NULL, 'synchro commits', '2013-11-29 16:31:04'),
(724, '923a5661b00caa9c5e94980160534b2b225b84d7', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-25 00:27:38', NULL, 0, 0, '2013-10-25 00:27:38', NULL, 'commits ordered', '2013-11-29 16:31:04'),
(725, 'b48fba01210909dca6effcd66399331633cfe585', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-25 01:12:54', NULL, 0, 0, '2013-10-25 01:12:54', NULL, 'commits browsing', '2013-11-29 16:31:04'),
(726, 'bce9693c8f20d0a25a1df1321b1eee9fcf4e203d', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-10-25 13:19:22', NULL, 0, 0, '2013-10-25 13:19:22', NULL, 'added commits cache (+md5.js)\nadded language detection', '2013-11-29 16:31:04'),
(727, '838aff08d96e755a9047476e54c3a2ee9497cf0a', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-10-25 14:35:41', NULL, 0, 0, '2013-10-25 14:35:41', NULL, 'fixed blob display', '2013-11-29 16:31:04'),
(728, '20b9fd3fda2db35971b87fd41c58aa1e47927cd6', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-10-25 16:14:43', NULL, 0, 0, '2013-10-25 16:14:43', NULL, 'commit view!', '2013-11-29 16:31:04'),
(729, '7f1c3a70a68dbb576b27123144faaa45dbd73514', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-10-25 16:22:29', NULL, 0, 0, '2013-10-25 16:22:29', NULL, 'fixed some commit views', '2013-11-29 16:31:04'),
(730, 'cf46655dcb3ff941ec3d9188d6b6c0919ec63826', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-27 18:33:46', NULL, 0, 0, '2013-10-27 18:33:46', NULL, 'fixed display ', '2013-11-29 16:31:04'),
(731, '70fd6715041b5383f9ab47254e054ea75ae27211', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-27 19:32:39', NULL, 0, 0, '2013-10-27 19:32:39', NULL, 'fixed display', '2013-11-29 16:31:04'),
(732, '5f9cb9fae7e54ad74f39dd5d23784ae8ff281fcf', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-27 22:11:14', NULL, 0, 0, '2013-10-27 22:11:14', NULL, 'fixed blob/api\nadded compare/diff \n', '2013-11-29 16:31:04'),
(733, '4d2849adaa1d82be097c1e437675ee1d5149a454', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-27 23:07:46', NULL, 0, 0, '2013-10-27 23:07:46', NULL, 'added buggy pushstate', '2013-11-29 16:31:04'),
(734, '3cfb11458fa1daee74d2d5d119e01f6ef455d1f1', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-10-28 17:32:55', NULL, 0, 0, '2013-10-28 17:32:55', NULL, 'better angular (services) \nneedfix commit view (not changing currentCommit)\nneedfix html5 pushState support\nneedfix compare broken\nneedfix compare direct link', '2013-11-29 16:31:04'),
(735, 'c8d89599709e59a2be86620927f6d41de230613f', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-29 00:18:36', NULL, 0, 0, '2013-10-29 00:18:36', NULL, 'tinyfix', '2013-11-29 16:31:04'),
(736, '5e2df7713bd02f5e321b79f900759684995c1756', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-10-29 09:49:32', NULL, 0, 0, '2013-10-29 09:49:32', NULL, 'fixed compare broken\nfixed compare direct link\nneedfix commit view (not changing currentCommit)\nneedfix html5 pushState support\n', '2013-11-29 16:31:04'),
(737, '6c5875cf277c637b62ce3f36825904da11545dc0', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-10-29 16:43:46', NULL, 0, 0, '2013-10-29 16:43:46', NULL, 'added pushState support (needfix/buggy)\nadded Branches/Tags view (need angularization)\n', '2013-11-29 16:31:04'),
(738, '4d9bb554385d0d5f95dca4904a1a1752a233fb16', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-10-29 17:01:59', NULL, 0, 0, '2013-10-29 17:01:59', NULL, 'added compare button', '2013-11-29 16:31:04'),
(739, '1427fa0af864814a1ba0ff345b4a82123e9f3516', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-29 21:05:34', NULL, 0, 0, '2013-10-29 21:05:34', NULL, 'fixed ui performances\nfixed commit navigation', '2013-11-29 16:31:04'),
(740, '798e784476cb4609e42941f19444955b1e210cc2', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-29 22:46:31', NULL, 0, 0, '2013-10-29 22:46:31', NULL, 'fixed html5 pushState ', '2013-11-29 16:31:04'),
(741, '9ed791bc7b47513dd4493e3a478da0f65978d31d', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-10-30 00:10:23', NULL, 0, 0, '2013-10-30 00:10:23', NULL, 'fixed commit view', '2013-11-29 16:31:04'),
(742, '1ddfba68f1e92292ce4b5019f69c4f75e71fd279', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-11-03 21:53:03', NULL, 0, 0, '2013-11-03 21:53:03', NULL, 'fixed commit view: display also remotes branches \nadded users support', '2013-11-29 16:31:04'),
(743, 'ecf247c9adcf09e70c5be23c940a908cab21600c', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-11-03 23:04:26', NULL, 0, 0, '2013-11-03 23:04:26', NULL, 'continued users page', '2013-11-29 16:31:04'),
(744, 'e71ab0613a809417c86f11f79a245b09db882c11', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-11-04 23:40:19', NULL, 0, 0, '2013-11-04 23:40:19', NULL, '...', '2013-11-29 16:31:04'),
(745, '98c72263f48762db3c06b93b4497151a0435188c', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-05 16:41:27', NULL, 0, 0, '2013-11-05 16:41:27', NULL, 'still working on users', '2013-11-29 16:31:04'),
(746, '19dd6a0e90e9fbd9c88d92ddd3cb91e68deff99c', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-11-07 22:44:04', NULL, 0, 0, '2013-11-07 22:44:04', NULL, 'added escaping in templates\nsynched repositories with database\nimproved user integration\nfixed default branch is now configurable\nfixed clone url is now configurable ', '2013-11-29 16:31:04'),
(747, '5e251e12f6a87b90a343f8c71770169b4d05b259', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-11-07 22:47:01', NULL, 0, 0, '2013-11-07 22:47:01', NULL, 'removed Finder dependency', '2013-11-29 16:31:04'),
(748, 'af103f633980c32a8bb07b7aa04775d009752c56', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-08 17:21:21', NULL, 0, 0, '2013-11-08 17:21:21', NULL, 'added docs etc..', '2013-11-29 16:31:04'),
(749, '4a79f970e83219c577720b5ac08fa8ab222a286e', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-12 12:47:18', NULL, 0, 0, '2013-11-12 12:47:18', NULL, 'fixed composer.json', '2013-11-29 16:31:04'),
(750, '06ea80868a037d3712d48d6a23771b703329a19a', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-12 13:28:07', NULL, 0, 0, '2013-11-12 13:28:07', NULL, 'working on commands', '2013-11-29 16:31:04'),
(751, 'a33a263606657ea9eae705c2d801a63b463506af', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-12 13:37:37', NULL, 0, 0, '2013-11-12 13:37:37', NULL, 'changed hook', '2013-11-29 16:31:04'),
(752, 'a1ab5f8decc0c364fe85c77455459acb0146927f', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-11-12 14:53:38', NULL, 0, 0, '2013-11-12 14:53:38', NULL, 'Forgery administration\n', '2013-11-29 16:31:04'),
(753, 'f3f60f3a3f0a4c61629eddf3d394c274f6b35b60', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-12 17:10:32', NULL, 0, 0, '2013-11-12 17:10:32', NULL, 'working on server-side\nadded "special repos" handling\n', '2013-11-29 16:31:04'),
(754, '702c4c08fc180d6c3b5cd2f89de206ab812c5fd2', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-11-12 17:13:49', NULL, 0, 0, '2013-11-12 17:13:49', NULL, 'oops\n', '2013-11-29 16:31:04'),
(755, '9fd1b76ff971ec1994dcfc1cefaa28e90187afaf', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-11-12 20:13:39', NULL, 0, 0, '2013-11-12 20:13:39', NULL, 'enhanced repository response time', '2013-11-29 16:31:04'),
(756, '5c631a164c57dc342ddb60d00e245759555dbea7', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-11-12 22:11:35', NULL, 0, 0, '2013-11-12 22:11:35', NULL, 'enhanced repository response time', '2013-11-29 16:31:04'),
(757, 'd26cbf76913b63fbd3c1b016b8c5abb3cf661366', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-13 16:17:00', NULL, 0, 0, '2013-11-13 16:17:00', NULL, 'removed config from repository', '2013-11-29 16:31:04'),
(758, '8dc5e060bce13723b30160f951380b9d628ce52c', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-11-13 16:22:13', NULL, 0, 0, '2013-11-13 16:22:13', NULL, 'added gitignore\n', '2013-11-29 16:31:04'),
(759, '1b770039cf1ca610f788c298f9f52f68b063b417', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-11-13 16:22:38', NULL, 0, 0, '2013-11-13 16:22:38', NULL, 'added gitignore\n', '2013-11-29 16:31:04'),
(760, '72436ea37273dfcc1937150603363218013dea69', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-13 16:26:33', NULL, 0, 0, '2013-11-13 16:26:33', NULL, 'removed config from repository', '2013-11-29 16:31:04'),
(761, '01b4ab858c55a84bc0c8a88aba5a0993d08558fd', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-13 16:27:19', NULL, 0, 0, '2013-11-13 16:27:19', NULL, 'removed config from repository', '2013-11-29 16:31:04'),
(762, '3abe4b27b338055650c7a06a77d24fec19278a11', 23, 23, 'neiluJ', 'julien@nitronet.org', '2013-11-13 16:29:03', NULL, 0, 0, '2013-11-13 16:29:03', NULL, 'gnak\n', '2013-11-29 16:31:04'),
(763, 'b7800b42a99c2b1dea26fb183d9884b3c9a9bebc', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-14 09:12:00', NULL, 0, 0, '2013-11-14 09:12:00', NULL, 'temp tests', '2013-11-29 16:31:05'),
(764, '92c2ed1967c544ca6adea516fcae61e227665fa9', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-14 16:10:27', NULL, 0, 0, '2013-11-14 16:10:27', NULL, 'try to fix hooks...', '2013-11-29 16:31:05'),
(765, '3ebe764099861a4b812a3b056d9ba582ed0f5cd8', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-14 16:14:26', NULL, 0, 0, '2013-11-14 16:14:26', NULL, 'try to fix hooks...', '2013-11-29 16:31:05'),
(766, '39c62872b57d030f3731649b1ec02ab2425da377', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-14 17:25:44', NULL, 0, 0, '2013-11-14 17:25:44', NULL, 'try to fix hooks...', '2013-11-29 16:31:05'),
(767, '187a58aa9d8c2c396fcc6204519355baacf69efa', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-14 17:27:51', NULL, 0, 0, '2013-11-14 17:27:51', NULL, 'try to fix hooks...', '2013-11-29 16:31:05'),
(768, 'f02d8a836ad880af927ede9bc25f4b61a3ae8830', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-14 17:30:21', NULL, 0, 0, '2013-11-14 17:30:21', NULL, 'try to fix hooks...', '2013-11-29 16:31:05'),
(769, 'f5e9084d1cdb0235c2dbf8a1fc75666d2978390d', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-14 17:34:15', NULL, 0, 0, '2013-11-14 17:34:15', NULL, 'try to fix hooks...', '2013-11-29 16:31:05'),
(770, 'd87b9e393ce1a2a175d44d4a06a567d2266b3b96', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-14 17:36:24', NULL, 0, 0, '2013-11-14 17:36:24', NULL, 'try to fix hooks...', '2013-11-29 16:31:05'),
(771, 'b7ac0a7595d7fc8b1ed314513ecc6e24c5a8fee8', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-14 17:42:42', NULL, 0, 0, '2013-11-14 17:42:42', NULL, 'try to fix hooks...', '2013-11-29 16:31:05'),
(772, '2b9fb95dcfddb543e4baed4cad0a9fb2b5e1700d', 23, 23, 'neiluj', 'neiluj@baltringue', '2013-11-14 17:44:37', NULL, 0, 0, '2013-11-14 17:44:37', NULL, 'try to fix hooks...', '2013-11-29 16:31:05');

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
(709, 75);

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

--
-- Dumping data for table `pushes`
--

INSERT INTO `pushes` (`id`, `userId`, `username`, `repositoryId`, `createdOn`) VALUES
(23, NULL, NULL, 23, '2013-11-29 16:31:03');

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

--
-- Dumping data for table `refs`
--

INSERT INTO `refs` (`id`, `name`, `type`, `fullname`, `repositoryId`, `pushId`, `createdOn`, `commitHash`) VALUES
(75, 'master', 'branch', 'refs/heads/master', 23, 23, '2013-11-29 16:31:03', '777ffd15548e724a3b5d5323f717a18eb7b8da9c');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=52 ;

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
