<?php
/**
 * @vendor          BiberLtd
 * @package         Core\Bundles\BlogBundle
 * @subpackage      Services
 * @name            BlogBundle
 *
 * @author        	Can Berkol
 * @author        	Said İmamoğlu
 *
 * @copyright   	Biber Ltd. (www.biberltd.com)
 *
 * @version     	1.2.2
 * @date        	18.09.2015
 */
namespace BiberLtd\Bundle\BlogBundle\Services;

/** Extends CoreModel */
use BiberLtd\Bundle\CoreBundle\CoreModel;

/** Entities to be used */
use BiberLtd\Bundle\BlogBundle\Entity as BundleEntity;
use BiberLtd\Bundle\CoreBundle\Responses\ModelResponse;
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
     * @param           string $dbConnection Database connection key as set in app/config.yml
     * @param           string $orm ORM that is used.
     */
    public function __construct($kernel, $dbConnection = 'default', $orm = 'doctrine')    {
        parent::__construct($kernel, $dbConnection, $orm);

        $this->entity = array(
            'abpl' 		=> array('name' => 'BlogBundle:ActiveBlogPostLocale', 'alias' => 'abpl'),
            'b' 		=> array('name' => 'BlogBundle:Blog', 'alias' => 'b'),
            'bl'		=> array('name' => 'BlogBundle:BlogLocalization', 'alias' => 'bl'),
            'bm' 		=> array('name' => 'BlogBundle:BlogModerator', 'alias' => 'bm'),
            'bp' 		=> array('name' => 'BlogBundle:BlogPost', 'alias' => 'bp'),
            'bpa' 		=> array('name' => 'BlogBundle:BlogPostAction', 'alias' => 'bpa'),
            'bpc' 	    => array('name' => 'BlogBundle:BlogPostCategory', 'alias' => 'bpc'),
            'bpcl' 		=> array('name' => 'BlogBundle:BlogPostCategoryLocalization', 'alias' => 'bpcl'),
            'bpcom' 	=> array('name' => 'BlogBundle:BlogPostComment', 'alias' => 'bpcom'),
            'bpca' 		=> array('name' => 'BlogBundle:BlogPostCommentAction', 'alias' => 'bpca'),
            'bpl' 		=> array('name' => 'BlogBundle:BlogPostLocalization', 'alias' => 'bpl'),
            'bpm' 		=> array('name' => 'BlogBundle:BlogPostModeration', 'alias' => 'bpmo'),
            'bpmr' 		=> array('name' => 'BlogBundle:BlogPostModerationReply', 'alias' => 'bpmor'),
            'bpr' 		=> array('name' => 'BlogBundle:BlogPostRevision', 'alias' => 'bpr'),
            'bpt' 		=> array('name' => 'BlogBundle:BlogPostTag', 'alias' => 'bpt'),
            'bptl' 		=> array('name' => 'BlogBundle:BlogPostTagLocalization', 'alias' => 'bptl'),
            'cobp' 		=> array('name' => 'BlogBundle:CategoriesOfBlogPost', 'alias' => 'cobp'),
            'fbpom' 	=> array('name' => 'BlogBundle:FavoriteBlogPostsOfMember', 'alias' => 'fbpom'),
            'fbp' 		=> array('name' => 'BlogBundle:FeaturedBlogPost', 'alias' => 'fpp'),
            'fobp' 		=> array('name' => 'BlogBundle:FilesOfBlogPost', 'alias' => 'fobp'),
            'rbp' 		=> array('name' => 'BlogBundle:RelatedBlogPost', 'alias' => 'rbp'),
            'tobp' 		=> array('name' => 'BlogBundle:TagsOfBlogPost', 'alias' => 'tobp'),
        );
    }

    /**
     * @name            __destruct()
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.9
     *
     */
    public function __destruct(){
        foreach ($this as $property => $value) {
            $this->$property = null;
        }
    }

    /**
     * @name            addCategoriesToPost()
     *
     * @since           1.0.2
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->isPostAssociatedWithCategory()
     *
     * @param           array 			$categories
     * @param           mixed			$post
     * @param           string 			$isPrimary
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function addCategoriesToPost(array $categories, $post, $isPrimary = 'n', $sortOrder = 1){
        $timeStamp = time();
        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;
        /** issue an error only if there is no valid file entries */
        if (count($categories) < 1) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. $categories parameter must be an array collection', 'E:S:001');
        }
        unset($count);
        $collection = array();
        $count = 0;
        /** Start persisting files */
        $now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
        foreach ($categories as $category) {
            $response = $this->getBlogPostCategory($category);
            if($response->error->exist){
                break;
            }
            $category = $response->result->set;

            /** Check if association exists */
            if ($this->isPostAssociatedWithCategory($post, $category, true)) {
                break;
            }
            /** prepare object */
            $assoc = new BundleEntity\CategoriesOfBlogPost();
            $assoc->setPost($post)->setCategory($category)->setDateAdded($now);
            $assoc->setIsPrimary($isPrimary);$assoc->setSortOrder($sortOrder);
            /** persist entry */
            $this->em->persist($assoc);
            $collection[] = $assoc;
            $count++;
        }
        /** flush all into database */
        if ($count > 0) {
            $this->em->flush();
            return new ModelResponse($collection, $count, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
    }

    /**
     * @param array $files
     * @param mixed $post
     *
     * @return \BiberLtd\Bundle\BlogBundle\Services\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function addFilesToBlogPost(array $files, $post){
        $timeStamp = time();
        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;
        if(!is_array($files)){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. $files parameter must be an array collection', 'E:S:001');
        }
        $toAdd = [];
        $fModel = $this->kernel->getContainer()->get('filemanagement.model');
        foreach($files as $file){
            $response = $fModel->getFile($file);
            if($response->error->exist){
                continue;
            }
            $file = $response->result->set;
            if(!$this->isFileAssociatedWithBlogPost($file, $post, true)){
                $toAdd[] = $file;
            }
        }
        $now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
        $insertedItems = [];
        foreach($toAdd as $file){
            $entity = new BundleEntity\FilesOfBlogPost();;
            $entity->setFile($file)->setPost($post)->setDateAdded($now);
            $this->em->persist($entity);
            $insertedItems[] = $entity;
        }
        $countInserts = count($toAdd);
        if($countInserts > 0){
            $this->em->flush();

            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
        }

        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
    }

    /**
     * @name            addPostsToCategory()
     *
     * @since           1.0.2
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->isPostAssociatedWithCategory()
     *
     * @param           array 			$posts
     * @param           mixed			$category
     * @param           string 			$isPrimary
     * @param           int             $sortOrder
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function addPostsToCategory(array $posts, $category, string $isPrimary = 'n', int $sortOrder)
    {
        $timeStamp = time();
        $response = $this->getBlogPostCategory($category);
        if($response->error->exist){
            return $response;
        }
        $category = $response->result->set;
        /** issue an error only if there is no valid file entries */
        if (count($posts) < 1) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. $posts parameter must be an array collection', 'E:S:001');
        }
        unset($count);
        $collection = array();
        $count = 0;
        /** Start persisting files */
        $now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
        foreach ($posts as $post) {
            $response = $this->getBlogPost($post);
            if($response->error->exist){
                continue;
            }
            $post = $response->result->set;

            /** Check if association exists */
            if ($this->isPostAssociatedWithCategory($post, $category, true)) {
                continue;
            }
            /** prepare object */
            $assoc = new BundleEntity\CategoriesOfBlogPost();
            $assoc->setPost($post)->setCategory($category)->setDateAdded($now);
            $assoc->setIsPrimary($isPrimary);$assoc->setSortOrder($sortOrder);
            /** persist entry */
            $this->em->persist($assoc);
            $collection[] = $assoc;
            $count++;
        }
        /** flush all into database */
        if ($count > 0) {
            $this->em->flush();
            return new ModelResponse($collection, $count, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
    }

    /**
     * @name            addLocalesToBlogPost()
     *
     * @since           1.1.3
     * @version         1.1.4
     * @author          Can Berkol
     *
     * @use             $this->isLocaleAssociatedWithBlogPost()
     * @use             $this->validateAndGetBlogPost()
     * @use             $this->validateAndGetLocale()
     *
     * @param           array       $locales
     * @param           mixed       $blogPost
     *
     * @return          array       $response
     */
    public function addLocalesToBlogPost($locales, $blogPost){
        $timeStamp = time();
        $response = $this->getBlogPost($blogPost);

        if($response->error->exist){
            return $response;
        }
        $blogPost = $response->result->set;
        unset($response);
        $abplCollection = array();
        $count = 0;
        $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
        foreach ($locales as $locale) {
            $response = $mlsModel->getLanguage($locale);
            if($response->error->exist){
                return $response;
            }
            $locale = $response->result->set;
            unset($response);
            /** If no entity s provided as file we need to check if it does exist */
            /** Check if association exists */
            if(!$this->isLocaleAssociatedWithBlogPost($locale, $blogPost, true)) {
                $abpl = new BundleEntity\ActiveBlogPostLocale();
                $abpl->setLanguage($locale)->setBlogPost($blogPost);
                $this->em->persist($abpl);
                $abplCollection[] = $abpl;
                $count++;
            }
        }
        if($count > 0){
            $this->em->flush();
            return new ModelResponse($abplCollection, $count, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
    }

    /**
     * @name            deleteBlog ()
     *
     * @since           1.0.2
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use				$this->deleteBlogs()
     * @param           array 			$blog
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlog($blog){
        return $this->deleteBlogs(array($blog));
    }

    /**
     * @name            deleteBlogs()
     *
     * @since           1.0.2
     * @version         1.0.9
     *
     * @use             $this->createException()
     *
     * @param           array 			$collection
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlogs($collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countDeleted = 0;
        foreach($collection as $entry){
            if($entry instanceof BundleEntity\Blog){
                $this->em->remove($entry);
                $countDeleted++;
            }
            else{
                $response = $this->getBlog($entry);
                if(!$response->error->exist){
                    $this->em->remove($response->result->set);
                    $countDeleted++;
                }
            }
        }
        if($countDeleted < 0){
            return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
        }
        $this->em->flush();

        return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
    }
    /**
     * @name            deleteBlogPost()
     *
     * @since           1.0.2
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use				$this->deleteBlogPosts()
     * @param           array 			$post
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlogPost($post){
        return $this->deleteBlogPosts(array($post));
    }

    /**
     * @name            deleteBlogPosts()
     *
     * @since           1.0.2
     * @version         1.0.9
     *
     * @use             $this->createException()
     *
     * @param           array 			$collection
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlogPosts($collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countDeleted = 0;
        foreach($collection as $entry){
            if($entry instanceof BundleEntity\BlogPost){
                $this->em->remove($entry);
                $countDeleted++;
            }
            else{
                $response = $this->getBlogPost($entry);
                if(!$response->error->exist){
                    $this->em->remove($response->result->set);
                    $countDeleted++;
                }
            }
        }
        if($countDeleted < 0){
            return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
        }
        $this->em->flush();

        return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
    }

    /**
     * @name            deleteBlogPostCategory()
     *
     * @since           1.0.2
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use				$this->deleteBlogPostCategories()
     * @param           array 			$category
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlogPostCategory($category){
        return $this->deleteBlogPostCategories(array($category));
    }

    /**
     * @name            deleteBlogPostCategories()
     *
     * @since           1.0.2
     * @version         1.0.9
     *
     * @use             $this->createException()
     *
     * @param           array 			$collection
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlogPostCategories($collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countDeleted = 0;
        foreach($collection as $entry){
            if($entry instanceof BundleEntity\BlogPostCategory){
                $this->em->remove($entry);
                $countDeleted++;
            }
            else{
                $response = $this->getBlogPostCategory($entry);
                if(!$response->error->exist){
                    $this->em->remove($response->result->set);
                    $countDeleted++;
                }
            }
        }
        if($countDeleted < 0){
            return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
        }
        $this->em->flush();

        return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
    }
    /**
     * @name            deleteBlogPostRevision()
     *
     * @since           1.0.8
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use				$this->deleteBlogPostCategories()
     * @param           array 			$revision
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlogPostRevision($revision){
        return $this->deleteBlogPostRevisions(array($revision));
    }

    /**
     * @name            deleteBlogPostRevisions()
     *
     * @since           1.0.8
     * @version         1.0.9
     *
     * @use             $this->createException()
     *
     * @param           array 			$collection
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlogPostRevisions($collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countDeleted = 0;
        foreach($collection as $entry){
            if($entry['entry]'] instanceof BundleEntity\BlogPostRevision){
                $this->em->remove($entry['entry']);
                $countDeleted++;
            }
            else{
                $response = $this->getBlogPostRevision($entry['entry'], $entry['language'], $entry['revisionNumber']);
                if(!$response->error->exist){
                    $this->em->remove($response->result->set);
                    $countDeleted++;
                }
            }
        }
        if($countDeleted < 0){
            return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
        }
        $this->em->flush();

        return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
    }
    /**
     * @name 			getBlog()
     *
     * @since			1.0.1
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @param           mixed           $blog
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlog($blog) {
        $timeStamp = time();
        if($blog instanceof BundleEntity\Blog){
            return new ModelResponse($blog, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
        }
        $result = null;
        switch($blog){
            case is_numeric($blog):
                $result = $this->em->getRepository($this->entity['b']['name'])->findOneBy(array('id' => $blog));
                break;
            case is_string($blog):
                $response = $this->getBlogByUrlKey($blog);
                if(!$response->error->exist){
                    $result = $response->result->set;
                }
                unset($response);
                break;
        }
        if(is_null($result)){
            return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
        }

        return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }
    /**
     * @name            getBlogByUrlKey ()
     *
     * @since           1.0.9
     * @version         1.1.6
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->listBlogs()
     * @use             $this->createException()
     *
     * @param           mixed 			$urlKey
     * @param			mixed			$language
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlogByUrlKey($urlKey, $language = null){
        $timeStamp = time();
        if(!is_string($urlKey)){
            return $this->createException('InvalidParameterValueException', '$urlKey must be a string.', 'E:S:007');
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bl']['alias'].'.url_key', 'comparison' => '=', 'value' => $urlKey),
                )
            )
        );
        if(!is_null($language)){
            $mModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
            $response = $mModel->getLanguage($language);
            if(!$response->error->exist){
                $filter[] = array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->entity['bl']['alias'].'.language', 'comparison' => '=', 'value' => $response->result->set->getId()),
                        )
                    )
                );
            }
        }
        $response = $this->listBlogs($filter, null, array('start' => 0, 'count' => 1));
        if ($response->error->exist) {
            return $response;
        }
        return new ModelResponse($response->result->set[0], 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }
    /**
     * @name 			getBlogPost()
     *
     * @since			1.0.1
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @param           mixed           $post
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlogPost($post) {
        $timeStamp = time();
        if($post instanceof BundleEntity\BlogPost){
            return new ModelResponse($post, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
        }
        $result = null;
        switch($post){
            case is_numeric($post):
                $result = $this->em->getRepository($this->entity['bp']['name'])->findOneBy(array('id' => $post));
                break;
            case is_string($post):
                $response = $this->getBlogPostByUrlKey($post);
                if(!$response->error->exist){
                    $result = $response->result->set;
                }
                unset($response);
                break;
        }
        if(is_null($result)){
            return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
        }

        return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }
    /**
     * @name            getBlogPostByUrlKey ()
     *
     * @since           1.0.9
     * @version         1.1.6
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->listBlogPosts()
     * @use             $this->createException()
     *
     * @param           mixed 			$urlKey
     * @param			mixed			$language
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlogPostByUrlKey($urlKey, $language = null){
        $timeStamp = time();
        if(!is_string($urlKey)){
            return $this->createException('InvalidParameterValueException', '$urlKey must be a string.', 'E:S:007');
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bpl']['alias'].'.url_key', 'comparison' => '=', 'value' => $urlKey),
                )
            )
        );
        if(!is_null($language)){
            $mModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
            $response = $mModel->getLanguage($language);
            if(!$response->error->exist){
                $filter[] = array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->entity['bpl']['alias'].'.language', 'comparison' => '=', 'value' => $response->result->set->getId()),
                        )
                    )
                );
            }
        }
        $response = $this->listBlogPosts($filter, null, array('start' => 0, 'count' => 1));
        if ($response->error->exist) {
            return $response;
        }
        return new ModelResponse($response->result->set[0], 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }

    /**
     * @name 			getBlogPostCategory()
     *
     * @since			1.0.1
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @param           mixed           $category
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlogPostCategory($category) {
        $timeStamp = time();
        if($category instanceof BundleEntity\BlogPostCategory){
            return new ModelResponse($category, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
        }
        $result = null;
        switch($category){
            case is_numeric($category):
                $result = $this->em->getRepository($this->entity['bpc']['name'])->findOneBy(array('id' => $category));
                break;
            case is_string($category):
                $response = $this->getBlogPostCategoryByUrlKey($category);
                if(!$response->error->exist){
                    $result = $response->result->set;
                }
                unset($response);
                break;
        }
        if(is_null($result)){
            return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
        }

        return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }
    
    /**
     * @name            getBlogPostByMetaTitle ()
     *
     * @since           1.1.8
     * @version         1.1.8
     * @author          Said İmamoğlu
     *
     * @use             $this->listBlogPosts()
     * @use             $this->createException()
     *
     * @param           mixed 			$metaTitle
     * @param			mixed			$language
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlogPostByMetaTitle($metaTitle, $language = null){
        $timeStamp = time();
        if(!is_string($metaTitle)){
            return $this->createException('InvalidParameterValueException', '$metaTitle must be a string.', 'E:S:007');
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bpl']['alias'].'.meta_title', 'comparison' => '=', 'value' => $metaTitle),
                )
            )
        );
        if(!is_null($language)){
            $mModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
            $response = $mModel->getLanguage($language);
            if(!$response->error->exist){
                $filter[] = array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->entity['bpl']['alias'].'.language', 'comparison' => '=', 'value' => $response->result->set->getId()),
                        )
                    )
                );
            }
        }
        $response = $this->listBlogPosts($filter, null, array('start' => 0, 'count' => 1));
        if ($response->error->exist) {
            return $response;
        }
        return new ModelResponse($response->result->set[0], 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }
    /**
     * @name            getBlogPostCategoryByUrlKey ()
     *
     * @since           1.0.9
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->listBlogPostss()
     * @use             $this->createException()
     *
     * @param           mixed 			$urlKey
     * @param			mixed			$language
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlogPostCategoryByUrlKey($urlKey, $language = null){
        $timeStamp = time();
        if(!is_string($urlKey)){
            return $this->createException('InvalidParameterValueException', '$urlKey must be a string.', 'E:S:007');
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bpcl']['alias'].'.url_key', 'comparison' => '=', 'value' => $urlKey),
                )
            )
        );
        if(!is_null($language)){
            $mModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
            $response = $mModel->getLanguage($language);
            if(!$response->error->exist){
                $filter[] = array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->entity['bpcl']['alias'].'.language', 'comparison' => '=', 'value' => $response->result->set->getId()),
                        )
                    )
                );
            }
        }
        $response = $this->listBlogPostCategories($filter, null, array('start' => 0, 'count' => 1));
        if($response->error->exist){
            return $response;
        }
        return new ModelResponse($response->result->set[0], 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }
    /**
     * @param $post
     * @param $language
     * @return array|ModelResponse
     */
    public function getBlogPostLocalization($post,$language){
        $timeStamp = time();

        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bpl']['alias'].'.blog_post', 'comparison' => '=', 'value' => $post),
                )
            )
        );
        if(!is_null($language)){
            $mModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
            $response = $mModel->getLanguage($language);
            if(!$response->error->exist){
                $filter[] = array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->entity['bpl']['alias'].'.language', 'comparison' => '=', 'value' => $response->result->set->getId()),
                        )
                    )
                );
            }
        }

        $wStr = $fStr = '';

        $qStr = 'SELECT '.$this->entity['bpl']['alias'].', '.$this->entity['bp']['alias']
            .' FROM '.$this->entity['bpl']['name'].' '.$this->entity['bpl']['alias']
            .' JOIN '.$this->entity['bpl']['alias'].'.blog_post '.$this->entity['bp']['alias'];


        if(!is_null($filter)){
            $fStr = $this->prepareWhere($filter);
            $wStr .= ' WHERE '.$fStr;
        }

        $qStr .= $wStr;
        $q = $this->em->createQuery($qStr);
        $result = $q->getResult();

        $entities = array();
        foreach($result as $entry){
            $id = $entry->getBlogPost()->getId();
            if(!isset($unique[$id])){
                $unique[$id] = '';
                $entities[] = $entry;
            }
        }
        $totalRows = count($entities);
        if ($totalRows < 1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }
        return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }
    /**
     * @name            getBlogPostRevision()
     *
     * @since           1.0.8
     * @version         1.0.9
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
        $timeStamp = time();

        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;

        $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
        $response = $mlsModel->getLanguage($language);
        if($response->error->exist){
            return $response;
        }
        $language = $response->result->set;

        $qStr = 'SELECT '.$this->entity['bpr']['alias']
            .' FROM '.$this->entity['bpr']['name'].' '.$this->entity['bpr']['alias']
            .' WHERE '.$this->entity['bpr']['alias'].'.post = '.$post->getId()
            .' AND '.$this->entity['bpr']['alias'].'.language = '.$language->getId()
            .' AND '.$this->entity['bpr']['alias'].'.revision_number = '.$revisionNumber;

        $q = $this->em->createQuery($qStr);

        $result = $q->getResult();

        if(is_null($result)){
            return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
        }

        return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }
    /**
     * @name            getLastRevisionOfBlogPost()
     *
     * @since           1.0.8
     * @version         1.0.9
     *
     * @author          Can Berkol
     *
     * @page			mixed			$post
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getLastRevisionOfBlogPost($post){
        $timeStamp = time();
        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;

        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' =>$this->entity['bpr']['alias']. '.post', 'comparison' => '=', 'value' => $post->getId()),
                )
            )
        );
        $response = $this->listBlogPostRevisions($filter, array('date_added' => 'desc'), array('start' => 0, 'count' => 1));
        if ($response->error->exist) {
            return $response;
        }
        $response->stats->execution->start = $timeStamp;
        $response->stats->execution->end = time();
        $response->result->set = $response->result->set[0];
        return $response;
    }

    /**
     * @name            getMaxSortOrderOfBlogPostFile()
     *
     * @since           1.0.4
     * @version         1.0.9
     * @author          Can Berkol
     *
     *
     * @param           mixed 			$post
     * @param           bool 			$bypass
     *
     * @return          mixed           bool | $response
     */
    public function getMaxSortOrderOfBlogPostFile($post, $bypass = false){
        $timeStamp = time();
        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $qStr = 'SELECT MAX('.$this->entity['fobp']['alias'].'.sort_order) FROM '.$this->entity['fobp']['name'].' '.$this->entity['fobp']['alias']
            .' WHERE '.$this->entity['fobp']['alias'].'.post = '.$post->getId();

        $q = $this->em->createQuery($qStr);
        $result = $q->getSingleScalarResult();

        if ($bypass) {
            return $result;
        }
        return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }

    /**
     * @name            insertBlog()
     *
     * @since           1.0.2
     * @version         1.0.9
     *
     * @author          Can Berkol
     *
     * @use             $this->insertBlogs()
     *
     * @param           mixed			 $blog
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertBlog($blog){
        return $this->insertBlogs(array($blog));
    }

    /**
     * @name            insertBlogLocalizations()
     *
     * @since           1.0.2
     * @version         1.1.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array 			$collection
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertBlogLocalizations($collection) {
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach($collection as $data){
            if($data instanceof BundleEntity\BlogLocalization){
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            }
            else{
                $blog = $data['entity'];
                foreach($data['localizations'] as $locale => $translation){
                    $entity = new BundleEntity\BlogLocalization();
                    $lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                    $response = $lModel->getLanguage($locale);
                    if($response->error->exist){
                        return $response;
                    }
                    $entity->setLanguage($response->result->set);
                    unset($response);
                    $entity->setBlog($blog);
                    foreach($translation as $column => $value){
                        $set = 'set'.$this->translateColumnName($column);
                        switch($column){
                            default:
                                if(is_object($value) || is_array($value)){
                                    $value = json_encode($value);
                                }
                                $entity->$set($value);
                                break;
                        }
                    }
                    $this->em->persist($entity);
                    $insertedItems[] = $entity;
                    $countInserts++;
                }
            }
        }
        if($countInserts > 0){
            $this->em->flush();
            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
    }

    /**
     * @name            insertBlogs()
     *
     * @since           1.0.2
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->insertBlogLocalization()
     *
     * @param           array 			$collection
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertBlogs($collection){
        $timeStamp = time();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
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
            }
            else if (is_object($data)) {
                $localizations = array();
                $entity = new BundleEntity\Blog;
                if (!property_exists($data, 'date_created')) {
                    $data->date_added = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = $data->date_added;
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
                            $response = $sModel->getSite($value);
                            if (!$response->error->exist) {
                                $entity->$set($response->result->set);
                            }
                            else {
                                return $this->createException('EntityDoesNotExist', 'The site with the id / key / domain "'.$value.'" does not exist in database.', 'E:D:002');
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
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /** Now handle localizations */
        if ($countInserts > 0 && $countLocalizations > 0) {
            $response = $this->insertBlogLocalizations($localizations);
        }
        if($countInserts > 0){
            $this->em->flush();
            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
    }

    /**
     * @name            insertBlogPost()
     *
     * @since           1.0.2
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->insertBlogPosts()
     *
     * @param           mixed $post
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertBlogPost($post){
        return $this->insertBlogPosts(array($post));
    }

    /**
     * @name            insertBlogPostLocalizations()
     *
     * @since           1.0.2
     * @version         1.1.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $collection Collection of entities or post data.
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertBlogPostLocalizations($collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach($collection as $data){
            if($data instanceof BundleEntity\BlogPostLocalization){
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            }
            else{
                $bPost = $data['entity'];
                foreach($data['localizations'] as $locale => $translation){
                    $entity = new BundleEntity\BlogPostLocalization();
                    $lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                    $response = $lModel->getLanguage($locale);
                    if($response->error->exist){
                        return $response;
                    }
                    $entity->setLanguage($response->result->set);
                    unset($response);
                    $entity->setBlogPost($bPost);
                    foreach($translation as $column => $value){
                        $set = 'set'.$this->translateColumnName($column);
                        switch($column){
                            default:
                                if(is_object($value) || is_array($value)){
                                    $value = json_encode($value);
                                }
                                $entity->$set($value);
                                break;
                        }
                    }
                    $this->em->persist($entity);
                    $insertedItems[] = $entity;
                    $countInserts++;
                }
            }
        }
        if($countInserts > 0){
            $this->em->flush();
            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
    }
    /**
     * @name            insertBlogPostRevision()
     *
     * @since           1.0.8
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->insertBlogPostRevisions()
     *
     * @param           mixed			$revision
     *
     * @return          array           $response
     */
    public function insertBlogPostRevision($revision){
        return $this->insertBlogPostRevisions(array($revision));
    }

    /**
     * @name            insertBlogPostRevisions()
     *
     * @since           1.0.8
     * @version         1.1.6
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array 			$collection
     *
     * @return          array           $response
     */
    public function insertBlogPostRevisions($collection) {
        $timeStamp = time();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
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
                            $response = $lModel->getLanguage($value);
                            if (!$response->error->exist) {
                                $entity->$set($response->result->set);
                            }
                            unset($response, $lModel);
                            break;
                        case 'post':
                            $response = $this->getBlogPost($value);
                            if (!$response->error->exist) {
                                $entity->$set($response->result->set);
                            }
                            unset($response);
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
        if($countInserts > 0){
            $this->em->flush();
            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
    }
    /**
     * @name            insertBlogPosts()
     *
     * @since           1.0.2
     * @version         1.1.0
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->insertBlogPostLocalizations()
     *
     * @param           array 			$collection
     *
     * @return          array           $response
     */
    public function insertBlogPosts($collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $countLocalizations = 0;
        $insertedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\BlogPost) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            }
            else if (is_object($data)) {
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
                            if ($value instanceof BundleEntity\Blog) {
                                $entity->$set($value);
                            }else{
                                $response = $this->getBlog($value);
                                if (!$response->error->exist) {
                                    $entity->$set($response->result->set);
                                } else {
                                    return $this->createException('EntityDoesNotExist', 'The blog with the id / url_key  "' . $value . '" does not exist in database.', 'E:D:002');
                                }
                                unset($response);
                            }
                            break;
                        case 'author':
                        case 'member':
                            $mModel = $this->kernel->getContainer()->get('membermanagement.model');
                            $response = $mModel->getMember($value);
                            if (!$response->error->exist) {
                                $entity->$set($response->result->set);
                            }
                            else {
                                return $this->createException('EntityDoesNotExist', 'The member with the id / username / e-mail  "'.$value.'" does not exist in database.', 'E:D:002');
                            }
                            unset($response);
                            break;
                        case 'file':
                        case 'preview_image':
                        case 'previewImage':
                            $fModel = $this->kernel->getContainer()->get('filemanagement.model');
                            $response = $fModel->getFile($value);
                            if (!$response->error->exist) {
                                $entity->$set($response->result->set);
                            }
                            else {
                                return $this->createException('EntityDoesNotExist', 'The file with the id / url_key  "'.$value.'" does not exist in database.', 'E:D:002');
                            }
                            unset($response, $sModel);
                            break;
                        case 'site':
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value);
                            if (!$response->error->exist) {
                                $entity->$set($response->result->set);
                            } else {
                                return $this->createException('EntityDoesNotExist', 'The site with the id / key / domain "'.$value.'" does not exist in database.', 'E:D:002');
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
            }
        }
        /** Now handle localizations */
        if ($countInserts > 0 && $countLocalizations > 0) {
            $response = $this->insertBlogPostLocalizations($localizations);
        }
        if($countInserts > 0){
            $this->em->flush();
            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
    }

    /**
     * @name            insertBlogPostCategory ()
     *
     * @since           1.0.2
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->insertBlogPostCategories()
     *
     * @param           mixed 			$category
     *
     * @return          array           $response
     */
    public function insertBlogPostCategory($category){
        return $this->insertBlogPostCategories(array($category));
    }

    /**
     * @name            insertBlogPostCategoryLocalizations()
     *
     * @since           1.0.2
     * @version         1.1.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array 			$collection
     *
     * @return          array           $response
     */
    public function insertBlogPostCategoryLocalizations($collection) {
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach($collection as $data){
            if($data instanceof BundleEntity\BlogPostCategoryLocalization){
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            }
            else{
                $bpCategory = $data['entity'];
                foreach($data['localizations'] as $locale => $translation){
                    $entity = new BundleEntity\BlogPostCategoryLocalization();
                    $lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                    $response = $lModel->getLanguage($locale);
                    if($response->error->exist){
                        return $response;
                    }
                    $entity->setLanguage($response->result->set);
                    unset($response);
                    $entity->setPostCategory($bpCategory);
                    foreach($translation as $column => $value){
                        $set = 'set'.$this->translateColumnName($column);
                        switch($column){
                            default:
                                if(is_object($value) || is_array($value)){
                                    $value = json_encode($value);
                                }
                                $entity->$set($value);
                                break;
                        }
                    }
                    $this->em->persist($entity);
                    $insertedItems[] = $entity;
                    $countInserts++;
                }
            }
        }
        if($countInserts > 0){
            $this->em->flush();
            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
    }

    /**
     * @name            insertBlogPostCategories ()
     *
     * @since           1.0.2
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->insertBlogLocalizations()
     *
     * @param           array 			$collection
     *
     * @return          array           $response
     */
    public function insertBlogPostCategories($collection){
        $timeStamp = time();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $countLocalizations = 0;
        $insertedItems = array();
        $localizations = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\BlogPostCategory) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            }
            else if (is_object($data)) {
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
                            $response = $this->getBlog($value);
                            if (!$response->error->exist) {
                                $entity->$set($response->result->set);
                            }
                            else {
                                return $this->createException('EntityDoesNotExist', 'The blog with the id / url_key '.$value.'" does not exist in database.', 'E:D:002');
                            }
                            unset($response);
                            break;
                        case 'parent':
                            $response = $this->getBlogPostCategory($value);
                            if (!$response->error->exist) {
                                $entity->$set($response->result->set);
                            } else {
                                return $this->createException('EntityDoesNotExist', 'The blog post category with the id / url_key '.$value.'" does not exist in database.', 'E:D:002');
                            }
                            unset($response);
                            break;
                        case 'site':
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value);
                            if (!$response->error->exist) {
                                $entity->$set($response->result->set);
                            }
                            else {
                                return $this->createException('EntityDoesNotExist', 'The site with the id / key / domain "'.$value.'" does not exist in database.', 'E:D:002');
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
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /** Now handle localizations */
        if ($countInserts > 0 && $countLocalizations > 0) {
            $response = $this->insertBlogPostCategoryLocalizations($localizations);
        }
        if($countInserts > 0){
            $this->em->flush();
            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
    }

    /**
     * @param mixed $file
     * @param mixed $post
     * @param bool  $bypass
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
     */
    public function isFileAssociatedWithBlogPost($file, $post, bool $bypass = false){
        $timeStamp = time();
        $fModel = $this->kernel->getContainer()->get('filemanagement.model');

        $response = $fModel->getFile($file);
        if($response->error->exist){
            return $response;
        }
        $file = $response->result->set;

        $response = $this->getBlogPost($post);

        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;

        $found = false;

        $qStr = 'SELECT COUNT('.$this->entity['fobp']['alias'].'.post)'
            .' FROM '.$this->entity['fobp']['name'].' '.$this->entity['fobp']['alias']
            .' WHERE '.$this->entity['fobp']['alias'].'.file = '.$file->getId()
            .' AND '.$this->entity['fobp']['alias'].'.post = '.$post->getId();
        $query = $this->em->createQuery($qStr);

        $result = $query->getSingleScalarResult();

        /** flush all into database */
        if($result > 0){
            $found = true;
        }
        if($bypass){
            return $found;
        }

        return new ModelResponse($found, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }

    /**
     * @name            isLocaleAssociatedWithBlogPost()
     *
     * @since           1.1.3
     * @version         1.2.0
     *
     * @author          S.S.Aylak
     *
     * @user            $this->createException
     *
     * @param           mixed 	$locale
     * @param           mixed 	$blogPost
     * @param           bool 	$bypass
     *
     * @return          mixed
     */
    public function isLocaleAssociatedWithBlogPost($locale, $blogPost, $bypass = false){
        $timeStamp = time();
        $response = $this->getBlogPost($blogPost);
        if($response->error->exist){
            return $response;
        }
        $blogPost = $response->result->set;
        $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
        $response = $mlsModel->getLanguage($locale);
        if($response->error->exist){
            return $response;
        }
        $locale = $response->result->set;
        unset($response);
        $found = false;

        $qStr = 'SELECT COUNT(' . $this->entity['abpl']['alias'] . '.blog_post)'
            . ' FROM ' . $this->entity['abpl']['name'] . ' ' . $this->entity['abpl']['alias']
            . ' WHERE ' . $this->entity['abpl']['alias'] . '.language = ' . $locale->getId()
            . ' AND ' . $this->entity['abpl']['alias'] . '.blog_post = ' . $blogPost->getId();
        $q = $this->em->createQuery($qStr);

        $result = $q->getSingleScalarResult();

        if ($result > 0) {
            $found = true;
        }
        if ($bypass) {
            return $found;
        }
        return new ModelResponse($found, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }

    /**
     * @name            isPostAssociatedWithCategory()
     *
     * @since           1.0.4
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           mixed 			$post
     * @param           mixed 			$category
     * @param           bool 			$bypass
     *
     * @return          mixed           bool or $response
     */
    public function isPostAssociatedWithCategory($post, $category, $bypass = false){
        $timeStamp = time();
        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;

        $response = $this->getBlogPostCategory($category);

        if($response->error->exist){
            return $response;
        }
        $category = $response->result->set;

        $found = false;

        $qStr = 'SELECT COUNT(' . $this->entity['cobp']['alias'] . '.category)'
            . ' FROM ' . $this->entity['cobp']['name'] . ' ' . $this->entity['cobp']['alias']
            . ' WHERE ' . $this->entity['cobp']['alias'] . '.post = ' . $post->getId()
            . ' AND ' . $this->entity['cobp']['alias'] . '.category = ' . $category->getId();
        $query = $this->em->createQuery($qStr);

        $result = $query->getSingleScalarResult();

        /** flush all into database */
        if ($result > 0) {
            $found = true;
        }
        if ($bypass) {
            return $found;
        }
        return new ModelResponse($found, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }

    /**
     * @name            listBlogPostCategories ()
     *                  List blog posts.
     *
     * @since           1.0.1
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listBlogPostCategories($filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        if(!is_array($sortOrder) && !is_null($sortOrder)){
            return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
        }
        $oStr = $wStr = $gStr = $fStr = '';

        $qStr = 'SELECT '.$this->entity['bpc']['alias'].', '.$this->entity['bpcl']['alias']
            .' FROM '.$this->entity['bpcl']['name'].' '.$this->entity['bpcl']['alias']
            .' JOIN '.$this->entity['bpcl']['alias'].'.post_category '.$this->entity['bpc']['alias'];

        if(!is_null($sortOrder)){
            foreach($sortOrder as $column => $direction){
                switch($column){
                    case 'id':
                    case 'parent':
                    case 'blog':
                    case 'date_added':
                    case 'date_updated':
                    case 'date_removed':
                    case 'site':
                        $column = $this->entity['bpc']['alias'].'.'.$column;
                        break;
                    case 'name':
                    case 'url_key':
                        $column = $this->entity['bpcl']['alias'].'.'.$column;
                        break;
                }
                $oStr .= ' '.$column.' '.strtoupper($direction).', ';
            }
            $oStr = rtrim($oStr, ', ');
            $oStr = ' ORDER BY '.$oStr.' ';
        }

        if(!is_null($filter)){
            $fStr = $this->prepareWhere($filter);
            $wStr .= ' WHERE '.$fStr;
        }

        $qStr .= $wStr.$gStr.$oStr;
        $q = $this->em->createQuery($qStr);
        $q = $this->addLimit($q, $limit);

        $result = $q->getResult();

        $entities = array();
        foreach($result as $entry){
            $id = $entry->getPostCategory()->getId();
            if(!isset($unique[$id])){
                $entities[] = $entry->getPostCategory();
                $unique[$id] = '';
            }
        }
        $totalRows = count($entities);
        if ($totalRows < 1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }
        return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
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
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listBlogPostRevisions($filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        if(!is_array($sortOrder) && !is_null($sortOrder)){
            return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
        }
        $oStr = $wStr = $gStr = $fStr = '';

        $qStr = 'SELECT '.$this->entity['bpr']['alias'].', '.$this->entity['bpr']['alias']
            .' FROM '.$this->entity['bpr']['name'].' '.$this->entity['bpr']['alias'];

        if(!is_null($sortOrder)){
            foreach($sortOrder as $column => $direction){
                switch($column){
                    case 'url_key':
                    case 'title':
                    case 'date_updated':
                    case 'revision_number':
                    case 'date_added':
                    case 'date_removed':
                        $column = $this->entity['bpr']['alias'].'.'.$column;
                        break;
                }
                $oStr .= ' '.$column.' '.strtoupper($direction).', ';
            }
            $oStr = rtrim($oStr, ', ');
            $oStr = ' ORDER BY '.$oStr.' ';
        }

        if(!is_null($filter)){
            $fStr = $this->prepareWhere($filter);
            $wStr .= ' WHERE '.$fStr;
        }

        $qStr .= $wStr.$gStr.$oStr;
        $q = $this->em->createQuery($qStr);
        $q = $this->addLimit($q, $limit);

        $result = $q->getResult();

        $totalRows = count($result);
        if ($totalRows < 1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }
        return new ModelResponse($result, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }
    /**
     * @name            listBlogPosts()
     *
     * @since           1.0.1
     * @version         1.1.8
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listBlogPosts($filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        if(!is_array($sortOrder) && !is_null($sortOrder)){
            return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
        }
        $oStr = $wStr = $gStr = $fStr = '';

        $qStr = 'SELECT '.$this->entity['bpl']['alias'].', '.$this->entity['bp']['alias']
            .' FROM '.$this->entity['bpl']['name'].' '.$this->entity['bpl']['alias']
            .' JOIN '.$this->entity['bpl']['alias'].'.blog_post '.$this->entity['bp']['alias'];

        if(!is_null($sortOrder)){
            foreach($sortOrder as $column => $direction){
                switch($column){
                    case 'id':
                    case 'author':
                    case 'blog':
                    case 'type':
                    case 'status':
                    case 'date_added':
                    case 'date_approved':
                    case 'date_published':
                    case 'date_updated':
                    case 'date_removed':
                    case 'date_unpublished':
                    case 'count_dislike':
                    case 'count_comment':
                    case 'count_view':
                    case 'count_like':
                        $column = $this->entity['bp']['alias'].'.'.$column;
                        break;
                    case 'title':
                    case 'url_key':
                        $column = $this->entity['bpl']['alias'].'.'.$column;
                        break;
                }
                $oStr .= ' '.$column.' '.strtoupper($direction).', ';
            }
            $oStr = rtrim($oStr, ', ');
            $oStr = ' ORDER BY '.$oStr.' ';
        }

        if(!is_null($filter)){
            $fStr = $this->prepareWhere($filter);
            $wStr .= ' WHERE '.$fStr;
        }

        $qStr .= $wStr.$gStr.$oStr;
        $q = $this->em->createQuery($qStr);
        $q = $this->addLimit($q, $limit);
        $result = $q->getResult();

        $entities = array();
        foreach($result as $entry){
            $id = $entry->getBlogPost()->getId();
            if(!isset($unique[$id])){
                $unique[$id] = '';
                $entities[] = $entry->getBlogPost();
            }
        }
        $totalRows = count($entities);
        if ($totalRows < 1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }
        return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }

    /**
     * @name            listBlogs()
     *
     * @since           1.0.1
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listBlogs($filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        if(!is_array($sortOrder) && !is_null($sortOrder)){
            return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
        }
        $oStr = $wStr = $gStr = $fStr = '';

        $qStr = 'SELECT '.$this->entity['b']['alias'].', '.$this->entity['bl']['alias']
            .' FROM '.$this->entity['bl']['name'].' '.$this->entity['bl']['alias']
            .' JOIN '.$this->entity['bl']['alias'].'.blog '.$this->entity['b']['alias'];

        if(!is_null($sortOrder)){
            foreach($sortOrder as $column => $direction){
                switch($column){
                    case 'id':
                    case 'date_added':
                    case 'date_updated':
                    case 'date_removed':
                    case 'count_posts':
                    case 'site':
                        $column = $this->entity['b']['alias'].'.'.$column;
                        break;
                    case 'title':
                    case 'url_key':
                        $column = $this->entity['bl']['alias'].'.'.$column;
                        break;
                }
                $oStr .= ' '.$column.' '.strtoupper($direction).', ';
            }
            $oStr = rtrim($oStr, ', ');
            $oStr = ' ORDER BY '.$oStr.' ';
        }

        if(!is_null($filter)){
            $fStr = $this->prepareWhere($filter);
            $wStr .= ' WHERE '.$fStr;
        }

        $qStr .= $wStr.$gStr.$oStr;
        $q = $this->em->createQuery($qStr);
        $q = $this->addLimit($q, $limit);
        $result = $q->getResult();

        $entities = array();
        $unique = array();
        foreach($result as $entry){
            $id = $entry->getBlog()->getId();
            if(!isset($unique[$id])){
                $entities[] = $entry->getBlog();
                $unique[$id] = '';
            }
        }
        $totalRows = count($entities);
        if ($totalRows < 1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }
        return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }

    /**
     * @name            listCategoriesOfPost(
     *
     * @since           1.0.1
     * @version         1.1.0
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlogPostCategory()
     * @use             $this->listPostCategories()
     *
     * @param           mixed 			$post
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listCategoriesOfPost($post, $filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;
        $query_str = 'SELECT ' . $this->entity['cobp']['alias']
            . ' FROM ' . $this->entity['cobp']['name'] . ' ' . $this->entity['cobp']['alias']
            . ' WHERE ' . $this->entity['cobp']['alias'] . '.post = ' . $post->getId();
        $query = $this->em->createQuery($query_str);
        $result = $query->getResult();

        $catsInPost = array();
        if (count($result) > 0) {
            foreach ($result as $cobp) {
                $catsInPost[] = $cobp->getCategory()->getId();
            }
        }
        if (count($catsInPost) < 1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }
        $columnI = $this->entity['bpc']['alias'] . '.id';
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
        return $this->listBlogPostCategories($filter, $sortOrder, $limit);
    }

    /**
     * @param mixed      $post
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listFilesOfPost($post, array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = time();
        $fModel = $this->kernel->getContainer()->get('filemanagement.model');

        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;
        $query_str = 'SELECT '.$this->entity['fobp']['alias']
            .' FROM '.$this->entity['fobp']['name'].' '.$this->entity['fobp']['alias']
            .' WHERE '.$this->entity['fobp']['alias'].'.post = '.$post->getId();
        $query = $this->em->createQuery($query_str);
        $result = $query->getResult();

        $filesInPost = [];
        if(count($result) > 0){
            foreach($result as $fobp){
                $filesInPost[] = $fobp->getFile()->getId();
            }
        }
        if(count($filesInPost) < 1){
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }
        $columnI = 'f.id';
        $conditionI = array('column' => $columnI, 'comparison' => 'in', 'value' => $filesInPost);
        $filter[] = array(
            'glue'      => 'and',
            'condition' => array(
                array(
                    'glue'      => 'and',
                    'condition' => $conditionI,
                )
            )
        );
        return $fModel->listFiles($filter, $sortOrder, $limit);
    }
    
    /**
     * @name            listMediaOfBlogPost()
     *                  Lists one ore more random media from gallery
     *
     * @since           1.0.7
     * @version         1.0.9
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           mixed       	$post
     * @param           string      	$mediaType      all, i, a, v, f, d, p, s
     * @param           array       	$sortOrder
     * @param           array       	$limit
     * @param           array       	$filter
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listMediaOfBlogPost($post, $mediaType = 'all', $sortOrder = null, $limit = null, $filter = null){
        $timeStamp = time();
        $allowedTypes = array('i', 'a', 'v', 'f', 'd', 'p', 's');
        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;
        if($mediaType != 'all' && !in_array($mediaType, $allowedTypes)){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. $mediaType can have only the following values: i, a, v, f, d, p, or s', 'E:S:001');
        }
        $qStr = 'SELECT '.$this->entity['fobp']['alias']
            .' FROM '.$this->entity['fobp']['name'].' '.$this->entity['fobp']['alias']
            .' WHERE '.$this->entity['fobp']['alias'].'.post = '.$post->getId();
        unset($response, $post);
        $whereStr = '';
        if($mediaType != 'all'){
            $whereStr = ' AND '.$this->entity['fobp']['alias'].".type = '".$mediaType."'";
        }
        $qStr .= $whereStr;

        $q = $this->em->createQuery($qStr);

        $result = $q->getResult();

        $fileIds = array();
        $totalRows = count($result);

        if($totalRows > 0){
            foreach($result as $gm){
                $fileIds[] = $gm->getFile()->getId();
            }
        }
        else{
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }

        $filter[] = array('glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => 'f.id', 'comparison' => 'in', 'value' => $fileIds),
                )
            )
        );
        $fModel = $this->kernel->getContainer()->get('filemanagement.model');

        $response = $fModel->listFiles($filter, $sortOrder, $limit);

        $response->stats->execution->start = $timeStamp;

        return $response;
    }
    /**
     * @name            listPostRevisionsInCategory()
     *
     * @since           1.2.0
     * @version         1.2.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->getBlogPostCategory()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed 			$category
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostRevisionsInCategory($category, $filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $response = $this->getBlogPostCategory($category);
        if($response->error->exist){
            return $response;
        }
        $category = $response->result->set;
        /** First identify posts associated with given category */
        $query_str = 'SELECT ' . $this->entity['cobp']['alias']
            . ' FROM ' . $this->entity['cobp']['name'] . ' ' . $this->entity['cobp']['alias']
            . ' WHERE ' . $this->entity['cobp']['alias'] . '.category = ' . $category->getId();
        $query = $this->em->createQuery($query_str);
        $result = $query->getResult();

        $revisions = array();
        if (count($result) > 0) {
            foreach ($result as $cobp) {
                $revisionResponse = $this->getLastRevisionOfBlogPost($cobp->getPost());
                if (!$revisionResponse->error->exist) {
                    $revisions[] = $revisionResponse->result->set;
                }
            }
        }
        if (count($revisions) < 1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }
        return new ModelResponse($revisions, count($revisions), 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());;
    }
    /**
     * @name            listPostsInCategory()
     *
     * @since           1.0.1
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlogPostCategory()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed 			$category
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostsInCategory($category, $filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $response = $this->getBlogPostCategory($category);
        if($response->error->exist){
            return $response;
        }
        $category = $response->result->set;
        /** First identify posts associated with given category */
        $query_str = 'SELECT ' . $this->entity['cobp']['alias']
            . ' FROM ' . $this->entity['cobp']['name'] . ' ' . $this->entity['cobp']['alias']
            . ' WHERE ' . $this->entity['cobp']['alias'] . '.category = ' . $category->getId();
        $query = $this->em->createQuery($query_str);
        $result = $query->getResult();

        $postsInCat = array();
        if (count($result) > 0) {
            foreach ($result as $cobp) {
                $postsInCat[] = $cobp->getPost()->getId();
            }
        }
        if (count($postsInCat)<1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }
        $columnI = $this->entity['bp']['alias'] . '.id';
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $columnI, 'comparison' => 'in', 'value' => $postsInCat),
                )
            )
        );
        $response = $this->listBlogPosts($filter, $sortOrder, $limit);
        $response->stats->execution->start = $timeStamp;

        return $response;
    }

    /**
     * @name            listPostCategoriesOfBlog()
     *
     * @since           1.0.3
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listBlogPostCategories()
     *
     * @param           mixed 			$blog
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostCategoriesOfBlog($blog, $filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $response = $this->getBlog($blog);
        if($response->error->exist){
            return $response;
        }
        $blog = $response->result->set;

        $column = $this->entity['bpc']['alias'] . '.blog';
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
        $response = $this->listBlogPostCategories($filter, $sortOrder, $limit);

        $response->stats->execution->start = $timeStamp;

        return $response;
    }

    /**
     * @name            listPostsOfBlog ()
     *                  List posts of a blog
     *
     * @since           1.0.1
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listBlogPosts()
     *
     * @param           mixed 		$blog
     * @param           array 		$filter
     * @param           array 		$sortOrder
     * @param           array 		$limit
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostsOfBlog($blog, $filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $response = $this->getBlog($blog);
        if($response->error->exist){
            return $response;
        }
        $blog = $response->result->set;

        $column = $this->entity['bp']['alias'] . '.blog';
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $column, 'comparison' => '=', 'value' => $blog->getId()),
                )
            )
        );
        return $this->listBlogPosts($filter, $sortOrder, $limit);
    }

    /**
     * @name            listPostsOfBlogInCategory ()
     *
     * @since           1.0.1
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed 			$blog
     * @param           mixed 			$category
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostsOfBlogInCategory($blog, $category, $filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $response = $this->getBlog($blog);
        if($response->error->exist){
            return $response;
        }
        $blog = $response->result->set;
        $response = $this->getBlogPostCategory($category);
        if($response->error->exist){
            return $response;
        }
        $category = $response->result->set;
        $qStr = 'SELECT ' . $this->entity['cobp']['alias']
            . ' FROM ' . $this->entity['cobp']['name'] . ' ' . $this->entity['cobp']['alias']
            . ' WHERE ' . $this->entity['cobp']['alias'] . '.category = ' . $category->getId();
        $q = $this->em->createQuery($qStr);
        $result = $q->getResult();
        $postsInCat = array();
        if (count($result) < 1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }
        foreach ($result as $cobp) {
            $postsInCat[] = $cobp->getPost()->getId();
        }
        $columnI = $this->entity['bp']['alias'] . '.id';
        $conditionI = array('column' => $columnI, 'comparison' => 'in', 'value' => $postsInCat);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $conditionI,
                )
            )
        );
        $response = $this->listPostsOfBlog($blog, $filter, $sortOrder, $limit);

        $response->stats->execution->start = $timeStamp;

        return $response;
    }
    /**
     * @name            listPostsOfBlogInCategoryAndSite()
     *
     * @since           1.1.2
     * @version         1.1.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed 			$blog
     * @param           mixed 			$category
     * @param           mixed 			$site
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostsOfBlogInCategoryAndSite($blog, $category, $site, $filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $response = $this->getBlog($blog);
        if($this->error->exist){
            return $response;
        }
        $blog = $response->result->set;
        unset($response);
        $response = $this->getBlogPostCategory($category);
        if($this->error->exist){
            return $response;
        }
        $category = $response->result->set;
        unset($response);
        $sModel = new SMMService\SiteManagementModel($this->kernel, $this->dbConnection, $this->orm);
        $response = $sModel->getSite($site);
        if($this->error->exist){
            return $response;
        }
        $site = $response->result->set;
        unset($response);
        $qStr = 'SELECT ' . $this->entity['cobp']['alias']
            . ' FROM ' . $this->entity['cobp']['name'] . ' ' . $this->entity['cobp']['alias']
            . ' WHERE ' . $this->entity['cobp']['alias'] . '.category = ' . $category->getId();
        $q = $this->em->createQuery($qStr);
        $result = $q->getResult();

        $postsInCat = array();
        if (count($result) > 0) {
            foreach ($result as $cobp) {
                $postsInCat[] = $cobp->getPost()->getId();
            }
        }
        $selectedIds = implode(',', $postsInCat);
        $columnI = $this->entity['bp']['alias'] . '.id';
        $conditionI = array('column' => $columnI, 'comparison' => '=', 'in' => $selectedIds);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $conditionI,
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bp']['alias'].'.site', 'comparison' => '=', 'value' => $site->getId()),
                )
            )
        );
        $response = $this->listPostsOfBlog($blog, $filter, $sortOrder, $limit);

        $response->stats->execution->start = $timeStamp;

        return $response;
    }
    /**
     * @name            listPostsOfBlogInSite()
     *
     * @since           1.1.2
     * @version         1.1.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed 			$blog
     * @param           mixed 			$site
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostsOfBlogInSite($blog, $site, $filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $response = $this->getBlog($blog);
        if($response->error->exist){
            return $response;
        }
        $blog = $response->result->set;
        unset($response);
        $sModel = new SMMService\SiteManagementModel($this->kernel, $this->dbConnection, $this->orm);
        $response = $sModel->getSite($site);
        if($response->error->exist){
            return $response;
        }
        $site = $response->result->set;

        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bp']['alias'].'.site', 'comparison' => '=', 'value' => $site->getId()),
                )
            )
        );
        $response = $this->listPostsOfBlog($blog, $filter, $sortOrder, $limit);

        $response->stats->execution->start = $timeStamp;

        return $response;
    }

    /**
     * @param      $site
     * @param null $filter
     * @param null $sortOrder
     * @param null $limit
     *
     * @return \BiberLtd\Bundle\BlogBundle\Services\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostRevisionsOfSite($site, $filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $sModel = new SMMService\SiteManagementModel($this->kernel, $this->dbConnection, $this->orm);
        $response = $sModel->getSite($site);
        if($response->error->exist){
            return $response;
        }
        $site = $response->result->set;

        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bp']['alias'].'.site', 'comparison' => '=', 'value' => $site->getId()),
                )
            )
        );
        $response = $this->listBlogPosts($filter, $sortOrder, $limit);
        unset($filter);
        if($response->error->exist){
            return $response;
        }
        $entries = $response->result->set;
        $postIds = array();
        foreach($entries as $entry){
            $postIds[] = $entry->getId();
        }

        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bpr']['alias'].'.id', 'comparison' => 'IN', 'value' => $postIds),
                )
            )
        );
        $response = $this->listBlogPostRevisions($filter, $sortOrder, $limit);
        if($response->error->exist){
            return $response;
        }
        $response->stats->execution->start = $timeStamp;
        return $response;
    }

    /**
     * @param           $site
     * @param \DateTime $dateStart
     * @param \DateTime $dateEnd
     * @param bool      $inclusive
     * @param null      $sortOrder
     * @param null      $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostRevisionsOfSiteUpdatedBetween($site, \DateTime $dateStart, \DateTime $dateEnd, $inclusive = true, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $sModel = new SMMService\SiteManagementModel($this->kernel, $this->dbConnection, $this->orm);
        $response = $sModel->getSite($site);
        if($response->error->exist){
            return $response;
        }
        $site = $response->result->set;
        $lt = '<';
        $gt = '>';
        if($inclusive){
            $lt = $lt.'=';
            $gt = $gt.'=';
        }

        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bp']['alias'].'.site', 'comparison' => '=', 'value' => $site->getId()),
                )
            )
        );
        $response = $this->listBlogPosts($filter, $sortOrder, $limit);
        unset($filter);
        if($response->error->exist){
            return $response;
        }
        $entries = $response->result->set;
        $postIds = array();
        foreach($entries as $entry){
            $postIds[] = $entry->getId();
        }

        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bpr']['alias'].'.post', 'comparison' => 'in', 'value' => $postIds),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bpr']['alias'].'.date_updated', 'comparison' => $gt, 'value' => $dateStart->format('Y-m-d H:i:s')),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bpr']['alias'].'.date_updated', 'comparison' => $lt, 'value' => $dateEnd->format('Y-m-d H:i:s')),
                )
            )
        );
        $response = $this->listBlogPostRevisions($filter, $sortOrder, $limit);
        if($response->error->exist){
            return $response;
        }
        $response->stats->execution->start = $timeStamp;
        return $response;
    }
    
    /**
     * @name            listPostsOfBlogInCategoryWithStatuses()
     *
     * @since           1.1.6
     * @version         1.1.6
     * @author          Can Berkol
     * @use             $this->getBlog()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed 			$blog
     * @param           mixed 			$category
     * @param           array 			$statuses
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostsOfBlogInCategoryWithStatuses($blog, $category, array $statuses, $sortOrder = null, $limit = null){
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bp']['alias'].'.status', 'comparison' => 'in', 'value' => $statuses),
                )
            )
        );
        return $this->listPostsOfBlogInCategory($blog, $category, $filter, $sortOrder, $limit);
    }
    
    /**
     * @name            listPostsOfBlogInSiteWithStatuses()
     *
     * @since           1.1.4
     * @version         1.1.4
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed 			$blog
     * @param           mixed 			$site
     * @param           array 			$statuses
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostsOfBlogInSiteWithStatuses($blog, $site, $statuses, $filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $response = $this->getBlog($blog);
        if($response->error->exist){
            return $response;
        }
        $blog = $response->result->set;
        unset($response);
        $sModel = new SMMService\SiteManagementModel($this->kernel, $this->dbConnection, $this->orm);
        $response = $sModel->getSite($site);
        if($response->error->exist){
            return $response;
        }
        $site = $response->result->set;

        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bp']['alias'].'.site', 'comparison' => '=', 'value' => $site->getId()),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['bp']['alias'].'.status', 'comparison' => 'in', 'value' => $statuses),
                )
            )
        );
        $response = $this->listPostsOfBlog($blog, $filter, $sortOrder, $limit);

        $response->stats->execution->start = $timeStamp;

        return $response;
    }
    /**
     * @name            listPublishedPosts()
     *
     * @since           1.0.5
     * @version         1.0.9
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->listBlogPosts()
     *
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPublishedPosts($filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
        $columnDA = $this->entity['bp']['alias'] . '.date_published';
        $conditionDA = array('column' => $columnDA, 'comparison' => '<=', 'value' => $now->format('Y-m-d h:i:s'));

        $columnDU = $this->entity['bp']['alias'] . '.date_unpublished';
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
        $response = $this->listBlogPosts($filter, $sortOrder, $limit);
        $response->stats->execution->start = $timeStamp;

        return $response;
    }

    /**
     * @name            listPublishedPostsOfBlog()
     *
     * @since           1.0.5
     * @version         1.0.9
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed 			$blog
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPublishedPostsOfBlog($blog, $filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $response = $this->getBlog($blog);
        if($response->error->exist){
            return $response;
        }
        $blog = $response->result->set;
        $columnDA = $this->entity['bp']['alias'] . '.blog';
        $conditionDA = array('column' => $columnDA, 'comparison' => '<=', 'value' => $blog->getId());
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $conditionDA,
                )
            )
        );
        $response = $this->listPublishedPosts($blog, $filter, $sortOrder, $limit);
        $response->stats->execution->start = $timeStamp;
        return $response;
    }

    /**
     * @name            listPublishedPostsOfBlogInCategory ()
     *
     * @since           1.0.1
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listPublishedBlogPosts()
     *
     * @param           mixed 			$blog
     * @param           mixed 			$category
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return           BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPublishedPostsOfBlogInCategory($blog, $category, $filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $response = $this->getBlog($blog);
        if($response->error->exist){
            return $response;
        }
        $blog = $response->result->set;
        $response = $this->getBlogPostCategory($category);
        if($response->error->exist){
            return $response;
        }
        $category = $response->result->set;
        /** First identify posts associated with given category */
        $qStr = 'SELECT ' . $this->entity['cobp']['alias']
            . ' FROM ' . $this->entity['cobp']['name'] . ' ' . $this->entity['cobp']['alias']
            . ' WHERE ' . $this->entity['cobp']['alias'] . '.category = ' . $category->getId();
        $query = $this->em->createQuery($qStr);
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
        $columnI = $this->entity['bp']['alias'] . '.id';
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
        $response = $this->listPublishedPostsOfBlog($blog, $filter, $sortOrder, $limit);
        $response->stats->execution->start = $timeStamp;
        return $response;
    }

    /**
     * @name            markPostsAsDeleted()
     *
     * @since           1.1.3
     * @version         1.1.3
     *
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array 			$collection
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function markPostsAsDeleted($collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
        $toUpdate = array();
        foreach ($collection as $post) {
            if(!$post instanceof BundleEntity\BlogPost){
                $response = $this->getBlogPost($post);
                if($response->error->exist){
                    return $response;
                }
                $post = $response->result->set;
                unset($response);
            }
            $post->setStatus('d');
            $post->setDateRemoved($now);
            $toUpdate[] = $post;
        }
        $response = $this->updateBlogPosts($toUpdate);
        $response->stats->execution->start = $timeStamp;
        $response->stats->execution->end = time();

        return $response;
    }
    /**
     * @name            publishBlogPosts()
     *
     * @since           1.1.4
     * @version         1.1.4
     *
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array 			$collection
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function publishBlogPosts($collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
        $toUpdate = array();
        foreach ($collection as $post) {
            if(!$post instanceof BundleEntity\BlogPost){
                $response = $this->getBlogPost($post);
                if($response->error->exist){
                    return $response;
                }
                $post = $response->result->set;
                unset($response);
            }
            $post->setStatus('p');
            $post->setDatePublished($now);
            $post->setDateUnpublished(null);
            $toUpdate[] = $post;
        }
        $response = $this->updateBlogPosts($toUpdate);
        $response->stats->execution->start = $timeStamp;
        $response->stats->execution->end = time();

        return $response;
    }
    /**
     * @name            removeCategoriesFromPost ()
     *
     * @since           1.0.2
     * @version         1.1.7
     * @author          Can Berkol
     *
     * @param           array 			$categories
     * @param           mixed			$post
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function removeCategoriesFromPost($categories, $post){
        $timeStamp = time();
        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;
        $idsToRemove = array();
        foreach ($categories as $category) {
            $response = $this->getBlogPostCategory($category);
            if($response->error->exist){
                return $response;
            }
            $idsToRemove[] = $response->result->set->getId();
        }
        $in = ' IN (' . implode(',', $idsToRemove) . ')';
        $qStr = 'DELETE FROM '.$this->entity['cobp']['name'].' '.$this->entity['cobp']['alias']
            .' WHERE '.$this->entity['cobp']['alias'].'.post = '.$post->getId()
            .' AND '.$this->entity['cobp']['alias'].'.category '.$in;

        $q = $this->em->createQuery($qStr);
        $result = $q->getResult();

        $deleted = true;
        if (!$result) {
            $deleted = false;
        }
        if ($deleted) {
            return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
    }

    /**
     * @param array $files
     * @param $post
     * @return ModelResponse
     */
    public function removeFilesFromPost(array $files, $post){
        $timeStamp = time();
        $fModel = $this->kernel->getContainer()->get('filemanagement.model');

        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;
        $idsToRemove = array();
        foreach ($files as $file) {
            $response = $fModel->getFile($file);
            if($response->error->exist){
                return $response;
            }
            $idsToRemove[] = $response->result->set->getId();
        }
        $in = ' IN (' . implode(',', $idsToRemove) . ')';
        $qStr = 'DELETE FROM '.$this->entity['fobp']['name'].' '.$this->entity['fobp']['alias']
            .' WHERE '.$this->entity['fobp']['alias'].'.post = '.$post->getId()
            .' AND '.$this->entity['fobp']['alias'].'.file '.$in;

        $q = $this->em->createQuery($qStr);
        $result = $q->getResult();

        $deleted = true;
        if (!$result) {
            $deleted = false;
        }
        if ($deleted) {
            return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
    }

    /**
     * @name            removeLocalesFromBlogPost()
     *
     * @since           1.1.3
     * @version         1.1.4
     * @author          Can Berkol
     *
     * @use             $this->doesBlogPostExist()
     * @use             $this->isLocaleAssociatedWithBlogPost()
     *
     * @param           array 		$locales
     * @param           mixed 		$blogPost
     *
     * @return          array           $response
     */
    public function removeLocalesFromBlogPost($locales, $blogPost){
        $timeStamp = time();
        $response = $this->getBlogPost($blogPost);
        if($response->error->exist){
            return $response;
        }
        $blogPost = $response->result->set;
        $idsToRemove = array();
        $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
        foreach ($locales as $locale) {
            $response = $mlsModel->getLanguage($locale);
            if($response->error->exist){
                continue;
            }
            $idsToRemove[] = $response->result->set->getId();
        }
        $in = ' IN (' . implode(',', $idsToRemove) . ')';
        $qStr = 'DELETE FROM '.$this->entity['abpl']['name'].' '.$this->entity['abpl']['alias']
            .' WHERE '.$this->entity['abpl']['alias'].'.blog_post = '.$blogPost->getId()
            .' AND '.$this->entity['abpl']['alias'].'.language '.$in;

        $q = $this->em->createQuery($qStr);
        $result = $q->getResult();

        $deleted = true;
        if (!$result) {
            $deleted = false;
        }
        if ($deleted) {
            return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
    }

    /**
     * @name            removePostsFromCategory ()
     *
     * @since           1.0.2
     * @version         1.1.7
     * @author          Can Berkol
     *
     * @param           array 			$posts
     * @param           mixed			$category
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function removePostsFromCategory($posts, $category){
        $timeStamp = time();
        $response = $this->getBlogPostCategory($category);
        if($response->error->exist){
            return $response;
        }
        $category = $response->result->set;
        $idsToRemove = array();
        foreach ($posts as $post) {
            $response = $this->getBlogPost($post);
            if($response->error->exist){
                return $response;
            }
            $idsToRemove[] = $response->result->set->getId();
        }
        $in = ' IN (' . implode(',', $idsToRemove) . ')';
        $qStr = 'DELETE FROM '.$this->entity['cobp']['name'].' '.$this->entity['cobp']['alias']
            .' WHERE '.$this->entity['cobp']['alias'].'.category = '.$category->getId()
            .' AND '.$this->entity['cobp']['alias'].'.post '.$in;

        $q = $this->em->createQuery($qStr);
        $result = $q->getResult();

        $deleted = true;
        if (!$result) {
            $deleted = false;
        }
        if ($deleted) {
            return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
    }
    /**
     * @name            unpublishBlogPosts()
     *
     * @since           1.1.4
     * @version         1.1.4
     *
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array 			$collection
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function unpublishBlogPosts($collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
        $toUpdate = array();
        foreach ($collection as $post) {
            if(!$post instanceof BundleEntity\BlogPost){
                $response = $this->getBlogPost($post);
                if($response->error->exist){
                    return $response;
                }
                $post = $response->result->set;
                unset($response);
            }
            $post->setStatus('u');
            $post->setDateUnpublished($now);
            $toUpdate[] = $post;
        }
        $response = $this->updateBlogPosts($toUpdate);
        $response->stats->execution->start = $timeStamp;
        $response->stats->execution->end = time();

        return $response;
    }

    /**
     * @name unPublishPostsOfBlogInSite()
     * @author  Said İmamoğlu
     * @since 1.2.1
     * @version 1.2.1
     * @param $blog
     * @param $site
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function unPublishPostsOfBlogInSite($blog,$site){

        $response = $this->listPostsOfBlogInSite($blog,$site);
        if ($response->error->exist) {
            return $response;
        }
        return $this->unpublishBlogPosts($response->result->set);
    }
    /**
     * @name            updateBlog ()
     *
     * @since           1.0.2
     * @version         1.0.9
     *
     * @author          Can Berkol
     *
     * @use             $this->updateBlogs()
     *
     * @param           mixed $blog
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function updateBlog($blog){
        return $this->updateBlogs(array($blog));
    }

    /**
     * @name            updateBlogs()
     *
     * @since           1.0.2
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array 			$collection
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function updateBlogs($collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countUpdates = 0;
        $updatedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\Blog) {
                $entity = $data;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            }
            else if (is_object($data)) {
                if(!property_exists($data, 'id') || !is_numeric($data->id)){
                    return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" property and id property ​must have an integer value.', 'E:S:003');
                }
                if (property_exists($data, 'date_created')) {
                    unset($data->date_created);
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                $response = $this->getBlog($data->id);
                if ($response->error->exist) {
                    return $response;
                }
                $oldEntity = $response->resul>set;
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
                                    $response = $mlsModel->getLanguage($langCode);
                                    $localization->setLanguage($response->result->set);
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
                            $response = $sModel->getSite($value);
                            if (!$response->error->exist) {
                                $oldEntity->$set($response->result->set);
                            }
                            else {
                                return $this->createException('EntityDoesNotExist', 'Site with id / url_key '.$value.' does not exist in database.', 'E:D:002');
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
            }
        }
        if($countUpdates > 0){
            $this->em->flush();
            return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, time());
    }

    /**
     * @name            updateBlogPost()
     *
     * @since           1.0.2
     * @version         1.0.9
     *
     * @author          Can Berkol
     *
     * @use             $this->updateBlogPosts()
     *
     * @param           mixed 			$post
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function updateBlogPost($post){
        return $this->updateBlogPosts(array($post));
    }

    /**
     * @name            updateBlogPosts()
     *
     * @since           1.0.2
     * @version         1.1.8
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array 			$collection
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function updateBlogPosts($collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
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
                if(!property_exists($data, 'id') || !is_numeric($data->id)){
                    return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" property and id property ​must have an integer value.', 'E:S:003');
                }
                if (property_exists($data, 'date_added')) {
                    unset($data->date_added);
                }
                $response = $this->getBlogPost($data->id);
                if ($response->error->exist) {
                    return $this->createException('EntityDoesNotExist', 'Page with id / code '.$data->id.' does not exist in database.', 'E:D:002');
                }
                $oldEntity = $response->result->set;
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
                                    $response = $mlsModel->getLanguage($langCode);
                                    $localization->setLanguage($response->result->set);
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
                        case 'blog':
                            if ($value instanceof BundleEntity\Blog) {
                                $oldEntity->$set($value);
                            }else{
                                $response = $this->getBlog($value);
                                if (!$response->error->exist) {
                                    $oldEntity->$set($response->result->set);
                                } else {
                                    return $this->createException('EntityDoesNotExist', 'The blog with the id / url_key  "' . $value . '" does not exist in database.', 'E:D:002');
                                }
                                unset($response);
                            }
                            break;
                        case 'author':
                            $mModel = $this->kernel->getContainer()->get('membermanagement.model');
                            $response = $mModel->getMember($value);
                            if (!$response->error->exist) {
                                $oldEntity->$set($response->result->set);
                            } else {
                                return $this->createException('EntityDoesNotExist', 'Member with id / username / e-mail '.$value.' does not exist in database.', 'E:D:002');
                            }
                            unset($response, $sModel);
                            break;
                        case 'file':
                        case 'preview_image':
                        case 'previewImage':
                            $fModel = $this->kernel->getContainer()->get('filemanagement.model');
                            $response = $fModel->getFile($value);
                            if (!$response->error->exist) {
                                $oldEntity->$set($response->result->set);
                            } else {
                                return $this->createException('EntityDoesNotExist', 'File with id / url_key '.$value.' does not exist in database.', 'E:D:002');
                            }
                            unset($response, $sModel);
                            break;
                        case 'site':
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value, 'id');
                            if (!$response->error) {
                                $oldEntity->$set($response->result->set);
                            } else {
                                return $this->createException('EntityDoesNotExist', 'Site with id / url_key '.$value.' does not exist in database.', 'E:D:002');
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
        if($countUpdates > 0){
            $this->em->flush();
            return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, time());
    }

    /**
     * @name            updateBlogPostCategory()
     *
     * @since           1.0.2
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->updateBlogPostCategories()
     *
     * @param           mixed 			$category
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function updateBlogPostCategory($category){
        return $this->updateBlogPostCategories(array($category));
    }

    /**
     * @name            updateBlogPostCategories ()
     *
     * @since           1.0.2
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array 				$collection
     *
     * @return         	\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function updateBlogPostCategories($collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
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
                if(!property_exists($data, 'id') || !is_numeric($data->id)){
                    return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" property and id property ​must have an integer value.', 'E:S:003');
                }
                if (property_exists($data, 'date_added')) {
                    unset($data->date_added);
                }
                $response = $this->getBlogPost($data->id);
                if ($response->error->exist) {
                    return $this->createException('EntityDoesNotExist', 'Page with id / code '.$data->id.' does not exist in database.', 'E:D:002');
                }
                $oldEntity = $response->result->set;
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
                                    $response = $mlsModel->getLanguage($langCode);
                                    $localization->setLanguage($response->result->set);
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
                            if (!$response->error) {
                                $oldEntity->$set($response->result->set);
                            } else {
                                return $this->createException('EntityDoesNotExist', 'Blog with id / url_key '.$value.' does not exist in database.', 'E:D:002');
                            }
                            unset($response, $sModel);
                            break;
                        case 'parent':
                            $response = $this->getBlogPostCategory($value);
                            if (!$response->error->exist) {
                                $oldEntity->$set($response->result->set);
                            } else {
                                return $this->createException('EntityDoesNotExist', 'Blog Post Category with id / url_key '.$value.' does not exist in database.', 'E:D:002');
                            }
                            unset($response, $sModel);
                            break;
                        case 'site':
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value);
                            if (!$response->error->exist) {
                                $oldEntity->$set($response->result->set);
                            } else {
                                return $this->createException('EntityDoesNotExist', 'Site with id / url_key '.$value.' does not exist in database.', 'E:D:002');
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
            }
        }
        if($countUpdates > 0){
            $this->em->flush();
            return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, time());
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
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function updateBlogPostRevision($revision){
        return $this->updateBlogPostRevisions(array($revision));
    }
    /**
     * @name            updateBlogPostRevisions()
     *
     * @since           1.0.8
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array 			$collection
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function updateBlogPostRevisions($collection) {
        $timeStamp = time();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
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
                $response = $this->getBlogPostRevision($data->post, $data->language, $data->revision_number);
                if ($response->error->exist) {
                    return $this->createException('EntityDoesNotExist', 'BlogPostRevision revision cannot be found in database.', 'E:D:002');
                }
                $oldEntity = $response->result->set;

                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'post':
                            $response = $this->getBlogPost($value);
                            if (!$response->error->exist) {
                                $oldEntity->$set($response->result->set);
                            }
                            else {
                                return $this->createException('EntityDoesNotExist', 'Blog post with id / url_key '.$value.' does not exist in database.', 'E:D:002');
                            }
                            unset($response);
                            break;
                        case 'language':
                            $lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                            $response = $lModel->getLanguage($value, 'id');
                            if (!$response->error->exist) {
                                $oldEntity->$set($response->result->set);
                            }
                            else {
                                return $this->createException('EntityDoesNotExist', 'Language with id / url_key / iso_code '.$data->id.' does not exist in database.', 'E:D:002');
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
            }
        }
        if($countUpdates > 0){
            $this->em->flush();
            return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, time());
    }

    /**
     * @name            listPostsInCategoryByPublishDate ()
     *
     * @since           1.0.5
     * @version         1.0.9
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->getBlog()
     * @use             $this->listPostsOfBlog()
     *
     * @param           mixed 	$category
     * @param           string 	$order
     * @param           array 	$filter
     * @param           array 	$limit
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostsInCategoryByPublishDate($category, $order = 'asc', $filter = null, $limit = null){
        $column = $this->entity['bp']['alias'] . '.date_published';
        $sortOrder[$column] = $order;
        return $this->listPostsInCategory($category, $filter, $sortOrder, $limit);
    }

    /**
     * @name            getNextPostInCategoryByPublishDate ()
     *
     * @since           1.0.5
     * @version         1.0.9
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->listPostsInCategoryByPublishDate()
     *
     * @param           mixed 	$post
     * @param           mixed 	$category
     * @param			string 	$order
     * @param           array 	$filter
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getNextPostInCategoryByPublishDate($post, $category, $order = 'asc', $filter = null){
        $response = $this->listPostsInCategoryByPublishDate($category, $order, $filter, null);
        if ($response->error->exist) {
            return $response;
        }
        $posts = $response->result->set;
        foreach ($posts as $key => $item) {
            if ($item->getId() == $post) {
                $currentKey = $key-1>=0 ? $key-1 : 0;
            }
        }
        $response->result->set = $posts[$currentKey];

        return $response;
    }
    /**
     * @name            getPreviousPostInCategoryByPublishDate ()
     *
     * @since           1.0.5
     * @version         1.0.9
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->listPostsInCategoryByPublishDate()
     *
     * @param           mixed 	$post
     * @param           mixed 	$category
     * @param			string 	$order
     * @param           array 	$filter
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getPreviousPostInCategoryByPublishDate($post, $category, $order = 'asc', $filter = null){
        $response = $this->listPostsInCategoryByPublishDate($category, $order, $filter, null);
        if ($response->error->exist) {
            return $response;
        }
        $posts = $response->result->set;
        foreach ($posts as $key => $item) {
            if ($item->getId() == $post) {
                $currentKey = $key+1>count($posts) ? count($posts) :  $key+1;
            }
        }
        $response->result->set = $posts[$currentKey];

        return $response;
    }
    /**
     * @name            getFirstPostInCategoryByPublishDate ()
     *
     * @since           1.0.5
     * @version         1.0.9
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     *
     * @use             $this->listPostsInCategoryByPublishDate()
     *
     * @param           mixed 	$category
     * @param			string 	$order
     * @param           array 	$filter
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getFirstPostInCategoryByPublishDate($category, $order = 'asc', $filter = null){
        $response = $this->listPostsInCategoryByPublishDate($category, $order, $filter, null);
        if ($response->error->exist) {
            return $response;
        }
        $posts = $response->result->set;

        $response->result->set = $posts[0];

        return $response;
    }
    /**
     * @name            getLastPostInCategoryByPublishDate ()
     *
     * @since           1.0.5
     * @version         1.0.9
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     *
     * @use             $this->listPostsInCategoryByPublishDate()
     *
     * @param           mixed 	$category
     * @param			string 	$order
     * @param           array 	$filter
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getLastPostInCategoryByPublishDate($category, $order = 'asc', $filter = null){
        $response = $this->listPostsInCategoryByPublishDate($category, $order, $filter, null);
        if ($response->error->exist) {
            return $response;
        }
        $posts = $response->result->set;

        $response->result->set = $posts[$response->result->count->total - 1];

        return $response;
    }
    /**
     * @name            countTotalPostsInCategory ()
     *
     * @since           1.0.5
     * @version         1.0.9
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->listPostsInCategory()
     *
     * @param           mixed $category
     * @param           array $filter
     * @param           array $sortOrder
     * @param           array $limit
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function countTotalPostsInCategory($category, $filter = null, $sortOrder = null, $limit = null){
        $response = $this->listPostsInCategory($category, $filter, $sortOrder, $limit);
        if ($response->error->exist) {
            return $response;
        }
        $count = $response->result->count->total;
        $response->result->set = $count;

        return $response;
    }

    /**
     * @name            countTotalPostsInBlog()
     *
     * @since           1.0.5
     * @version         1.0.9
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu

     * @use             $this->listPostsInCategory()
     *
     * @param           mixed $blog
     * @param           array $filter
     * @param           array $sortOrder
     * @param           array $limit
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function countTotalPostsInBlog($blog, $filter = null, $sortOrder = null, $limit = null){
        $response = $this->getBlog($blog);
        if($response->error->exist){
            return $response;
        }
        $blog = $response->result->set;
        $column = $this->entity['bp']['alias'] . '.blog';
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
        $response = $this->listBlogPosts($filter, $sortOrder, $limit);
        if ($response->error->exist) {
            return $response;
        }
        $count = count($response->result->set);
        $response->result->set = $count;

        return $response;
    }

    /**
     * @name listBlogPostsByDateColumnWhichBeforeGivenDate()
     * @author  Said Imamoglu
     * @since 1.2.2
     * @version 1.2.2
     * @param $dateColumn
     * @param $date
     * @param $filter
     * @param $sortOrder
     * @param $limit
     * @return ModelResponse
     */
    public function listBlogPostsByDateColumnWhichBeforeGivenDate($dateColumn,$date,$filter = array(),$sortOrder = null,$limit = null){
        $timeStamp = time();
        if (! $date instanceof \DateTime) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'Invalid date object.', $timeStamp, time());
        }
        if (!in_array($dateColumn,array('date_added','date_published','date_unpublished'))) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'Invalid date column.', $timeStamp, time());
        }
        // Prepare SQL conditions
        $filter[] = array(
            'glue' => 'and',
            'condition' => array('column' => $this->entity['bp']['alias'].'.'.$dateColumn, 'comparison' => '<', 'value' => $date->format('Y-m-d H:i:s')),
        );
        $response = $this->listBlogPosts($filter,$sortOrder,$limit);
        $response->stats->execution->start = $timeStamp;
        $response->stats->execution->end = time();
        return $response;
    }
    /**
     * @name unPublishBlogPostsByDateColumnWhichBeforeGivenDate()
     * @author  Said Imamoglu
     * @since 1.2.2
     * @version 1.2.2
     * @param $dateColumn
     * @param $date
     * @return ModelResponse
     */
    public function unPublishActiveBlogPostsByDateColumnWhichBeforeGivenDate($dateColumn,$date){
        $timeStamp = time();
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array('column' => $this->entity['bp']['alias'] . '.status', 'comparison' => '!=', 'value' => 'u'),
        );
        $response = $this->listBlogPostsByDateColumnWhichBeforeGivenDate($dateColumn,$date,$filter);
        if ($response->error->exist) {
            return $response;
        }
        $response = $this->unpublishBlogPosts($response->result->set);
        $response->stats->execution->start = $timeStamp;
        $response->stats->execution->end = time();
        return $response;
    }

    /**
     * @name unPublishBlogPostsByDateColumnWhichBeforeGivenDate()
     * @author  Said Imamoglu
     * @since 1.2.2
     * @version 1.2.2
     * @param $dateColumn
     * @param $date
     * @return ModelResponse
     */
    public function unPublishBlogPostsByDateColumnWhichBeforeGivenDate($dateColumn,$date){
        $timeStamp = time();
        $response = $this->listBlogPostsByDateColumnWhichBeforeGivenDate($dateColumn,$date);
        if ($response->error->exist()) {
            return $response;
        }
        $response = $this->unpublishBlogPosts($response->result->set);
        $response->stats->execution->start = $timeStamp;
        $response->stats->execution->end = time();
        return $response;
    }

    /**
     * @name            listCategoriesOfPostByPost()
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          S.S.Aylak
     * @use             $this->listCategoriesOfPostItem()
     *
     * @param           mixed 			$post
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listCategoriesOfPostByPost($post, $filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;

        $column = $this->entity['cobp']['alias'] . '.post';
        $condition = array('column' => $column, 'comparison' => '=', 'value' => $post->getId());
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listCategoriesOfPostItem($filter, $sortOrder, $limit);

        $response->stats->execution->start = $timeStamp;

        return $response;
    }

    /**
     * @name            listCategoriesOfPostByCategory()
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          S.S.Aylak
     * @use             $this->listCategoriesOfPostItem()
     *
     * @param           mixed 			$category
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listCategoriesOfPostByCategory($category, $filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        $response = $this->getBlogPostCategory($category);
        if($response->error->exist){
            return $response;
        }
        $category = $response->result->set;

        $column = $this->entity['cobp']['alias'] . '.category';
        $condition = array('column' => $column, 'comparison' => '=', 'value' => $category->getId());
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listCategoriesOfPostItem($filter, $sortOrder, $limit);

        $response->stats->execution->start = $timeStamp;

        return $response;
    }
    
    /**
     * @name 			listCategoriesOfPostItem()
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          S.S.Aylak
     *
     * @param           array 			$filter
     * @param           array 			$sortOrder
     * @param           array 			$limit
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listCategoriesOfPostItem($filter = null, $sortOrder = null, $limit = null) {
        $timeStamp = time();
        if(!is_array($sortOrder) && !is_null($sortOrder)){
            return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
        }
        $oStr = $wStr = $gStr = $fStr = '';
        $qStr = 'SELECT '.$this->entity['cobp']['alias']
            .' FROM '.$this->entity['cobp']['name'].' '.$this->entity['cobp']['alias'];

        if(!is_null($sortOrder)){
            foreach($sortOrder as $column => $direction){
                switch($column){
                    case 'post':
                    case 'category':
                    case 'blog':
                    case 'date_added':
                    case 'is_primary':
                    case 'sort_order':
                }
                $oStr .= ' '.$column.' '.strtoupper($direction).', ';
            }
            $oStr = rtrim($oStr, ', ');
            $oStr = ' ORDER BY '.$this->entity['cobp']['alias'].'.'.$oStr.' ';
        }

        if(!is_null($filter)){
            $fStr = $this->prepareWhere($filter);
            $wStr .= ' WHERE '.$fStr;
        }

        $qStr .= $wStr.$gStr.$oStr;
        $q = $this->em->createQuery($qStr);
        $q = $this->addLimit($q, $limit);
        $result = $q->getResult();
        $entities = array();
        foreach($result as $entry){
            $entities[] = $entry;
        }
        $totalRows = count($entities);
        if ($totalRows < 1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }
        return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }

    /**
     * @name            updateCategoriesOfPost()
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          S.S.Aylak
     *
     * @use             $this->createException()
     *
     * @param           array 			$categories
     * @param           mixed           $postEntry
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function updateCategoriesOfPost(array $categories, $postEntry) {
        $timeStamp = time();
        $countUpdates = 0;
        $updatedItems = array();
        $response = $this->getBlogPost($postEntry);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;
        /** Categories must be an array */
        if(!is_array($categories)){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        if (count($categories) < 1) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. $categories parameter must be an array collection', 'E:S:001');
        }
        foreach($categories as $data){
            if ($data instanceof BundleEntity\CategoriesOfBlogPost) {
                $entity = $data;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            } else if (is_object($data)) {
                if(!property_exists($data, 'post') || !is_numeric($data->post->getId())){
                    return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" property and id property ​must have an integer value.', 'E:S:003');
                }
                if (property_exists($data, 'date_added')) {
                    unset($data->date_added);
                }
                $response = $this->getBlogPost($data->post->getId());
                if ($response->error->exist) {
                    return $this->createException('EntityDoesNotExist', 'Page with id / code '.$data->post->getId().' does not exist in database.', 'E:D:002');
                }
                $oldEntity = $response->result->set;
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'post':
                            $response = $this->getBlog($value, 'id');
                            if (!$response->error) {
                                $oldEntity->$set($response->result->set);
                            } else {
                                return $this->createException('EntityDoesNotExist', 'Blog with id / url_key '.$value.' does not exist in database.', 'E:D:002');
                            }
                            unset($response, $sModel);
                            break;
                        case 'category':
                            $response = $this->getBlogPostCategory($value);
                            if (!$response->error->exist) {
                                $oldEntity->$set($response->result->set);
                            } else {
                                return $this->createException('EntityDoesNotExist', 'Blog Post Category with id / url_key '.$value.' does not exist in database.', 'E:D:002');
                            }
                            unset($response, $sModel);
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
            }
        }
        if($countUpdates > 0){
            $this->em->flush();
            return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, time());
    }
    /**
     * @name            listActiveLocalesOfBlogPost()
     *                  List active locales of a given gallery.
     *
     * @since           1.1.3
     * @version         1.1.4
     * @author          S.S.Aylak
     *
     * @use             $this->createException()
     *
     * @param           mixed           $blogPost
     *
     * @return          array           $blogpost
     */
    public function listActiveLocalesOfBlogPost($blogPost){
        $timeStamp = time();
        $response = $this->getBlogPost($blogPost);
        if($response->error->exist){
            return $response;
        }
        $blogPost = $response->result->set;
        unset($response);
        $qStr = 'SELECT ' . $this->entity['abpl']['alias']
            . ' FROM ' . $this->entity['abpl']['name'] . ' ' . $this->entity['abpl']['alias']
            . ' WHERE ' . $this->entity['abpl']['alias'] . '.blog_post = ' . $blogPost->getId();
        $query = $this->em->createQuery($qStr);
        
        $result = $query->getResult();
        $locales = array();
        $unique = array();
        foreach ($result as $entry) {
            $id = $entry->getLanguage()->getId();
            if (!isset($unique[$id])) {
                $locales[] = $entry->getLanguage();
                $unique[$id] = $entry->getLanguage();
            }
        }
        unset($unique);
        $totalRows = count($locales);
        if ($totalRows < 1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }
        return new ModelResponse($locales, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }
}

