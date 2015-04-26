<?php
/**
 * BlogModel Class
 *
 * This class acts as a database proxy model for Blog functionalities.
 *
 * @vendor          BiberLtd
 * @package         Core\Bundles\BlogBundle
 * @subpackage      Services
 * @name            BlogBundle
 *
 * @author        	Can Berkol
 *
 * @copyright   	Biber Ltd. (www.biberltd.com)
 *
 * @version     	1.0.8
 * @date        	26.04.2015
 */
namespace BiberLtd\Bundle\BlogBundle\Services;

/** Extends CoreModel */
use BiberLtd\Bundle\CoreBundle\CoreModel;

/** Entities to be used */
use BiberLtd\Bundle\BlogBundle\Entity as BundleEntity;
use BiberLtd\Bundle\FileManagementBundle\Entity as FileEntity;

/** Helper Models */
use BiberLtd\Bundle\SiteManagementBundle\Services as SMMService;

/** Core Service*/
use BiberLtd\Bundle\CoreBundle\Services as CoreServices;

class BlogModel extends CoreModel
{
    /**
     * @name            __construct ()
     *                  Constructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           object $kernel
     * @param           string $db_connection Database connection key as set in app/config.yml
     * @param           string $orm ORM that is used.
     */
    public function __construct($kernel, $db_connection = 'default', $orm = 'doctrine')
    {
        parent::__construct($kernel, $db_connection, $orm);

        /**
         * Register entity names for easy reference.
         */
        $this->entity = array(
            'blog' => array('name' => 'BlogBundle:Blog', 'alias' => 'b'),
            'blog_localization' => array('name' => 'BlogBundle:BlogLocalization', 'alias' => 'bl'),
            'blog_moderator' => array('name' => 'BlogBundle:BlogModerator', 'alias' => 'bm'),
            'blog_post' => array('name' => 'BlogBundle:BlogPost', 'alias' => 'bp'),
            'blog_post_action' => array('name' => 'BlogBundle:BlogPostAction', 'alias' => 'bpa'),
            'blog_post_category' => array('name' => 'BlogBundle:BlogPostCategory', 'alias' => 'bpcat'),
            'blog_post_category_localization' => array('name' => 'BlogBundle:BlogPostCategoryLocalization', 'alias' => 'bpcl'),
            'blog_post_comment' => array('name' => 'BlogBundle:BlogPostComment', 'alias' => 'bpcom'),
            'blog_post_comment_action' => array('name' => 'BlogBundle:BlogPostCommentAction', 'alias' => 'bpca'),
            'blog_post_field' => array('name' => 'BlogBundle:BlogPostField', 'alias' => 'bpf'),
            'blog_post_field_content' => array('name' => 'BlogBundle:BlogPostFieldContent', 'alias' => 'bpfc'),
            'blog_post_field_localization' => array('name' => 'BlogBundle:BlogPostFieldLocalization', 'alias' => 'bpfl'),
            'blog_post_localization' => array('name' => 'BlogBundle:BlogPostLocalization', 'alias' => 'bpl'),
            'blog_post_moderation' => array('name' => 'BlogBundle:BlogPostModeration', 'alias' => 'bpmo'),
            'blog_post_moderation_reply' => array('name' => 'BlogBundle:BlogPostModerationReply', 'alias' => 'bpmor'),
            'blog_post_tag' => array('name' => 'BlogBundle:BlogPostTag', 'alias' => 'bpt'),
            'blog_post_tag_localization' => array('name' => 'BlogBundle:BlogPostTagLocalzation', 'alias' => 'bptl'),
            'categories_of_blog_post' => array('name' => 'BlogBundle:CategoriesOfBlogPost', 'alias' => 'cobp'),
            'favorite_blog_posts_of_member' => array('name' => 'BlogBundle:FavroiteBlogPostsOfMember', 'alias' => 'fbpom'),
            'featured_blog_post' => array('name' => 'BlogBundle:FeaturedBlogPost', 'alias' => 'fpğ'),
            'files_of_blog_post' => array('name' => 'BlogBundle:FilesOfBlogPost', 'alias' => 'fobp'),
            'related_blog_post' => array('name' => 'BlogBundle:RelatedBlogPost', 'alias' => 'rblp'),
            'tags_of_blog_post' => array('name' => 'BlogBundle:TagsOfBlogPost', 'alias' => 'tobp'),

        );
    }

    /**
     * @name            __destruct ()
     *                  Destructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     */
    public function __destruct()
    {
        foreach ($this as $property => $value) {
            $this->$property = null;
        }
    }

