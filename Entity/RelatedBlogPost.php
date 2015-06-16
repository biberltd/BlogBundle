<?php
/**
 * @name        relatedBlogPost
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
 */
namespace BiberLtd\Bundle\BlogBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="related_blog_post",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idxNRelatedBlogPostDateAdded", columns={"date_added"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxURelatedBlogPost", columns={"post","related_post"})}
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
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $post;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="related_post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $related_post;

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
     * @param           mixed $blog_post
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
     * @return          mixed           $this->blog_post
     */
    public function getBlogPost() {
        return $this->post;
    }

    /**
     * @name            setRelatedPost ()
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