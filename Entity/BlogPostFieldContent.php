<?php
/**
 * @name        BlogPostFieldContent
 * @package		BiberLtd\Bundle\CoreBundle\BlogBundle
 *
 * @author		Murat Ünal
 *
 * @version     1.0.0
 * @date        13.09.2013
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
 *     name="blog_post_field_content", 
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"}, 
 *     indexes={
 *         @ORM\Index(name="idx_u_blog_post_field_content", columns={"post","field","language","blog"}),
 *         @ORM\Index(name="idx_n_blog_post_field_content_date_added", columns={"date_added"}),
 *         @ORM\Index(name="idx_n_blog_post_field_content_date_modified", columns={"date_modified"})
 *     }
 * )
 */
class BlogPostFieldContent extends CoreEntity
{
    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_modified;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $language;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostField",
     *     inversedBy="blog_post_field_contents"
     * )
     * @ORM\JoinColumn(name="field", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog_post_field;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\BlogBundle\Entity\Blog",
     *     inversedBy="blog_post_field_contents"
     * )
     * @ORM\JoinColumn(name="blog", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost",
     *     inversedBy="blog_post_field_contents"
     * )
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog_post;

    /**
     * @name                  setBlog ()
     *                                Sets the blog property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog
     *
     * @return          object                $this
     */
    public function setBlog($blog) {
        if(!$this->setModified('blog', $blog)->isModified()) {
            return $this;
        }
		$this->blog = $blog;
		return $this;
    }

    /**
     * @name            getBlog ()
     *                          Returns the value of blog property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog
     */
    public function getBlog() {
        return $this->blog;
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
     * @name                  setBlogPostField ()
     *                                         Sets the blog_post_field property.
     *                                         Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_field
     *
     * @return          object                $this
     */
    public function setBlogPostField($blog_post_field) {
        if(!$this->setModified('blog_post_field', $blog_post_field)->isModified()) {
            return $this;
        }
		$this->blog_post_field = $blog_post_field;
		return $this;
    }

    /**
     * @name            getBlogPostField ()
     *                                   Returns the value of blog_post_field property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_field
     */
    public function getBlogPostField() {
        return $this->blog_post_field;
    }

    /**
     * @name                  setDateModified ()
     *                                        Sets the date_modified property.
     *                                        Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_modified
     *
     * @return          object                $this
     */
    public function setDateModified($date_modified) {
        if(!$this->setModified('date_modified', $date_modified)->isModified()) {
            return $this;
        }
		$this->date_modified = $date_modified;
		return $this;
    }

    /**
     * @name            getDateModified ()
     *                                  Returns the value of date_modified property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_modified
     */
    public function getDateModified() {
        return $this->date_modified;
    }

    /**
     * @name                  setLanguage ()
     *                                    Sets the language property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $language
     *
     * @return          object                $this
     */
    public function setLanguage($language) {
        if(!$this->setModified('language', $language)->isModified()) {
            return $this;
        }
		$this->language = $language;
		return $this;
    }

    /**
     * @name            getLanguage ()
     *                              Returns the value of language property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->language
     */
    public function getLanguage() {
        return $this->language;
    }
    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Murat Ünal
 * 13.09.2013
 * **************************************
 * A getDateAdded()
 * A getDateModified()
 * A getLanguage()
 * A getBlogPostField()
 * A getBlog()
 * A getBlogPost()
 *
 * A setDateAdded()
 * A setDateModified()
 * A setLanguage()
 * A setBlogPostField()
 * A setBlog()
 * A setBlogPost()
 *
 */