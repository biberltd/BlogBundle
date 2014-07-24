<?php
/**
 * @name        FeaturedBlogPost
 * @package		BiberLtd\Core\BlogBundle
 *
 * @author		Murat Ünal
 *
 * @version     1.0.1
 * @date        10.10.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Core\Bundles\BlogBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreEntity;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="featured_blog_post",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_n_featured_blog_post_date_added", columns={"date_added"}),
 *         @ORM\Index(name="idx_n_featured_blog_post_date_published", columns={"date_published"}),
 *         @ORM\Index(name="idx_n_featured_blog_post_unpublished", columns={"date_unpublished"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_featured_blog_post_id", columns={"id"})}
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
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $sort_order;

    /** 
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\FeaturedBlogPostLocalization",
     *     mappedBy="featured_blog_post"
     * )
     */
    protected $localizations;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog_post;
    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

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
     * @name                  setBlogPost ()
     *                                    Sets the blog_post property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post
     *
     * @return          object                $this
     */
    public function setBlogPost($blog_post) {
        if(!$this->setModified('blog_post', $blog_post)->isModified()) {
            return $this;
        }
		$this->blog_post = $blog_post;
		return $this;
    }

    /**
     * @name            getBlogPost ()
     *                              Returns the value of blog_post property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post
     */
    public function getBlogPost() {
        return $this->blog_post;
    }

    /**
     * @name                  setDatePublished ()
     *                                         Sets the date_published property.
     *                                         Updates the data only if stored value and value to be set are different.
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
     *                                   Returns the value of date_published property.
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
     * @name                  setDateUnpublished ()
     *                                           Sets the date_unpublished property.
     *                                           Updates the data only if stored value and value to be set are different.
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
     *                                     Returns the value of date_unpublished property.
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
     * @name                  setSortOrder ()
     *                                     Sets the sort_order property.
     *                                     Updates the data only if stored value and value to be set are different.
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
     *                               Returns the value of sort_order property.
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