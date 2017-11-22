-- phpMyAdmin SQL Dump
-- http://www.phpmyadmin.net
--
-- Generated: 22 nov 2017 at 01:10

--
-- Database: `XirtCMS`
-- Manually cleansed (Work In Progress)
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_articles`
--

CREATE TABLE IF NOT EXISTS `xcms_articles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `author_id` int(11) NOT NULL,
  `dt_created` datetime NOT NULL DEFAULT current_timestamp(),
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `dt_publish` datetime NOT NULL DEFAULT current_timestamp(),
  `dt_unpublish` datetime DEFAULT '2199-12-31 23:59:59',
  `version` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_articles_attributes`
--

CREATE TABLE IF NOT EXISTS `xcms_articles_attributes` (
  `ref_id` bigint(20) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`ref_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_articles_categories`
--

CREATE TABLE IF NOT EXISTS `xcms_articles_categories` (
  `article_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`article_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_articles_comments`
--

CREATE TABLE IF NOT EXISTS `xcms_articles_comments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) NOT NULL,
  `article_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `author_name` varchar(128) DEFAULT NULL,
  `author_email` varchar(254) DEFAULT NULL,
  `dt_created` datetime NOT NULL DEFAULT current_timestamp(),
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_categories`
--

CREATE TABLE IF NOT EXISTS `xcms_categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_categories_relations`
--

CREATE TABLE IF NOT EXISTS `xcms_categories_relations` (
  `node_id` bigint(20) NOT NULL,
  `parent_id` bigint(20) NOT NULL DEFAULT 0,
  `level` tinyint(99) NOT NULL DEFAULT 1,
  `ordering` bigint(20) NOT NULL,
  PRIMARY KEY (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_configuration`
--

