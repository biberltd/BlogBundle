<?php
/**
 * @author		Can Berkol
 * @author		Murat Ünal
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
 *     name="blog_post_comment",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUBlogPostCommentId", columns={"id"})}
 * )
 */
class BlogPostComment extends CoreEntity
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
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    public $date_removed;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    private $date_published;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $name;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $email;

    /** 
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $url;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     * @var int
     */
    private $count_likes;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     * @var int
     */
    private $count_dislikes;

    /**
     * @ORM\OneToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment", mappedBy="comment")
     * @var \BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment
     */
    private $parent;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\SiteManagementBundle\Entity\Site
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="author", referencedColumnName="id", onDelete="SET NULL")
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $author;

    /**
     * 
     * @var
     * @ORM\OneToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost", inversedBy="comments") \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
     */
    private $post;

    /**
     * @ORM\OneToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment", inversedBy="parent")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=false, unique=true)
     */
    private $comment;

	/**
	 * @return mixed
	 */
    public function getId(){
        return $this->id;
    }

	/**
	 * @param \BiberLtd\Bundle\BlogBundle\Entity\BlogPost $post
	 *
	 * @return $this
	 */
    public function setPost(\BiberLtd\Bundle\BlogBundle\Entity\BlogPost $post) {
        if(!$this->setModified('post', $post)->isModified()) {
            return $this;
        }
		$this->post = $post;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
	 */
    public function getPost() {
        return $this->post;
    }

	/**
	 * @param \BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment $parent
	 *
	 * @return $this
	 */
    public function setParent(\BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment $parent) {
        if(!$this->setModified('parent', $parent)->isModified()) {
            return $this;
        }
		$this->parent = $parent;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment
	 */
    public function getParent() {
        return $this->parent;
    }

	/**
	 * @param int $count_dislikes
	 *
	 * @return $this
	 */
    public function setCountDislikes(int $count_dislikes) {
        if(!$this->setModified('count_dislikes', $count_dislikes)->isModified()) {
            return $this;
        }
		$this->count_dislikes = $count_dislikes;
		return $this;
    }

	/**
	 * @return int
	 */
    public function getCountDislikes() {
        return $this->count_dislikes;
    }

	/**
	 * @param int $count_likes
	 *
	 * @return $this
	 */
    public function setCountLikes(int $count_likes) {
        if(!$this->setModified('count_likes', $count_likes)->isModified()) {
            return $this;
        }
		$this->count_likes = $count_likes;
		return $this;
    }

	/**
	 * @return int
	 */
    public function getCountLikes() {
        return $this->count_likes;
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
	 * @param string $email
	 *
	 * @return $this
	 */
    public function setEmail(string $email) {
        if(!$this->setModified('email', $email)->isModified()) {
            return $this;
        }
		$this->email = $email;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getEmail() {
        return $this->email;
    }

	/**
	 * @param \BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member
	 *
	 * @return $this
	 */
    public function setAuthor(\BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member) {
        if(!$this->setModified('author', $member)->isModified()) {
            return $this;
        }
		$this->author = $member;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
	 */
    public function getAuthor() {
        return $this->author;
    }

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
    public function setName(string $name) {
        if(!$this->setModified('name', $name)->isModified()) {
            return $this;
        }
		$this->name = $name;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getName() {
        return $this->name;
    }

	/**
	 * @param \BiberLtd\Bundle\SiteManagementBundle\Entity\Site $site
	 *
	 * @return $this
	 */
    public function setSite(\BiberLtd\Bundle\SiteManagementBundle\Entity\Site $site) {
        if(!$this->setModified('site', $site)->isModified()) {
            return $this;
        }
		$this->site = $site;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\SiteManagementBundle\Entity\Site
	 */
    public function getSite() {
        return $this->site;
    }

	/**
	 * @param string $url
	 *
	 * @return $this
	 */
    public function setUrl(string $url) {
        if(!$this->setModified('url', $url)->isModified()) {
            return $this;
        }
		$this->url = $url;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getUrl() {
        return $this->url;
    }
}