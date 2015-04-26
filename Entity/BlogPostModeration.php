<?php
/**
 * @name        BlogPostPostModeration
 * @package		BiberLtd\Core\BlogBundle
 *
 * @author		Can Berkol
 * @author		Murat Ünal
 *
 * @version     1.0.1
 * @date        26.04.2015
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Bundle\BlogBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreEntity;

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
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"a"})
     */
    private $status;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_reviewed;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $date_updated;

    /**
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostModerationReply",
     *     mappedBy="moderation"
     * )
     */
    private $replies;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="moderator", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    private $moderator;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost", inversedBy="moderations")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $post;

    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/
    /**
     * @name            getId()
     *  				Gets $id property.
     * .
     * @author          Murat Ünal
     * @since			1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->id
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @name            setPost()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post
     *
     * @return          object                $this
     */
    public function setPost($blog_post) {
        if(!$this->setModified('post', $blog_post)->isModified()) {
            return $this;
        }
		$this->post = $blog_post;
		return $this;
    }

    /**
     * @name            getPost()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          mixed           $this->post
     */
    public function getPost() {
        return $this->post;
    }

    /**
     * @name           setReplies()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @use             $this->setModified()
     *
     * @param           mixed $replies
     *
     * @return          object                $this
     */
    public function setReplies($replies) {
        if(!$this->setModified('replies', $replies)->isModified()) {
            return $this;
        }
		$this->replies = $replies;
		return $this;
    }

    /**
     * @name            getReplises()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          mixed           $this->replies
     */
    public function getReplies() {
        return $this->replies;
    }

    /**
     * @name            setComment()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $comment
     *
     * @return          object                $this
     */
    public function setComment($comment) {
        if(!$this->setModified('comment', $comment)->isModified()) {
            return $this;
        }
		$this->comment = $comment;
		return $this;
    }

    /**
     * @name            getComment ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->comment
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * @name            setDateReviewed()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_reviewed
     *
     * @return          object                $this
     */
    public function setDateReviewed($date_reviewed) {
        if(!$this->setModified('date_reviewed', $date_reviewed)->isModified()) {
            return $this;
        }
		$this->date_reviewed = $date_reviewed;
		return $this;
    }

    /**
     * @name            getDateReviewed()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_reviewed
     */
    public function getDateReviewed() {
        return $this->date_reviewed;
    }

    /**
     * @name            setModerator()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @use             $this->setModified()
     *
     * @param           mixed $member
     *
     * @return          object                $this
     */
    public function setModerator($member) {
        if(!$this->setModified('moderator', $member)->isModified()) {
            return $this;
        }
		$this->moderator = $member;
		return $this;
    }

    /**
     * @name            getModerator()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          mixed           $this->member
     */
    public function getModerator() {
        return $this->moderator;
    }

    /**
     * @name            setStatus()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $status
     *
     * @return          object                $this
     */
    public function setStatus($status) {
        if(!$this->setModified('status', $status)->isModified()) {
            return $this;
        }
		$this->status = $status;
		return $this;
    }

    /**
     * @name            getStatus ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->status
     */
    public function getStatus() {
        return $this->status;
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.1  					   26.04.2015
 * TW #3568845
 * Can Berkol
 * **************************************
 * Major changes!!
 *
 * **************************************
 * v1.0.0                      Murat Ünal
 * 13.09.2013
 * **************************************
 * A getBlogPost()
 * A getBlogPostModerationReplies()
 * A getComment()
 * A getDateReviewed()
 * A getDateUpdated()
 * A getId()
 * A getMember()
 * A getStatus()
 *
 * A setBlogPost()
 * A setBlogPostModerationReplies()
 * A setComment()
 * A setDateReviewed()
 * A setDateUpdated()
 * A setMember()
 * A setStatus()
 *
 */