CREATE TABLE IF NOT EXISTS `xcms_configuration` (
  `name` varchar(32) NOT NULL,
  `value` varchar(128) NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `xcms_configuration`
--

INSERT INTO `xcms_configuration` (`name`, `value`) VALUES
('AUTH_HASH_TYPE', 'sha512'),
('AUTH_LOCKTIME', '10'),
('AUTH_MAX_ATTEMPTS', '3'),
('AUTH_SECRET', ''),
('COMBINE_SCRIPTS', 'TRUE'),
('DEBUG_MODE', 'FALSE'),
('DEFAULT_CHMOD_FILE', '0777'),
('DEFAULT_CHMOD_FOLDER', '0777'),
('EMAIL_HOST', ''),
('EMAIL_PASS', ''),
('EMAIL_PORT', ''),
('EMAIL_PROTOCOL', 'smtp'),
('EMAIL_SENDER_EMAIL', 'no-reply@yourdomain.com'),
('EMAIL_SENDER_NAME', 'YourName'),
('EMAIL_USER', 'no-reply@yourdomain.com'),
('ERROR_NOTIFY', 'TRUE'),
('ERROR_NOTIFY_EMAIL', 'admin@yourdomain.com'),
('MIN_ADMIN_LEVEL', '75'),
('SEO_LINKS', 'TRUE'),
('SESSION_LANGUAGE', 'en-GB'),
('SESSION_TIMEZONE', 'Europe/Amsterdam'),
('SESSION_TPL_NAME', 'default'),
('SESSION_USE_DB', 'TRUE'),
('SESSSION_TPL_ENABLED', 'TRUE'),
('THUMBS_CACHE', 'cache/thumbs/'),
('THUMBS_DIMS', '100'),
('USE_TEMPLATE', 'TRUE'),
('WEBSITE_DESCRIPTION', 'XirtCMS: Simple Content Management for Online Applications'),
('WEBSITE_GENERATOR', 'XirtCMS 3.0'),
('WEBSITE_KEYWORDS', 'Xirt, XirtCMS, CMS, content, management, online, easy, modular, php'),
('WEBSITE_ROBOTS', 'index, follow'),
('WEBSITE_TITLE', 'XirtCMS: Simple Content Management for Online Applications');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_menus`
--

CREATE TABLE IF NOT EXISTS `xcms_menus` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '0',
  `ordering` int(6) NOT NULL DEFAULT 999999,
  `sitemap` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Gegevens worden uitgevoerd voor tabel `xcms_menus`
--

INSERT INTO `xcms_menus` (`id`, `name`, `ordering`, `sitemap`) VALUES
(1, 'Main Menu', 1, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_menu_items`
--

CREATE TABLE IF NOT EXISTS `xcms_menu_items` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `menu_id` bigint(20) NOT NULL,
  `name` tinytext NOT NULL,
  `type` varchar(16) NOT NULL,
  `uri` varchar(256) DEFAULT NULL,
  `anchor` varchar(64) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `sitemap` tinyint(1) NOT NULL DEFAULT 1,
  `home` tinyint(1) NOT NULL DEFAULT 0,
  `access_min` tinyint(3) NOT NULL DEFAULT 1,
  `access_max` tinyint(3) NOT NULL DEFAULT 100,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Gegevens worden uitgevoerd voor tabel `xcms_menu_items`
--

INSERT INTO `xcms_menu_items` (`id`, `menu_id`, `name`, `type`, `uri`, `anchor`, `published`, `sitemap`, `home`, `access_min`, `access_max`) VALUES
(1, 1, 'Home', 'internal', NULL, '', 1, 1, 1, 1, 100);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_menu_relations`
--

CREATE TABLE IF NOT EXISTS `xcms_menu_relations` (
  `node_id` bigint(20) NOT NULL,
  `parent_id` bigint(20) NOT NULL DEFAULT 0,
  `level` tinyint(99) NOT NULL DEFAULT 1,
  `ordering` bigint(20) NOT NULL,
  PRIMARY KEY (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `xcms_menu_relations`
--

INSERT INTO `xcms_menu_relations` (`node_id`, `parent_id`, `level`, `ordering`) VALUES
(1, 0, 0, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_menu_routes`
--

CREATE TABLE IF NOT EXISTS `xcms_menu_routes` (
  `route_id` int(6) NOT NULL,
  `menuitem_id` int(6) NOT NULL,
  PRIMARY KEY (`route_id`,`menuitem_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_modules`
--

CREATE TABLE IF NOT EXISTS `xcms_modules` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(32) NOT NULL,
  `name` varchar(150) NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT 0,
  `access_min` tinyint(3) NOT NULL DEFAULT 1,
  `access_max` tinyint(3) NOT NULL DEFAULT 100,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_modules_settings`
--

CREATE TABLE IF NOT EXISTS `xcms_modules_settings` (
  `module_id` bigint(20) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  `value` varchar(128) NOT NULL,
  PRIMARY KEY (`module_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_routes`
--

CREATE TABLE IF NOT EXISTS `xcms_routes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `public_url` tinytext NOT NULL,
  `target_url` tinytext NOT NULL,
  `module_config` int(11) DEFAULT NULL,
  `master` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_templates`
--

CREATE TABLE IF NOT EXISTS `xcms_templates` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `folder` varchar(32) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Gegevens worden uitgevoerd voor tabel `xcms_templates`
--

INSERT INTO `xcms_templates` (`id`, `name`, `folder`, `published`) VALUES
(1, 'Default template', 'default', 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_templates_positions`
--

CREATE TABLE IF NOT EXISTS `xcms_templates_positions` (
  `template_id` int(6) unsigned NOT NULL,
  `position` varchar(32) NOT NULL,
  PRIMARY KEY (`template_id`,`position`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_usergroups`
--

CREATE TABLE IF NOT EXISTS `xcms_usergroups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `authorization_level` smallint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Gegevens worden uitgevoerd voor tabel `xcms_usergroups`
--

INSERT INTO `xcms_usergroups` (`id`, `name`, `authorization_level`) VALUES
(1, 'Guest', 1),
(2, 'Member', 100),
(3, 'Moderator', 1000),
(4, 'Administrator', 2500),
(5, 'Super Administrator', 9999);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_users`
--

CREATE TABLE IF NOT EXISTS `xcms_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `real_name` varchar(128) NOT NULL,
  `email` varchar(254) NOT NULL,
  `password` varchar(128) NOT NULL,
  `salt` varchar(21) NOT NULL,
  `usergroup_id` tinyint(3) NOT NULL DEFAULT 1,
  `dt_created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Gegevens worden uitgevoerd voor tabel `xcms_users`
--

INSERT INTO `xcms_users` (`id`, `username`, `real_name`, `email`, `password`, `salt`, `usergroup_id`, `dt_created`) VALUES
(1, 'root', 'John Doe', 'yourname@yourdomain.com', '$2a$08$3c109a94976c70cf3e1a6.009Ikqktga7orkOv/n4O9J7x2Ibr6ae', '3c109a94976c70cf3e1a6', 5, '2017-11-22 00:05:23');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_users_attributes`
--

CREATE TABLE IF NOT EXISTS `xcms_users_attributes` (
  `ref_id` bigint(20) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`ref_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `xcms_users_attributes`
--

INSERT INTO `xcms_users_attributes` (`ref_id`, `name`, `value`) VALUES
(1, 'name_display', ''),
(1, 'name_first', ''),
(1, 'name_family', ''),
(1, 'short_description', '');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_widgets`
--

CREATE TABLE IF NOT EXISTS `xcms_widgets` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `ordering` int(6) NOT NULL DEFAULT 0,
  `position` varchar(15) DEFAULT 'module',
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(32) NOT NULL,
  `page_all` tinyint(1) NOT NULL DEFAULT 1,
  `page_default` tinyint(1) NOT NULL DEFAULT 0,
  `page_module` int(6) NOT NULL DEFAULT 0,
  `access_min` tinyint(3) NOT NULL DEFAULT 1,
  `access_max` tinyint(3) NOT NULL DEFAULT 100,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_widgets_pages`
--

CREATE TABLE IF NOT EXISTS `xcms_widgets_pages` (
  `widget_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) NOT NULL,
  PRIMARY KEY (`widget_id`,`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `xcms_widgets_settings`
--

CREATE TABLE IF NOT EXISTS `xcms_widgets_settings` (
  `widget_id` bigint(20) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  `value` tinytext NOT NULL,
  PRIMARY KEY (`widget_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;