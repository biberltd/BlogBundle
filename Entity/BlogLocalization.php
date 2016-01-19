<?php
/**
 * @author		Can Berkol
 * @author		Murat Ünal
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        10.12.2015
 */
namespace BiberLtd\Bundle\BlogBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="blog_localization",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idxUBlogUrlKey", columns={"language","url_key"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUBlogLocalization", columns={"blog","language"})}
 * )
 */
class BlogLocalization extends CoreEntity
{
    /** 
     * @ORM\Column(type="string", length=155, nullable=false)
     */
    private $title;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $url_key;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $meta_description;

    /** 
     * @ORM\Column(type="string", length=155, nullable=true)
     */
    private $meta_keywords;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $language;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\Blog", inversedBy="localizations")
     * @ORM\JoinColumn(name="blog", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog;

    /**
     * @param \BiberLtd\Bundle\BlogBundle\Entity\Blog $blog
     *
     * @return $this
     */
    public function setBlog(\BiberLtd\Bundle\BlogBundle\Entity\Blog $blog) {
        if(!$this->setModified('blog', $blog)->isModified()) {
            return $this;
        }
		$this->blog = $blog;
		return $this;
    }

    /**
     * @return mixed
     */
    public function getBlog() {
        return $this->blog;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description) {
        if(!$this->setModified('description', $description)->isModified()) {
            return $this;
        }
		$this->description = $description;
		return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->description;
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
     * @return mixed
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * @param string $metaDescription
     *
     * @return $this
     */
    public function setMetaDescription(string $metaDescription) {
        if(!$this->setModified('meta_description', $metaDescription)->isModified()) {
            return $this;
        }
		$this->meta_description = $metaDescription;
		return $this;
    }

    /**
     * @return mixed
     */
    public function getMetaDescription() {
        return $this->meta_description;
    }

    /**
     * @param \ßtring $metaKeywords
     *
     * @return $this
     */
    public function setMetaKeywords(\ßtring $metaKeywords) {
        if(!$this->setModified('meta_keywords', $metaKeywords)->isModified()) {
            return $this;
        }
		$this->meta_keywords = $metaKeywords;
		return $this;
    }

    /**
     * @return mixed
     */
    public function getMetaKeywords() {
        return $this->meta_keywords;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title) {
        if(!$this->setModified('title', $title)->isModified()) {
            return $this;
        }
		$this->title = $title;
		return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $urlKey
     *
     * @return $this
     */
    public function setUrlKey(string $urlKey) {
        if(!$this->setModified('url_key', $urlKey)->isModified()) {
            return $this;
        }
		$this->url_key = $urlKey;
		return $this;
    }

    /**
     * @return mixed
     */
    public function getUrlKey() {
        return $this->url_key;
    }
}