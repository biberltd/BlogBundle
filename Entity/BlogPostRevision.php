<?php
/**
 * @name        BlogPostRevision
 * @package		BiberLtd\Core\BlogBundle
 *
 * @author		Can Berkol
 *
 * @version     1.0.0
 * @date        26.04.2015
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 */
namespace BiberLtd\Bundle\BlogBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;

use BiberLtd\Core\CoreEntity;
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
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $url_key;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $summary;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $meta_description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $meta_keywords;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
	public $date_updated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
	public $date_removed;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $revision_number;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $language;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $post;

	/**
	 * @name        getContent ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @return      mixed
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @name        setContent ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @param       mixed $content
	 *
	 * @return      $this
	 */
	public function setContent($content) {
		if (!$this->setModified('content', $content)->isModified()) {
			return $this;
		}
		$this->content = $content;

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
	public function getLanguage() {
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
	public function setLanguage($language) {
		if (!$this->setModified('language', $language)->isModified()) {
			return $this;
		}
		$this->language = $language;

		return $this;
	}

	/**
	 * @name        getMetaDescription ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @return      mixed
	 */
	public function getMetaDescription() {
		return $this->meta_description;
	}

	/**
	 * @name        setMetaDescription ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @param       mixed $meta_description
	 *
	 * @return      $this
	 */
	public function setMetaDescription($meta_description) {
		if (!$this->setModified('meta_description', $meta_description)->isModified()) {
			return $this;
		}
		$this->meta_description = $meta_description;

		return $this;
	}

	/**
	 * @name        getMetaKeywords ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @return      mixed
	 */
	public function getMetaKeywords() {
		return $this->meta_keywords;
	}

	/**
	 * @name        setMetaKeywords ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @param       mixed $meta_keywords
	 *
	 * @return      $this
	 */
	public function setMetaKeywords($meta_keywords) {
		if (!$this->setModified('meta_keywords', $meta_keywords)->isModified()) {
			return $this;
		}
		$this->meta_keywords = $meta_keywords;

		return $this;
	}

	/**
	 * @name        getPost ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @return      mixed
	 */
	public function getPost() {
		return $this->post;
	}

	/**
	 * @name              setPost ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @param       mixed $post
	 *
	 * @return      $this
	 */
	public function setPost($post) {
		if (!$this->setModified('post', $post)->isModified()) {
			return $this;
		}
		$this->post = $post;

		return $this;
	}

	/**
	 * @name        getRevisionNumber ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @return      mixed
	 */
	public function getRevisionNumber() {
		return $this->revision_number;
	}

	/**
	 * @name              setRevisionNumber ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @param       mixed $revision_number
	 *
	 * @return      $this
	 */
	public function setRevisionNumber($revision_number) {
		if (!$this->setModified('revision_number', $revision_number)->isModified()) {
			return $this;
		}
		$this->revision_number = $revision_number;

		return $this;
	}

	/**
	 * @name        getSummary ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @return      mixed
	 */
	public function getSummary() {
		return $this->summary;
	}

	/**
	 * @name              setSummary ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @param       mixed $summary
	 *
	 * @return      $this
	 */
	public function setSummary($summary) {
		if (!$this->setModified('summary', $summary)->isModified()) {
			return $this;
		}
		$this->summary = $summary;

		return $this;
	}

	/**
	 * @name        getTitle ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @return      mixed
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @name        setTitle ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @param       mixed $title
	 *
	 * @return      $this
	 */
	public function setTitle($title) {
		if (!$this->setModified('title', $title)->isModified()) {
			return $this;
		}
		$this->title = $title;

		return $this;
	}

	/**
	 * @name        getUrlKey ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @return      mixed
	 */
	public function getUrlKey() {
		return $this->url_key;
	}

	/**
	 * @name        setUrlKey ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @param       mixed $url_key
	 *
	 * @return      $this
	 */
	public function setUrlKey($url_key) {
		if (!$this->setModified('url_key', $url_key)->isModified()) {
			return $this;
		}
		$this->url_key = $url_key;

		return $this;
	}


}
/**
 * Change Log:
 * **************************************
 * v1.0.0  					   26.04.2015
 * TW #3568845
 * Can Berkol
 * **************************************
 * File created for the first time.
 *
 */