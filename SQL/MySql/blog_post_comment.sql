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

 Date: 08/17/2016 14:00:49 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `blog_post_comment`
-- ----------------------------
DROP TABLE IF EXISTS `blog_post_comment`;
CREATE TABLE `blog_post_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post` int(10) unsigned NOT NULL COMMENT 'Post of the comment.',
  `parent` int(10) unsigned DEFAULT NULL COMMENT 'Parent comment.',
  `author` int(10) unsigned DEFAULT NULL COMMENT 'Member who wrote the comment.',
  `comment` text COLLATE utf8_turkish_ci NOT NULL COMMENT 'Content of the comment.',
  `date_added` datetime NOT NULL COMMENT 'Date that comment has been edded.',
  `date_removed` datetime NOT NULL COMMENT 'Date that comment has been removed.',
  `date_published` datetime DEFAULT NULL COMMENT 'Date when the comment is published.',
  `name` varchar(255) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Name of person who wrote the comment.',
  `email` varchar(255) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Email of the person who wrote the comment.',
  `url` text COLLATE utf8_turkish_ci COMMENT 'URL of commentor.',
  `count_likes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of likes.',
  `count_dislikes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of dislikes.',
  `site` int(10) unsigned DEFAULT NULL COMMENT 'Site that blog post comment belongs to.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUIdOfBlogPostComment` (`id`) USING BTREE,
  KEY `idxNAuthorOfBlogPostComment` (`author`) USING BTREE,
  KEY `idxNPostOfBlogPostComment` (`post`) USING BTREE,
  KEY `idxNSiteOfBlogPostComment` (`site`) USING BTREE,
  KEY `idxNParentOfBlogPostComment` (`parent`) USING BTREE,
  CONSTRAINT `idxAuthorOfBlogPostComment` FOREIGN KEY (`author`) REFERENCES `member` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `idxFBlogPostOfComment` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFParentBlogPostComment` FOREIGN KEY (`parent`) REFERENCES `blog_post_comment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxSiteOfBlogPostComment` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

SET FOREIGN_KEY_CHECKS = 1;
