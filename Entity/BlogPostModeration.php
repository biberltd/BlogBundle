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
 *     name="blog_post_moderation",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxNBlogPostModerationDateReviewed", columns={"date_reviewed"}),
 *         @ORM\Index(name="idxNBlogPostModerationDateUpdated", columns={"date_updated"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUBlogPostModerationId", columns={"id"})}
 * )
 */
class BlogPostModeration extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $comment;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"a"})
     * @var string
     */
    private $status;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $date_reviewed;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    public $date_updated;

    /**
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostModerationReply", mappedBy="moderation")
     * @var array
     */
    private $replies;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="moderator", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $moderator;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost", inversedBy="moderations")
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
	 * @param array $replies
	 *
	 * @return $this
	 */
    public function setReplies(array $replies) {
	    $validCollection = [];
	    foreach($replies as $reply){
		    if($reply instanceof \BiberLtd\Bundle\BlogBundle\Entity\BlogPostModerationReply){
			    $validCollection[] = $reply;
		    }
	    }
	    unset($replies);
        if(!$this->setModified('replies', $validCollection)->isModified()) {
            return $this;
        }
		$this->replies = $validCollection;
		return $this;
    }

	/**
	 * @return array
	 */
    public function getReplies() {
        return $this->replies;
    }

	/**
	 * @param string $comment
	 *
	 * @return $this
	 */
    public function setComment(\string $comment) {
        if(!$this->setModified('comment', $comment)->isModified()) {
            return $this;
        }
		$this->comment = $comment;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getComment() {
        return $this->comment;
    }

	/**
	 * @param \DateTime $date_reviewed
	 *
	 * @return $this
	 */
    public function setDateReviewed(\DateTime $date_reviewed) {
        if(!$this->setModified('date_reviewed', $date_reviewed)->isModified()) {
            return $this;
        }
		$this->date_reviewed = $date_reviewed;
		return $this;
    }

	/**
	 * @return \DateTime
	 */
    public function getDateReviewed() {
        return $this->date_reviewed;
    }

	/**
	 * @param \BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member
	 *
	 * @return $this
	 */
    public function setModerator(\BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member) {
        if(!$this->setModified('moderator', $member)->isModified()) {
            return $this;
        }
		$this->moderator = $member;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
	 */
    public function getModerator() {
        return $this->moderator;
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
}