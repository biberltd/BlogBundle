<?php

/**
 * @name        ActiveGalleryLocale
 * @package		BiberLtd\Core\GalleryBundle
 *
 * @author      Can Berkol
 *
 * @version     1.0.0
 * @date        21.08.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Bundle\BlogBundle\Entity;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="active_blogpost_locale",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_active_blogpost_locale", columns={"blog_post","language"})}
 * )
 */
class ActiveBlogPostLocale extends CoreEntity
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="blog_post", referencedColumnName="id", nullable=false)
     */
    private $blog_post;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false)
     */
    private $language;

    /**
     * @name            getBlogPost()
     *                  Returns the value of gallery property.
     *
     * @author          S.S.Aylak
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->folder
     */
    public function getBlogPost()
    {
        return $this->blog_post;
    }

    /**
     * @name            setBlogPost()
     *                  Sets the gallery property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          S.S.Aylak
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed                   $blog_post
     *
     * @return          object                  $this
     */
    public function setBlogPost($blog_post)
    {
        if($this->setModified('blog_post', $blog_post)->isModified()) {
            $this->blog_post = $blog_post;
        }

        return $this;
    }

    /**
     * @name            getLanguage()
     *                  Returns the value of language property.
     *
     * @author          S.S.Aylak
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->folder
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @name            setLanguage()
     *                  Sets the language property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          S.S.Aylak
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed                   $language
     *
     * @return          object                  $this
     */
    public function setLanguage($language)
    {
        if($this->setModified('language', $language)->isModified()) {
            $this->language = $language;
        }

        return $this;
    }
}