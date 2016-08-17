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

 Date: 08/17/2016 14:17:35 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `blog_post_comment_action`
-- ----------------------------
DROP TABLE IF EXISTS `blog_post_comment_action`;
CREATE TABLE `blog_post_comment_action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post` int(10) unsigned NOT NULL COMMENT 'Post of the comment.',
  `comment` int(10) unsigned NOT NULL COMMENT 'Comment',
  `member` int(10) unsigned NOT NULL COMMENT 'Member who took the action.',
  `action` char(1) COLLATE utf8_turkish_ci NOT NULL COMMENT 'l:like;d:dislike',
  `date_added` datetime NOT NULL COMMENT 'Date when acction has taken place.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUIdOfBlogPostCommentAction` (`id`) USING BTREE,
  UNIQUE KEY `idxUPostOfBlogPostCommentAction` (`post`,`comment`,`member`) USING BTREE,
  KEY `idxFCommentOfBlogPostCommentAction` (`comment`) USING BTREE,
  KEY `idxFMemberOfBlogPostCommentAction` (`member`) USING BTREE,
  KEY `idxNDateAddedOfBlogPostCommentAction` (`date_added`) USING BTREE,
  CONSTRAINT `idxFActedCommentOfBlogPost` FOREIGN KEY (`comment`) REFERENCES `blog_post_comment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFBlogPostOfActedComment` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFOwnerOfBlogPostCommentAction` FOREIGN KEY (`member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

SET FOREIGN_KEY_CHECKS = 1;
