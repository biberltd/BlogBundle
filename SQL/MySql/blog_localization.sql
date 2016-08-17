/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50712
 Source Host           : localhost
 Source Database       : core34bundle

 Target Server Type    : MySQL
 Target Server Version : 50712
 File Encoding         : utf-8

 Date: 08/17/2016 10:52:01 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `blog_localization`
-- ----------------------------
DROP TABLE IF EXISTS `blog_localization`;
CREATE TABLE `blog_localization` (
  `blog` int(10) unsigned NOT NULL COMMENT 'Localized blog.',
  `language` int(10) unsigned NOT NULL COMMENT 'Localization language.',
  `title` varchar(155) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized title of blog.',
  `url_key` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized url key of the blog',
  `description` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized description of blog.',
  `meta_description` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized meta description of blog.',
  `meta_keywords` varchar(155) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized meta keywords.',
  PRIMARY KEY (`blog`,`language`),
  UNIQUE KEY `idxUBlogLocalizationBlogLanguage` (`blog`,`language`) USING BTREE,
  UNIQUE KEY `idxUBlogLocalizationLanguageUrlKey` (`language`,`url_key`) USING BTREE,
  CONSTRAINT `idxFBlogLocalizationLanguage` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFLocalizedBlog` FOREIGN KEY (`blog`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

SET FOREIGN_KEY_CHECKS = 1;
