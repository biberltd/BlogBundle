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

 Date: 08/17/2016 11:23:18 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `blog_post_category`
-- ----------------------------
DROP TABLE IF EXISTS `blog_post_category`;
CREATE TABLE `blog_post_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(10) unsigned DEFAULT NULL COMMENT 'Parent category.',
  `blog` int(10) unsigned NOT NULL COMMENT 'Blog that category belongs to.',
  `date_added` datetime NOT NULL COMMENT 'Date when category is added.',
  `site` int(10) unsigned DEFAULT NULL COMMENT 'Site that blog post category belongs to.',
  `date_updated` datetime NOT NULL COMMENT 'Date when the entry is last updated.',
  `date_removed` datetime DEFAULT NULL COMMENT 'Date when the entry is marked as removed.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUIdOfBlogPostCategory` (`id`) USING BTREE,
  KEY `idxNDateAddedOfBlogPostCategory` (`date_added`) USING BTREE,
  KEY `idxNDateUpdatedOfBlogPostCategory` (`date_updated`) USING BTREE,
  KEY `idxNDateRemovedOfBlogPostCategory` (`date_removed`) USING BTREE,
  KEY `idxNBlogOfBlgPostCategory` (`blog`) USING BTREE,
  KEY `idxNParentOfBlogPostCategory` (`parent`) USING BTREE,
  KEY `idxNSiteOfBlogPostCategory` (`site`) USING BTREE,
  CONSTRAINT `idxFBlogOfBlgPostCategory` FOREIGN KEY (`blog`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFParentOfBlogPostCategory` FOREIGN KEY (`parent`) REFERENCES `blog_post_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFSiteOfBlogPostCategory` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

SET FOREIGN_KEY_CHECKS = 1;
