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

 Date: 08/17/2016 10:27:34 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `files_of_blog_post`
-- ----------------------------
DROP TABLE IF EXISTS `files_of_blog_post`;
CREATE TABLE `files_of_blog_post` (
  `post` int(5) unsigned DEFAULT NULL,
  `file` int(10) unsigned DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `sort_order` int(10) DEFAULT NULL,
  `type` char(1) DEFAULT NULL,
  `count_view` int(11) DEFAULT NULL,
  UNIQUE KEY `idxNFilesOfBlogPostFilePost` (`post`,`file`) USING BTREE,
  KEY `idxNFilesOfBlogPostDateAdded` (`date_added`) USING BTREE,
  KEY `idxNFilesOfBlogPostFile` (`file`) USING BTREE,
  CONSTRAINT `idxFFileOfFilesOfBlogPost` FOREIGN KEY (`file`) REFERENCES `file` (`id`) ON DELETE CASCADE,
  CONSTRAINT `idxFPostOfFilesOfBlogPost` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