/**
 * Change Log
 * **************************************
 * v1.2.2                      30.03.2016
 * S.S.Aylak
 * **************************************
 * FR :: listCategoriesOfPostByPost() method implemented.
 * FR :: listCategoriesOfPostByCategory() method implemented.
 * FR :: listCategoriesOfPostItem() method implemented.
 * FR :: updateCategoriesOfPost() method implemented.
 * **************************************
 * v1.2.1                      10.08.2015
 * Said İmamoğlu
 * **************************************
 * FR :: unPublishPostsOfBlogInSite() method implemented.
 * **************************************
 * v1.2.0                      23.07.2015
 * Said İmamoğlu
 * **************************************
 * BF :: BlogPostRevision entity added to $entity array.
 * **************************************
 * v1.1.9                      16.07.2015
 * Can Berkol
 * **************************************
 * BF :: listPostCategoriesOfBlog() method fixed.
 * BF :: list methods were returning duplicate values. fixed.
 *
 * **************************************
 * v1.1.8                      01.07.2015
 * Said İmamoğlu
 * **************************************
 * FR :: getBlogPostByMetaTitle() method added.
 * BF :: updateBlogPosts() does not update blog column. Fixed
 * BF :: listBlogPosts() was listing wrong. Fixed.
 *
 * **************************************
 * v1.1.7                      08.06.2015
 * Can Berkol
 * **************************************
 * BF :: remove.. methods had invalid DQL syntax. Fixed.
 *
 * **************************************
 * v1.1.6                      16.06.2015
 * Said İmamoğlu, Can Berkol
 * **************************************
 * BF :: getBlogByUrlKey() was returning wrong response. Fixed
 * BF :: getBlogPostByUrlKey() was returning wrong response. Fixed
 * BF :: listPostsOfBlogInCategory() had invalid filter, fixed.
 * BF :: listPostsOfBlogInCategory() now returns ModelResponse if no posts found.
 * BF :: listPostsOfBlog() was resetting filters, fixed.
 * FR :: listPostsOfBlogInCategoryWithStatuses() added.
 *
 * **************************************
 * v1.1.5                      14.06.2015
 * Can Berkol
 * **************************************
 * BF :: getPost calls are replaced with getBlogPost
 * FR :: publishBlogPosts()
 * FR :: unpublishBlogPosts()
 *
 * **************************************
 * v1.1.4                      13.06.2015
 * Can Berkol
 * **************************************
 * BF :: post property must be blog_post. Fixed.
 * BF :: listPostsOfBlogInSiteWithStatuses() added.
 *
 * **************************************
 * v1.1.3                      11.06.2015
 * Can Berkol
 * **************************************
 * BF :: insertBloglocalizations() method rewritten.
 * BF :: insertBlogPostLocalizations() method rewritten.
 * BF :: insertBlogPostLocalizations() method rewritten.
 * FR :: markPostsAsDeleted() method rewritten.
 *
 * **************************************
 * v1.1.2                      10.06.2015
 * Can Berkol
 * **************************************
 * FR :: listPostsOfBlogInCategoryAndSite() method implemented.
 * FR :: listPostsOfBlogInSite() method implemented.
 *
 * **************************************
 * v1.1.1                      25.05.2015
 * Can Berkol
 * **************************************
 * BF :: db_connection is replaced with dbConnection
 *
 * **************************************
 * v1.1.0                      11.05.2015
 * Can Berkol
 * **************************************
 * BF :: Old habits detected and cleaned in the code.
 *
 * **************************************
 * v1.0.9                      10.05.2015
 * Can Berkol
 * **************************************
 * CR :: Made compatible with Core 3.3.
 *
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
