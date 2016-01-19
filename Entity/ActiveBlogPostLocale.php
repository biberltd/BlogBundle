<?php
/**
 * @author		Can Berkol
 * @author      Caner Buga
 * @author		Suleyman Aylak
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        19.01.2016
 */
namespace BiberLtd\Bundle\BlogBundle\Entity;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
use BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language;
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
     * @var BiberLtd\Bundle\BlogBundle\Entity\BlogPost
     */
    private $blog_post;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false)
     * @var BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
     */
    private $language;

    /**
     * @return mixed
     */
    public function getBlogPost()
    {
        return $this->blog_post;
    }

    /**
     * @param BlogPost $blog_post
     * @return $this
     */
    public function setBlogPost(BlogPost $blog_post)
    {
        if($this->setModified('blog_post', $blog_post)->isModified()) {
            $this->blog_post = $blog_post;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param Language $language
     * @return $this
     */
    public function setLanguage(Language $language)
    {
        if($this->setModified('language', $language)->isModified()) {
            $this->language = $language;
        }

        return $this;
    }
}