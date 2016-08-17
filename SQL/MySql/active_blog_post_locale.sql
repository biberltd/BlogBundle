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

 Date: 08/17/2016 10:36:02 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `active_blog_post_locale`
-- ----------------------------
DROP TABLE IF EXISTS `active_blog_post_locale`;
CREATE TABLE `active_blog_post_locale` (
  `blog_post` int(10) unsigned DEFAULT NULL,
  `language` int(5) unsigned DEFAULT NULL,
  KEY `idxNActiveBlogPostLocaleBlogPost` (`blog_post`) USING BTREE,
  KEY `idxNActiveBlogPostLocaleLanguage` (`language`) USING BTREE,
  CONSTRAINT `idxFActiveBlogPostOfLanguage` FOREIGN KEY (`blog_post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE,
  CONSTRAINT `idxFActiveLanguageOfBlogPost` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
