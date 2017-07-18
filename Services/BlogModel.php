<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        14.12.2015
 */
namespace BiberLtd\Bundle\BlogBundle\Services;

/** Extends CoreModel */
use BiberLtd\Bundle\CoreBundle\CoreModel;
use BiberLtd\Bundle\BlogBundle\Entity as BundleEntity;
use BiberLtd\Bundle\CoreBundle\Responses\ModelResponse;
use BiberLtd\Bundle\FileManagementBundle\Entity as FileEntity;
use BiberLtd\Bundle\SiteManagementBundle\Services as SMMService;
use BiberLtd\Bundle\CoreBundle\Services as CoreServices;

class BlogModel extends CoreModel{
    /**
     * BlogModel constructor.
     *
     * @param object $kernel
     * @param string $dbConnection
     * @param string $orm
     */
    public function __construct($kernel, $dbConnection = 'default', $orm = 'doctrine'){
        parent::__construct($kernel, $dbConnection, $orm);

        $this->entity = array(
            'abpl'  => array('name' => 'BlogBundle:ActiveBlogPostLocale', 'alias' => 'abpl'),
            'b'     => array('name' => 'BlogBundle:Blog', 'alias' => 'b'),
            'bl'    => array('name' => 'BlogBundle:BlogLocalization', 'alias' => 'bl'),
            'bm'    => array('name' => 'BlogBundle:BlogModerator', 'alias' => 'bm'),
            'bp'    => array('name' => 'BlogBundle:BlogPost', 'alias' => 'bp'),
            'bpa'   => array('name' => 'BlogBundle:BlogPostAction', 'alias' => 'bpa'),
            'bpc'   => array('name' => 'BlogBundle:BlogPostCategory', 'alias' => 'bpc'),
            'bpcl'  => array('name' => 'BlogBundle:BlogPostCategoryLocalization', 'alias' => 'bpcl'),
            'bpcom' => array('name' => 'BlogBundle:BlogPostComment', 'alias' => 'bpcom'),
            'bpca'  => array('name' => 'BlogBundle:BlogPostCommentAction', 'alias' => 'bpca'),
            'bpl'   => array('name' => 'BlogBundle:BlogPostLocalization', 'alias' => 'bpl'),
            'bpm'   => array('name' => 'BlogBundle:BlogPostModeration', 'alias' => 'bpmo'),
            'bpmr'  => array('name' => 'BlogBundle:BlogPostModerationReply', 'alias' => 'bpmor'),
            'bpr'   => array('name' => 'BlogBundle:BlogPostRevision', 'alias' => 'bpr'),
            'bpt'   => array('name' => 'BlogBundle:BlogPostTag', 'alias' => 'bpt'),
            'bptl'  => array('name' => 'BlogBundle:BlogPostTagLocalization', 'alias' => 'bptl'),
            'cobp'  => array('name' => 'BlogBundle:CategoriesOfBlogPost', 'alias' => 'cobp'),
            'fbpom' => array('name' => 'BlogBundle:FavoriteBlogPostsOfMember', 'alias' => 'fbpom'),
            'fbp'   => array('name' => 'BlogBundle:FeaturedBlogPost', 'alias' => 'fpp'),
            'fobp'  => array('name' => 'BlogBundle:FilesOfBlogPost', 'alias' => 'fobp'),
            'rbp'   => array('name' => 'BlogBundle:RelatedBlogPost', 'alias' => 'rbp'),
            'tobp'  => array('name' => 'BlogBundle:TagsOfBlogPost', 'alias' => 'tobp'),
        );
    }

    /**
     * Destructor
     */
    public function __destruct(){
        foreach($this as $property => $value){
            $this->$property = null;
        }
    }

    /**
     * @param array  $categories
     * @param mixed  $post
     * @param string $isPrimary
     * @param int    $sortOrder
     *
     * @return \BiberLtd\Bundle\BlogBundle\Services\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function addCategoriesToPost(array $categories, $post, string $isPrimary = 'n',int $sortOrder = 1){
        $timeStamp = microtime(true);
        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;
        if(count($categories) < 1){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. $categories parameter must be an array collection', 'E:S:001');
        }
        unset($count);
        $collection = [];
        $count = 0;
        /** Start persisting files */
        $now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
        foreach($categories as $category){
            $response = $this->getBlogPostCategory($category);
            if($response->error->exist){
                break;
            }
            $category = $response->result->set;

            /** Check if association exists */
            if($this->isPostAssociatedWithCategory($post, $category, true)){
                break;
            }
            /** prepare object */
            $assoc = new BundleEntity\CategoriesOfBlogPost();
            $assoc->setPost($post)->setCategory($category)->setDateAdded($now);
            $assoc->setIsPrimary($isPrimary);
            $assoc->setSortOrder($sortOrder);
            /** persist entry */
            $this->em->persist($assoc);
            $collection[] = $assoc;
            $count ++;
        }
        /** flush all into database */
        if($count > 0){
            $this->em->flush();

            return new ModelResponse($collection, $count, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }

        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
    }

