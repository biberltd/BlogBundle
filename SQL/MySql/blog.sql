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

 Date: 08/17/2016 10:45:46 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `blog`
-- ----------------------------
DROP TABLE IF EXISTS `blog`;
CREATE TABLE `blog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `date_added` datetime NOT NULL COMMENT 'Date when the blog is first created.',
  `date_updated` datetime NOT NULL COMMENT 'Date when the blog is first updated.',
  `count_posts` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of total posts in this blog.',
  `site` int(10) unsigned DEFAULT NULL COMMENT 'Site that blog belongs to.',
  `date_removed` datetime DEFAULT NULL COMMENT 'Date when the entry is marked as removed.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUBlogId` (`id`) USING BTREE,
  KEY `idxNBlogDateAdded` (`date_added`) USING BTREE,
  KEY `idxNBlogDateUpdated` (`date_updated`) USING BTREE,
  KEY `idxNBlogSite` (`site`) USING BTREE,
  KEY `idxNBlogDateRemoved` (`date_removed`) USING BTREE,
  CONSTRAINT `idxFSiteOfBlog` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

SET FOREIGN_KEY_CHECKS = 1;
