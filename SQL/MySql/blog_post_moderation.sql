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

 Date: 08/17/2016 14:34:28 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `blog_post_moderation`
-- ----------------------------
DROP TABLE IF EXISTS `blog_post_moderation`;
CREATE TABLE `blog_post_moderation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `post` int(10) unsigned NOT NULL COMMENT 'Moderated post.',
  `moderator` int(10) unsigned NOT NULL COMMENT 'Moderator of post.',
  `comment` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Comment about moderation.',
  `date_reviewed` datetime DEFAULT NULL COMMENT 'Date when the post is first moderated.',
  `date_updated` datetime DEFAULT NULL COMMENT 'Date when the moderation review is last updated.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUIdOfBlogPostModeration` (`id`) USING BTREE,
  KEY `idxNDateReviewedOfBlogPostModeration` (`date_reviewed`) USING BTREE,
  KEY `idxNDateUpdatedOfBlogPostModeration` (`date_updated`) USING BTREE,
  KEY `idxNPostOfBlogPostModeration` (`post`) USING BTREE,
  KEY `idxNModeratorOfBlogPostModeration` (`moderator`) USING BTREE,
  CONSTRAINT `idxFModeratedBlogPost` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFModeratorOfBlogPost` FOREIGN KEY (`moderator`) REFERENCES `member` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

SET FOREIGN_KEY_CHECKS = 1;