    /**
     * @param array $files
     * @param mixed $post
     *
     * @return \BiberLtd\Bundle\BlogBundle\Services\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function addFilesToBlogPost(array $files, $post){
        $timeStamp = microtime(true);
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

            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }

        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
    }

    /**
     * @param array  $posts
     * @param mixed  $category
     * @param string $isPrimary
     * @param int    $sortOrder
     *
     * @return \BiberLtd\Bundle\BlogBundle\Services\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function addPostsToCategory(array $posts, $category, string $isPrimary = 'n', int $sortOrder)
    {
        $timeStamp = microtime(true);
        $response = $this->getBlogPostCategory($category);
        if ($response->error->exist) {
            return $response;
        }
        $category = $response->result->set;
        /** issue an error only if there is no valid file entries */
        if (count($posts) < 1) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. $posts parameter must be an array collection', 'E:S:001');
        }
        unset($count);
        $collection = [];
        $count = 0;
        /** Start persisting files */
        $now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
        foreach ($posts as $post) {
            $response = $this->getBlogPost($post);
            if ($response->error->exist) {
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
            $assoc->setIsPrimary($isPrimary);
            $assoc->setSortOrder($sortOrder);
            /** persist entry */
            $this->em->persist($assoc);
            $collection[] = $assoc;
            $count++;
        }
        /** flush all into database */
        if ($count > 0) {
            $this->em->flush();

            return new ModelResponse($collection, $count, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }
    }

    /**
     * @param array $locales
     * @param $blogPost
     * @return ModelResponse
     */
    public function addLocalesToBlogPost(array $locales, $blogPost){
        $timeStamp = microtime(true);
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
            return new ModelResponse($abplCollection, $count, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $blog
     *
     * @return \BiberLtd\Bundle\BlogBundle\Services\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlog($blog){
        return $this->deleteBlogs(array($blog));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlogs(array $collection){
        $timeStamp = microtime(true);
        if(!is_array($collection)){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countDeleted = 0;
        foreach($collection as $entry){
            if($entry instanceof BundleEntity\Blog){
                $this->em->remove($entry);
                $countDeleted ++;
            }
            else{
                $response = $this->getBlog($entry);
                if(!$response->error->exist){
                    $this->em->remove($response->result->set);
                    $countDeleted ++;
                }
            }
        }
        if($countDeleted < 0){
            return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
        }
        $this->em->flush();

        return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $post
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlogPost($post){
        return $this->deleteBlogPosts(array($post));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlogPosts(array $collection){
        $timeStamp = microtime(true);
        if(!is_array($collection)){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countDeleted = 0;
        foreach($collection as $entry){
            if($entry instanceof BundleEntity\BlogPost){
                $this->em->remove($entry);
                $countDeleted ++;
            }
            else{
                $response = $this->getBlogPost($entry);
                if(!$response->error->exist){
                    $this->em->remove($response->result->set);
                    $countDeleted ++;
                }
            }
        }
        if($countDeleted < 0){
            return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
        }
        $this->em->flush();

        return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $category
     *
     * @return \BiberLtd\Bundle\BlogBundle\Services\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlogPostCategory($category){
        return $this->deleteBlogPostCategories(array($category));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlogPostCategories(array $collection){
        $timeStamp = microtime(true);
        if(!is_array($collection)){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countDeleted = 0;
        foreach($collection as $entry){
            if($entry instanceof BundleEntity\BlogPostCategory){
                $this->em->remove($entry);
                $countDeleted ++;
            }
            else{
                $response = $this->getBlogPostCategory($entry);
                if(!$response->error->exist){
                    $this->em->remove($response->result->set);
                    $countDeleted ++;
                }
            }
        }
        if($countDeleted < 0){
            return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
        }
        $this->em->flush();

        return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $revision
     *
     * @return \BiberLtd\Bundle\BlogBundle\Services\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlogPostRevision($revision){
        return $this->deleteBlogPostRevisions(array($revision));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteBlogPostRevisions(array $collection){
        $timeStamp = microtime(true);
        if(!is_array($collection)){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countDeleted = 0;
        foreach($collection as $entry){
            if($entry['entry]'] instanceof BundleEntity\BlogPostRevision){
                $this->em->remove($entry['entry']);
                $countDeleted ++;
            }
            else{
                $response = $this->getBlogPostRevision($entry['entry'], $entry['language'], $entry['revisionNumber']);
                if(!$response->error->exist){
                    $this->em->remove($response->result->set);
                    $countDeleted ++;
                }
            }
        }
        if($countDeleted < 0){
            return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
        }
        $this->em->flush();

        return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $blog
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlog($blog){
        $timeStamp = microtime(true);
        if($blog instanceof BundleEntity\Blog){
            return new ModelResponse($blog, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
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
            return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
        }

        return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param string     $urlKey
     * @param mixed|null $language
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlogByUrlKey(string $urlKey, $language = null){
        $timeStamp = microtime(true);
        if(!is_string($urlKey)){
            return $this->createException('InvalidParameterValueException', '$urlKey must be a string.', 'E:S:007');
        }
        $filter[] = array(
            'glue'      => 'and',
            'condition' => array(
                array(
                    'glue'      => 'and',
                    'condition' => array('column' => $this->entity['bl']['alias'].'.url_key', 'comparison' => '=', 'value' => $urlKey),
                )
            )
        );
        if(!is_null($language)){
            $mModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
            $response = $mModel->getLanguage($language);
            if(!$response->error->exist){
                $filter[] = array(
                    'glue'      => 'and',
                    'condition' => array(
                        array(
                            'glue'      => 'and',
                            'condition' => array('column' => $this->entity['bl']['alias'].'.language', 'comparison' => '=', 'value' => $response->result->set->getId()),
                        )
                    )
                );
            }
        }
        $response = $this->listBlogs($filter, null, array('start' => 0, 'count' => 1));
        if($response->error->exist){
            return $response;
        }

        return new ModelResponse($response->result->set[0], 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $post
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlogPost($post){
        $timeStamp = microtime(true);
        if($post instanceof BundleEntity\BlogPost){
            return new ModelResponse($post, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
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
            return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
        }

        return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param string     $urlKey
     * @param mixed|null $language
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlogPostByUrlKey(string $urlKey, $language = null){
        $timeStamp = microtime(true);
        if(!is_string($urlKey)){
            return $this->createException('InvalidParameterValueException', '$urlKey must be a string.', 'E:S:007');
        }
        $filter[] = array(
            'glue'      => 'and',
            'condition' => array(
                array(
                    'glue'      => 'and',
                    'condition' => array('column' => $this->entity['bpl']['alias'].'.url_key', 'comparison' => '=', 'value' => $urlKey),
                )
            )
        );
        if(!is_null($language)){
            $mModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
            $response = $mModel->getLanguage($language);
            if(!$response->error->exist){
                $filter[] = array(
                    'glue'      => 'and',
                    'condition' => array(
                        array(
                            'glue'      => 'and',
                            'condition' => array('column' => $this->entity['bpl']['alias'].'.language', 'comparison' => '=', 'value' => $response->result->set->getId()),
                        )
                    )
                );
            }
        }
        $response = $this->listBlogPosts($filter, null, array('start' => 0, 'count' => 1));
        if($response->error->exist){
            return $response;
        }

        return new ModelResponse($response->result->set[0], 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $category
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlogPostCategory($category){
        $timeStamp = microtime(true);
        if($category instanceof BundleEntity\BlogPostCategory){
            return new ModelResponse($category, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
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
            return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
        }

        return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param string     $metaTitle
     * @param mixed|null $language
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlogPostByMetaTitle(string $metaTitle, $language = null){
        $timeStamp = microtime(true);
        if(!is_string($metaTitle)){
            return $this->createException('InvalidParameterValueException', '$metaTitle must be a string.', 'E:S:007');
        }
        $filter[] = array(
            'glue'      => 'and',
            'condition' => array(
                array(
                    'glue'      => 'and',
                    'condition' => array('column' => $this->entity['bpl']['alias'].'.meta_title', 'comparison' => '=', 'value' => $metaTitle),
                )
            )
        );
        if(!is_null($language)){
            $mModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
            $response = $mModel->getLanguage($language);
            if(!$response->error->exist){
                $filter[] = array(
                    'glue'      => 'and',
                    'condition' => array(
                        array(
                            'glue'      => 'and',
                            'condition' => array('column' => $this->entity['bpl']['alias'].'.language', 'comparison' => '=', 'value' => $response->result->set->getId()),
                        )
                    )
                );
            }
        }
        $response = $this->listBlogPosts($filter, null, array('start' => 0, 'count' => 1));
        if($response->error->exist){
            return $response;
        }

        return new ModelResponse($response->result->set[0], 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param string     $urlKey
     * @param mixed|null $language
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlogPostCategoryByUrlKey(string $urlKey, $language = null){
        $timeStamp = microtime(true);
        if(!is_string($urlKey)){
            return $this->createException('InvalidParameterValueException', '$urlKey must be a string.', 'E:S:007');
        }
        $filter[] = array(
            'glue'      => 'and',
            'condition' => array(
                array(
                    'glue'      => 'and',
                    'condition' => array('column' => $this->entity['bpcl']['alias'].'.url_key', 'comparison' => '=', 'value' => $urlKey),
                )
            )
        );
        if(!is_null($language)){
            $mModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
            $response = $mModel->getLanguage($language);
            if(!$response->error->exist){
                $filter[] = array(
                    'glue'      => 'and',
                    'condition' => array(
                        array(
                            'glue'      => 'and',
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

        return new ModelResponse($response->result->set[0], 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
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
     * @param mixed  $post
     * @param mixed  $language
     * @param string $revisionNumber
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getBlogPostRevision($post, $language, string $revisionNumber){
        $timeStamp = microtime(true);

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
            return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
        }

        return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $post
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getLastRevisionOfBlogPost($post){
        $timeStamp = microtime(true);
        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;

        $filter[] = array(
            'glue'      => 'and',
            'condition' => array(
                array(
                    'glue'      => 'and',
                    'condition' => array('column' => $this->entity['bpr']['alias'].'.post', 'comparison' => '=', 'value' => $post->getId()),
                )
            )
        );
        $response = $this->listBlogPostRevisions($filter, array('date_added' => 'desc'), array('start' => 0, 'count' => 1));
        if($response->error->exist){
            return $response;
        }
        $response->stats->execution->start = $timeStamp;
        $response->stats->execution->end = microtime(true);
        $response->result->set = $response->result->set[0];

        return $response;
    }

    /**
     * @param mixed $post
     * @param bool  $bypass
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getMaxSortOrderOfBlogPostFile($post, bool $bypass = false){
        $timeStamp = microtime(true);
        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $qStr = 'SELECT MAX('.$this->entity['fobp']['alias'].'.sort_order) FROM '.$this->entity['fobp']['name'].' '.$this->entity['fobp']['alias']
            .' WHERE '.$this->entity['fobp']['alias'].'.post = '.$post->getId();

        $q = $this->em->createQuery($qStr);
        $result = $q->getSingleScalarResult();

        if($bypass){
            return $result;
        }

        return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $blog
     *
     * @return \BiberLtd\Bundle\BlogBundle\Services\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertBlog($blog){
        return $this->insertBlogs(array($blog));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertBlogLocalizations(array $collection){
        $timeStamp = microtime(true);
        if(!is_array($collection)){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $insertedItems = [];
        foreach($collection as $data){
            if($data instanceof BundleEntity\BlogLocalization){
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts ++;
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
                    $countInserts ++;
                }
            }
        }
        if($countInserts > 0){
            $this->em->flush();

            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }

        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertBlogs(array $collection){
        $timeStamp = microtime(true);
        /** Parameter must be an array */
        if(!is_array($collection)){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $countLocalizations = 0;
        $insertedItems = [];
        foreach($collection as $data){
            if($data instanceof BundleEntity\Blog){
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts ++;
            }
            else{
                if(is_object($data)){
                    $localizations = [];
                    $entity = new BundleEntity\Blog;
                    if(!property_exists($data, 'date_created')){
                        $data->date_added = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()
                                                                                                ->getParameter('app_timezone')));
                    }
                    if(!property_exists($data, 'date_updated')){
                        $data->date_updated = $data->date_added;
                    }
                    if(!property_exists($data, 'site')){
                        $data->site = 1;
                    }
                    if(!property_exists($data, 'count_posts')){
                        $data->count_posts = 0;
                    }
                    foreach($data as $column => $value){
                        $localeSet = false;
                        $set = 'set'.$this->translateColumnName($column);
                        switch($column){
                            case 'local':
                                $localizations[ $countInserts ]['localizations'] = $value;
                                $localeSet = true;
                                $countLocalizations ++;
                                break;
                            case 'site':
                                $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                                $response = $sModel->getSite($value);
                                if(!$response->error->exist){
                                    $entity->$set($response->result->set);
                                }
                                else{
                                    return $this->createException('EntityDoesNotExist', 'The site with the id / key / domain "'.$value.'" does not exist in database.', 'E:D:002');
                                }
                                unset($response, $sModel);
                                break;
                            default:
                                $entity->$set($value);
                                break;
                        }
                        if($localeSet){
                            $localizations[ $countInserts ]['entity'] = $entity;
                        }
                    }
                    $this->em->persist($entity);
                    $insertedItems[] = $entity;

                    $countInserts ++;
                }
            }
        }
        if($countInserts > 0){
            $this->em->flush();
        }
        /** Now handle localizations */
        if($countInserts > 0 && $countLocalizations > 0){
            $response = $this->insertBlogLocalizations($localizations);
        }
        if($countInserts > 0){
            $this->em->flush();

            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }

        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $post
     *
     * @return array
     */
    public function insertBlogPost($post){
        return $this->insertBlogPosts(array($post));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertBlogPostLocalizations(array $collection){
        $timeStamp = microtime(true);
        if(!is_array($collection)){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $insertedItems = [];
        foreach($collection as $data){
            if($data instanceof BundleEntity\BlogPostLocalization){
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts ++;
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
                    $countInserts ++;
                }
            }
        }
        if($countInserts > 0){
            $this->em->flush();

            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }

        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $revision
     *
     * @return array
     */
    public function insertBlogPostRevision($revision){
        return $this->insertBlogPostRevisions(array($revision));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertBlogPostRevisions(array $collection){
        $timeStamp = microtime(true);
        /** Parameter must be an array */
        if(!is_array($collection)){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $insertedItems = [];
        foreach($collection as $data){
            if($data instanceof BundleEntity\BlogPostRevision){
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts ++;
            }
            else{
                if(is_object($data)){
                    $entity = new BundleEntity\BlogPostRevision();
                    foreach($data as $column => $value){
                        $set = 'set'.$this->translateColumnName($column);
                        switch($column){
                            case 'language':
                                $lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                                $response = $lModel->getLanguage($value);
                                if(!$response->error->exist){
                                    $entity->$set($response->result->set);
                                }
                                unset($response, $lModel);
                                break;
                            case 'post':
                                $response = $this->getBlogPost($value);
                                if(!$response->error->exist){
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

                    $countInserts ++;
                }
                else{
                    new CoreExceptions\InvalidDataException($this->kernel);
                }
            }
        }
        if($countInserts > 0){
            $this->em->flush();

            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }

        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertBlogPosts(array $collection){
        $timeStamp = microtime(true);
        if(!is_array($collection)){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $countLocalizations = 0;
        $insertedItems = [];
        foreach($collection as $data){
            if($data instanceof BundleEntity\BlogPost){
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts ++;
            }
            else{
                if(is_object($data)){
                    $localizations = [];
                    $entity = new BundleEntity\BlogPost();
                    if(!property_exists($data, 'date_added')){
                        $data->date_added = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()
                                                                                                ->getParameter('app_timezone')));
                    }
                    if(!property_exists($data, 'site')){
                        $data->site = 1;
                    }
                    if(!property_exists($data, 'type')){
                        $data->type = 'a';
                    }
                    if(!property_exists($data, 'count_like')){
                        $data->count_like = 0;
                    }
                    if(!property_exists($data, 'count_view')){
                        $data->count_view = 0;
                    }
                    if(!property_exists($data, 'count_dislike')){
                        $data->count_dislike = 0;
                    }
                    if(!property_exists($data, 'count_comment')){
                        $data->count_comment = 0;
                    }
                    foreach($data as $column => $value){
                        $localeSet = false;
                        $set = 'set'.$this->translateColumnName($column);
                        switch($column){
                            case 'local':
                                $localizations[ $countInserts ]['localizations'] = $value;
                                $localeSet = true;
                                $countLocalizations ++;
                                break;
                            case 'blog':
                                if($value instanceof BundleEntity\Blog){
                                    $entity->$set($value);
                                }
                                else{
                                    $response = $this->getBlog($value);
                                    if(!$response->error->exist){
                                        $entity->$set($response->result->set);
                                    }
                                    else{
                                        return $this->createException('EntityDoesNotExist', 'The blog with the id / url_key  "'.$value.'" does not exist in database.', 'E:D:002');
                                    }
                                    unset($response);
                                }
                                break;
                            case 'author':
                            case 'member':
                                $mModel = $this->kernel->getContainer()->get('membermanagement.model');
                                $response = $mModel->getMember($value);
                                if(!$response->error->exist){
                                    $entity->$set($response->result->set);
                                }
                                else{
                                    return $this->createException('EntityDoesNotExist', 'The member with the id / username / e-mail  "'.$value.'" does not exist in database.', 'E:D:002');
                                }
                                unset($response);
                                break;
                            case 'file':
                            case 'preview_image':
                            case 'previewImage':
                                $fModel = $this->kernel->getContainer()->get('filemanagement.model');
                                $response = $fModel->getFile($value);
                                if(!$response->error->exist){
                                    $entity->$set($response->result->set);
                                }
                                else{
                                    return $this->createException('EntityDoesNotExist', 'The file with the id / url_key  "'.$value.'" does not exist in database.', 'E:D:002');
                                }
                                unset($response, $sModel);
                                break;
                            case 'site':
                                $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                                $response = $sModel->getSite($value);
                                if(!$response->error->exist){
                                    $entity->$set($response->result->set);
                                }
                                else{
                                    return $this->createException('EntityDoesNotExist', 'The site with the id / key / domain "'.$value.'" does not exist in database.', 'E:D:002');
                                }
                                unset($response, $sModel);
                                break;
                            default:
                                $entity->$set($value);
                                break;
                        }
                        if($localeSet){
                            $localizations[ $countInserts ]['entity'] = $entity;
                        }
                    }
                    $this->em->persist($entity);
                    $insertedItems[] = $entity;

                    $countInserts ++;
                }
            }
        }
        /** Now handle localizations */
        if($countInserts > 0 && $countLocalizations > 0){
            $response = $this->insertBlogPostLocalizations($localizations);
        }
        if($countInserts > 0){
            $this->em->flush();

            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }

        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $category
     *
     * @return array
     */
    public function insertBlogPostCategory($category){
        return $this->insertBlogPostCategories(array($category));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertBlogPostCategoryLocalizations(array $collection){
        $timeStamp = microtime(true);
        if(!is_array($collection)){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $insertedItems = [];
        foreach($collection as $data){
            if($data instanceof BundleEntity\BlogPostCategoryLocalization){
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts ++;
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
                    $entity->setCategory($bpCategory);
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
                    $countInserts ++;
                }
            }
        }
        if($countInserts > 0){
            $this->em->flush();

            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }

        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertBlogPostCategories(array $collection){
        $timeStamp = microtime(true);
        /** Parameter must be an array */
        if(!is_array($collection)){
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $countLocalizations = 0;
        $insertedItems = [];
        $localizations = [];
        foreach($collection as $data){
            if($data instanceof BundleEntity\BlogPostCategory){
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts ++;
            }
            else{
                if(is_object($data)){
                    $localizations = [];
                    $entity = new BundleEntity\BlogPostCategory();
                    if(!property_exists($data, 'date_added')){
                        $data->date_added = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()
                                                                                                ->getParameter('app_timezone')));
                    }
                    if(!property_exists($data, 'site')){
                        $data->site = 1;
                    }
                    if(!property_exists($data, 'blog')){
                        $data->blog = 1;
                    }
                    foreach($data as $column => $value){
                        $localeSet = false;
                        $set = 'set'.$this->translateColumnName($column);
                        switch($column){
                            case 'local':
                                $localizations[ $countInserts ]['localizations'] = $value;
                                $localeSet = true;
                                $countLocalizations ++;
                                break;
                            case 'blog':
                                $response = $this->getBlog($value);
                                if(!$response->error->exist){
                                    $entity->$set($response->result->set);
                                }
                                else{
                                    return $this->createException('EntityDoesNotExist', 'The blog with the id / url_key '.$value.'" does not exist in database.', 'E:D:002');
                                }
                                unset($response);
                                break;
                            case 'parent':
                                $response = $this->getBlogPostCategory($value);
                                if(!$response->error->exist){
                                    $entity->$set($response->result->set);
                                }
                                else{
                                    return $this->createException('EntityDoesNotExist', 'The blog post category with the id / url_key '.$value.'" does not exist in database.', 'E:D:002');
                                }
                                unset($response);
                                break;
                            case 'site':
                                $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                                $response = $sModel->getSite($value);
                                if(!$response->error->exist){
                                    $entity->$set($response->result->set);
                                }
                                else{
                                    return $this->createException('EntityDoesNotExist', 'The site with the id / key / domain "'.$value.'" does not exist in database.', 'E:D:002');
                                }
                                unset($response, $sModel);
                                break;
                            default:
                                $entity->$set($value);
                                break;
                        }
                        if($localeSet){
                            $localizations[ $countInserts ]['entity'] = $entity;
                        }
                    }
                    $this->em->persist($entity);
                    $insertedItems[] = $entity;

                    $countInserts ++;
                }
            }
        }
        if($countInserts > 0){
            $this->em->flush();
        }
        /** Now handle localizations */
        if($countInserts > 0 && $countLocalizations > 0){
            $response = $this->insertBlogPostCategoryLocalizations($localizations);
        }
        if($countInserts > 0){
            $this->em->flush();

            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }

        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $file
     * @param mixed $post
     * @param bool  $bypass
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
     */
    public function isFileAssociatedWithBlogPost($file, $post, bool $bypass = false){
        $timeStamp = microtime(true);
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

        return new ModelResponse($found, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param $locale
     * @param $blogPost
     * @param bool|null $bypass
     * @return ModelResponse|bool
     */
    public function isLocaleAssociatedWithBlogPost($locale, $blogPost, bool $bypass = null){
        $timeStamp = microtime(true);
        $bypass = $bypass ?? false;
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
        return new ModelResponse($found, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $post
     * @param mixed $category
     * @param bool  $bypass
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
     */
    public function isPostAssociatedWithCategory($post, $category, bool $bypass = false){
        $timeStamp = microtime(true);
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

        $qStr = 'SELECT COUNT('.$this->entity['cobp']['alias'].'.category)'
            .' FROM '.$this->entity['cobp']['name'].' '.$this->entity['cobp']['alias']
            .' WHERE '.$this->entity['cobp']['alias'].'.post = '.$post->getId()
            .' AND '.$this->entity['cobp']['alias'].'.category = '.$category->getId();
        $query = $this->em->createQuery($qStr);

        $result = $query->getSingleScalarResult();

        /** flush all into database */
        if($result > 0){
            $found = true;
        }
        if($bypass){
            return $found;
        }

        return new ModelResponse($found, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listBlogPostCategories(array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
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

        $entities = [];
        foreach($result as $entry){
            $id = $entry->getCategory()->getId();
            if(!isset($unique[ $id ])){
                $entities[] = $entry->getCategory();
                $unique[ $id ] = '';
            }
        }
        $totalRows = count($entities);
        if($totalRows < 1){
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
        }

        return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listBlogPostRevisions(array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
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
        if($totalRows < 1){
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
        }

        return new ModelResponse($result, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listBlogPosts(array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
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

        $entities = [];
        foreach($result as $entry){
            $id = $entry->getBlogPost()->getId();
            if(!isset($unique[ $id ])){
                $unique[ $id ] = '';
                $entities[] = $entry->getBlogPost();
            }
        }
        $totalRows = count($entities);
        if($totalRows < 1){
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
        }

        return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listBlogs(array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
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

        $entities = [];
        $unique = [];
        foreach($result as $entry){
            $id = $entry->getBlog()->getId();
            if(!isset($unique[ $id ])){
                $entities[] = $entry->getBlog();
                $unique[ $id ] = '';
            }
        }
        $totalRows = count($entities);
        if($totalRows < 1){
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
        }

        return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed      $post
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listCategoriesOfPost($post, array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
        $response = $this->getBlogPost($post);
        if($response->error->exist){
            return $response;
        }
        $post = $response->result->set;
        $query_str = 'SELECT '.$this->entity['cobp']['alias']
            .' FROM '.$this->entity['cobp']['name'].' '.$this->entity['cobp']['alias']
            .' WHERE '.$this->entity['cobp']['alias'].'.post = '.$post->getId();
        $query = $this->em->createQuery($query_str);
        $result = $query->getResult();

        $catsInPost = [];
        if(count($result) > 0){
            foreach($result as $cobp){
                $catsInPost[] = $cobp->getCategory()->getId();
            }
        }
        if(count($catsInPost) < 1){
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
        }
        $columnI = $this->entity['bpc']['alias'].'.id';
        $conditionI = array('column' => $columnI, 'comparison' => 'in', 'value' => $catsInPost);
        $filter[] = array(
            'glue'      => 'and',
            'condition' => array(
                array(
                    'glue'      => 'and',
                    'condition' => $conditionI,
                )
            )
        );

        return $this->listBlogPostCategories($filter, $sortOrder, $limit);
    }

    /**
     * @param mixed      $post
     * @param string     $mediaType
     * @param array|null $sortOrder
     * @param array|null $limit
     * @param array|null $filter
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listMediaOfBlogPost($post, string $mediaType = 'all', array $sortOrder = null, array $limit = null,
                                        array $filter = null
    ){
        $timeStamp = microtime(true);
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

        $fileIds = [];
        $totalRows = count($result);

        if($totalRows > 0){
            foreach($result as $gm){
                $fileIds[] = $gm->getFile()->getId();
            }
        }
        else{
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
        }

        $filter[] = array('glue'      => 'and',
                          'condition' => array(
                              array(
                                  'glue'      => 'and',
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
     * @param mixed      $category
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostRevisionsInCategory($category, array $filter = null, array $sortOrder = null,
                                                array $limit = null
    ){
        $timeStamp = microtime(true);
        $response = $this->getBlogPostCategory($category);
        if($response->error->exist){
            return $response;
        }
        $category = $response->result->set;
        /** First identify posts associated with given category */
        $query_str = 'SELECT '.$this->entity['cobp']['alias']
            .' FROM '.$this->entity['cobp']['name'].' '.$this->entity['cobp']['alias']
            .' WHERE '.$this->entity['cobp']['alias'].'.category = '.$category->getId();
        $query = $this->em->createQuery($query_str);
        $result = $query->getResult();

        $revisions = [];
        if(count($result) > 0){
            foreach($result as $cobp){
                $revisionResponse = $this->getLastRevisionOfBlogPost($cobp->getPost());
                if(!$revisionResponse->error->exist){
                    $revisions[] = $revisionResponse->result->set;
                }
            }
        }
        if(count($revisions) < 1){
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
        }

        return new ModelResponse($revisions, count($revisions), 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));;
    }

    /**
     * @param mixed      $category
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostsInCategory($category, array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
        $response = $this->getBlogPostCategory($category);
        if($response->error->exist){
            return $response;
        }
        $category = $response->result->set;
        /** First identify posts associated with given category */
        $query_str = 'SELECT '.$this->entity['cobp']['alias']
            .' FROM '.$this->entity['cobp']['name'].' '.$this->entity['cobp']['alias']
            .' WHERE '.$this->entity['cobp']['alias'].'.category = '.$category->getId();
        $query = $this->em->createQuery($query_str);
        $result = $query->getResult();

        $postsInCat = [];
        if(count($result) > 0){
            foreach($result as $cobp){
                $postsInCat[] = $cobp->getPost()->getId();
            }
        }
        if(count($postsInCat) < 1){
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
        }
        $columnI = $this->entity['bp']['alias'].'.id';
        $filter[] = array(
            'glue'      => 'and',
            'condition' => array(
                array(
                    'glue'      => 'and',
                    'condition' => array('column' => $columnI, 'comparison' => 'in', 'value' => $postsInCat),
                )
            )
        );
        $response = $this->listBlogPosts($filter, $sortOrder, $limit);
        $response->stats->execution->start = $timeStamp;

        return $response;
    }

    /**
     * @param mixed      $blog
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostCategoriesOfBlog($blog, array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
        $response = $this->getBlog($blog);
        if($response->error->exist){
            return $response;
        }
        $blog = $response->result->set;

        $column = $this->entity['bpc']['alias'].'.blog';
        $condition = array('column' => $column, 'comparison' => '=', 'value' => $blog->getId());
        $filter[] = array(
            'glue'      => 'and',
            'condition' => array(
                array(
                    'glue'      => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listBlogPostCategories($filter, $sortOrder, $limit);

        $response->stats->execution->start = $timeStamp;

        return $response;
    }

    /**
     * @param mixed      $blog
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostsOfBlog($blog, array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
        $response = $this->getBlog($blog);
        if($response->error->exist){
            return $response;
        }
        $blog = $response->result->set;

        $column = $this->entity['bp']['alias'].'.blog';
        $filter[] = array(
            'glue'      => 'and',
            'condition' => array(
                array(
                    'glue'      => 'and',
                    'condition' => array('column' => $column, 'comparison' => '=', 'value' => $blog->getId()),
                )
            )
        );

        return $this->listBlogPosts($filter, $sortOrder, $limit);
    }

    /**
     * @param mixed      $blog
     * @param mixed      $category
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostsOfBlogInCategory($blog, $category, array $filter = null, array $sortOrder = null,
                                              array $limit = null
    ){
        $timeStamp = microtime(true);
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
        $qStr = 'SELECT '.$this->entity['cobp']['alias']
            .' FROM '.$this->entity['cobp']['name'].' '.$this->entity['cobp']['alias']
            .' WHERE '.$this->entity['cobp']['alias'].'.category = '.$category->getId();
        $q = $this->em->createQuery($qStr);
        $result = $q->getResult();
        $postsInCat = [];
        if(count($result) < 1){
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
        }
        foreach($result as $cobp){
            $postsInCat[] = $cobp->getPost()->getId();
        }
        $columnI = $this->entity['bp']['alias'].'.id';
        $conditionI = array('column' => $columnI, 'comparison' => 'in', 'value' => $postsInCat);
        $filter[] = array(
            'glue'      => 'and',
            'condition' => array(
                array(
                    'glue'      => 'and',
                    'condition' => $conditionI,
                )
            )
        );
        $response = $this->listPostsOfBlog($blog, $filter, $sortOrder, $limit);

        $response->stats->execution->start = $timeStamp;

        return $response;
    }

    /**
     * @param mixed      $blog
     * @param mixed      $category
     * @param mixed      $site
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostsOfBlogInCategoryAndSite($blog, $category, $site, array $filter = null,
                                                     array $sortOrder = null, array $limit = null
    ){
        $timeStamp = microtime(true);
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
        $qStr = 'SELECT '.$this->entity['cobp']['alias']
            .' FROM '.$this->entity['cobp']['name'].' '.$this->entity['cobp']['alias']
            .' WHERE '.$this->entity['cobp']['alias'].'.category = '.$category->getId();
        $q = $this->em->createQuery($qStr);
        $result = $q->getResult();

        $postsInCat = [];
        if(count($result) > 0){
            foreach($result as $cobp){
                $postsInCat[] = $cobp->getPost()->getId();
            }
        }
        $selectedIds = implode(',', $postsInCat);
        $columnI = $this->entity['bp']['alias'].'.id';
        $conditionI = array('column' => $columnI, 'comparison' => '=', 'in' => $selectedIds);
        $filter[] = array(
            'glue'      => 'and',
            'condition' => array(
                array(
                    'glue'      => 'and',
                    'condition' => $conditionI,
                ),
                array(
                    'glue'      => 'and',
                    'condition' => array('column' => $this->entity['bp']['alias'].'.site', 'comparison' => '=', 'value' => $site->getId()),
                )
            )
        );
        $response = $this->listPostsOfBlog($blog, $filter, $sortOrder, $limit);

        $response->stats->execution->start = $timeStamp;

        return $response;
    }

    /**
     * @param mixed      $blog
     * @param mixed      $site
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listPostsOfBlogInSite($blog, $site, array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
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
            'glue'      => 'and',
            'condition' => array(
                array(
                    'glue'      => 'and',
                    'condition' => array('column' => $this->entity['bp']['alias'].'.site', 'comparison' => '=', 'value' => $site->getId()),
                )
            )
        );
        $response = $this->listPostsOfBlog($blog, $filter, $sortOrder, $limit);

        $response->stats->execution->start = $timeStamp;

        return $response;
    }

    /**
     * @param $site
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     * @return ModelResponse
     */
    public function listPostRevisionsOfSite($site, array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
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
     * @param $site
     * @param \DateTime $dateStart
     * @param \DateTime $dateEnd
     * @param bool|null $inclusive
     * @param array|null $sortOrder
     * @param array|null $limit
     * @return ModelResponse
     */
    public function listPostRevisionsOfSiteUpdatedBetween($site, \DateTime $dateStart, \DateTime $dateEnd, bool $inclusive = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
        $inclusive = $inclusive ?? true;
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
     * @param $blog
     * @param $category
     * @param array $statuses
     * @param array|null $sortOrder
     * @param array|null $limit
     * @return ModelResponse
     */
    public function listPostsOfBlogInCategoryWithStatuses($blog, $category, array $statuses, array $sortOrder = null, array $limit = null){
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
     * @param $blog
     * @param $site
     * @param array $statuses
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     * @return ModelResponse
     */
    public function listPostsOfBlogInSiteWithStatuses($blog, $site, array $statuses, array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
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
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     * @return ModelResponse
     */
    public function listPublishedPosts(array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
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
     * @param $blog
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     * @return ModelResponse
     */
    public function listPublishedPostsOfBlog($blog, array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
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
     * @param $blog
     * @param $category
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     * @return ModelResponse
     */
    public function listPublishedPostsOfBlogInCategory($blog, $category, array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
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
     * @param array $collection
     * @return ModelResponse
     */
    public function markPostsAsDeleted(array $collection){
        $timeStamp = microtime(true);
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
        $response->stats->execution->end = microtime(true);

        return $response;
    }

    /**
     * @param array $collection
     * @return ModelResponse
     */
    public function publishBlogPosts(array $collection){
        $timeStamp = microtime(true);
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
        $response->stats->execution->end = microtime(true);

        return $response;
    }

    /**
     * @param array $categories
     * @param $post
     * @return ModelResponse
     */
    public function removeCategoriesFromPost(array $categories, $post){
        $timeStamp = microtime(true);
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
            return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
    }

    /**
     * @param array $locales
     * @param $blogPost
     * @return ModelResponse
     */
    public function removeLocalesFromBlogPost(array $locales, $blogPost){
        $timeStamp = microtime(true);
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
            return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
    }

    /**
     * @param array $posts
     * @param $category
     * @return ModelResponse
     */
    public function removePostsFromCategory(array $posts, $category){
        $timeStamp = microtime(true);
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
            return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
    }

    /**
     * @param array $files
     * @param $post
     * @return ModelResponse
     */
    public function removeFilesFromPost(array $files, $post){
        $timeStamp = microtime(true);
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
            return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
    }

    /**
     * @param array $collection
     * @return ModelResponse
     */
    public function unpublishBlogPosts(array $collection){
        $timeStamp = microtime(true);
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
        $response->stats->execution->end = microtime(true);

        return $response;
    }

    /**
     * @param array $collection
     * @return ModelResponse
     */
    public function updateBlogPosts(array $collection){
        $timeStamp = microtime(true);
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
                    return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" property and id property ‚Äãmust have an integer value.', 'E:S:003');
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
            }
        }
        if($countUpdates > 0){
            $this->em->flush();
            return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
    }

    /**
     * @param $category
     * @return ModelResponse
     */
    public function updateBlogPostCategory($category){
        return $this->updateBlogPostCategories(array($category));
    }

    /**
     * @param array $collection
     * @return ModelResponse
     */
    public function updateBlogPostCategories(array $collection){
        $timeStamp = microtime(true);
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
                    return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" property and id property ‚Äãmust have an integer value.', 'E:S:003');
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
            return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
    }

    /**
     * @param $revision
     * @return ModelResponse
     */
    public function updateBlogPostRevision($revision){
        return $this->updateBlogPostRevisions(array($revision));
    }

    /**
     * @param array $collection
     * @return ModelResponse
     */
    public function updateBlogPostRevisions(array $collection) {
        $timeStamp = microtime(true);
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
            return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
    }

    /**
     * @param $blog
     * @param $site
     * @return ModelResponse
     */
    public function unPublishPostsOfBlogInSite($blog,$site)
    {

        $response = $this->listPostsOfBlogInSite($blog, $site);
        if ($response->error->exist) {
            return $response;
        }
        return $this->unpublishBlogPosts($response->result->set);
    }

    /**
     * @param mixed                                       $post
     * @param array|null                                  $filter
     * @param array|null                                  $sortOrder
     * @param array|null                                  $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listCategoriesOfPostByPost($post, array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
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
     * @param            $category
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listCategoriesOfPostByCategory($category, array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
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
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listCategoriesOfPostItem(array $filter = null, array $sortOrder = null, array $limit = null) {
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
     * @param mixed      $post
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listFilesOfPost($post, array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = microtime(true);
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
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
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
     * @param array $collection
     * @return ModelResponse
     */
    public function updateBlogs(array $collection){
        $timeStamp = microtime(true);
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
                    return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" property and id property ‚Äãmust have an integer value.', 'E:S:003');
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
            return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
    }
    
    /**
     * @param array $categories
     * @param       $postEntry
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
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
     * @param  mixed $blogPost
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listActiveLocalesOfBlogPost($blogPost){
        $timeStamp = microtime(true);
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
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
        }
        return new ModelResponse($locales, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }
}
