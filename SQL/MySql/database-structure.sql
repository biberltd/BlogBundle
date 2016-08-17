/*
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        10.12.2015
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
  KEY `idxNBlogSite` (`site`) USING BTREE,
  KEY `idxNBlogDateRemoved` (`date_removed`) USING BTREE,
  CONSTRAINT `idxFSiteOfBlog` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

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
  UNIQUE KEY `idxUBlogLocalizationBlogLanguage` (`blog`,`language`) USING BTREE,
  UNIQUE KEY `idxUBlogLocalizationLanguageUrlKey` (`language`,`url_key`) USING BTREE,
  CONSTRAINT `idxFLanguageOfBlogLocalization` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFBlogOfBlogLocalization` FOREIGN KEY (`blog`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  CONSTRAINT `idxFBlogOfBlogModerator` FOREIGN KEY (`blog`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFCategoryOfBlogModerator` FOREIGN KEY (`category`) REFERENCES `blog_post_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFModeratorOfBlogModerator` FOREIGN KEY (`moderator`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  UNIQUE KEY `idxUIdOfBlogPostAction` (`id`) USING BTREE,
  KEY `idxNDateAddedOfBlogPostAction` (`date_added`) USING BTREE,
  KEY `idxNPostOfBlogPostAction` (`post`) USING BTREE,
  KEY `idxNMemberOfBlogPostAction` (`member`) USING BTREE,
  CONSTRAINT `idxFPostOfBlogPostOfAction` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFMemberOfBlogPostAction` FOREIGN KEY (`member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  `meta_keywords` LONGTEXT COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized comma seperated keywords.',
  `meta_description` LONGTEXT COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized meta description of category.',
  PRIMARY KEY (`post_category`,`language`),
  UNIQUE KEY `idxULanguageOfBlogPostCategoryLocalization` (`language`,`post_category`) USING BTREE,
  UNIQUE KEY `idxUUrlKeyOfBlogPostCategoryLocalization` (`language`,`url_key`,`post_category`) USING BTREE,
  CONSTRAINT `idxFLanguageOfBlogPostCategoryLocalization` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFPostCategoryOfBlogPostCategoryLocalization` FOREIGN KEY (`post_category`) REFERENCES `blog_post_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  UNIQUE KEY `idxUIdOfBlogPostComment` (`id`) USING BTREE,
  KEY `idxNAuthorOfBlogPostComment` (`author`) USING BTREE,
  KEY `idxNPostOfBlogPostComment` (`post`) USING BTREE,
  KEY `idxNSiteOfBlogPostComment` (`site`) USING BTREE,
  KEY `idxNParentOfBlogPostComment` (`parent`) USING BTREE,
  CONSTRAINT `idxFAuthorOfBlogPostComment` FOREIGN KEY (`author`) REFERENCES `member` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `idxFPostOfBlogPostComment` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFParentOfBlogPostComment` FOREIGN KEY (`parent`) REFERENCES `blog_post_comment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFSiteOfBlogPostComment` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  UNIQUE KEY `idxUIdOfBlogPostCommentAction` (`id`) USING BTREE,
  UNIQUE KEY `idxUPostOfBlogPostCommentAction` (`post`,`comment`,`member`) USING BTREE,
  KEY `idxNCommentOfBlogPostCommentAction` (`comment`) USING BTREE,
  KEY `idxNMemberOfBlogPostCommentAction` (`member`) USING BTREE,
  KEY `idxNDateAddedOfBlogPostCommentAction` (`date_added`) USING BTREE,
  CONSTRAINT `idxFCommentOBlogPostCommentAction` FOREIGN KEY (`comment`) REFERENCES `blog_post_comment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFPostOfBlogPostCommentAction` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFMemberOfBlogPostCommentAction` FOREIGN KEY (`member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  `meta_title` varchar(155) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Meta title of blog post.',
  `meta_description` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized meta description of blog.',
  `meta_keywords` varchar(155) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized meta keywords.',
  `content` text COLLATE utf8_turkish_ci COMMENT 'Blog content.',
  PRIMARY KEY (`post`,`language`),
  UNIQUE KEY `idxULanguageOfBlogPostLocalization` (`language`,`post`) USING BTREE,
  UNIQUE KEY `idxUUrlKeyOfBlogPostLocalization` (`language`,`url_key`) USING BTREE,
  CONSTRAINT `idxFLanguageOfBlogPostLocalization` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFPostOfBlogPostLocalization` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  UNIQUE KEY `idxUIdOfBlogPostModeration` (`id`) USING BTREE,
  KEY `idxNDateReviewedOfBlogPostModeration` (`date_reviewed`) USING BTREE,
  KEY `idxNDateUpdatedOfBlogPostModeration` (`date_updated`) USING BTREE,
  KEY `idxNPostOfBlogPostModeration` (`post`) USING BTREE,
  KEY `idxNModeratorOfBlogPostModeration` (`moderator`) USING BTREE,
  CONSTRAINT `idxFPostOfBlogPostModeration` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFModeratorOfBlogPostModeration` FOREIGN KEY (`moderator`) REFERENCES `member` (`id`) ON UPDATE CASCADE
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
  UNIQUE KEY `idxUIdOfBlogPostModerationReply` (`id`) USING BTREE,
  KEY `idxNDateRepliedOfBlogPostModerationReply` (`date_replied`) USING BTREE,
  KEY `idxNModerationOfBlogPostModerationReply` (`moderation`) USING BTREE,
  KEY `idxNAuthorOfBlogPostModerationReply` (`author`) USING BTREE,
  CONSTRAINT `idxFAuthorOfBlogPostModerationReply` FOREIGN KEY (`author`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFModerationOfBlogPostModerationReply` FOREIGN KEY (`moderation`) REFERENCES `blog_post_moderation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  UNIQUE KEY `idxULanguageOfBlogPostRevision` (`language`,`post`),
  KEY `idxNPostOfBlogPostRevision` (`post`),
  CONSTRAINT `idxFPostOfBlogPostRevision` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFLanguageOfBlogPostRevision` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  UNIQUE KEY `idxUIdOfBlogPostTag` (`id`) USING BTREE,
  KEY `idxNDateAddedOfBlogPostTag` (`date_added`) USING BTREE,
  KEY `idxNMemberOfBlogPostTag` (`member`) USING BTREE,
  KEY `idxNSiteOfBlogPostTag` (`site`) USING BTREE,
  KEY `idxNBlogOfBlogPostTag` (`blog`) USING BTREE,
  KEY `idxNDateUpdatedOfBlogPostTag` (`date_updated`),
  KEY `idxNDateRemovedOfBlogPostTag` (`date_removed`),
  CONSTRAINT `idxFBlogOfBlogPostTag` FOREIGN KEY (`blog`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFMemberOfBlogPostTag` FOREIGN KEY (`member`) REFERENCES `blog_post_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFSiteOfBlogPostTag` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  UNIQUE KEY `idxUTagOfBlogPostTagLocalization` (`tag`,`language`) USING BTREE,
  UNIQUE KEY `idxUUrlKeyOfBlogPostTagLocalization` (`language`,`url_key`) USING BTREE,
  CONSTRAINT `idxFLanguageOfBlogPostTagLocalization` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFTagOfBlogPostTagLocalization` FOREIGN KEY (`tag`) REFERENCES `blog_post_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  `sort_order` int(10) NOT NULL DEFAULT 1 COMMENT 'Custom sort order.',
  PRIMARY KEY (`post`,`category`),
  UNIQUE KEY `idxUCategoryOfCategoriesOfBlogPost` (`post`,`category`) USING BTREE,
  KEY `idxNPostOfCategoriesOfBlogPost` (`post`) USING BTREE,
  KEY `idxNDateAddedOfCategoriesOfBlogPost` (`date_added`) USING BTREE,
  KEY `idxNCategoryOfCategoriesOfBlogPost` (`category`) USING BTREE,
  CONSTRAINT `idxFPostOfCategoriesOfBlogPost` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFCategoryOfCategoriesOfBlogPost` FOREIGN KEY (`category`) REFERENCES `blog_post_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for favorite_blog_post_of_member
-- ----------------------------
DROP TABLE IF EXISTS `favorite_blog_post_of_member`;
CREATE TABLE `favorite_blog_post_of_member` (
  `member` int(10) unsigned NOT NULL COMMENT 'Member who added post to favorite.',
  `post` int(10) unsigned NOT NULL COMMENT 'Post that is being favorited.',
  `date_added` datetime NOT NULL COMMENT 'Date whenthe post is added as favorite.',
  UNIQUE KEY `idxUMemberPostOfFavoriteBlogPostOfMember` (`post`,`member`) USING BTREE,
  KEY `idxNMemberOfFavoriteBlogPostOfMember` (`member`) USING BTREE,
  KEY `idxNPostOfFavoriteBlogPostOfMember` (`post`) USING BTREE,
  KEY `idxNDateAddedOfFavoriteBlogPostOfMember` (`date_added`) USING BTREE,
  CONSTRAINT `idxFPostOfFavoriteBlogPostOfMember` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFMemberOfFavoriteBlogPostOfMember` FOREIGN KEY (`member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  UNIQUE KEY `idxUIdOfFeaturedBlogPost` (`id`) USING BTREE,
  KEY `idxNDateAddedOfFeaturedBlogPost` (`date_added`) USING BTREE,
  KEY `idxNDatePublishedOfFeaturedBlogPost` (`date_published`) USING BTREE,
  KEY `idxNDateUnpublishedOfFeaturedBlogPost` (`date_unpublished`) USING BTREE,
  KEY `idxNPostOfFeaturedBlogPost` (`post`) USING BTREE,
  CONSTRAINT `idxFPostOfFeaturedBlogPost` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for related_blog_post
-- ----------------------------
DROP TABLE IF EXISTS `related_blog_post`;
CREATE TABLE `related_blog_post` (
  `post` int(10) unsigned NOT NULL COMMENT 'Post that is associated with anohter.',
  `related_post` int(10) unsigned NOT NULL COMMENT 'The main post that is to be associated.',
  `date_added` datetime DEFAULT NULL COMMENT 'Date when the blog is added.',
  UNIQUE KEY `idxUPostOfRelatedBlogPost` (`post`,`related_post`) USING BTREE,
  KEY `idxNRelatedPostOfRelatedBlogPost` (`related_post`) USING BTREE,
  KEY `idxNDateAddedOfRelatedBlogPost` (`date_added`) USING BTREE,
  CONSTRAINT `idxFPostOfRelatedBlogPost` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFRelatedPostOfRelatedBlogPost` FOREIGN KEY (`related_post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  UNIQUE KEY `idxUPostOfTagsOfBlogPost` (`post`,`tag`) USING BTREE,
  KEY `idxNDateAddedOfTagsOfBlogPost` (`date_added`) USING BTREE,
  KEY `idxNTagOfTagsOfBlogPost` (`tag`) USING BTREE,
  CONSTRAINT `idxFPostOfTagsOfBlogPost` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFTagOfTagsOfBlogPost` FOREIGN KEY (`tag`) REFERENCES `blog_post_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `active_blog_post_locale`
-- ----------------------------
DROP TABLE IF EXISTS `active_blog_post_locale`;
CREATE TABLE `active_blog_post_locale` (
  `blog_post` int(10) unsigned DEFAULT NULL,
  `language` int(5) unsigned DEFAULT NULL,
  KEY `idxNActiveBlogPostLocaleBlogPost` (`blog_post`) USING BTREE,
  KEY `idxNActiveBlogPostLocaleLanguage` (`language`) USING BTREE,
  CONSTRAINT `idxFBlogPostOfActiveBlogPostLocale` FOREIGN KEY (`blog_post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE,
  CONSTRAINT `idxFLanguageOfActiveBlogPostLocale` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `featured_blog_post_localization`
-- ----------------------------
DROP TABLE IF EXISTS `featured_blog_post_localization`;
CREATE TABLE `featured_blog_post_localization` (
  `language` int(5) unsigned DEFAULT NULL,
  `content` text,
  `post` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `idxULanguageOfFeaturedBlogPostLocalization` (`language`),
  KEY `idxNPostOfFeaturedBlogPostLocalization` (`post`),
  CONSTRAINT `idxFLanguageOfFeaturedBlogPostLocalization` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE,
  CONSTRAINT `idxFPostOfFeaturedBlogPostLocalization` FOREIGN KEY (`post`) REFERENCES `featured_blog_post` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
  UNIQUE KEY `idxUPostOfFilesOfBlogPost` (`post`,`file`) USING BTREE,
  KEY `idxNDateAddedOfFilesOfBlogPost` (`date_added`) USING BTREE,
  KEY `idxNFileOfFilesOfBlogPost` (`file`) USING BTREE,
  CONSTRAINT `idxFFileOfFilesOfBlogPost` FOREIGN KEY (`file`) REFERENCES `file` (`id`) ON DELETE CASCADE,
  CONSTRAINT `idxFPostOfFilesOfBlogPost` FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS=1;
