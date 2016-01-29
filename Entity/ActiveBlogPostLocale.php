<?php
namespace BiberLtd\Bundle\BlogBundle\Entity;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
use BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="active_blog_post_locale",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"}
 * )
 */
class ActiveBlogPostLocale extends CoreEntity
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="blogPost", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var BlogPost
     */
    private $blogPost;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var Language
     */
    private $language;

    /**
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

        return $this;
    }

    /**
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
}