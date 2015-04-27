/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : bod_core

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2015-04-27 10:30:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for blog
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
  KEY `idxFSiteOfBlog` (`site`) USING BTREE,
  CONSTRAINT `idxFSiteOfBlog` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for blog_localization
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
  UNIQUE KEY `idxUBlogLocalization` (`blog`,`language`) USING BTREE,
  UNIQUE KEY `idxUBlogUrlKey` (`language`,`url_key`) USING BTREE,
  CONSTRAINT `idxFBlogLocalizationLanguage` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFLocalizedBlog` FOREIGN KEY (`blog`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for blog_moderator
-- ----------------------------
DROP TABLE IF EXISTS `blog_moderator`;
CREATE TABLE `blog_moderator` (
  `moderator` int(10) unsigned NOT NULL COMMENT 'Member who can moderate the blog.',
  `blog` int(10) unsigned NOT NULL COMMENT 'Blog that field belongs to.',
  `date_added` datetime DEFAULT NULL COMMENT 'Date when the member has been added.',
  `category` int(10) unsigned DEFAULT NULL COMMENT 'If member is a moderator of only a specific category.',
  UNIQUE KEY `idxUBlogModerator` (`moderator`,`blog`,`category`) USING BTREE,
  KEY `idxFBlogOfModerator` (`blog`) USING BTREE,
  KEY `idxFBlogPostCategoryToModerate` (`category`) USING BTREE,
  KEY `idxNBlogModeratorDateAdded` (`date_added`) USING BTREE,
  CONSTRAINT `idxFBlogOfModerator` FOREIGN KEY (`blog`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFBlogPostCategoryToModerate` FOREIGN KEY (`category`) REFERENCES `blog_post_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFModeratorOfBlog` FOREIGN KEY (`moderator`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for blog_post
-- ----------------------------
DROP TABLE IF EXISTS `blog_post`;
CREATE TABLE `blog_post` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `author` int(10) unsigned NOT NULL COMMENT 'Author of the post.',
  `blog` int(10) unsigned NOT NULL COMMENT 'Blog in which the post is published.',
  `type` char(1) COLLATE utf8_turkish_ci NOT NULL DEFAULT 'a' COMMENT 'a:article;i:image;g:gallery;v:video;d:document;',
  `preview_image` int(10) unsigned DEFAULT NULL COMMENT 'File of preview image.',
  `status` char(1) COLLATE utf8_turkish_ci NOT NULL COMMENT 'o:open,c:closed,p:published,m:modetation;u:unpublished',
  `date_added` datetime NOT NULL COMMENT 'Date when post is added.',
  `date_approved` datetime DEFAULT NULL COMMENT 'Date when the  post is approved.',
  `date_published` datetime NOT NULL COMMENT 'Date when the post will be published.',
  `date_unpublished` datetime DEFAULT NULL COMMENT 'Date when the post will be unpublished.',
  `count_view` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of total views accumulated.',
  `count_like` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of total likes.',
  `count_dislike` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of total dislikes accumulated.',
  `count_comment` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Count of total comments written to the post.',
  `site` int(10) unsigned DEFAULT NULL COMMENT 'Site that blog post belongs to.',
  `date_updated` datetime NOT NULL COMMENT 'Date when the entry is last updated.',
  `date_removed` datetime DEFAULT NULL COMMENT 'Date when the entry is marked as removed.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUBlogPostId` (`id`) USING BTREE,
  KEY `idxNBlogPostDateAdded` (`date_added`) USING BTREE,
  KEY `idxNBlogPostDateApproved` (`date_approved`) USING BTREE,
  KEY `idxNBlogPostDatePublished` (`date_published`) USING BTREE,
  KEY `idxNBlogPostDateUnpublished` (`date_unpublished`) USING BTREE,
  KEY `idxFAuthorOfBlogPost` (`author`) USING BTREE,
  KEY `idxFPostsOfBlog` (`blog`) USING BTREE,
  KEY `idxFPrevieImageOfBlogPost` (`preview_image`) USING BTREE,
  KEY `idxFSiteOfBlogPost` (`site`) USING BTREE,
  KEY `idxNBlogPostDateUpdated` (`date_updated`),
  KEY `idxNBlogPostDateRemoved` (`date_removed`),
  CONSTRAINT `idxFAuthorOfBlogPost` FOREIGN KEY (`author`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFBlogOfBlogPost` FOREIGN KEY (`blog`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFPrevieImageOfBlogPost` FOREIGN KEY (`preview_image`) REFERENCES `file` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFSiteOfBlogPost` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for blog_post_action
-- ----------------------------
DROP TABLE IF EXISTS `blog_post_action`;
CREATE TABLE `blog_post_action` (
  `id` int(15) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `post` int(10) unsigned NOT NULL COMMENT 'Post that action is associated with.',
  `member` int(10) unsigned NOT NULL COMMENT 'Mmeber who has done the action.',
  `action` char(1) COLLATE utf8_turkish_ci NOT NULL DEFAULT 'v' COMMENT 'v:view;l:like;d:dislike;f:favorite;u:unfavorite',
  `date_added` datetime NOT NULL COMMENT 'Date when the action has occured.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUBlogPostActionId` (`id`) USING BTREE,
  KEY `idxNBlogPostActionDateAdded` (`date_added`) USING BTREE,
  KEY `idxFBlogPostOfAction` (`post`) USING BTREE,
  KEY `idxFOwnerOfBlogPostAction` (`member`) USING BTREE,
  CONSTRAINT `idxFBlogPostOfAction` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFOwnerOfBlogPostAction` FOREIGN KEY (`member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for blog_post_category
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
  UNIQUE KEY `idxUBlogPostCategoryId` (`id`) USING BTREE,
  KEY `idxNBlogPostCategoryDateAdded` (`date_added`) USING BTREE,
  KEY `idxNBlogPostCategoryDateUpdated` (`date_updated`),
  KEY `idxNBlogPostCategoryDateRemoved` (`date_removed`),
  KEY `idxFBlogOfBlgPostCategory` (`blog`) USING BTREE,
  KEY `idxFParentOfBlogPostCategory` (`parent`) USING BTREE,
  KEY `idxFSiteOfBlogPostCategory` (`site`) USING BTREE,
  CONSTRAINT `idxFBlogOfBlgPostCategory` FOREIGN KEY (`blog`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFParentOfBlogPostCategory` FOREIGN KEY (`parent`) REFERENCES `blog_post_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFSiteOfBlogPostCategory` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for blog_post_category_localization
-- ----------------------------
DROP TABLE IF EXISTS `blog_post_category_localization`;
CREATE TABLE `blog_post_category_localization` (
  `post_category` int(10) unsigned NOT NULL COMMENT 'Localized post category.',
  `language` int(10) unsigned NOT NULL COMMENT 'Post that content belongst to.',
  `name` varchar(155) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localied name.',
  `url_key` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized url key.',
  `description` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized description of category.',
  PRIMARY KEY (`post_category`,`language`),
  UNIQUE KEY `idxUBlogPostLocalization` (`language`,`post_category`) USING BTREE,
  UNIQUE KEY `idxUBlogPostUrlKey` (`language`,`url_key`) USING BTREE,
  CONSTRAINT `idxFBlogPostCategoryLocalizationLanguage` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFLocalizedBlogPostCategory` FOREIGN KEY (`post_category`) REFERENCES `blog_post_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for blog_post_comment
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
  UNIQUE KEY `idxUBlogPostCommentId` (`id`) USING BTREE,
  KEY `idxAuthorOfBlogPostComment` (`author`) USING BTREE,
  KEY `idxFBlogPostOfComment` (`post`) USING BTREE,
  KEY `idxSiteOfBlogPostComment` (`site`) USING BTREE,
  KEY `idxFParentBlogPostComment` (`parent`) USING BTREE,
  CONSTRAINT `idxAuthorOfBlogPostComment` FOREIGN KEY (`author`) REFERENCES `member` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `idxFBlogPostOfComment` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFParentBlogPostComment` FOREIGN KEY (`parent`) REFERENCES `blog_post_comment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxSiteOfBlogPostComment` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for blog_post_comment_action
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
  UNIQUE KEY `idxUBlogPostCommentActionId` (`id`) USING BTREE,
  UNIQUE KEY `idxUBlogPostCommentAction` (`post`,`comment`,`member`) USING BTREE,
  KEY `idxFActedCommentOfBlogPost` (`comment`) USING BTREE,
  KEY `idxFOwnerOfBlogPostCommentAction` (`member`) USING BTREE,
  KEY `idxNBlogPostCommentDateAdded` (`date_added`) USING BTREE,
  CONSTRAINT `idxFActedCommentOfBlogPost` FOREIGN KEY (`comment`) REFERENCES `blog_post_comment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFBlogPostOfActedComment` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFOwnerOfBlogPostCommentAction` FOREIGN KEY (`member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for blog_post_localization
-- ----------------------------
DROP TABLE IF EXISTS `blog_post_localization`;
CREATE TABLE `blog_post_localization` (
  `post` int(10) unsigned NOT NULL COMMENT 'Localized post.',
  `language` int(10) unsigned NOT NULL COMMENT 'Localization language.',
  `title` varchar(155) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized title of blog.',
  `url_key` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized url key of the blog',
  `summary` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized description of blog.',
  `meta_description` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized meta description of blog.',
  `meta_keywords` varchar(155) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized meta keywords.',
  `content` text COLLATE utf8_turkish_ci COMMENT 'Blog content.',
  PRIMARY KEY (`post`,`language`),
  UNIQUE KEY `idxUBlogPostLocalization` (`language`,`post`) USING BTREE,
  UNIQUE KEY `idxUBlogPostUrlKey` (`language`,`url_key`) USING BTREE,
  CONSTRAINT `idxFBlogPostLocalizationLanguage` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFLocalizedBlogPost` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for blog_post_moderation
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
  UNIQUE KEY `idxUBlogPostModerationId` (`id`) USING BTREE,
  KEY `idxNBlogPostModerationDateReviewed` (`date_reviewed`) USING BTREE,
  KEY `idxNBlogPostModerationDateUpdated` (`date_updated`) USING BTREE,
  KEY `idxFModeratedBlogPost` (`post`) USING BTREE,
  KEY `idxFModeratorOfBlogPost` (`moderator`) USING BTREE,
  CONSTRAINT `idxFModeratedBlogPost` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFModeratorOfBlogPost` FOREIGN KEY (`moderator`) REFERENCES `member` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for blog_post_moderation_reply
-- ----------------------------
DROP TABLE IF EXISTS `blog_post_moderation_reply`;
CREATE TABLE `blog_post_moderation_reply` (
  `id` int(15) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `moderation` int(10) unsigned NOT NULL COMMENT 'Post moderation entry.',
  `author` int(10) unsigned NOT NULL COMMENT 'Author of the reply.',
  `date_replied` datetime NOT NULL COMMENT 'Date when the reply is sent.',
  `comment` varchar(255) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Comment to the moderation notice.',
  `sent_from` char(1) COLLATE utf8_turkish_ci NOT NULL DEFAULT 'a' COMMENT 'm:moderator, a:author',
  `is_read` char(1) COLLATE utf8_turkish_ci NOT NULL DEFAULT 'y' COMMENT 'y:yes;n:no',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUBlogPostModerationId` (`id`) USING BTREE,
  KEY `idxNBlogPostModerationDateReplied` (`date_replied`) USING BTREE,
  KEY `idxFModerationOfBlogPostReply` (`moderation`) USING BTREE,
  KEY `idxFMemberOfBlogPostModerationReply` (`author`) USING BTREE,
  CONSTRAINT `idxFMemberOfBlogPostModerationReply` FOREIGN KEY (`author`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFModerationOfBlogPostReply` FOREIGN KEY (`moderation`) REFERENCES `blog_post_moderation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for blog_post_revision
-- ----------------------------
DROP TABLE IF EXISTS `blog_post_revision`;
CREATE TABLE `blog_post_revision` (
  `title` varchar(155) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Blog post title.',
  `url_key` varchar(255) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Blog post url key.',
  `summary` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Summary of blog post.',
  `meta_description` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Description of blog post.',
  `meta_keywords` text COLLATE utf8_turkish_ci COMMENT 'Meta keyword of blog post.',
  `content` tinytext COLLATE utf8_turkish_ci COMMENT 'Content of blog post.',
  `date_added` datetime NOT NULL COMMENT 'Date when the entry is added.',
  `date_updated` datetime NOT NULL COMMENT 'Date when the entry is updated.',
  `date_removed` datetime DEFAULT NULL COMMENT 'Date when the entry is marked as removed.',
  `revision_number` int(10) unsigned DEFAULT NULL COMMENT 'Revision number.',
  `language` int(5) unsigned NOT NULL COMMENT 'Language of revision.',
  `post` int(10) unsigned NOT NULL COMMENT 'Post in revision.',
  UNIQUE KEY `idxUBlogPostRevision` (`language`,`post`),
  KEY `idxFBlogPostOfRevision` (`post`),
  CONSTRAINT `idxFBlogPostOfRevision` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFLanguageOfBlogPostReview` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for blog_post_tag
-- ----------------------------
DROP TABLE IF EXISTS `blog_post_tag`;
CREATE TABLE `blog_post_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `date_added` datetime NOT NULL COMMENT 'Date when the tag is added.',
  `count_posts` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of posts associated with this tag.',
  `member` int(10) unsigned NOT NULL COMMENT 'Member who created this tag.',
  `blog` int(10) unsigned NOT NULL COMMENT 'Blog that tag belongs to.',
  `site` int(10) unsigned DEFAULT NULL COMMENT 'Site that tag belongs to.',
  `date_updated` datetime NOT NULL COMMENT 'Date when the entry is last updated.',
  `date_removed` datetime DEFAULT NULL COMMENT 'Date when the entry is marked as removed.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUBlogPostTagId` (`id`) USING BTREE,
  KEY `idxNBlogPostTagDateAdded` (`date_added`) USING BTREE,
  KEY `idxFMemberOfBlogPost` (`member`) USING BTREE,
  KEY `idxSiteOfBlogPostTag` (`site`) USING BTREE,
  KEY `idxFBlogOfTag` (`blog`) USING BTREE,
  KEY `idxNBlogPostTagDateUpdated` (`date_updated`),
  KEY `idxNBlogPostTagDateRemoved` (`date_removed`),
  CONSTRAINT `idxFBlogOfTag` FOREIGN KEY (`blog`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFMemberOfBlogPost` FOREIGN KEY (`member`) REFERENCES `blog_post_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxSiteOfBlogPostTag` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for blog_post_tag_localization
-- ----------------------------
DROP TABLE IF EXISTS `blog_post_tag_localization`;
CREATE TABLE `blog_post_tag_localization` (
  `tag` int(10) unsigned NOT NULL COMMENT 'Associated tag.',
  `language` int(5) unsigned NOT NULL COMMENT 'Localization language.',
  `name` varchar(155) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Localized tag name.',
  `url_key` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized url key of tag.',
  PRIMARY KEY (`tag`),
  UNIQUE KEY `idxUBlogPostTagLocalization` (`tag`,`language`) USING BTREE,
  UNIQUE KEY `idxUBlogPostTagUrlKey` (`language`,`name`) USING BTREE,
  CONSTRAINT `idxFBlogPostTagLocalizationLanguage` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFLocalizedBlogPostTag` FOREIGN KEY (`tag`) REFERENCES `blog_post_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for categories_of_blog_post
-- ----------------------------
DROP TABLE IF EXISTS `categories_of_blog_post`;
CREATE TABLE `categories_of_blog_post` (
  `post` int(10) unsigned NOT NULL COMMENT 'Post that is associated with the category.',
  `category` int(10) unsigned NOT NULL COMMENT 'Category of blog post.',
  `date_added` datetime NOT NULL COMMENT 'Date when the member has been added.',
  `is_primary` char(1) COLLATE utf8_turkish_ci NOT NULL DEFAULT 'n' COMMENT 'Indicator whether the category is primary.',
  PRIMARY KEY (`post`,`category`),
  UNIQUE KEY `idxUCategoriesOfBlogPost` (`post`,`category`) USING BTREE,
  KEY `idxFBlogPostOfCategory` (`post`) USING BTREE,
  KEY `idxNCategoriesOfBlogPostDateAdded` (`date_added`) USING BTREE,
  KEY `idxFCategoryOfBlogPost` (`category`) USING BTREE,
  CONSTRAINT `idxFBlogPostOfCategory` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFCategoryOfBlogPost` FOREIGN KEY (`category`) REFERENCES `blog_post_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for favorite_blog_post_of_member
-- ----------------------------
DROP TABLE IF EXISTS `favorite_blog_post_of_member`;
CREATE TABLE `favorite_blog_post_of_member` (
  `member` int(10) unsigned NOT NULL COMMENT 'Member who added post to favorite.',
  `post` int(10) unsigned NOT NULL COMMENT 'Post that is being favorited.',
  `date_added` datetime NOT NULL COMMENT 'Date whenthe post is added as favorite.',
  UNIQUE KEY `idxUFavoriteBlogPostOfMember` (`post`,`member`) USING BTREE,
  KEY `idxFOwnerOfFavoriteBlogPost` (`member`) USING BTREE,
  KEY `idxFFavoritedBlogPost` (`post`) USING BTREE,
  KEY `idxNFavoriteBlogPostOfMemberDateAdded` (`date_added`) USING BTREE,
  CONSTRAINT `idxFFavoritedBlogPost` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFOwnerOfFavoriteBlogPost` FOREIGN KEY (`member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for featured_blog_post
-- ----------------------------
DROP TABLE IF EXISTS `featured_blog_post`;
CREATE TABLE `featured_blog_post` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `post` int(10) unsigned NOT NULL COMMENT 'Featured post.',
  `date_added` datetime NOT NULL COMMENT 'Date that post is added.',
  `date_published` datetime NOT NULL COMMENT 'Date to publish.',
  `date_unpublished` datetime NOT NULL COMMENT 'Date to unpublish.',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Custom sort order.',
  `date_updated` datetime NOT NULL COMMENT 'Date  when the entry is last updated.',
  `date_removed` datetime DEFAULT NULL COMMENT 'Date when the entry is marked as removed.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUFeaturedBlogPostId` (`id`) USING BTREE,
  KEY `idxNFeaturedBlogPostDateAdded` (`date_added`) USING BTREE,
  KEY `idxNFeaturedBlogPostDatePublished` (`date_published`) USING BTREE,
  KEY `idxNFeaturedBlogPostDateUnpublished` (`date_unpublished`) USING BTREE,
  KEY `idxFFeaturedBlogPost` (`post`) USING BTREE,
  CONSTRAINT `idxFFeaturedBlogPost` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for related_blog_post
-- ----------------------------
DROP TABLE IF EXISTS `related_blog_post`;
CREATE TABLE `related_blog_post` (
  `post` int(10) unsigned NOT NULL COMMENT 'Post that is associated with anohter.',
  `related_post` int(10) unsigned NOT NULL COMMENT 'The main post that is to be associated.',
  `date_added` datetime DEFAULT NULL COMMENT 'Date when the blog is added.',
  UNIQUE KEY `idxURelatedBlogPost` (`post`,`related_post`) USING BTREE,
  KEY `idxFRelatedrPost` (`related_post`) USING BTREE,
  KEY `idxNRelatedBlogPostDateAdded` (`date_added`) USING BTREE,
  CONSTRAINT `idxFOwnerPost` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFRelatedPost` FOREIGN KEY (`related_post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for tags_of_blog_post
-- ----------------------------
DROP TABLE IF EXISTS `tags_of_blog_post`;
CREATE TABLE `tags_of_blog_post` (
  `post` int(10) unsigned NOT NULL COMMENT 'Post that tags are associated with.',
  `tag` int(10) unsigned NOT NULL COMMENT 'Associated tag.',
  `date_added` datetime NOT NULL COMMENT 'Date when tag has been added to the post.',
  PRIMARY KEY (`post`,`tag`),
  UNIQUE KEY `idxUTagsOfBlogPost` (`post`,`tag`) USING BTREE,
  KEY `idxUTagsOfBlogPostsDateAdded` (`date_added`) USING BTREE,
  KEY `idxFTagOfBlogPost` (`tag`) USING BTREE,
  CONSTRAINT `idxFTaggedBlogPost` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFTagOfBlogPost` FOREIGN KEY (`tag`) REFERENCES `blog_post_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;
