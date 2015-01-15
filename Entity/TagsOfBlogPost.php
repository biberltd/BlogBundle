<?php
/**
 * @name        CategoriesOfBlogPost
 * @package		BiberLtd\Bundle\CoreBundle\BlogBundle
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
namespace BiberLtd\Bundle\BlogBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="tags_of_blog_post",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_u_tags_of_blog_post", columns={"post","tag"}),
 *         @ORM\Index(name="idx_n_tags_of_blog_post_date_added", columns={"tag"})
 *     }
 * )
 */
class TagsOfBlogPost extends CoreEntity
{
    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostTag")
     * @ORM\JoinColumn(name="tag", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog_post_tag;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog_post;

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
     * @name                  setBlogPostTag ()
     *                                       Sets the blog_post_tag property.
     *                                       Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_tag
     *
     * @return          object                $this
     */
    public function setBlogPostTag($blog_post_tag) {
        if(!$this->setModified('blog_post_tag', $blog_post_tag)->isModified()) {
            return $this;
        }
		$this->blog_post_tag = $blog_post_tag;
		return $this;
    }

    /**
     * @name            getBlogPostTag ()
     *                                 Returns the value of blog_post_tag property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_tag
     */
    public function getBlogPostTag() {
        return $this->blog_post_tag;
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
 * A getBlogPostTag()
 * A getDateAdded()
 *
 * A setBlogPost()
 * A setBlogPostTag()
 * A setDateAdded()
 *
 */