    /**
     * @name            addFilesToBlogPost ()
     *                  Associates files with a given blog post by creating new row in files_of_blog_psot table.
     *
     * @since           1.0.2
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->isFileAssociatedWithBlogPost()
     * @use             $this->getMaxSortOrderOfBlogPostFile()
     *
     * @param           array $files
     * @param           mixed $post
     *
     * @return          array           $response
     */
    public function addFilesToBlogPost($files, $post)
    {
        $this->resetResponse();
        /**
         * Validate Parameters
         */
        $count = 0;
        /** remove invalid file entries */
        foreach ($files as $file) {
            if (!is_numeric($file['file']) && !$file['file'] instanceof FileEntity\File) {
                unset($files[$count]);
            }
            $count++;
        }
        /** issue an error only if there is no valid file entries */
        if (count($files) < 1) {
            return $this->createException('InvalidParameter', '$files', 'err.invalid.parameter.files');
        }
        unset($count);
        if (!is_numeric($post) && !$post instanceof BundleEntity\BlogPost) {
            return $this->createException('InvalidParameter', 'BlogPost', 'err.invalid.parameter.post');
        }
        /** If no entity is provided as post we need to check if it does exist */
        if (is_numeric($post)) {
            $response = $this->getBlogPost($post, 'id');
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'BlogPost', 'err.db.entry.notexist');
            }
            $post = $response['result']['set'];
        }
        $fModel = $this->kernel->getContainer()->get('filemanagement.model');

        $fop_collection = array();
        $count = 0;
        /** Start persisting files */
        foreach ($files as $file) {
            /** If no entity s provided as file we need to check if it does exist */
            if (is_numeric($file['file'])) {
                $response = $fModel->getFile($file['file'], 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'File', 'err.db.file.notexist');
                }
                $file['file'] = $response['result']['set'];
            }
            /** Check if association exists */
            if ($this->isFileAssociatedWithBlogPost($file['file'], $post, true)) {
                $this->createException('DuplicateAssociation', 'File => BlogPost', 'err.db.entry.notexist');
                /** If file association already exist move silently to next file */
                break;
            }
            $fop = new BundleEntity\FilesOfBlogPost();
            $now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
            $fop->setFile($file['file'])->setPost($post)->setDateAdded($now);
            if (!is_null($file['sort_order'])) {
                $fop->setSortOrder($file['sort_order']);
            } else {
                $fop->setSortOrder($this->getMaxSortOrderOfBlogPostFile($post, true) + 1);
            }
            /** persist entry */
            $this->em->persist($fop);
            $fop_collection[] = $fop;
            $count++;
        }
        /** flush all into database */
        if ($count > 0) {
            $this->em->flush();
        } else {
            $this->response['code'] = 'err.db.insert.failed';
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $fop_collection,
                'total_rows' => $count,
                'last_insert_id' => -1,
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        unset($count, $aplCollection);
        return $this->response;
    }

    /**
     * @name            addCategoriesToPost ()
     *                  Associates categories with post
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->isPostAssociatedWithCategory()
     *
     * @param           array $posts mixed array collection
     * @param           mixed $category 'entity' or 'entity' id.
     *
     * @return          array           $response
     */
    public function addCategoriesToPost($categories, $post, $primary = 'n')
    {
        $this->resetResponse();
        /**
         * Validate Parameters
         */
        $count = 0;
        /** remove invalid entries */
        foreach ($categories as $cat) {
            if (!is_numeric($cat) && !is_string($cat) && !$cat instanceof BundleEntity\BlogPostCategory) {
                unset($categories[$count]);
            }
            $count++;
        }
        /** issue an error only if there is no valid file entries */
        if (count($categories) < 1) {
            return $this->createException('InvalidParameter', 'Array of BlogPostCategory', 'err.invalid.parameter.categories');
        }
        unset($count);
        if (!is_numeric($post) && !$post instanceof BundleEntity\BlogPost) {
            return $this->createException('InvalidParameter', 'BlogpOST or integer', 'err.invalid.parameter.POST');
        }
        /** If no entity is provided as category we need to check if it does exist */
        if (is_numeric($post)) {
            $response = $this->getBlogPost($post, 'id');
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'Blog', 'err.db.entry.notexist');
            }
            $category = $response['result']['set'];
        }
        $collection = array();
        $count = 0;
        /** Start persisting files */
        foreach ($categories as $category) {
            /** If no entity is provided we need to check if it does exist */
            if (is_numeric($category)) {
                $category = $category->getBlogPostCategory($category, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'BlogPostCategory', 'err.db.entity.notexist');
                }
                $post = $response['result']['set'];
            } else if (is_string($post)) {
                $response = $this->getBlogPostCaetgory($post, 'url_key');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'BlogPost', 'err.db.entity.notexist');
                }
                $post = $response['result']['set'];
            }

            /** Check if association exists */
            if ($this->isPostAssociatedWithCategory($post, $category, true)) {
                $this->createException('DuplicateAssociationException', 'Product => Category', 'err.db.duplicate.entity');
                $this->response['code'] = 'err.db.entry.exist';
                /** If file association already exist move silently to next file */
                break;
            }
            /** prepare object */
            $assoc = new BundleEntity\CategoriesOfBlogPost();
            $now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
            $assoc->setBlogPost($post)->setCategory($category)->setDateAdded($now);
            $assoc->setIsPrimary($primary);
            /** persist entry */
            $this->em->persist($assoc);
            $collection[] = $assoc;
            $count++;
        }
        /** flush all into database */
        if ($count > 0) {
            $this->em->flush();
        } else {
            $this->response['code'] = 'err.db.insert.failed';
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection,
                'total_rows' => $count,
                'last_insert_id' => -1,
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        unset($count, $collection);
        return $this->response;
    }

    /**
     * @name            addPostsToCategory ()
     *                  Associates posts with categıry
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->isPostAssociatedWithCategory()
     *
     * @param           array $posts mixed array collection
     * @param           mixed $category 'entity' or 'entity' id.
     *
     * @return          array           $response
     */
    public function addPostsToCategory($posts, $category, $primary = 'n')
    {
        $this->resetResponse();
        /**
         * Validate Parameters
         */
        $count = 0;
        /** remove invalid entries */
        foreach ($posts as $post) {
            if (!is_numeric($post) && !is_string($post) && !$post instanceof BundleEntity\BlogPost) {
                unset($posts[$count]);
            }
            $count++;
        }
        /** issue an error only if there is no valid file entries */
        if (count($posts) < 1) {
            return $this->createException('InvalidParameter', 'Array of BlogPost', 'err.invalid.parameter.posts');
        }
        unset($count);
        if (!is_numeric($category) && !$category instanceof BundleEntity\BlogPostCategory) {
            return $this->createException('InvalidParameter', 'BlogPostCategory or integer', 'err.invalid.parameter.post_category');
        }
        /** If no entity is provided as category we need to check if it does exist */
        if (is_numeric($category)) {
            $response = $this->getBlogPostCategory($category, 'id');
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'BlogPostCategory', 'err.db.entry.notexist');
            }
            $category = $response['result']['set'];
        }
        $collection = array();
        $count = 0;
        /** Start persisting files */
        foreach ($posts as $post) {
            /** If no entity is provided we need to check if it does exist */
            if (is_numeric($post)) {
                $response = $this->getBlogPost($post, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'BlogPost', 'err.db.entity.notexist');
                }
                $post = $response['result']['set'];
            } else if (is_string($post)) {
                $response = $this->getBlogPost($post, 'url_key');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'Product', 'err.db.entry.notexist');
                }
                $post = $response['result']['set'];
            }

            /** Check if association exists */
            if ($this->isPostAssociatedWithCategory($post, $category, true)) {
//                $this->createException('DuplicateAssociationException', 'Post => Category', 'err.db.duplicate.entity');
//                $this->response['code'] = 'err.db.entry.exist';
                /** If file association already exist move silently to next file */
                break;
            }
            /** prepare object */
            $assoc = new BundleEntity\CategoriesOfBlogPost();
            $now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
            $assoc->setPost($post)->setCategory($category)->setDateAdded($now);
            $assoc->setIsPrimary($primary);
            /** persist entry */
            $this->em->persist($assoc);
            $collection[] = $assoc;
            $count++;
        }
        /** flush all into database */
        if ($count > 0) {
            $this->em->flush();
        } else {
            $this->response['code'] = 'err.db.insert.failed';
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection,
                'total_rows' => $count,
                'last_insert_id' => -1,
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        unset($count, $collection);
        return $this->response;
    }

    /**
     * @name            deleteBlog ()
     *                  Deletes provided blog from database.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $blog
     *
     * @return          array           $response
     */
    public function deleteBlog($blog)
    {
        return $this->deleteBlogs(array($blog));
    }

    /**
     * @name            deleteBlogs ()
     *                  Deletes provided blogs from database.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $collection
     *
     * @return          array           $response
     */
    public function deleteBlogs($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValue', 'Array', 'err.invalid.parameter.collection');
        }
        $countDeleted = 0;
        foreach ($collection as $entry) {
            if ($entry instanceof BundleEntity\Blog) {
                $this->em->remove($entry);
                $countDeleted++;
            } else {
                switch ($entry) {
                    case is_numeric($entry):
                        $response = $this->getBlog($entry, 'id');
                        break;
                    case is_string($entry):
                        $response = $this->getBlog($entry, 'url_key');
                        break;
                }
                if ($response['error']) {
                    $this->createException('EntryDoesNotExist', $entry, 'err.invalid.entry');
                }
                $entry = $response['result']['set'];
                $this->em->remove($entry);
                $countDeleted++;
            }
        }
        if ($countDeleted < 0) {
            $this->response['error'] = true;
            $this->response['code'] = 'err.db.fail.delete';

            return $this->response;
        }
        $this->em->flush();
        $this->response = array(
            'rowCount' => 0,
            'result' => array(
                'set' => null,
                'total_rows' => $countDeleted,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.deleted',
        );
        return $this->response;
    }

    /**
     * @name            deleteBlogPost ()
     *                  Deletes provided blog post from database.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $post
     *
     * @return          array           $response
     */
    public function deleteBlogPost($post)
    {
        return $this->deleteBlogPosts(array($post));
    }

    /**
     * @name            deleteBlogPosts ()
     *                  Deletes provided blog posts from database.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $collection
     *
     * @return          array           $response
     */
    public function deleteBlogPosts($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValue', 'Array', 'err.invalid.parameter.collection');
        }
        $countDeleted = 0;
        foreach ($collection as $entry) {
            if ($entry instanceof BundleEntity\BlogPost) {
                $this->em->remove($entry);
                $countDeleted++;
            } else {
                switch ($entry) {
                    case is_numeric($entry):
                        $response = $this->getBloBlogPostg($entry, 'id');
                        break;
                    case is_string($entry):
                        $response = $this->getBloBlogPostg($entry, 'url_key');
                        break;
                }
                if ($response['error']) {
                    $this->createException('EntryDoesNotExist', $entry, 'err.invalid.entry');
                }
                $entry = $response['result']['set'];
                $this->em->remove($entry);
                $countDeleted++;
            }
        }
        if ($countDeleted < 0) {
            $this->response['error'] = true;
            $this->response['code'] = 'err.db.fail.delete';

            return $this->response;
        }
        $this->em->flush();
        $this->response = array(
            'rowCount' => 0,
            'result' => array(
                'set' => null,
                'total_rows' => $countDeleted,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.deleted',
        );
        return $this->response;
    }

    /**
     * @name            deleteBlogPostCategory ()
     *                  Deletes provided blog post from database.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $category
     *
     * @return          array           $response
     */
    public function deleteBlogPostCategory($category)
    {
        return $this->deleteBlogPostCategories(array($category));
    }

    /**
     * @name            deleteBlogPostCategories ()
     *                  Deletes provided blog post ccategories from database.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $collection
     *
     * @return          array           $response
     */
    public function deleteBlogPostCategories($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValue', 'Array', 'err.invalid.parameter.collection');
        }
        $countDeleted = 0;
        foreach ($collection as $entry) {
            if ($entry instanceof BundleEntity\BlogPostCategory) {
                $this->em->remove($entry);
                $countDeleted++;
            } else {
                switch ($entry) {
                    case is_numeric($entry):
                        $response = $this->getBloBlogPostCategory($entry, 'id');
                        break;
                    case is_string($entry):
                        $response = $this->getBloBlogPostCategory($entry, 'url_key');
                        break;
                }
                if ($response['error']) {
                    $this->createException('EntryDoesNotExist', $entry, 'err.invalid.entry');
                }
                $entry = $response['result']['set'];
                $this->em->remove($entry);
                $countDeleted++;
            }
        }
        if ($countDeleted < 0) {
            $this->response['error'] = true;
            $this->response['code'] = 'err.db.fail.delete';

            return $this->response;
        }
        $this->em->flush();
        $this->response = array(
            'rowCount' => 0,
            'result' => array(
                'set' => null,
                'total_rows' => $countDeleted,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.deleted',
        );
        return $this->response;
    }
	/**
	 * @name            deleteBlogPostRevision()
	 *
	 * @since           1.0.8
	 * @version         1.0.8
	 * @author          Can Berkol
	 *
	 * @use             $this->deleteBlogPostRevisions()
	 *
	 * @param           mixed 			$revision
	 * @param           string 			$by
	 *
	 * @return          mixed           $response
	 */
	public function deleteBlogPostRevision($revision, $by = 'entity'){
		return $this->deleteBlogPostRevisions(array($revision), $by);
	}

	/**
	 * @name            deleteBlogPostRevisions()
	 *
	 * @since           1.0.8
	 * @version         1.0.8
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 *
	 * @param           array           $collection             Collection consists one of the following: PageRevision
	 *
	 * @return          array           $response
	 */
	public function deleteBlogPostRevisions($collection){
		$this->resetResponse();
		/** Parameter must be an array */
		if (!is_array($collection)) {
			return $this->createException('InvalidCollection', 'The $collection parameter must be an array collection.', 'msg.error.invalid.collection.array');
		}
		$countDeleted = 0;
		foreach ($collection as $entry){
			if($entry instanceof BundleEntity\BlogPostRevision){
				$this->em->remove($entry);
				$countDeleted++;
			}
		}
		if ($countDeleted < 1) {
			$this->response['error'] = true;
			$this->response['code'] = 'msg.error.db.delete.failed';

			return $this->response;
		}
		$this->em->flush();
		$this->response = array(
			'rowCount' => 0,
			'result' => array(
				'set' => null,
				'total_rows' => $countDeleted,
				'last_insert_id' => null,
			),
			'error' => false,
			'code' => 'msg.success.db.delete',
		);
		return $this->response;
	}

    /**
     * @name            getBlog ()
     *                  Returns details of a blog.
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->listBlogs()
     *
     * @param           mixed $blog
     * @param           string $by entity, id, url_key
     *
     * @return          mixed           $response
     */
    public function getBlog($blog, $by = 'id')
    {
        $this->resetResponse();
        $by_opts = array('id', 'url_key', 'entity');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValue', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($blog) && !is_numeric($blog) && !is_string($blog)) {
            return $this->createException('InvalidParameter', 'Blog', 'err.invalid.parameter.blog');
        }
        if (is_object($blog)) {
            if (!$blog instanceof BundleEntity\Blog) {
                return $this->createException('InvalidParameter', 'Blog', 'err.invalid.parameter.blog');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $blog,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->response;
        }
        switch($by){
            case 'url_key':
                $column = $this->entity['blog_localization']['alias'] . '.' . $by;
                break;
            default:
                $column = $this->entity['blog']['alias'] . '.' . $by;
                break;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $column, 'comparison' => '=', 'value' => $blog),
                )
            )
        );
        $response = $this->listBlogs($filter);
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name            getBlogPost ()
     *                  Returns details of a blog post.
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->listBlogs()
     *
     * @param           mixed $post
     * @param           string $by entity, id, url_key
     *
     * @return          mixed           $response
     */
    public function getBlogPost($post, $by = 'id')
    {
        $this->resetResponse();
        $by_opts = array('id', 'url_key', 'entity');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValue', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($post) && !is_numeric($post) && !is_string($post)) {
            return $this->createException('InvalidParameter', 'BlogPost', 'err.invalid.parameter.post');
        }
        if (is_object($post)) {
            if (!$post instanceof BundleEntity\BlogPost) {
                return $this->createException('InvalidParameter', 'BlogPost', 'err.invalid.parameter.post');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $post,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->response;
        }
        switch($by){
            case 'url_key':
                $column = $this->entity['blog_post_localization']['alias'] . '.' . $by;
                break;
            default:
                $column = $this->entity['blog_post']['alias'] . '.' . $by;
                break;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $column, 'comparison' => '=', 'value' => $post),
                )
            )
        );
        $response = $this->listBlogPosts($filter);
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name            getBlogPostCategory ()
     *                  Returns details of a blog post category.
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->listBlogs()
     *
     * @param           mixed $category
     * @param           string $by entity, id, url_key
     *
     * @return          mixed           $response
     */
    public function getBlogPostCategory($category, $by = 'id')
    {
        $this->resetResponse();
        $by_opts = array('id', 'url_key', 'entity');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValue', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($category) && !is_numeric($category) && !is_string($category)) {
            return $this->createException('InvalidParameter', 'BlogPostCategory', 'err.invalid.parameter.category');
        }
        if (is_object($category)) {
            if (!$category instanceof BundleEntity\BlogPostCategory) {
                return $this->createException('InvalidParameter', 'BlogPostCategory', 'err.invalid.parameter.category');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $category,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->response;
        }
        switch($by){
            case 'url_key':
                $column = $this->entity['blog_post_category_localization']['alias'] . '.' . $by;
                break;
            default:
                $column = $this->entity['blog_post_category']['alias'] . '.' . $by;
                break;
        }
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $column, 'comparison' => '=', 'value' => $category),
                )
            )
        );
        $response = $this->listBlogPostCategories($filter);
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }
	/**
	 * @name            getBlogPostRevision()
	 *
	 * @since           1.0.8
	 * @version         1.0.8
	 *
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 * @use             $this->listBlogPostRevisions()
	 * @use             $this->resetResponse()
	 *
	 * @param           mixed           $post
	 * @param			mixed			$language
	 * @param			integer			$revisionNumber
	 *
	 * @return          mixed           $response
	 */
	public function getBlogPostRevision($post, $language, $revisionNumber){
		$this->resetResponse();

		if (!$post instanceof BundleEntity\BlogPost && !is_numeric($post) && !is_string($post)) {
			return $this->createException('InvalidParameter', '$post parameter must hold BiberLtd\\Core\\Bundles\\BlogBundle\\Entity\\BlogPost entity, a string representing url_key, or an integer representing database row id.', 'msg.error.invalid.parameter.page');
		}

		if (is_object($post)) {
			$postId = $post->getId();
		}
		elseif(is_numeric($post)){
			$postId = $post;
		}
		elseif(is_string($post)){
			$response = $this->getBlogPost($post, 'url_key');
			if($response['error']){
				return $this->createException('InvalidParameter', '$page parameter must hold BiberLtd\\Core\\Bundles\\BlogBundle\\Entity\\BlogPost entity, a string representing url_key, or an integer representing database row id.', 'msg.error.invalid.parameter.page');
			}
			$postId = $response['result']['set'];
		}

		$mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
		if (!$language instanceof MLSEntity\Language && !is_integer($language) && !is_string($language)) {
			return $this->createException('InvalidParameter', 'Language', 'err.invalid.parameter.language');
		}

		if (is_object($language)) {
			$languageId = $language->getId();
		}
		elseif(is_numeric($language)){
			$languageId = $language;
		}
		elseif(is_string($language)){
			$response = $mlsModel->getLanguage($language, 'iso_code');
			if($response['error']){
				return $this->createException('InvalidParameter', '$page parameter must hold BiberLtd\\Core\\Bundles\\ContentManagementBundle\\Entity\\Page entity, a string representing url_key, or an integer representing database row id.', 'msg.error.invalid.parameter.page');
			}
			$blogId = $response['result']['set'];
		}

		$q = 'SELECT '.$this->entity['blog_post_revision']['alias']
			.' FROM '.$this->entity['blog_post_revision']['name'].' '.$this->entity['blog_post_revision']['alias']
			.' WHERE '.$this->entity['blog_post_revision']['name'].'.blog_post = '.$postId
			.' AND '.$this->entity['blog_post_revision']['name'].'.language = '.$languageId
			.' AND '.$this->entity['blog_post_revision']['name'].'.revision_number = '.$revisionNumber;

		$query = $this->em->createQuery($q);

		$result = $query->getResult();

		/**
		 * Prepare & Return Response
		 */
		$this->response = array(
			'rowCount' => $this->response['rowCount'],
			'result' => array(
				'set' => $result,
				'total_rows' => 1,
				'last_insert_id' => null,
			),
			'error' => false,
			'code' => 'msg.success.db.entry.exists',
		);
		return $this->response;
	}
	/**
	 * @name            getLastRevisionOfBlogPost()
	 *
	 * @since           1.0.8
	 * @version         1.0.8
	 *
	 * @author          Can Berkol
	 *
	 * @page			mixed			$post
	 * @return          mixed           $response
	 */
	public function getLastRevisionOfBlogPost($post){
		if(is_object($post) && $post instanceof BundleEntity\BlogPost){
			$blogId = $post->getId();
		}
		elseif(is_numeric($post)){
			$response = $this->getBlogPost($post, 'id');
			if(!$response['error']){
				$blogId = $response['result']['set']->getId();
			}
		}
		elseif(is_string($post)){
			$response = $this->getBlogPost($post, 'url_key');
			if(!$response['error']){
				$blogId = $response['result']['set']->getId();
			}
		}
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' =>$this->entity['blog_post_revision']['alias']. '.page', 'comparison' => '=', 'value' => $blogId),
				)
			)
		);
		$response = $this->listBlogPostRevisions($filter, array('date_added' => 'desc'), array('start' => 0, 'count' => 1));
		/**
		 * Prepare & Return Response
		 */
		$this->response = array(
			'rowCount' => $this->response['rowCount'],
			'result' => array(
				'set' => $response['result']['set'][0],
				'total_rows' => 1,
				'last_insert_id' => null,
			),
			'error' => false,
			'code' => 'scc.db.entity.exist',
		);
		return $this->response;
	}

    /**
     * @name            getMaxSortOrderOfBlogPostFile ()
     *                  Returns the largest sort order value for a given post from files_of_blog table.
     *
     * @since           1.0.4
     * @version         1.0.4
     * @author          Can Berkol
     *
     *
     * @param           mixed $post entity, id, sku
     * @param           bool $bypass if set to true return bool instead of response
     *
     * @return          mixed           bool | $response
     */
    public function getMaxSortOrderOfBlogPostFile($post, $bypass = false)
    {
        $this->resetResponse();
        if (!is_object($post) && !is_numeric($post) && !is_string($post)) {
            return $this->createException('InvalidParameter', 'BlogPost', 'err.invalid.parameter.blog_post');
        }
        if (is_object($post)) {
            if (!$post instanceof BundleEntity\BlogPost) {
                return $this->createException('InvalidParameter', 'BlogPost', 'err.invalid.parameter.blog_post');
            }
        } else {
            /** if numeric value given check if category exists */
            switch ($post) {
                case is_numeric($post):
                    $response = $this->getBlogPost($post, 'id');
                    break;
                case is_string($post):
                    $response = $this->getBlogPost($post, 'sku');
                    if ($response['error']) {
                        $response = $this->getBlogPost($post, 'url_key');
                    }
                    break;
            }
            if ($response['error']) {
                return $this->createException('InvalidParameter', 'BlogPost', 'err.invalid.parameter.post');
            }
            $post = $response['result']['set'];
        }
        $q_str = 'SELECT MAX(' . $this->entity['files_of_blog_post']['alias'] . '.sort_order) FROM ' . $this->entity['files_of_blog_post']['name'] . ' ' . $this->entity['files_of_blog_post']['alias']
            . ' WHERE ' . $this->entity['files_of_blog_post']['alias'] . '.post  = ' . $post->getId();

        $query = $this->em->createQuery($q_str);
        $result = $query->getSingleScalarResult();

        if ($bypass) {
            return $result;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $result,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name            insertBlog ()
     *                  Inserts one blog entry.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->insertBlogs()
     *
     * @param           mixed $blog
     * @param           mixed $by entity, or, post
     *
     * @return          array           $response
     */
    public function insertBlog($blog)
    {
        return $this->insertBlogs(array($blog));
    }

    /**
     * @name            insertBlogLocalizations ()
     *                  Inserts one or more localizations into database.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $collection Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertBlogLocalizations($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach ($collection as $item) {
            if ($item instanceof BundleEntity\BlogLocalization) {
                $entity = $item;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else {
                foreach ($item['localizations'] as $language => $data) {
                    $entity = new BundleEntity\BlogLocalization;
                    $entity->setBlog($item['entity']);
                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                    $response = $mlsModel->getLanguage($language, 'iso_code');
                    if (!$response['error']) {
                        $entity->setLanguage($response['result']['set']);
                    } else {
                        break 1;
                    }
                    foreach ($data as $column => $value) {
                        $set = 'set' . $this->translateColumnName($column);
                        $entity->$set($value);
                    }
                    $this->em->persist($entity);
                }
                $insertedItems[] = $entity;
                $countInserts++;
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => -1,
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name            insertBlogs ()
     *                  Inserts one or more pblogs into database.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->insertBlogLocalization()
     *
     * @param           array $collection Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertBlogs($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $countLocalizations = 0;
        $insertedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\Blog) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($data)) {
                $localizations = array();
                $entity = new BundleEntity\Blog;
                if (!property_exists($data, 'date_created')) {
                    $data->date_created = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = $data->date_created;
                }
                if (!property_exists($data, 'site')) {
                    $data->site = 1;
                }
                if (!property_exists($data, 'count_posts')) {
                    $data->count_posts = 0;
                }
                foreach ($data as $column => $value) {
                    $localeSet = false;
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations[$countInserts]['localizations'] = $value;
                            $localeSet = true;
                            $countLocalizations++;
                            break;
                        case 'site':
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'Array', 'err.invalid.entity');
                            }
                            unset($response, $sModel);
                            break;
                        default:
                            $entity->$set($value);
                            break;
                    }
                    if ($localeSet) {
                        $localizations[$countInserts]['entity'] = $entity;
                    }
                }
                $this->em->persist($entity);
                $insertedItems[] = $entity;

                $countInserts++;
            } else {
                $this->createException('InvalidDataException', '$data', 'err.invalid.data');
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /** Now handle localizations */
        if ($countInserts > 0 && $countLocalizations > 0) {
            $this->insertBlogLocalizations($localizations);
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $entity->getId(),
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name            insertBlogPost ()
     *                  Inserts one blog post entry.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->insertBlogPosts()
     *
     * @param           mixed $post
     *
     * @return          array           $response
     */
    public function insertBlogPost($post)
    {
        return $this->insertBlogPosts(array($post));
    }

    /**
     * @name            insertBlogPostLocalizations ()
     *                  Inserts one or more localizations into database.
     *
     * @since           1.0.2
     * @version         1.0.6
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $collection Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertBlogPostLocalizations($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach ($collection as $item) {
            if ($item instanceof BundleEntity\BlogPostLocalization) {
                $entity = $item;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else {
                foreach ($item['localizations'] as $language => $data) {
                    $entity = new BundleEntity\BlogPostLocalization;
                    $entity->setBlogPost($item['entity']);
                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                    $response = $mlsModel->getLanguage($language, 'iso_code');
                    if (!$response['error']) {
                        $entity->setLanguage($response['result']['set']);
                    } else {
                        break 1;
                    }
                    foreach ($data as $column => $value) {
                        $set = 'set' . $this->translateColumnName($column);
                        $entity->$set($value);
                    }
                    $this->em->persist($entity);
                }
                $insertedItems[] = $entity;
                $countInserts++;
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => -1,
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }
	/**
	 * @name            insertBlogPostRevision()
	 *
	 * @since           1.0.8
	 * @version         1.0.8
	 * @author          Can Berkol
	 *
	 * @use             $this->insertBlogPostRevisions()
	 *
	 * @param           mixed			$revision
	 *
	 * @return          array           $response
	 */
	public function insertBlogPostRevision($revision) {
		return $this->insertBlogPostRevisions(array($revision));
	}

	/**
	 * @name            insertBlogPostRevisions()
	 *
	 * @since           1.0.8
	 * @version         1.0.8
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 *
	 * @param           array 			$collection
	 *
	 * @return          array           $response
	 */
	public function insertBlogPostRevisions($collection) {
		$this->resetResponse();
		/** Parameter must be an array */
		if (!is_array($collection)) {
			return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
		}
		$countInserts = 0;
		$insertedItems = array();
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\BlogPostRevision) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			}
			else if (is_object($data)) {
				$entity = new BundleEntity\BlogPostRevision();
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'language':
							$lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
							$response = $lModel->getLanguage($value, 'id');
							if (!$response['error']) {
								$entity->$set($response['result']['set']);
							} else {
								$response = $lModel->getLanguage($value, 'iso_code');
								if (!$response['error']) {
									$entity->$set($response['result']['set']);
								} else {
									new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
								}
							}
							unset($response, $sModel);
							break;
						case 'post':
							$response = $this->getBlogPost($value, 'id');
							if (!$response['error']) {
								$entity->$set($response['result']['set']);
							} else {
								new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
							}
							unset($response, $sModel);
							break;
						default:
							$entity->$set($value);
							break;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;

				$countInserts++;
			} else {
				new CoreExceptions\InvalidDataException($this->kernel);
			}
		}
		if ($countInserts > 0) {
			$this->em->flush();
		}
		/**
		 * Prepare & Return Response
		 */
		$this->response = array(
			'rowCount' => $this->response['rowCount'],
			'result' => array(
				'set' => $insertedItems,
				'total_rows' => $countInserts,
				'last_insert_id' => $entity->getId(),
			),
			'error' => false,
			'code' => 'scc.db.insert.done',
		);
		return $this->response;
	}
    /**
     * @name            insertBlogPosts ()
     *                  Inserts one or more blog posts into database.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->insertBlogLocalization()
     *
     * @param           array $collection Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertBlogPosts($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $countLocalizations = 0;
        $insertedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\BlogPosts) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($data)) {
                $localizations = array();
                $entity = new BundleEntity\BlogPost();
                if (!property_exists($data, 'date_added')) {
                    $data->date_added = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (!property_exists($data, 'site')) {
                    $data->site = 1;
                }
                if (!property_exists($data, 'type')) {
                    $data->type = 'a';
                }
                if (!property_exists($data, 'count_like')) {
                    $data->count_like = 0;
                }
                if (!property_exists($data, 'count_view')) {
                    $data->count_view = 0;
                }
                if (!property_exists($data, 'count_dislike')) {
                    $data->count_dislike = 0;
                }
                if (!property_exists($data, 'count_comment')) {
                    $data->count_comment = 0;
                }
                foreach ($data as $column => $value) {
                    $localeSet = false;
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations[$countInserts]['localizations'] = $value;
                            $localeSet = true;
                            $countLocalizations++;
                            break;
                        case 'blog':
                            $response = $this->getBlog($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'Array', 'err.invalid.entity');
                            }
                            unset($response);
                            break;
                        case 'author':
                        case 'member':
                            $mModel = $this->kernel->getContainer()->get('membermanagement.model');
                            $response = $mModel->getMember($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'Array', 'err.invalid.entity');
                            }
                            unset($response);
                            break;
                        case 'file':
                        case 'preview_image':
                        case 'previewImage':
                            $fModel = $this->kernel->getContainer()->get('filemanagement.model');
                            $response = $fModel->getFile($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'File', 'err.invalid.entity');
                            }
                            unset($response, $sModel);
                            break;
                        case 'site':
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'Array', 'err.invalid.entity');
                            }
                            unset($response, $sModel);
                            break;
                        default:
                            $entity->$set($value);
                            break;
                    }
                    if ($localeSet) {
                        $localizations[$countInserts]['entity'] = $entity;
                    }
                }
                $this->em->persist($entity);
                $insertedItems[] = $entity;

                $countInserts++;
            } else {
                $this->createException('InvalidDataException', '$data', 'err.invalid.data');
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /** Now handle localizations */
        if ($countInserts > 0 && $countLocalizations > 0) {
            $this->insertBlogPostLocalizations($localizations);
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $entity->getId(),
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name            insertBlogPostCategory ()
     *                  Inserts one blog  category entry.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->insertBlogCaetgories()
     *
     * @param           mixed $category
     *
     * @return          array           $response
     */
    public function insertBlogPostCategory($category)
    {
        return $this->insertBlogCategories(array($category));
    }

    /**
     * @name            insertBlogPostCategoryLocalizations ()
     *                  Inserts one or more localizations into database.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $collection Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertBlogPostCategoryLocalizations($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach ($collection as $item) {
            if ($item instanceof BundleEntity\BlogPostCategoryLocalization) {
                $entity = $item;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else {
                foreach ($item['localizations'] as $language => $data) {
                    $entity = new BundleEntity\BlogPostCategoryLocalization;
                    $entity->setBlogPostCategory($item['entity']);
                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                    $response = $mlsModel->getLanguage($language, 'iso_code');
                    if (!$response['error']) {
                        $entity->setLanguage($response['result']['set']);
                    } else {
                        break 1;
                    }
                    foreach ($data as $column => $value) {
                        $set = 'set' . $this->translateColumnName($column);
                        $entity->$set($value);
                    }
                    $this->em->persist($entity);
                }
                $insertedItems[] = $entity;
                $countInserts++;
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => -1,
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name            insertBlogPostCategories ()
     *                  Inserts one or more pblogs into database.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->insertBlogLocalization()
     *
     * @param           array $collection Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertBlogPostCategories($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $countLocalizations = 0;
        $insertedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\BlogPostCategory) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($data)) {
                $localizations = array();
                $entity = new BundleEntity\BlogPostCategory();
                if (!property_exists($data, 'date_added')) {
                    $data->date_added = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (!property_exists($data, 'site')) {
                    $data->site = 1;
                }
                if (!property_exists($data, 'blog')) {
                    $data->blog = 1;
                }
                foreach ($data as $column => $value) {
                    $localeSet = false;
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations[$countInserts]['localizations'] = $value;
                            $localeSet = true;
                            $countLocalizations++;
                            break;
                        case 'blog':
                            $response = $this->getBlog($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'Array', 'err.invalid.entity');
                            }
                            unset($response);
                            break;
                        case 'parent':
                            $response = $this->getBlogPostCategory($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'Array', 'err.invalid.entity');
                            }
                            unset($response);
                            break;
                        case 'site':
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'Array', 'err.invalid.entity');
                            }
                            unset($response, $sModel);
                            break;
                        default:
                            $entity->$set($value);
                            break;
                    }
                    if ($localeSet) {
                        $localizations[$countInserts]['entity'] = $entity;
                    }
                }
                $this->em->persist($entity);
                $insertedItems[] = $entity;

                $countInserts++;
            } else {
                $this->createException('InvalidDataException', '$data', 'err.invalid.data');
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /** Now handle localizations */
        if ($countInserts > 0 && $countLocalizations > 0) {
            $this->insertBlogPostCategoryLocalizations($localizations);
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $entity->getId(),
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name            isFileAssociatedWithBlogPost ()
     *                  Checks if the file is already associated with the blog post.
     *
     * @since           1.0.4
     * @version         1.0.4
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           mixed $file 'entity' or 'entity' id
     * @param           mixed $post 'entity' or 'entity' id.
     * @param           bool $bypass true or false
     *
     * @return          mixed                    bool or $response
     */
    public function isFileAssociatedWithBlogPost($file, $post, $bypass = false)
    {
        $this->resetResponse();
        /**
         * Validate Parameters
         */
        if (!is_numeric($file) && !$file instanceof FileEntity\File) {
            return $this->createException('InvalidParameter', 'File', 'err.invalid.parameter.file');
        }

        if (!is_numeric($post) && !$post instanceof BundleEntity\BlogPost) {
            return $this->createException('InvalidParameter', 'BlogPost', 'err.invalid.parameter.post');
        }
        $fModel = $this->kernel->getContainer()->get('filemanagement.model');
        /** If no entity is provided as file we need to check if it does exist */
        if (is_numeric($file)) {
            $response = $fModel->getFile($file, 'id');
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'File', 'err.db.file.notexist');
            }
            $file = $response['result']['set'];
        }
        /** If no entity is provided as entry we need to check if it does exist */
        if (is_numeric($post)) {
            $response = $this->getBlogPost($post, 'id');
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'Product', 'err.db.entry.notexist');
            }
            $post = $response['result']['set'];
        }
        $found = false;

        $q_str = 'SELECT COUNT(' . $this->entity['files_of_blog_post']['alias'] . ')'
            . ' FROM ' . $this->entity['files_of_blog_post']['name'] . ' ' . $this->entity['files_of_blog_post']['alias']
            . ' WHERE ' . $this->entity['files_of_blog_post']['alias'] . '.file = ' . $file->getId()
            . ' AND ' . $this->entity['files_of_blog_post']['alias'] . '.post = ' . $post->getId();
        $query = $this->em->createQuery($q_str);

        $result = $query->getSingleScalarResult();

        /** flush all into database */
        if ($result > 0) {
            $found = true;
            $code = 'scc.db.entry.exist';
        } else {
            $code = 'scc.db.entry.noexist';
        }

        if ($bypass) {
            return $found;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $found,
                'total_rows' => $result,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => $code,
        );
        return $this->response;
    }

    /**
     * @name            isPostAssociatedWithCategory ()
     *                  Checks if the category is already associated with the blog post.
     *
     * @since           1.0.4
     * @version         1.0.4
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           mixed $file 'entity' or 'entity' id
     * @param           mixed $post 'entity' or 'entity' id.
     * @param           bool $bypass true or false
     *
     * @return          mixed                    bool or $response
     */
    public function isPostAssociatedWithCategory($post, $category, $bypass = false)
    {
        $this->resetResponse();
        /**
         * Validate Parameters
         */
        if (!is_numeric($category) && !$category instanceof BundleEntity\BlogPostCategory) {
            return $this->createException('InvalidParameter', 'BlogPostCategory', 'err.invalid.parameter.category');
        }

        if (!is_numeric($post) && !$post instanceof BundleEntity\BlogPost) {
            return $this->createException('InvalidParameter', 'BlogPost', 'err.invalid.parameter.post');
        }
        /** If no entity is provided as file we need to check if it does exist */
        if (is_numeric($category)) {
            $response = $this->getBlogPostCategory($category, 'id');
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'BlogPostCategory', 'err.db.category.notexist');
            }
            $file = $response['result']['set'];
        }
        /** If no entity is provided as entry we need to check if it does exist */
        if (is_numeric($post)) {
            $response = $this->getBlogPost($post, 'id');
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'Product', 'err.db.entry.notexist');
            }
            $post = $response['result']['set'];
        }
        $found = false;

        $q_str = 'SELECT COUNT(' . $this->entity['categories_of_blog_post']['alias'] . ')'
            . ' FROM ' . $this->entity['categories_of_blog_post']['name'] . ' ' . $this->entity['categories_of_blog_post']['alias']
            . ' WHERE ' . $this->entity['categories_of_blog_post']['alias'] . '.category = ' . $category->getId()
            . ' AND ' . $this->entity['categories_of_blog_post']['alias'] . '.post = ' . $post->getId();
        $query = $this->em->createQuery($q_str);

        $result = $query->getSingleScalarResult();

        /** flush all into database */
        if ($result > 0) {
            $found = true;
            $code = 'scc.db.entry.exist';
        } else {
            $code = 'scc.db.entry.noexist';
        }

        if ($bypass) {
            return $found;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $found,
                'total_rows' => $result,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => $code,
        );
        return $this->response;
    }

    /**
     * @name            listBlogPostCategories ()
     *                  List blog posts.
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $filter Multi-dimensional array
     * @param           array $sortorder key => order
     * @param           array $limit start,count
     * @param           string $query_str If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listBlogPostCategories($filter = null, $sortorder = null, $limit = null, $query_str = null)
    {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrder', '', 'err.invalid.parameter.sortorder');
        }

        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';
        if (is_null($query_str)) {
            $query_str = 'SELECT ' . $this->entity['blog_post_category_localization']['alias'] . ', ' . $this->entity['blog_post_category']['alias']
                . ' FROM ' . $this->entity['blog_post_category_localization']['name'] . ' ' . $this->entity['blog_post_category_localization']['alias']
                . ' JOIN ' . $this->entity['blog_post_category_localization']['alias'] . '.post_category ' . $this->entity['blog_post_category']['alias'];
        }
        /**
         * Prepare ORDER BY section of query.
         */
        if ($sortorder != null) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'date_added':
                    case 'date_approved':
                    case 'date_published':
                    case 'count_view':
                    case 'count_like':
                    case 'count_dislike':
                    case 'count_comment':
                        $column = $this->entity['blog_post_category']['alias'] . '.' . $column;
                        break;
                    case 'name':
                    case 'url_key':
                        $column = $this->entity['blog_post_category_localization']['alias'] . '.' . $column;
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepareWhere($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }

        if ($limit != null) {
            $lqStr = 'SELECT ' . $this->entity['blog_post_category']['alias'] . ' FROM ' . $this->entity['blog_post_category']['name'] . ' ' . $this->entity['blog_post_category']['alias'];
            $lqStr .= $where_str . $group_str . $order_str;
            $lQuery = $this->em->createQuery($lqStr);
            $lQuery = $this->addLimit($lQuery, $limit);
            $result = $lQuery->getResult();
            $selectedIds = array();
            foreach ($result as $entry) {
                $selectedIds[] = $entry->getId();
            }
            $where_str .= ' AND ' . $this->entity['blog_post_category_localization']['alias'] . '.category IN(' . implode(',', $selectedIds) . ')';
        }

        $query_str .= $where_str . $group_str . $order_str;
        $query = $this->em->createQuery($query_str);

        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();

        $categories = array();
        $unique = array();
        foreach ($result as $entry) {
            $id = $entry->getBlogPostCategory()->getId();
            if (!isset($unique[$id])) {
                $categories[] = $entry->getBlogPostCategory();
                $unique[$id] = $entry->getBlogPostCategory();
            }
        }
        unset($unique);
        $total_rows = count($categories);
        if ($total_rows < 1) {
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $categories,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }
	/**
	 * @name            listBlogPostRevisions()
	 *
	 * @since           1.0.8
	 * @version         1.0.8
	 *
	 * @author          Can Berkol
	 *
	 * @param           array 			$filter
	 * @param			array			$sortOrder
	 * @param			array			$limit
	 * @param			integer			$site
	 * @param			string			$queryStr
	 *
	 * @return          array           $response
	 */
	public function listBlogPostRevisions($filter = null, $sortOrder = null, $limit = null, $site = 1, $queryStr = null){
		$this->resetResponse();
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
		}

		$orderStr = '';
		$whereStr = '';
		$groupStr = '';
		$filterStr = '';

		$queryStr = 'SELECT '.$this->entity['blog_post_revision']['alias']
			.' FROM '.$this->entity['blog_post_revision']['alias']['name'].' '.$this->entity['blog_post_revision']['alias'];

		if ($sortOrder != null) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					default:
						$column = $this->entity['blog_post_revision']['alias'].'.'.$column;
						break;
				}
				$orderStr .= ' '.$column.' '.strtoupper($direction).', ';
			}
			$orderStr = rtrim($orderStr, ', ');
			$orderStr = ' ORDER BY '.$orderStr.' ';
		}

		/**
		 * Prepare WHERE section of query.
		 */
		if ($filter != null) {
			$filter_str = $this->prepareWhere($filter);
			$whereStr .= ' WHERE ' . $filter_str;
		}

		$queryStr .= $whereStr.$groupStr.$orderStr;

		$query = $this->em->createQuery($queryStr);

		$query = $this->addLimit($query, $limit);
		/**
		 * Prepare & Return Response
		 */
		$result = $query->getResult();

		$this->response = array(
			'rowCount' => $this->response['rowCount'],
			'result' => array(
				'set' => $result,
				'total_rows' => count($result),
				'last_insert_id' => null,
			),
			'error' => false,
			'code' => 'scc.db.entry.exist',
		);
		return $this->response;
	}
    /**
     * @name            listBlogPostss ()
     *                  List blog posts.
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $filter Multi-dimensional array
     * @param           array $sortorder key => order
     * @param           array $limit start,count
     * @param           string $query_str If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listBlogPosts($filter = null, $sortorder = null, $limit = null, $query_str = null)
    {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrder', '', 'err.invalid.parameter.sortorder');
        }
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        if (is_null($query_str)) {
            $query_str = 'SELECT ' . $this->entity['blog_post_localization']['alias'] . ', ' . $this->entity['blog_post']['alias']
                . ' FROM ' . $this->entity['blog_post_localization']['name'] . ' ' . $this->entity['blog_post_localization']['alias']
                . ' JOIN ' . $this->entity['blog_post_localization']['alias'] . '.blog_post ' . $this->entity['blog_post']['alias'];
        }
        /**
         * Prepare ORDER BY section of query.
         */
        if ($sortorder != null) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'date_added':
                    case 'date_approved':
                    case 'date_published':
                    case 'count_view':
                    case 'count_like':
                    case 'count_dislike':
                    case 'count_comment':
                        $column = $this->entity['blog_post']['alias'] . '.' . $column;
                        break;
                    case 'title':
                    case 'url_key':
                        $column = $this->entity['blog_post_localization']['alias'] . '.' . $column;
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }
        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepareWhere($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }
        if ($limit != null) {
            $lqStr = 'SELECT ' . $this->entity['blog_post']['alias'] . ' FROM ' . $this->entity['blog_post']['name'] . ' ' . $this->entity['blog_post']['alias'];
            $lqStr .= $where_str . $group_str . $order_str;
            $lQuery = $this->em->createQuery($lqStr);
            $lQuery = $this->addLimit($lQuery, $limit);
            $result = $lQuery->getResult();
            $selectedIds = array();
            foreach ($result as $entry) {
                $selectedIds[] = $entry->getId();
            }
            $where_str .= ' AND ' . $this->entity['blog_post_localization']['alias'] . '.blog_post IN(' . implode(',', $selectedIds) . ')';
        }

        $query_str .= $where_str . $group_str . $order_str;
        $query = $this->em->createQuery($query_str);

        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();

        $posts = array();
        $unique = array();
        foreach ($result as $entry) {
            $id = $entry->getBlogPost()->getId();
            if (!isset($unique[$id])) {
                $posts[] = $entry->getBlogPost();
                $unique[$id] = $entry->getBlogPost();
            }
        }
        unset($unique);
        $total_rows = count($posts);
        if ($total_rows < 1) {
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $posts,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name            listBlogs ()
     *                  List registered blogs.
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $filter Multi-dimensional array
     * @param           array $sortorder key => order
     * @param           array $limit start,count
     * @param           string $query_str If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listBlogs($filter = null, $sortorder = null, $limit = null, $query_str = null)
    {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrder', '', 'err.invalid.parameter.sortorder');
        }

        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';
        if (is_null($query_str)) {
            $query_str = 'SELECT ' . $this->entity['blog_localization']['alias'] . ', ' . $this->entity['blog']['alias']
                . ' FROM ' . $this->entity['blog_localization']['name'] . ' ' . $this->entity['blog_localization']['alias']
                . ' JOIN ' . $this->entity['blog_localization']['alias'] . '.blog ' . $this->entity['blog']['alias'];
        }
        /**
         * Prepare ORDER BY section of query.
         */
        if ($sortorder != null) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'date_created':
                    case 'date_updated':
                    case 'count_posts':
                    case 'site':
                        $column = $this->entity['blog']['alias'] . '.' . $column;
                        break;
                    case 'title':
                    case 'url_key':
                        $column = $this->entity['blog_localization']['alias'] . '.' . $column;
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepareWhere($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }

        if ($limit != null) {
            $lqStr = 'SELECT ' . $this->entity['blog']['alias'] . ' FROM ' . $this->entity['blog']['name'] . ' ' . $this->entity['blog']['alias'];
            $lqStr .= $where_str . $group_str . $order_str;
            $lQuery = $this->em->createQuery($lqStr);
            $lQuery = $this->addLimit($lQuery, $limit);
            $result = $lQuery->getResult();
            $selectedIds = array();
            foreach ($result as $entry) {
                $selectedIds[] = $entry->getId();
            }
            $where_str .= ' AND ' . $this->entity['blog_localization']['alias'] . '.blog IN(' . implode(',', $selectedIds) . ')';
        }

        $query_str .= $where_str . $group_str . $order_str;
        $query = $this->em->createQuery($query_str);

        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();

        $blogs = array();
        $unique = array();
        foreach ($result as $entry) {
            $id = $entry->getBlog()->getId();
            if (!isset($unique[$id])) {
                $blogs[] = $entry->getBlog();
                $unique[$id] = $entry->getBlog();
            }
        }
        unset($unique);
        $total_rows = count($blogs);
        if ($total_rows < 1) {
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $blogs,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name            listCategoriesOfPost ()
     *                  List categories associated with a post
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlogPostCategory()
     * @use             $this->listPostCategories()
     *
     * @param           mixed $post
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function listCategoriesOfPost($post, $filter = null, $sortorder = null, $limit = null)
    {
        $this->resetResponse();
        if (!$post instanceof BundleEntity\BlogPost && !is_numeric($post) && !is_string($post)) {
            return $this->createException('InvalidParameter', 'BlogPost entity', 'err.invalid.parameter.post');
        }
        if (!is_object($post)) {
            switch ($post) {
                case is_numeric($post):
                    $response = $this->getBlogPost($post, 'id');
                    break;
                case is_string($post):
                    $response = $post->getBlogPost($post, 'url_key');
                    break;
            }
            if ($response['error']) {
                return $this->createException('InvalidParameter', 'BlogPost entity', 'err.invalid.parameter.post');
            }
            $post = $response['result']['set'];
        }
        /** First identify posts associated with given category */
        $query_str = 'SELECT ' . $this->entity['categories_of_blog_post']['alias']
            . ' FROM ' . $this->entity['categories_of_blog_post']['name'] . ' ' . $this->entity['categories_of_blog_post']['alias']
            . ' WHERE ' . $this->entity['categories_of_blog_post']['alias'] . '.post = ' . $post->getId();
        $query = $this->em->createQuery($query_str);
        $result = $query->getResult();

        $catsInPost = array();
        if (count($result) > 0) {
            foreach ($result as $cobp) {
                $catsInPost[] = $cobp->getCategory()->getId();
            }
        }
        if (count($catsInPost) < 1) {
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => null,
                    'total_rows' => 0,
                    'last_insert_id' => null,
                ),
                'error' => true,
                'code' => 'err.db.entry.notexist',
            );
            return $this->response;
        }
        $columnI = $this->entity['blog_post_category']['alias'] . '.id';
        $conditionI = array('column' => $columnI, 'comparison' => 'in', 'value' => $catsInPost);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $conditionI,
                )
            )
        );
        return $this->listBlogPostCategories($filter, $sortorder, $limit);
    }
    /**
     * @name            listMediaOfBlogPost()
     *                  Lists one ore more random media from gallery
     *
     * @since           1.0.7
     * @version         1.0.7
     *
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           mixed       $post
     * @param           string      $mediaType      all, i, a, v, f, d, p, s
     * @param           array       $sortorder
     * @param           array       $limit
     * @param           array       $filter
     *
     * @return          array           $response
     */
    public function listFilesOfBlogPost($post, $mediaType = 'all', $sortorder = null, $limit = null, $filter = null){
        $this->resetResponse();
        $allowedTypes = array('i', 'a', 'v', 'f', 'd', 'p', 's');
        if(!$post instanceof BundleEntity\BlogPost && !is_numeric($post)){
            return $this->createException('InvalidParameterValueException', 'BlogPost entity or integer  representing row id', 'err.invalid.parameter.gallery');
        }
        if($mediaType != 'all' && !in_array($mediaType, $allowedTypes)){
            return $this->createException('InvalidParameterValueException', 'i, a, v, f, d, p, or s', 'err.invalid.parameter.mediaType');
        }
        if(is_numeric($post)){
            $response = $this->getBlogPost($post);
            if($response['error']){
                return $this->createException('InvalidParameterValueException', 'BlogPost entity or integer  representing row id', 'err.invalid.parameter.gallery');
            }
            $post = $response['result']['set'];
        }
        $qStr = 'SELECT '.$this->entity['files_of_blog_post']['alias']
            .' FROM '.$this->entity['files_of_blog_post']['name'].' '.$this->entity['files_of_blog_post']['alias']
            .' WHERE '.$this->entity['files_of_blog_post']['alias'].'.post = '.$post->getId();
        unset($response, $post);
        $whereStr = '';
        if($mediaType != 'all'){
            $whereStr = ' AND '.$this->entity['files_of_blog_post']['alias'].".type = '".$mediaType."'";
        }
        $qStr .= $whereStr;

        $query = $this->em->createQuery($qStr);

        $result = $query->getResult();

        $fileIds = array();
        $totalRows = count($result);

        if($totalRows > 0){
            foreach($result as $gm){
                $fileIds[] = $gm->getFile()->getId();
            }
        }
        else{
            $this->response = array(
                'result' => array(
                    'set' => null,
                    'total_rows' => 0,
                    'last_insert_id' => null,
                ),
                'error' => true,
                'code' => 'err.db.entry.notexist',
            );
            return $this->response;
        }

        $fileFilter[] = array('glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => 'f.id', 'comparison' => 'in', 'value' => $fileIds),
                )
            )
        );
        $fModel = $this->kernel->getContainer()->get('filemanagement.model');

        return $fModel->listFiles($fileFilter, $sortorder, $limit);
    }
    /**
     * @name            listPostsInCategory ()
     *                  List posts in a given category
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlogPostCategory()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed $category
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function listPostsInCategory($category, $filter = null, $sortorder = null, $limit = null)
    {
        $this->resetResponse();
        if (!$category instanceof BundleEntity\BlogPostCategory && !is_numeric($category) && !is_string($category)) {
            return $this->createException('InvalidParameter', 'BlogPostCategory entity', 'err.invalid.parameter.category');
        }
        if (!is_object($category)) {
            switch ($category) {
                case is_numeric($category):
                    $response = $this->getBlogPostCategory($category, 'id');
                    break;
                case is_string($category):
                    $response = $this->getBlogPostCategory($category, 'url_key');
                    break;
            }
            if ($response['error']) {
                return $this->createException('InvalidParameter', 'BlogPostCategory entity', 'err.invalid.parameter.blog');
            }
            $category = $response['result']['set'];
        }
        /** First identify posts associated with given category */
        $query_str = 'SELECT ' . $this->entity['categories_of_blog_post']['alias']
            . ' FROM ' . $this->entity['categories_of_blog_post']['name'] . ' ' . $this->entity['categories_of_blog_post']['alias']
            . ' WHERE ' . $this->entity['categories_of_blog_post']['alias'] . '.category = ' . $category->getId();
        $query = $this->em->createQuery($query_str);
        $result = $query->getResult();

        $postsInCat = array();
        if (count($result) > 0) {
            foreach ($result as $cobp) {
                $postsInCat[] = $cobp->getPost()->getId();
            }
        }
        if (count($postsInCat)<1) {
            return  $this->response = array(
                'result' => array(
                    'set' => null,
                    'total_rows' => null,
                    'last_insert_id' => null,
                ),
                'error' => true,
                'code' => 'err.collection.empty',
            );
        }
        $columnI = $this->entity['blog_post']['alias'] . '.id';
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $columnI, 'comparison' => 'in', 'value' => $postsInCat),
                )
            )
        );
        return $this->listBlogPosts($filter, $sortorder, $limit);
    }

    /**
     * @name            listPostCategoriesOfBlog()
     *                  List posts categories of a blog
     *
     * @since           1.0.3
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listBlogPostCategoriess()
     *
     * @param           mixed $blog
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function listPostCategoriesOfBlog($blog, $filter = null, $sortorder = null, $limit = null)
    {
        $this->resetResponse();
        if (!$blog instanceof BundleEntity\Blog && !is_numeric($blog) && !is_string($blog)) {
            return $this->createException('InvalidParameter', 'BlogPostCategory entity', 'err.invalid.parameter.category');
        }
        if (!is_object($blog)) {
            switch ($blog) {
                case is_numeric($blog):
                    $response = $this->getBlog($blog, 'id');
                    break;
                case is_string($blog):
                    $response = $this->getBlog($blog, 'url_key');
                    break;
            }
            if ($response['error']) {
                return $this->createException('InvalidParameter', 'Blog entity', 'err.invalid.parameter.blog');
            }
            $blog = $response['result']['set'];
        }
        /**
         * Prepare $filter
         */
        $column = $this->entity['blog_post_category']['alias'] . '.blog';
        $condition = array('column' => $column, 'comparison' => '=', 'value' => $blog->getId());
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        return $this->listBlogPostCategories($filter, $sortorder, $limit);
    }

    /**
     * @name            listPostsOfBlog ()
     *                  List posts of a blog
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listBlogPosts()
     *
     * @param           mixed $blog
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function listPostsOfBlog($blog, $filter = null, $sortorder = null, $limit = null)
    {
        $this->resetResponse();
        if (!$blog instanceof BundleEntity\Blog && !is_numeric($blog) && !is_string($blog)) {
            return $this->createException('InvalidParameter', 'Blog entity', 'err.invalid.parameter.blog');
        }
        if (!is_object($blog)) {
            switch ($blog) {
                case is_numeric($blog):
                    $response = $this->getBlog($blog, 'id');
                    break;
                case is_string($blog):
                    $response = $this->getBlog($blog, 'url_key');
                    break;
            }
            if ($response['error']) {
                return $this->createException('InvalidParameter', 'Blog entity', 'err.invalid.parameter.blog');
            }
            $blog = $response['result']['set'];
        }
        /**
         * Prepare $filter
         */
        $column = $this->entity['blog_post']['alias'] . '.blog';
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $column, 'comparison' => '=', 'value' => $blog->getId()),
                )
            )
        );
        return $this->listBlogPosts($filter, $sortorder, $limit);
    }

    /**
     * @name            listPostsOfBlogInCategory ()
     *                  List posts of a blog in a given category
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed $blog
     * @param           mixed $category
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function listPostsOfBlogInCategory($blog, $category, $filter = null, $sortorder = null, $limit = null)
    {
        $this->resetResponse();
        if (!$blog instanceof BundleEntity\Blog && !is_numeric($blog) && !is_string($blog)) {
            return $this->createException('InvalidParameter', 'Blog entity', 'err.invalid.parameter.blog');
        }
        if (!$category instanceof BundleEntity\BlogPostCategory && !is_numeric($category) && !is_string($category)) {
            return $this->createException('InvalidParameter', 'BlogPostCategory entity', 'err.invalid.parameter.category');
        }
        if (!is_object($blog)) {
            switch ($blog) {
                case is_numeric($blog):
                    $response = $this->getBlog($blog, 'id');
                    break;
                case is_string($blog):
                    $response = $this->getBlog($blog, 'url_key');
                    break;
            }
            if ($response['error']) {
                return $this->createException('InvalidParameter', 'Blog entity', 'err.invalid.parameter.blog');
            }
            $blog = $response['result']['set'];
        }
        if (!is_object($category)) {
            switch ($category) {
                case is_numeric($category):
                    $response = $this->getBlogPostCategory($category, 'id');
                    break;
                case is_string($category):
                    $response = $this->getBlogPostCategory($category, 'url_key');
                    break;
            }
            if ($response['error']) {
                return $this->createException('InvalidParameter', 'BlogPostCategory entity', 'err.invalid.parameter.blog');
            }
            $category = $response['result']['set'];
        }
        /** First identify posts associated with given category */
        $query_str = 'SELECT ' . $this->entity['categories_of_blog_post']['alias']
            . ' FROM ' . $this->entity['categories_of_blog_post']['name'] . ' ' . $this->entity['categories_of_blog_post']['alias']
            . ' WHERE ' . $this->entity['categories_of_blog_post']['alias'] . '.category = ' . $category->getId();
        $query = $this->em->createQuery($query_str);
        $result = $query->getResult();

        $postsInCat = array();
        if (count($result) > 0) {
            foreach ($result as $cobp) {
                $postsInCat[] = $cobp->getPost()->getId();
            }
        }
        $selectedIds = implode(',', $postsInCat);
        $columnI = $this->entity['blog_post']['alias'] . '.id';
        $conditionI = array('column' => $columnI, 'comparison' => '=', 'in' => $selectedIds);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $conditionI,
                )
            )
        );
        return $this->listPostsOfBlog($blog, $filter, $sortorder, $limit);
    }

    /**
     * @name            listPublishedPosts ()
     *                  List published posts.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->listBlogPosts()
     *
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function listPublishedPosts($filter = null, $sortorder = null, $limit = null)
    {
        /**
         * Prepare date_published filter
         */
        $now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
        $columnDA = $this->entity['blog_post']['alias'] . '.date_published';
        $conditionDA = array('column' => $columnDA, 'comparison' => '<=', 'value' => $now->format('Y-m-d h:i:s'));
        /**
         * Prepare date_unpublished filter
         */
        $columnDU = $this->entity['blog_post']['alias'] . '.date_unpublished';
        $conditionDU = array('column' => $columnDU, 'comparison' => 'isnull', 'value' => '');
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $conditionDA,
                ),
                array(
                    'glue' => 'and',
                    'condition' => $conditionDU,
                )
            )
        );
        return $this->listBlogPosts($filter, $sortorder, $limit);
    }

    /**
     * @name            listPublishedPostsOfBlog ()
     *                  List published posts of a blog. Published posts are those that have a
     *                  published date set in the past with no unpublished date (null).
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed $blog
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function listPublishedPostsOfBlog($blog, $filter = null, $sortorder = null, $limit = null)
    {
        /**
         * Prepare  filter
         */
        $columnDA = $this->entity['blog_post']['alias'] . '.blog';
        $conditionDA = array('column' => $columnDA, 'comparison' => '<=', 'value' => $blog);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $conditionDA,
                )
            )
        );
        return $this->listPublishedPosts($blog, $filter, $sortorder, $limit);
    }

    /**
     * @name            listPublishedPostsOfBlogInCategory ()
     *                  List published posts of a blog in a category.
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listPublishedBlogPosts()
     *
     * @param           mixed $blog
     * @param           mixed $category
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function listPublishedPostsOfBlogInCategory($blog, $category, $filter = null, $sortorder = null, $limit = null)
    {
        $this->resetResponse();
        if (!$blog instanceof BundleEntity\Blog && !is_numeric($blog) && !is_string($blog)) {
            return $this->createException('InvalidParameter', 'Blog entity', 'err.invalid.parameter.blog');
        }
        if (!$category instanceof BundleEntity\BlogPostCategory && !is_numeric($category) && !is_string($category)) {
            return $this->createException('InvalidParameter', 'BlogPostCategory entity', 'err.invalid.parameter.category');
        }
        if (!is_object($blog)) {
            switch ($blog) {
                case is_numeric($blog):
                    $response = $this->getBlog($blog, 'id');
                    break;
                case is_string($blog):
                    $response = $this->getBlog($blog, 'url_key');
                    break;
            }
            if ($response['error']) {
                return $this->createException('InvalidParameter', 'Blog entity', 'err.invalid.parameter.blog');
            }
            $blog = $response['result']['set'];
        }
        if (!is_object($category)) {
            switch ($category) {
                case is_numeric($category):
                    $response = $this->getBlogPostCategory($category, 'id');
                    break;
                case is_string($category):
                    $response = $this->getBlogPostCategory($category, 'url_key');
                    break;
            }
            if ($response['error']) {
                return $this->createException('InvalidParameter', 'BlogPostCategory entity', 'err.invalid.parameter.blog');
            }
            $category = $response['result']['set'];
        }
        /** First identify posts associated with given category */
        $query_str = 'SELECT ' . $this->entity['categories_of_blog_post']['alias']
            . ' FROM ' . $this->entity['categories_of_blog_post']['name'] . ' ' . $this->entity['categories_of_blog_post']['alias']
            . ' WHERE ' . $this->entity['categories_of_blog_post']['alias'] . '.category = ' . $category->getId();
        $query = $this->em->createQuery($query_str);
        $result = $query->getResult();

        $postsInCat = array();
        if (count($result) > 0) {
            foreach ($result as $cobp) {
                $postsInCat[] = $cobp->getPost()->getId();
            }
        }
        $selectedIds = implode(',', $postsInCat);
        /**
         * Prepare $filter
         */
        $columnI = $this->entity['blog_post']['alias'] . '.id';
        $conditionI = array('column' => $columnI, 'comparison' => '=', 'in' => $selectedIds);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $conditionI,
                ),
            )
        );
        return $this->listPublishedPostsOfBlog($blog, $filter, $sortorder, $limit);
    }

    /**
     * @name            removeCategoriesFromPost ()
     *                  Removes the association of categories with posts.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->doesProductExist()
     * @use             $this->isPostAssociatedWithCategory()
     *
     * @param           array $categories
     * @param           mixed $post 'entity' or 'entity' id.
     *
     * @return          array           $response
     */
    public function removeCategoriesFromPost($categories, $post)
    {
        $this->resetResponse();
        /**
         * Validate Parameters
         */
        $count = 0;
        /** remove invalid file entries */
        foreach ($categories as $category) {
            if (!is_numeric($category) && !$category instanceof BundleEntity\BlogPostCategory && !$category instanceof BundleEntity\CategoriesOfPost) {
                unset($category[$count]);
            }
            $count++;
        }
        if (count($categories) < 1) {
            $this->response = array(
                'result' => array(
                    'set' => null,
                    'total_rows' => null,
                    'last_insert_id' => null,
                ),
                'error' => true,
                'code' => 'err.collection.empty',
            );
        }
        if (!is_numeric($post) && !$post instanceof BundleEntity\BlogPost) {
            return $this->createException('InvalidParameter', 'BlogPost', 'err.invalid.parameter.post');
        }
        /** If no entity is provided as post we need to check if it does exist */
        if (is_numeric($post)) {
            $response = $this->getBlogPost($post, 'id');
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'BlogPost', 'err.db.entry.notexist');
            }
            $post = $response['result']['set'];
        }
        $cop_count = 0;
        $to_remove = array();
        $count = 0;
        /** Start persisting entries */
        foreach ($categories as $category) {
            /** If no entity is provided as file we need to check if it does exist */
            if (is_numeric($category)) {
                $response = $this->getBlogPostCategory($category, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'BlogPostCategory', 'err.db.entity.notexist');
                }
                $to_remove[] = $category;
            }
            if ($category instanceof BundleEntity\CategoriesOfBlogPost) {
                $this->em->remove($category);
                $cop_count++;
            }
            $count++;
        }
        /** flush all into database */
        if ($cop_count > 0) {
            $this->em->flush();
        }

        if (count($to_remove) > 0) {
            $ids = implode(',', $to_remove);
            $table = $this->entity['categories_of_blog_post']['name'] . ' ' . $this->entity['categories_of_blog_post']['alias'];
            $q_str = 'DELETE FROM ' . $table
                . ' WHERE ' . $this->entity['categories_of_blog_post']['alias'] . '.post = ' . $post->getId()
                . ' AND ' . $this->entity['categories_of_blog_post']['alias'] . '.category IN(' . $ids . ')';

            $query = $this->em->createQuery($q_str);
            /**
             * 6. Run query
             */
            $query->getResult();
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $to_remove,
                'total_rows' => $count,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.delete.done',
        );
        unset($count, $to_remove);
        return $this->response;
    }

    /**
     * @name            removePostsFromCategory ()
     *                  Removes the association of categories with posts.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->doesProductExist()
     *
     * @param           array $categories
     * @param           mixed $post 'entity' or 'entity' id.
     *
     * @return          array           $response
     */
    public function removePostsFromCategory($posts, $category)
    {
        $this->resetResponse();
        /**
         * Validate Parameters
         */
        $count = 0;
        /** remove invalid file entries */
        foreach ($posts as $post) {
            if (!is_numeric($post) && !$post instanceof BundleEntity\BlogPost && !$category instanceof BundleEntity\CategoriesOfPost) {
                unset($posts[$count]);
            }
            $count++;
        }
        if (count($post) < 1) {
            $this->response = array(
                'result' => array(
                    'set' => null,
                    'total_rows' => null,
                    'last_insert_id' => null,
                ),
                'error' => true,
                'code' => 'err.collection.empty',
            );
        }
        if (!is_numeric($category) && !$category instanceof BundleEntity\BlogPostCategory) {
            return $this->createException('InvalidParameter', 'BlogPostCategory', 'err.invalid.parameter.post');
        }
        /** If no entity is provided as post we need to check if it does exist */
        if (is_numeric($post)) {
            $response = $this->getBlogPost($post, 'id');
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'BlogPost', 'err.db.post.notexist');
            }
            $post = $response['result']['set'];
        }
        $cop_count = 0;
        $to_remove = array();
        $count = 0;
        /** Start persisting entries */
        foreach ($posts as $post) {
            /** If no entity is provided as file we need to check if it does exist */
            if (is_numeric($post)) {
                $response = $this->getBlogPost($post, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'BlogPost', 'err.db.entity.notexist');
                }
                $to_remove[] = $category;
            }
            if ($category instanceof BundleEntity\CategoriesOfBlogPost) {
                $this->em->remove($category);
                $cop_count++;
            }
            $count++;
        }
        /** flush all into database */
        if ($cop_count > 0) {
            $this->em->flush();
        }

        if (count($to_remove) > 0) {
            $ids = implode(',', $to_remove);
            $table = $this->entity['categories_of_blog_post']['name'] . ' ' . $this->entity['categories_of_blog_post']['alias'];
            $q_str = 'DELETE FROM ' . $table
                . ' WHERE ' . $this->entity['categories_of_blog_post']['alias'] . '.category  = ' . $category->getId()
                . ' AND ' . $this->entity['categories_of_blog_post']['alias'] . '.post IN(' . $ids . ')';

            $query = $this->em->createQuery($q_str);
            /**
             * 6. Run query
             */
            $query->getResult();
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $to_remove,
                'total_rows' => $count,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.delete.done',
        );
        unset($count, $to_remove);
        return $this->response;
    }

    /**
     * @name            updateBlog ()
     *                  Updates a single blog entry.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->updateBlogs()
     *
     * @param           mixed $blog
     *
     * @return          array           $response
     */
    public function updateBlog($blog)
    {
        return $this->updateBlogs(array($blog));
    }

    /**
     * @name            updateBlogs ()
     *                  Updates one or more blog entries.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $collection Collection of entities or stdClass of entity details.
     *
     * @return          array           $response
     */
    public function updateBlogs($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countUpdates = 0;
        $updatedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\Blog) {
                $entity = $data;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            } else if (is_object($data)) {
                if (!property_exists($data, 'id') || !is_numeric($data->id)) {
                    return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
                }
                if (property_exists($data, 'date_created')) {
                    unset($data->date_created);
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                $response = $this->getBlog($data->id, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'Blog with id ' . $data->id, 'err.invalid.entity');
                }
                $oldEntity = $response['result']['set'];
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations = array();
                            foreach ($value as $langCode => $translation) {
                                $localization = $oldEntity->getLocalization($langCode, true);
                                $newLocalization = false;
                                if (!$localization) {
                                    $newLocalization = true;
                                    $localization = new BundleEntity\BlogLocalization();
                                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                                    $response = $mlsModel->getLanguage($langCode, 'iso_code');
                                    $localization->setLanguage($response['result']['set']);
                                    $localization->setBlog($oldEntity);
                                }
                                foreach ($translation as $transCol => $transVal) {
                                    $transSet = 'set' . $this->translateColumnName($transCol);
                                    $localization->$transSet($transVal);
                                }
                                if ($newLocalization) {
                                    $this->em->persist($localization);
                                }
                                $localizations[] = $localization;
                            }
                            $oldEntity->setLocalizations($localizations);
                            break;
                        case 'site':
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'Site', 'err.invalid.entity');
                            }
                            unset($response, $sModel);
                            break;
                        case 'id':
                            break;
                        default:
                            $oldEntity->$set($value);
                            break;
                    }
                    if ($oldEntity->isModified()) {
                        $this->em->persist($oldEntity);
                        $countUpdates++;
                        $updatedItems[] = $oldEntity;
                    }
                }
            } else {
                $this->createException('InvalidData', '$data', 'err.invalid.data');
            }
        }
        if ($countUpdates > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $updatedItems,
                'total_rows' => $countUpdates,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.update.done',
        );
        return $this->response;
    }

    /**
     * @name            updateBlogPost ()
     *                  Updates a single blog post.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->updateBlogPosts()
     *
     * @param           mixed $post
     *
     * @return          array           $response
     */
    public function updateBlogPost($post)
    {
        return $this->updateBlogPosts(array($post));
    }

    /**
     * @name            updateBlogPosts ()
     *                  Updates one or more blog entries.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $collection Collection of Product entities or array of entity details.
     * @param           array $collection Collection of entities or stdClass of entity details.
     *
     * @return          array           $response
     */
    public function updateBlogPosts($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countUpdates = 0;
        $updatedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\BlogPost) {
                $entity = $data;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            } else if (is_object($data)) {
                if (!property_exists($data, 'id') || !is_numeric($data->id)) {
                    return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
                }
                if (property_exists($data, 'date_added')) {
                    unset($data->date_added);
                }
                $response = $this->getBlogPost($data->id, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'BlogPost with id ' . $data->id, 'err.invalid.entity');
                }
                $oldEntity = $response['result']['set'];
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations = array();
                            foreach ($value as $langCode => $translation) {
                                $localization = $oldEntity->getLocalization($langCode, true);
                                $newLocalization = false;
                                if (!$localization) {
                                    $newLocalization = true;
                                    $localization = new BundleEntity\BlogPostLocalization();
                                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                                    $response = $mlsModel->getLanguage($langCode, 'iso_code');
                                    $localization->setLanguage($response['result']['set']);
                                    $localization->setBlogPost($oldEntity);
                                }
                                foreach ($translation as $transCol => $transVal) {
                                    $transSet = 'set' . $this->translateColumnName($transCol);
                                    $localization->$transSet($transVal);
                                }
                                if ($newLocalization) {
                                    $this->em->persist($localization);
                                }
                                $localizations[] = $localization;
                            }
                            $oldEntity->setLocalizations($localizations);
                            break;
                        case 'author':
                            $mModel = $this->kernel->getContainer()->get('membermanagement.model');
                            $response = $mModel->getMember($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'Site', 'err.invalid.entity');
                            }
                            unset($response, $sModel);
                            break;
                        case 'file':
                        case 'preview_image':
                        case 'previewImage':
                            $fModel = $this->kernel->getContainer()->get('filemanagement.model');
                            $response = $fModel->getFile($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'File', 'err.invalid.entity');
                            }
                            unset($response, $sModel);
                            break;
                        case 'site':
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'Site', 'err.invalid.entity');
                            }
                            unset($response, $sModel);
                            break;
                        case 'id':
                            break;
                        default:
                            $oldEntity->$set($value);
                            break;
                    }
                    if ($oldEntity->isModified()) {
                        $this->em->persist($oldEntity);
                        $countUpdates++;
                        $updatedItems[] = $oldEntity;
                    }
                }
            } else {
                $this->createException('InvalidData', '$data', 'err.invalid.data');
            }
        }
        if ($countUpdates > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $updatedItems,
                'total_rows' => $countUpdates,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.update.done',
        );
        return $this->response;
    }

    /**
     * @name            updateBlogPostCategory ()
     *                  Updates a single blog post category.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->updateBlogPostCategories()
     *
     * @param           mixed $category
     *
     * @return          array           $response
     */
    public function updateBlogPostCategory($category)
    {
        return $this->updateBlogPostCategories(array($category));
    }

    /**
     * @name            updateBlogPostCategories ()
     *                  Updates one or more blog categories.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $collection Collection of Product entities or array of entity details.
     * @param           array $collection Collection of entities or stdClass of entity details.
     *
     * @return          array           $response
     */
    public function updateBlogPostCategories($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countUpdates = 0;
        $updatedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\BlogPostCategory) {
                $entity = $data;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            } else if (is_object($data)) {
                if (!property_exists($data, 'id') || !is_numeric($data->id)) {
                    return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
                }
                if (property_exists($data, 'date_added')) {
                    unset($data->date_added);
                }
                $response = $this->getBlogPost($data->id, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'BlogPostCategory with id ' . $data->id, 'err.invalid.entity');
                }
                $oldEntity = $response['result']['set'];
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations = array();
                            foreach ($value as $langCode => $translation) {
                                $localization = $oldEntity->getLocalization($langCode, true);
                                $newLocalization = false;
                                if (!$localization) {
                                    $newLocalization = true;
                                    $localization = new BundleEntity\BlogPostCategoryLocalization();
                                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                                    $response = $mlsModel->getLanguage($langCode, 'iso_code');
                                    $localization->setLanguage($response['result']['set']);
                                    $localization->setBlogPostCategory($oldEntity);
                                }
                                foreach ($translation as $transCol => $transVal) {
                                    $transSet = 'set' . $this->translateColumnName($transCol);
                                    $localization->$transSet($transVal);
                                }
                                if ($newLocalization) {
                                    $this->em->persist($localization);
                                }
                                $localizations[] = $localization;
                            }
                            $oldEntity->setLocalizations($localizations);
                            break;
                        case 'blog':
                            $response = $this->getBlog($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'Blog', 'err.invalid.entity');
                            }
                            unset($response, $sModel);
                            break;
                        case 'parent':
                            $response = $this->getBlogPostCategory($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'BlogPostCategory', 'err.invalid.entity');
                            }
                            unset($response, $sModel);
                            break;
                        case 'site':
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                $this->createException('EntityDoesNotExist', 'Site', 'err.invalid.entity');
                            }
                            unset($response, $sModel);
                            break;
                        case 'id':
                            break;
                        default:
                            $oldEntity->$set($value);
                            break;
                    }
                    if ($oldEntity->isModified()) {
                        $this->em->persist($oldEntity);
                        $countUpdates++;
                        $updatedItems[] = $oldEntity;
                    }
                }
            } else {
                $this->createException('InvalidData', '$data', 'err.invalid.data');
            }
        }
        if ($countUpdates > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $updatedItems,
                'total_rows' => $countUpdates,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.update.done',
        );
        return $this->response;
    }
	/**
	 * @name            updateBlogPostRevision()
	 *
	 * @since           1.0.8
	 * @version         1.0.8
	 * @author          Can Berkol
	 *
	 * @use             $this->updatePageRevisions()
	 *
	 * @param           mixed 			$revision
	 *
	 * @return          mixed           $response
	 */
	public function updateBlogPostRevision($revision){
		return $this->updateBlogPostRevisions(array($revision));
	}
	/**
	 * @name            updateBlogPostRevisions()
	 *
	 * @since           1.0.8
	 * @version         1.0.8
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 *
	 * @param           array 			$collection
	 *
	 * @return          array           $response
	 */
	public function updateBlogPostRevisions($collection) {
		$this->resetResponse();
		/** Parameter must be an array */
		if (!is_array($collection)) {
			return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
		}
		$countUpdates = 0;
		$updatedItems = array();
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\BlogPostRevision) {
				$entity = $data;
				$this->em->persist($entity);
				$updatedItems[] = $entity;
				$countUpdates++;
			}
			else if (is_object($data)) {
				if (!property_exists($data, 'date_updated')) {
					$data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
				}
				if (property_exists($data, 'date_added')) {
					unset($data->date_added);
				}
				$response = $this->getBlogPostRevision($data->page, $data->language, $data->revision_number);
				if ($response['error']) {
					return $this->createException('EntityDoesNotExist', 'BlogPostRevision', 'err.invalid.entity');
				}
				$oldEntity = $response['result']['set'];

				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'post':
							$response = $this->getBlogPost($value, 'id');
							if (!$response['error']) {
								$oldEntity->$set($response['result']['set']);
							} else {
								new CoreExceptions\EntityDoesNotExistException($this->kernel, $value);
							}
							unset($response, $pModel);
							break;
						case 'language':
							$lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
							$response = $lModel->getLanguage($value, 'id');
							if (!$response['error']) {
								$oldEntity->$set($response['result']['set']);
							} else {
								new CoreExceptions\EntityDoesNotExistException($this->kernel, $value);
							}
							unset($response, $lModel);
							break;
						default:
							$oldEntity->$set($value);
							break;
					}
					if ($oldEntity->isModified()) {
						$this->em->persist($oldEntity);
						$countUpdates++;
						$updatedItems[] = $oldEntity;
					}
				}
			} else {
				new CoreExceptions\InvalidDataException($this->kernel);
			}
		}
		if ($countUpdates > 0) {
			$this->em->flush();
		}
		/**
		 * Prepare & Return Response
		 */
		$this->response = array(
			'rowCount' => $this->response['rowCount'],
			'result' => array(
				'set' => $updatedItems,
				'total_rows' => $countUpdates,
				'last_insert_id' => null,
			),
			'error' => false,
			'code' => 'scc.db.update.done',
		);
		return $this->response;
	}
    /**
     * @name            listPublishedPostsOfBlog ()
     *                  List published posts of a blog that between given dates.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed $blog
     * @param           mixed $dates
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function listPublishedPostsOfBlogBetween($blog, $dates, $filter = null, $sortorder = null, $limit = null)
    {
        /**
         * Prepare date_published filter
         */
        $now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
        $columnDA = $this->entity['blog_post']['alias'] . '.date_published';
        $conditionDA = array('column' => $columnDA, 'comparison' => '<=', 'value' => $now->format('Y-m-d h:i:s'));
        /**
         * Prepare date_unpublished filter
         */
        $columnDU = $this->entity['blog_post']['alias'] . '.date_published';
        $conditionDU = array('column' => $columnDU, 'comparison' => 'between', 'value' => '');
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $conditionDA,
                ),
                array(
                    'glue' => 'and',
                    'condition' => $conditionDU,
                )
            )
        );
        return $this->listPostsOfBlog($blog, $filter, $sortorder, $limit);
    }

    /**
     * @name            listPublishedPostsOfBlogBefore ()
     *                  List published posts of a blog that before than given date.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed $blog
     * @param           mixed $date
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function listPublishedPostsOfBlogBefore($blog, $date, $filter = null, $sortorder = null, $limit = null)
    {
        /**
         * Prepare date_published filter
         */
        $column = $this->entity['blog_post']['alias'] . '.date_published';
        $condition = array('column' => $column, 'comparison' => 'before', 'value' => $date);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        return $this->listPublishedPostsOfBlog($blog, $filter, $sortorder, $limit);
    }

    /**
     * @name            listPublishedPostsOfBlogAfter ()
     *                  List published posts of a blog that after than given date.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed $blog
     * @param           mixed $date
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function listPublishedPostsOfBlogAfter($blog, $date, $filter = null, $sortorder = null, $limit = null)
    {
        /**
         * Prepare date_published filter
         */
        $column = $this->entity['blog_post']['alias'] . '.date_published';
        $condition = array('column' => $column, 'comparison' => 'after', 'value' => $date);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        return $this->listPublishedPostsOfBlog($blog, $filter, $sortorder, $limit);
    }

    /**
     * @name            listPostsInCategoryByPublishDate ()
     *                 Lists posts in category by publis date.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed $category
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function listPostsInCategoryByPublishDate($category, $filter = null, $sortorder = null, $limit = null)
    {
        /**
         * Prepare date_published filter
         */
        $column = $this->entity['blog_post']['alias'] . '.date_published';
        $sortorder[$column] = 'asc';
        $response = $this->listPostsInCategory($category, $filter, $sortorder, $limit);
        if ($response['error']) {
            return $response;
        }
        $posts = $response['result']['set'];
        unset($response);
        $collection = array();
        foreach ($posts as $item) {
            $collection[] = $item;
        }
        unset($posts);

        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection,
                'total_rows' => count($collection),
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.list.done',
        );
        return $this->response;
    }

    /**
     * @name            getNextPostInCategoryByPublishDate ()
     *                 Gets next post in category by publish date.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     *
     * @use             $this->listPostsInCategoryByPublishDate()
     *
     * @param           mixed $post
     * @param           mixed $category
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function getNextPostInCategoryByPublishDate($post, $category, $filter = null, $sortorder = null, $limit = null)
    {
        $response = $this->listPostsInCategoryByPublishDate($category, $filter, $sortorder, $limit);
        if ($response['error']) {
            return $response;
        }
        $posts = $response['result']['set'];
        unset($response);
        foreach ($posts as $key => $item) {
            if ($item->getId() == $post) {
                $currentKey = $key-1>=0 ? $key-1 : 0;
            }
        }

        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $posts[$currentKey],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.get.done',
        );
        return $this->response;
    }
    /**
     * @name            getPreviousPostInCategoryByPublishDate ()
     *                 Gets previous post in category by publis date.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     *
     * @use             $this->listPostsInCategoryByPublishDate()
     *
     * @param           mixed $post
     * @param           mixed $category
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function getPreviousPostInCategoryByPublishDate($post, $category, $filter = null, $sortorder = null, $limit = null)
    {
        $response = $this->listPostsInCategoryByPublishDate($category, $filter, $sortorder, $limit);
        if ($response['error']) {
            return $response;
        }
        $posts = $response['result']['set'];
        unset($response);
        foreach ($posts as $key => $item) {
            if ($item->getId() == $post) {
                $currentKey = $key+1>count($posts) ? count($posts) :  $key+1;
            }
        }

        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $posts[$currentKey],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.get.done',
        );
        return $this->response;
    }
    /**
     * @name            getFirstPostInCategoryByPublishDate ()
     *                 Gets first post in category by publis date.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     *
     * @use             $this->listPostsInCategoryByPublishDate()
     *
     * @param           mixed $category
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function getFirstPostInCategoryByPublishDate($category, $filter = null, $sortorder = null, $limit = null)
    {
        $response = $this->listPostsInCategoryByPublishDate($category, $filter, $sortorder, $limit);
        if ($response['error']) {
            return $response;
        }
        $posts = $response['result']['set'];
        unset($response);
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $posts[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.get.done',
        );
        return $this->response;
    }
    /**
     * @name            getLastPostInCategoryByPublishDate ()
     *                 Gets last post in category by publis date.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     *
     * @use             $this->listPostsInCategoryByPublishDate()
     *
     * @param           mixed $category
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function getLastPostInCategoryByPublishDate($category, $filter = null, $sortorder = null, $limit = null)
    {
        $response = $this->listPostsInCategoryByPublishDate($category, $filter, $sortorder, $limit);
        if ($response['error']) {
            return $response;
        }
        $posts = $response['result']['set'];
        unset($response);

        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $posts[count($posts)-1],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.get.done',
        );
        return $this->response;
    }
    /**
     * @name            countTotalPostsInCategory ()
     *                 Counts total post in category.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     *
     * @use             $this->listPostsInCategory()
     *
     * @param           mixed $category
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function countTotalPostsInCategory($category, $filter = null, $sortorder = null, $limit = null)
    {
        $response = $this->listPostsInCategory($category, $filter, $sortorder, $limit);
        if ($response['error']) {
            return $response;
        }
        $count = $response['result']['total_rows'];
        unset($response);

        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $count,
                'total_rows' => $count,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.get.done',
        );
        return $this->response;
    }

    /**
     * @name            countTotalPostsInBlog ()
     *                 Counts total post in blog.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     *
     * @use             $this->listPostsInCategory()
     *
     * @param           mixed $blog
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function countTotalPostsInBlog($blog, $filter = null, $sortorder = null, $limit = null)
    {
        /**
         * Prepare date_published filter
         */
        $column = $this->entity['blog_post']['alias'] . '.blog';
        $condition = array('column' => $column, 'comparison' => '=', 'value' => $blog);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listBlogPosts($filter, $sortorder, $limit);
        if ($response['error']) {
            return $response;
        }
        $count = count($response['result']['set']);
        unset($response);

        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $count,
                'total_rows' => $count,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.get.done',
        );
        return $this->response;
    }
    /**
     * @name            listPostsInBlogByPublishDate ()
     *                 Lists posts in blog by publis date.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed $blog
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function listPostsInBlogByPublishDate($blog, $filter = null, $sortorder = null, $limit = null)
    {
        /**
         * Prepare date_published filter
         */
        $column = $this->entity['blog_post']['alias'] . '.date_published';
        $sortorder[$column] = 'asc';
        $response = $this->listPostsOfBlog($blog,$filter, $sortorder, $limit);
        if ($response['error']) {
            return $response;
        }
        $posts = $response['result']['set'];
        unset($response);
        $collection = array();
        foreach ($posts as $item) {
            $collection[] = $item;
        }
        unset($posts);

        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection,
                'total_rows' => count($collection),
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.list.done',
        );
        return $this->response;
    }
    /**
     * @name            getNextPostInBlogByPublishDate ()
     *                 Gets next post in category by publis date.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     *
     * @use             $this->listPostsInBlogByPublishDate()
     *
     * @param           mixed $post
     * @param           mixed $category
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function getNextPostInBlogByPublishDate($post, $category, $filter = null, $sortorder = null, $limit = null)
    {
        $response = $this->listPostsInBlogByPublishDate($category, $filter, $sortorder, $limit);
        if ($response['error']) {
            return $response;
        }
        $posts = $response['result']['set'];
        unset($response);
        foreach ($posts as $key => $item) {
            if ($item->getId() == $post) {
                $currentKey = $key-1>=0 ? $key-1 : 0;
            }
        }

        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $posts[$currentKey],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.get.done',
        );
        return $this->response;
    }
    /**
     * @name            getPreviousPostInBlogByPublishDate ()
     *                 Gets previous post in category by publis date.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     *
     * @use             $this->listPostsInBlogByPublishDate()
     *
     * @param           mixed $post
     * @param           mixed $category
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function getPreviousPostInBlogByPublishDate($post, $category, $filter = null, $sortorder = null, $limit = null)
    {
        $response = $this->listPostsInBlogByPublishDate($category, $filter, $sortorder, $limit);
        if ($response['error']) {
            return $response;
        }
        $posts = $response['result']['set'];
        unset($response);
        foreach ($posts as $key => $item) {
            if ($item->getId() == $post) {
                $currentKey = $key+1>count($posts) ? count($posts) :  $key+1;
            }
        }

        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $posts[$currentKey],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.get.done',
        );
        return $this->response;
    }
    /**
     * @name            getFirstPostInBlogByPublishDate ()
     *                 Gets first post in category by publis date.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     *
     * @use             $this->listPostsInBlogByPublishDate()
     *
     * @param           mixed $category
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function getFirstPostInBlogByPublishDate($category, $filter = null, $sortorder = null, $limit = null)
    {
        $response = $this->listPostsInBlogByPublishDate($category, $filter, $sortorder, $limit);
        if ($response['error']) {
            return $response;
        }
        $posts = $response['result']['set'];
        unset($response);
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $posts[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.get.done',
        );
        return $this->response;
    }
    /**
     * @name            getLastPostInBlogByPublishDate ()
     *                 Gets last post in category by publis date.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Said İmamoğlu
     *
     *
     * @use             $this->listPostsInBlogByPublishDate()
     *
     * @param           mixed $category
     * @param           array $filter
     * @param           array $sortorder
     * @param           array $limit
     *
     * @return          array           $response
     */
    public function getLastPostInBlogByPublishDate($category, $filter = null, $sortorder = null, $limit = null)
    {
        $response = $this->listPostsInBlogByPublishDate($category, $filter, $sortorder, $limit);
        if ($response['error']) {
            return $response;
        }
        $posts = $response['result']['set'];
        unset($response);

        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $posts[count($posts)-1],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.get.done',
        );
        return $this->response;
    }
}

