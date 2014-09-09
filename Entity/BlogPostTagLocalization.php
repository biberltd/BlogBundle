<?php
/**
 * @name        BlogPostTagLocalization
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
 *     name="blog_post_tag_localization",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idx_u_blog_post_tag_localization", columns={"tag","language"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_blog_post_tag_localization_url_key", columns={"language","url_key"})}
 * )
 */
class BlogPostTagLocalization extends CoreEntity
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
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogPostTag", inversedBy="localizations")
     * @ORM\JoinColumn(name="tag", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog_post_tag;

    /** 
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="BiberLtd\Core\Bundles\MultiLanguageSupportBundle\Entity\Language")
     * 
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, unique=true, onDelete="CASCADE")
     */
    private $language;

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

    /**
     * @name                  setName ()
     *                                Sets the name property.
     *                                Updates the data only if stored value and value to be set are different.
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
     *                          Returns the value of name property.
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
     * @name                  setUrlKey ()
     *                                  Sets the url_key property.
     *                                  Updates the data only if stored value and value to be set are different.
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
     *                            Returns the value of url_key property.
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
 * A getBlogPostTag()
 * A getLanguage()
 * A getName()
 * A getUrlKey()
 *
 * A setBlogPostTag()
 * A setLanguage()
 * A setName()
 * A setUrlKey()
 *
 */
