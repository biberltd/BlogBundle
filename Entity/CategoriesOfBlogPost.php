<?php
/**
 * @name        CategoriesOfBlogPost
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
namespace BiberLtd\Bundle\BlogBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="categories_of_blog_post",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idxNCategoriesOfBlogPostDateAdded", columns={"date_added"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUCategoriesOfBlogPost", columns={"post","category"})}
 * )
 */
class CategoriesOfBlogPost extends CoreEntity
{
    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /**
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"y"})
     */
    private $is_primary;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory")
     * @ORM\JoinColumn(name="category", referencedColumnName="id", nullable=false)
     */
    private $category;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false)
     */
    private $post;


    /**
     * @name                  setBlogPostCategories ()
     *                                              Sets the blog_post_categories property.
     *                                              Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $category
     *
     * @return          object                $this
     */
    public function setCategory($category) {
        if(!$this->setModified('category', $category)->isModified()) {
            return $this;
        }
		$this->category = $category;
		return $this;
    }

    /**
     * @name            getBlogPostCategories ()
     *                                        Returns the value of blog_post_categories property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_categories
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @name                  setBlogPosts ()
     *                                     Sets the blog_posts property.
     *                                     Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $post
     *
     * @return          object                $this
     */
    public function setPost($post) {
        if(!$this->setModified('post', $post)->isModified()) {
            return $this;
        }
		$this->post = $post;
		return $this;
    }

    /**
     * @name            getBlogPosts ()
     *                               Returns the value of blog_posts property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_posts
     */
    public function getPost() {
        return $this->post;
    }

    /**
     * @name                  setIsPrimary()
     *                            Sets the is_primary property.
     *                            Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $is_primary
     *
     * @return          object                $this
     */
    public function setIsPrimary($is_primary) {
        if(!$this->setModified('is_primary', $is_primary)->isModified()) {
            return $this;
        }
		$this->is_primary = $is_primary;
		return $this;
    }

    /**
     * @name            getIsPrimary()
     *                      Returns the value of is_primary property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->is_primary
     */
    public function getIsPrimary() {
        return $this->is_primary;
    }

    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

}
/**
 * Change Log:
 * **************************************
 * v1.0.01                     Murat Ünal
 * 10.10.2013
 * **************************************
 * A getBlogPosts()
 * A getBlogPostCategories()
 * A getDateAdded()
 * A getIsPrimary()
 *
 * A setBlogPosts()
 * A setBlogPostCategories()
 * A setDateAdded()
 * A setIsPrimary()
 *
 */