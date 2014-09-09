<?php
/**
 * @name        relatedBlogPost
 * @package		BiberLtd\Core\BlogBundle
 *
 * @author		Murat Ünal
 *
 * @version     1.0.0
 * @date        15.09.2013
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
 *     name="related_blog_post",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idx_n_related_blog_post_date_added", columns={"date_added"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_related_blog_post", columns={"post","related_post"})}
 * )
 */
class RelatedBlogPost extends CoreEntity
{
    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog_post;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="related_post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $related_post;

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
     * @name                  setRelatedPost ()
     *                                       Sets the related_post property.
     *                                       Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $related_post
     *
     * @return          object                $this
     */
    public function setRelatedPost($related_post) {
        if(!$this->setModified('related_post', $related_post)->isModified()) {
            return $this;
        }
		$this->related_post = $related_post;
		return $this;
    }

    /**
     * @name            getRelatedPost ()
     *                                 Returns the value of related_post property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->related_post
     */
    public function getRelatedPost() {
        return $this->related_post;
    }
    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Murat Ünal
 * 15.09.2013
 * **************************************
 * A getBlogPost()
 * A getDateAdded()
 * A getRelatedPost()
 *
 * A setBlogPost()
 * A setDateAdded()
 * A setRelatedPost()
 *
 */