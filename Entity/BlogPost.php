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
use BiberLtd\Bundle\CoreBundle\CoreLocalizableEntity;
use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="blog_post",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxNBlogPostDateAdded", columns={"date_added"}),
 *         @ORM\Index(name="idxNBlogPostDatePublished", columns={"date_published"}),
 *         @ORM\Index(name="idxNBlogPostDateApproved", columns={"date_approved"}),
 *         @ORM\Index(name="idxNBlogPostDateUnpublished", columns={"date_unpublished"}),
 *         @ORM\Index(name="idxNBlogPostDateUpdated", columns={"date_updated"}),
 *         @ORM\Index(name="idxNBlogPostDateRemoved", columns={"date_removed"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUBlogPostId", columns={"id"})}
 * )
 */
class BlogPost extends CoreLocalizableEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"a"})
     * @var string
     */
    private $type;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"o"})
     * @var string
     */
    private $status;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $date_approved;

    /** 
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $date_published;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $date_unpublished;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     * @var int
     */
    private $count_view;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     * @var int
     */
    private $count_like;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     * @var int
     */
    private $count_dislike;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     * @var int
     */
    private $count_comment;

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
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostModeration", mappedBy="post")
     * @var array
     */
	public $moderations;

    /**
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostLocalization", mappedBy="blog_post", cascade={"persist"})
     * @var array
     */
    protected $localizations;

    /**
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment", mappedBy="post")
     * @var  array
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostAction", mappedBy="post")
     * @var array
     */
    private $actions;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\FileManagementBundle\Entity\File")
     * @ORM\JoinColumn(name="preview_image", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\FileManagementBundle\Entity\File
     */
    private $preview_image;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\Blog", inversedBy="posts")
     * @ORM\JoinColumn(name="blog", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\BlogBundle\Entity\Blog
     */
    private $blog;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\SiteManagementBundle\Entity\Site
     */
    private $site;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="author", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $author;

    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }

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
     * @return \BiberLtd\Bundle\BlogBundle\Entity\Blog
     */
    public function getBlog() {
        return $this->blog;
    }

    /**
     * @param array $actions
     *
     * @return $this
     */
    public function setActions(array $actions) {
	    $validActions = [];
	    foreach($actions as $action){
		    if($action instanceof \BiberLtd\Bundle\LogBundle\Entity\Action){
			    $validActions[] = $action;
		    }
	    }
	    unset($actions);
        if(!$this->setModified('actions', $validActions)->isModified()) {
            return $this;
        }
		$this->actions = $validActions;
		return $this;
    }

	/**
	 * @return array
	 */
    public function getActions() {
        return $this->actions;
    }

	/**
	 * @param array $comments
	 *
	 * @return $this
	 */
    public function setComments(array $comments) {
	    $validComments = [];
	    foreach($comments as $comment){
		    if($comment instanceof \BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment){
			    $validComments[] = $comment;
		    }
	    }
        if(!$this->setModified('comments', $validComments)->isModified()) {
            return $this;
        }
		$this->comments = $validComments;
		return $this;
    }

	/**
	 * @return array
	 */
    public function getComments() {
        return $this->comments;
    }

	/**
	 * @param array $blog_post_moderations
	 *
	 * @return $this
	 */
    public function setModerations(array $blog_post_moderations) {
	    $validCollection = [];
	    foreach($blog_post_moderations as $moderation){
		    if($moderation instanceof \BiberLtd\Bundle\BlogBundle\Entity\BlogPostModeration){
			    $validCollection[] = $moderation;
		    }
	    }
        if(!$this->setModified('moderations', $validCollection)->isModified()) {
            return $this;
        }
		$this->moderations = $validCollection;
		return $this;
    }

	/**
	 * @return array
	 */
    public function getModerations() {
        return $this->moderations;
    }

	/**
	 * @param int $count_comment
	 *
	 * @return $this
	 */
    public function setCountComment(\integer $count_comment) {
        if(!$this->setModified('count_comment', $count_comment)->isModified()) {
            return $this;
        }
		$this->count_comment = $count_comment;
		return $this;
    }

	/**
	 * @return int
	 */
    public function getCountComment() {
        return $this->count_comment;
    }

	/**
	 * @param int $count_dislike
	 *
	 * @return $this
	 */
    public function setCountDislike(\integer $count_dislike) {
        if(!$this->setModified('count_dislike', $count_dislike)->isModified()) {
            return $this;
        }
		$this->count_dislike = $count_dislike;
		return $this;
    }

	/**
	 * @return int
	 */
    public function getCountDislike() {
        return $this->count_dislike;
    }

	/**
	 * @param int $count_like
	 *
	 * @return $this
	 */
    public function setCountLike(\integer $count_like) {
        if(!$this->setModified('count_like', $count_like)->isModified()) {
            return $this;
        }
		$this->count_like = $count_like;
		return $this;
    }

	/**
	 * @return int
	 */
    public function getCountLike() {
        return $this->count_like;
    }

	/**
	 * @param int $count_view
	 *
	 * @return $this
	 */
    public function setCountView(\integer $count_view) {
        if(!$this->setModified('count_view', $count_view)->isModified()) {
            return $this;
        }
		$this->count_view = $count_view;
		return $this;
    }

	/**
	 * @return int
	 */
    public function getCountView() {
        return $this->count_view;
    }

	/**
	 * @param \DateTime $date_approved
	 *
	 * @return $this
	 */
    public function setDateApproved(\DateTime $date_approved) {
        if(!$this->setModified('date_approved', $date_approved)->isModified()) {
            return $this;
        }
		$this->date_approved = $date_approved;
		return $this;
    }

	/**
	 * @return \DateTime
	 */
    public function getDateApproved() {
        return $this->date_approved;
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
	 * @param \BiberLtd\Bundle\FileManagementBundle\Entity\File $file
	 *
	 * @return $this
	 */
    public function setFile(\BiberLtd\Bundle\FileManagementBundle\Entity\File $file) {
        if(!$this->setModified('file', $file)->isModified()) {
            return $this;
        }
		$this->file = $file;
		return $this;
    }

	/**
	 * @return mixed
	 */
    public function getFile() {
        return $this->file;
    }

	/**
	 * @param \BiberLtd\Bundle\MemberManagementBundle\Entity\Member $author
	 *
	 * @return $this
	 */
    public function setAuthor(\BiberLtd\Bundle\MemberManagementBundle\Entity\Member $author) {
        if(!$this->setModified('author', $author)->isModified()) {
            return $this;
        }
		$this->author = $author;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
	 */
    public function getAuthor() {
        return $this->author;
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
	 * @param string $status
	 *
	 * @return $this
	 */
    public function setStatus(\string $status) {
        if(!$this->setModified('status', $status)->isModified()) {
            return $this;
        }
		$this->status = $status;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getStatus() {
        return $this->status;
    }

	/**
	 * @param string $type
	 *
	 * @return $this
	 */
    public function setType(\string $type) {
        if(!$this->setModified('type', $type)->isModified()) {
            return $this;
        }
		$this->type = $type;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getType() {
        return $this->type;
    }

	/**
	 * @return \BiberLtd\Bundle\FileManagementBundle\Entity\File
	 */
	public function getPreviewImage() {
		return $this->preview_image;
	}

	/**
	 * @param \BiberLtd\Bundle\FileManagementBundle\Entity\File $preview_image
	 *
	 * @return $this
	 */
	public function setPreviewImage(\BiberLtd\Bundle\FileManagementBundle\Entity\File $preview_image) {
		if (!$this->setModified('preview_image', $preview_image)->isModified()) {
			return $this;
		}
		$this->preview_image = $preview_image;

		return $this;
	}

}