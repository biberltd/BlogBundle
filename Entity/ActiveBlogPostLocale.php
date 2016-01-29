<?php
namespace BiberLtd\Bundle\BlogBundle\Entity;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="active_blog_post_locale", options={"charset":"utf8","collate":"utf8_turkish_ci"})
 */
class ActiveBlogPostLocale extends CoreEntity
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="blog_post", referencedColumnName="id", nullable=false)
     */
    private $blogPost;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $language;

    /**
     * @name        getBlogPost ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      mixed
     */
    public function getBlogPost(){
        return $this->blogPost;
    }

    /**
     * @name        setBlogPost ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       mixed $blogPost
     *
     * @return      $this
     */
    public function setBlogPost($blogPost){
        if(!$this->setModified('blogPost', $blogPost)->isModified()){
            return $this;
        }
        $this->blogPost = $blogPost;

        return $this;
    }

    /**
     * @name        getLanguage ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      mixed
     */
    public function getLanguage(){
        return $this->language;
    }

    /**
     * @name        setLanguage ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       mixed $language
     *
     * @return      $this
     */
    public function setLanguage($language){
        if(!$this->setModified('language', $language)->isModified()){
            return $this;
        }
        $this->language = $language;

        return $this;
    }


}