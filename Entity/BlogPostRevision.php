<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        13.12.2015
 */
namespace BiberLtd\Bundle\BlogBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;

use BiberLtd\Bundle\CoreBundle\CoreEntity;
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="blog_post_revision",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUBlogPostRevision", columns={"language","post"})}
 * )
 */
class BlogPostRevision extends CoreEntity{
    /**
     * @ORM\Column(type="string", length=155, nullable=false)
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $url_key;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $summary;

    /**
     * @ORM\Column(type="string", length=155, nullable=true)
     * @var string
     */
    private $meta_title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $meta_description;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $meta_keywords;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
	public $date_updated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
	public $date_removed;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false)
     * @var string
     */
    private $revision_number;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, options={"default":"w"})
     * @var string
     */
    private $status;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
     */
    private $language;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
     */
    private $post;

	/**
	 * @return mixed
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param string $content
	 *
	 * @return $this
	 */
	public function setContent(string $content) {
		if (!$this->setModified('content', $content)->isModified()) {
			return $this;
		}
		$this->content = $content;

		return $this;
	}

	/**
	 * @return \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
	 */
	public function getLanguage() {
		return $this->language;
	}

	/**
	 * @param \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language
	 *
	 * @return $this
	 */
	public function setLanguage(\BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language) {
		if (!$this->setModified('language', $language)->isModified()) {
			return $this;
		}
		$this->language = $language;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getMetaDescription() {
		return $this->meta_description;
	}

	/**
	 * @param string $meta_description
	 *
	 * @return $this
	 */
	public function setMetaDescription(string $meta_description) {
		if (!$this->setModified('meta_description', $meta_description)->isModified()) {
			return $this;
		}
		$this->meta_description = $meta_description;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getMetaKeywords() {
		return $this->meta_keywords;
	}

	/**
	 * @param string $meta_keywords
	 *
	 * @return $this
	 */
	public function setMetaKeywords(string $meta_keywords) {
		if (!$this->setModified('meta_keywords', $meta_keywords)->isModified()) {
			return $this;
		}
		$this->meta_keywords = $meta_keywords;

		return $this;
	}

	/**
	 * @return \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
	 */
	public function getPost() {
		return $this->post;
	}

	/**
	 * @param \BiberLtd\Bundle\BlogBundle\Entity\BlogPost $post
	 *
	 * @return $this
	 */
	public function setPost(\BiberLtd\Bundle\BlogBundle\Entity\BlogPost $post) {
		if (!$this->setModified('post', $post)->isModified()) {
			return $this;
		}
		$this->post = $post;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRevisionNumber() {
		return $this->revision_number;
	}

	/**
	 * @param string $revision_number
	 *
	 * @return $this
	 */
	public function setRevisionNumber(string $revision_number) {
		if (!$this->setModified('revision_number', $revision_number)->isModified()) {
			return $this;
		}
		$this->revision_number = $revision_number;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSummary() {
		return $this->summary;
	}

	/**
	 * @param $summary
	 *
	 * @return $this
	 */
	public function setSummary($summary) {
		if (!$this->setModified('summary', $summary)->isModified()) {
			return $this;
		}
		$this->summary = $summary;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 *
	 * @return $this
	 */
	public function setTitle(string $title) {
		if (!$this->setModified('title', $title)->isModified()) {
			return $this;
		}
		$this->title = $title;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUrlKey() {
		return $this->url_key;
	}

	/**
	 * @param string $url_key
	 *
	 * @return $this
	 */
	public function setUrlKey(string $url_key) {
		if (!$this->setModified('url_key', $url_key)->isModified()) {
			return $this;
		}
		$this->url_key = $url_key;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getMetaTitle() {
		return $this->meta_title;
	}

	/**
	 * @param string $meta_title
	 *
	 * @return $this
	 */
	public function setMetaTitle(string $meta_title) {
		if (!$this->setModified('meta_title', $meta_title)->isModified()) {
			return $this;
		}
		$this->meta_title = $meta_title;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param string $status
	 *
	 * @return $this
	 */
	public function setStatus(string $status) {
		if (!$this->setModified('status', $status)->isModified()) {
			return $this;
		}
		$this->status = $status;

		return $this;
	}

}