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
 *     name="featured_blog_post",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxNFeaturedBlogPostDateAdded", columns={"date_added"}),
 *         @ORM\Index(name="idxNFeaturedBlogPostDatePublished", columns={"date_published"}),
 *         @ORM\Index(name="idxNFeaturedBlogPostDateUnpublished", columns={"date_unpublished"}),
 *         @ORM\Index(name="idxNFeaturedBlogPostDateUpdated", columns={"date_updated"}),
 *         @ORM\Index(name="idxNFeaturedBlogPostDateRemoved", columns={"date_removed"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUFeaturedBlogPostId", columns={"id"})}
 * )
 */
class FeaturedBlogPost extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    private $date_published;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    private $date_unpublished;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":1})
     * @var int
     */
    private $sort_order;

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
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
     */
    private $post;

	/**
	 * @return mixed
	 */
    public function getId(){
        return $this->id;
    }

	/**
	 * @param \BiberLtd\Bundle\BlogBundle\Entity\BlogPost $blog_post
	 *
	 * @return $this
	 */
    public function setPost(\BiberLtd\Bundle\BlogBundle\Entity\BlogPost $blog_post) {
        if(!$this->setModified('post', $blog_post)->isModified()) {
            return $this;
        }
		$this->post = $blog_post;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
	 */
    public function getPost() {
        return $this->post;
    }

	/**
	 * @param \DateTime $date_published
	 *
	 * @return $this
	 */
    public function setDatePublished(\DateTime $date_published) {
        if(!$this->setModified('date_published', $date_published)->isModified()) {
            return $this;
        }
		$this->date_published = $date_published;
		return $this;
    }

	/**
	 * @return \DateTime
	 */
    public function getDatePublished() {
        return $this->date_published;
    }

	/**
	 * @param \DateTime $date_unpublished
	 *
	 * @return $this
	 */
    public function setDateUnpublished(\DateTime $date_unpublished) {
        if(!$this->setModified('date_unpublished', $date_unpublished)->isModified()) {
            return $this;
        }
		$this->date_unpublished = $date_unpublished;
		return $this;
    }

	/**
	 * @return \DateTime
	 */
    public function getDateUnpublished() {
        return $this->date_unpublished;
    }

	/**
	 * @param int $sort_order
	 *
	 * @return $this
	 */
    public function setSortOrder(\integer $sort_order) {
        if(!$this->setModified('sort_order', $sort_order)->isModified()) {
            return $this;
        }
		$this->sort_order = $sort_order;
		return $this;
    }

	/**
	 * @return int
	 */
    public function getSortOrder() {
        return $this->sort_order;
    }
}