/**
 * Change Log
 * **************************************
 * v1.0.8                      24.04.2015
 * TW #3568873
 * Can Berkol
 * **************************************
 * A deleteBlogPostRevision()
 * A deleteBlogPostRevisions()
 * A getBlogPostRevision()
 * A getLastRevisionOfPage()
 * A insertBlogPostRevision()
 * A insertBlogPostRevisions()
 * A listBlogPostRevisions()
 * A updateBlogPostRevision()
 * A updateBlogPostRevisions()
 *
 * **************************************
 * v1.0.7                   Said İmamoğlu
 * 15.01.2015
 * **************************************
 * A listFilesOfBlogPost()
 *
 * **************************************
 * v1.0.6                      Can Berkol
 * 14.10.2014
 * **************************************
 * U insertBlogPosts()
 * U updateBlogPosts()
 *
 * **************************************
 * v1.0.5                   Said İmamoğlu
 * 02.04.2014
 * **************************************
 * A listPublishedPosts()
 * U listPublishedPostsOfBlog()
 * A listPublishedPostsAfter()
 * A listPublishedPostsBefore()
 * A listPublishedPostsBetween()
 * A listPublishedPostsOfBlogAfter()
 * A listPublishedPostsOfBlogBefore()
 * A listPublishedPostsOfBlogBetween()
 * A listPostsInCategoryByPublishDate()
 * A getNextPostInCategoryByPublishDate()
 * A getPreviousPostInCategoryByPublishDate()
 * A getFirstPostInCategoryByPublishDate()
 * A getLastPostInCategoryByPublishDate()
 * A listPostsInBlogByPublishDate()
 * A getNextPostInBlogByPublishDate()
 * A getPreviousPostInBlogByPublishDate()
 * A getFirstPostInBlogByPublishDate()
 * A getLastPostInBlogByPublishDate()
 *
 * **************************************
 * v1.0.4                      Can Berkol
 * 31.03.2014
 * **************************************
 * A addFilesToBlogPost()
 * A isFileAssociatedWithBlogPost()
 * A getMaxSortOrderOfBlogPostFile()
 *
 * **************************************
 * v1.0.3                      Can Berkol
 * 31.03.2014
 * **************************************
 * A listPostCategoriesOfBlog()
 *
 * **************************************
 * v1.0.2                      Can Berkol
 * 31.03.2014
 * **************************************
 * A addCategoriesToPost()
 * A addPostsToCategory()
 * A deleteBlog()
 * A deleteBlogs()
 * A deleteBlogPost()
 * A deleteBlogPosts()
 * A deleteBlogPostCategory()
 * A deleteBlogPostCategories()
 * A insertBlog()
 * A insertBlogLocalizations()
 * A insertBlogs()
 * A insertBlogCategory()
 * A insertBlogCategoryLocalizations()
 * A insertCategoryBlogs()
 * A removeCategoriesFromPost()
 * A removePostsFromCategory()
 * A updateBlog()
 * A updateBlogs()
 * A updateBlogCategory()
 * A updateBlogCategories()
 * A updateBlogPost()
 * A updateBlogPosts()
 * A updateBlogPostCategory()
 * A updateBlogPostCategories()
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 30.03.2014
 * **************************************
 * A getBlog()
 * A getBlogPost()
 * A getBlogPostCategory()
 * A listBlogPostCategories()
 * A listBlogPosts()
 * A listBlogs()
 * A listCategoriesOfPost()
 * A listPostsInCategory()
 * A listPostsOfBlog()
 * A listPostsOfBlogInCategory()
 * A listPublishedPostsOfBlog()
 * A listPublishedPostsOfBlogInCategory()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 18.10.2013
 * **************************************
 * Initial setup of class has been added.
 */