<?php
/**
 * @author		Can Berkol
 * @author		Murat Ünal
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        14.12.2015
 */
namespace BiberLtd\Bundle\BlogBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;

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
     * @var string
     */
    private $content;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
     */
    private $language;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\FeaturedBlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\BlogBundle\Entity\FeaturedBlogPost
     */
    private $featured_blog_post;

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent(string $content) {
        if(!$this->setModified('content', $content)->isModified()) {
            return $this;
        }
		$this->content = $content;
		return $this;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param \BiberLtd\Bundle\BlogBundle\Entity\FeaturedBlogPost $featured_blog_post
     *
     * @return $this
     */
    public function setFeaturedBlogPost(\BiberLtd\Bundle\BlogBundle\Entity\FeaturedBlogPost $featured_blog_post) {
        if(!$this->setModified('featured_blog_post', $featured_blog_post)->isModified()) {
            return $this;
        }
		$this->featured_blog_post = $featured_blog_post;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\BlogBundle\Entity\FeaturedBlogPost
     */
    public function getFeaturedBlogPost() {
        return $this->featured_blog_post;
    }

    /**
     * @param \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language
     *
     * @return $this
     */
    public function setLanguage(\BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language) {
        if(!$this->setModified('language', $language)->isModified()) {
            return $this;
        }
		$this->language = $language;
		return $this;
    }

    /**
     * turn          mixed           $this->language
     */
    public function getLanguage() {
        return $this->language;
    }
}