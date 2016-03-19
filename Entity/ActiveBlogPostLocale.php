<?php
namespace BiberLtd\Bundle\BlogBundle\Entity;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
<<<<<<< HEAD
use BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language;
=======
>>>>>>> c16988b65157239621309d5468e2493309930d0a
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
<<<<<<< HEAD
     * @var BlogPost
=======
>>>>>>> c16988b65157239621309d5468e2493309930d0a
     */
    private $blogPost;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
<<<<<<< HEAD
     * @var Language
=======
>>>>>>> c16988b65157239621309d5468e2493309930d0a
     */
    private $language;

    /**
<<<<<<< HEAD
     * @return mixed
     */
    public function getLanguage(){
        return $this->language;
    }

    /**
     * @param \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language
     *
     * @return $this
     */
    public function setLanguage(Language $language){
        if(!$this->setModified('language', $language)->isModified()){
            return $this;
        }
        $this->language = $language;
=======
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
>>>>>>> c16988b65157239621309d5468e2493309930d0a

        return $this;
    }

    /**
<<<<<<< HEAD
     * @return mixed
     */
    public function getBlogPost(){
        return $this->blogPost;
    }

    /**
     * @param \BiberLtd\Bundle\BlogBundle\Entity\BlogPost $blogPost
     *
     * @return $this
     */
    public function setBlogPost(BlogPost $blogPost){
        if(!$this->setModified('blogPost', $blogPost)->isModified()){
            return $this;
        }
        $this->blogPost = $blogPost;

        return $this;
    }
=======
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


>>>>>>> c16988b65157239621309d5468e2493309930d0a
}