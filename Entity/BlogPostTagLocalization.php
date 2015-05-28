<?php
/**
 * @name        BlogPostTagLocalization
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
 *     name="blog_post_tag_localization",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idxUBlogPostTagLocalization", columns={"tag","language"}),
 *         @ORM\UniqueConstraint(name="idxUBlogPostTagUrlKey", columns={"language","url_key"})
 *     }
 * )
 */
class BlogPostTagLocalization extends CoreEntity{
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
     * @ORM\OneToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * 
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, unique=true, onDelete="CASCADE")
     */
    private $language;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostTag", inversedBy="localizations")
     * @ORM\JoinColumn(name="tag", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $tag;

    /**
     * @name            setTag()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @use             $this->setModified()
     *
     * @param           mixed 			$tag
     *
     * @return          object          $this
     */
    public function setTag($tag) {
        if(!$this->setModified('tag', $tag)->isModified()) {
            return $this;
        }
		$this->tag = $tag;
		return $this;
    }

    /**
     * @name            getTag()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          mixed           $this->tag
     */
    public function getTag() {
        return $this->tag;
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
