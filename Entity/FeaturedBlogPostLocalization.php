<?php
/**
 * @name        CategoriesOfBlogPost
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
 *     name="featured_blog_post_localization",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_featured_blog_post_localization", columns={"language"})}
 * )
 */
class FeaturedBlogPostLocalization extends CoreEntity
{
    /** 
     * @ORM\Column(type="text", nullable=false)
     */
    private $content;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $language;

    /** 
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\FeaturedBlogPost",
     *     inversedBy="localizations"
     * )
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $featured_blog_post;

    /**
     * @name                  setContent ()
     *                                   Sets the content property.
     *                                   Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $content
     *
     * @return          object                $this
     */
    public function setContent($content) {
        if(!$this->setModified('content', $content)->isModified()) {
            return $this;
        }
		$this->content = $content;
		return $this;
    }

    /**
     * @name            getContent ()
     *                             Returns the value of content property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->content
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @name                  setFeaturedBlogPost ()
     *                                            Sets the featured_blog_post property.
     *                                            Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $featured_blog_post
     *
     * @return          object                $this
     */
    public function setFeaturedBlogPost($featured_blog_post) {
        if(!$this->setModified('featured_blog_post', $featured_blog_post)->isModified()) {
            return $this;
        }
		$this->featured_blog_post = $featured_blog_post;
		return $this;
    }

    /**
     * @name            getFeaturedBlogPost ()
     *                                      Returns the value of featured_blog_post property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->featured_blog_post
     */
    public function getFeaturedBlogPost() {
        return $this->featured_blog_post;
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
 * 15.09.2013
 * **************************************
 * A getContent()
 * A getFeaturedBlogPost()
 * A getLanguage()
 *
 * A setContent()
 * A setFeaturedBlogPost()
 * A setLanguage()
 *
 */
