<?php
/**
 * @name        BlogPostCategoryLocalization
 * @package		BiberLtd\Core\BlogBundle
 *
 * @author		Can Berkol
 * @author		Murat Ünal
 *
 * @version     1.0.4
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
 *     name="blog_post_category_localization",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idxUBlogPostLocalization", columns={"language","post_category"}),
 *         @ORM\UniqueConstraint(name="idxUBlogPostUrlKey", columns={"language","url_key","post_category"})
 *     }
 * )
 */
class BlogPostCategoryLocalization extends CoreEntity
{
    /**
     * @ORM\Column(type="string", length=155, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $url_key;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $meta_keywords;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $meta_description;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $language;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory",
     *     inversedBy="localizations",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="post_category", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $post_category;


    /**
     * @name            setCategory ()
     *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_category
     *
     * @return          object                $this
     */
    public function setCategory($blog_post_category) {
        if(!$this->setModified('post_category', $blog_post_category)->isModified()) {
            return $this;
        }
        $this->post_category = $blog_post_category;
        return $this;
    }

    /**
     * @name            getCategory()
     *                  Returns the value of blog_post_category property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          mixed           $this->category
     */
    public function getCategory() {
        return $this->post_category;
    }

    /**
     * @name            setDescription ()
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $description
     *
     * @return          object                $this
     */
    public function setDescription($description) {
        if(!$this->setModified('description', $description)->isModified()) {
            return $this;
        }
        $this->description = $description;
        return $this;
    }

    /**
     * @name            getDescription ()
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->description
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @name            setLanguage ()
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
     *                  Returns the value of language property.
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

    /**
     * @name            setName ()
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $name
     *
     * @return          object                $this
     */
    public function setName($name) {
        if(!$this->setModified('name', $name)->isModified()) {
            return $this;
        }
        $this->name = $name;
        return $this;
    }

    /**
     * @name            getName ()
     *                  Returns the value of name property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @name            setUrlKey ()
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $url_key
     *
     * @return          object                $this
     */
    public function setUrlKey($url_key) {
        if(!$this->setModified('url_key', $url_key)->isModified()) {
            return $this;
        }
        $this->url_key = $url_key;
        return $this;
    }

    /**
     * @name            getUrlKey ()
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->url_key
     */
    public function getUrlKey() {
        return $this->url_key;
    }
    /**
     * @name            setUrlKey ()
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $desc
     *
     * @return          object                $this
     */
    public function setMetaDescription($desc) {
        if(!$this->setModified('meta_description', $desc)->isModified()) {
            return $this;
        }
        $this->meta_description = $desc;
        return $this;
    }

    /**
     * @name            getUrlKey ()
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->url_key
     */
    public function getMetaDescription() {
        return $this->meta_description;
    }
    /**
     * @name            setUrlKey ()
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $url_key
     *
     * @return          object                $this
     */
    public function setMetaKeywords($keywords) {
        if(!$this->setModified('meta_keywords', $keywords)->isModified()) {
            return $this;
        }
        $this->meta_keywords = $keywords;
        return $this;
    }

    /**
     * @name            getUrlKey ()
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->url_key
     */
    public function getMetaKeywords() {
        return $this->meta_keywords;
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.1  					   26.04.2015
 * TW #3568845
 * Can Berkol
 * **************************************
 * A getCategory()
 * A setCategory()
 *
 * **************************************
 * v1.0.0                      Murat Ünal
 * 13.09.2013
 * **************************************
 * A getBlogPostCategory()
 * A getDescription()
 * A getLanguage()
 * A getName()
 * A getUrlKey()
 *
 * A setBlogPostCategory()
 * A setDescription()
 * A setLanguage()
 * A setName()
 * A setUrlKey()
 *
 */