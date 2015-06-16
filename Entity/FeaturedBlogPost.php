<?php
/**
 * @name        FeaturedBlogPost
 * @package		BiberLtd\Core\BlogBundle
 *
 * @author		Can Berkol
 * @author		Murat Ünal
 *
 * @version     1.0.1
 * @date        26.04.2015
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Bundle\BlogBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="featured_blog_post",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxNFeaturedBlogPostDateAdded", columns={"date_added"}),
 *         @ORM\Index(name="idxNFeaturedBlogPostDatePublished", columns={"date_published"}),
 *         @ORM\Index(name="idxNFeaturedBlogPostDateUnpublished", columns={"date_unpublished"}),
 *         @ORM\Index(name="idxNFeaturedBlogPostDateUpdated", columns={"date_updated"}),
 *         @ORM\Index(name="idxNFeaturedBlogPostDateRemoved", columns={"date_removed"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUFeaturedBlogPostId", columns={"id"})}
 * )
 */
class FeaturedBlogPost extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_published;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_unpublished;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":1})
     */
    private $sort_order;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_updated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
	public $date_removed;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $post;

    /**
     * @name            getId()
     *                  Gets $id property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          integer          $this->id
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @name            setPost()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @use             $this->setModified()
     *
     * @param           mixed 		$blog_post
     *
     * @return          object                $this
     */
    public function setPost($blog_post) {
        if(!$this->setModified('post', $blog_post)->isModified()) {
            return $this;
        }
		$this->post = $blog_post;
		return $this;
    }

    /**
     * @name            getPost()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          mixed           $this->post
     */
    public function getPost() {
        return $this->post;
    }

    /**
     * @name            setDatePublished ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_published
     *
     * @return          object                $this
     */
    public function setDatePublished($date_published) {
        if(!$this->setModified('date_published', $date_published)->isModified()) {
            return $this;
        }
		$this->date_published = $date_published;
		return $this;
    }

    /**
     * @name            getDatePublished ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_published
     */
    public function getDatePublished() {
        return $this->date_published;
    }

    /**
     * @name            setDateUnpublished ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_unpublished
     *
     * @return          object                $this
     */
    public function setDateUnpublished($date_unpublished) {
        if(!$this->setModified('date_unpublished', $date_unpublished)->isModified()) {
            return $this;
        }
		$this->date_unpublished = $date_unpublished;
		return $this;
    }

    /**
     * @name            getDateUnpublished ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_unpublished
     */
    public function getDateUnpublished() {
        return $this->date_unpublished;
    }

    /**
     * @name            setSortOrder ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $sort_order
     *
     * @return          object                $this
     */
    public function setSortOrder($sort_order) {
        if(!$this->setModified('sort_order', $sort_order)->isModified()) {
            return $this;
        }
		$this->sort_order = $sort_order;
		return $this;
    }

    /**
     * @name            getSortOrder ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->sort_order
     */
    public function getSortOrder() {
        return $this->sort_order;
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.1  					   26.04.2015
 * TW #3568845
 * Can Berkol
 * **************************************
 * Major changes!!
 *
 * **************************************
 * v1.0.1                      Murat Ünal
 * 10.10.2013
 * **************************************
 * A getBlogPost()
 * A getDateAdded()
 * A getDatePublished()
 * A getDateUnpublished()
 * A getId()
 * A getLocalizations()
 * A getSortOrder()
 *
 * A setBlogPost()
 * A setDateAdded()
 * A setDatePublished()
 * A setDateUnpublished()
 * A setLocalizations()
 * A setSortOrder()
 *
 */