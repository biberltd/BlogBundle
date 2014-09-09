<?php
/**
 * @name        BlogPostPostModeration
 * @package		BiberLtd\Core\BlogBundle
 *
 * @author		Murat Ünal
 *
 * @version     1.0.0
 * @date        13.09.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Core\Bundles\BlogBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreEntity;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="blog_post_moderation",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_n_blog_post_moderation_date_reviewed", columns={"date_reviewed"}),
 *         @ORM\Index(name="idx_n_blog_post_moderation_date_updated", columns={"date_updated"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_blog_post_moderation_id", columns={"id"})}
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
     * @ORM\Column(type="string", length=1, nullable=false)
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
     *     targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogPostModerationReply",
     *     mappedBy="blog_post_moderation"
     * )
     */
    private $blog_post_moderation_replies;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="moderator", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    private $member;

    /** 
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogPost",
     *     inversedBy="blog_post_moderations"
     * )
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog_post;
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
     * @name                  setBlogPost ()
     *                                    Sets the blog_post property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post
     *
     * @return          object                $this
     */
    public function setBlogPost($blog_post) {
        if(!$this->setModified('blog_post', $blog_post)->isModified()) {
            return $this;
        }
		$this->blog_post = $blog_post;
		return $this;
    }

    /**
     * @name            getBlogPost ()
     *                              Returns the value of blog_post property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post
     */
    public function getBlogPost() {
        return $this->blog_post;
    }

    /**
     * @name                  setBlogPostModerationReplies ()
     *                                                     Sets the blog_post_moderation_replies property.
     *                                                     Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_moderation_replies
     *
     * @return          object                $this
     */
    public function setBlogPostModerationReplies($blog_post_moderation_replies) {
        if(!$this->setModified('blog_post_moderation_replies', $blog_post_moderation_replies)->isModified()) {
            return $this;
        }
		$this->blog_post_moderation_replies = $blog_post_moderation_replies;
		return $this;
    }

    /**
     * @name            getBlogPostModerationReplies ()
     *                                               Returns the value of blog_post_moderation_replies property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_moderation_replies
     */
    public function getBlogPostModerationReplies() {
        return $this->blog_post_moderation_replies;
    }

    /**
     * @name                  setComment ()
     *                                   Sets the comment property.
     *                                   Updates the data only if stored value and value to be set are different.
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
     *                             Returns the value of comment property.
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
     * @name                  setDateReviewed ()
     *                                        Sets the date_reviewed property.
     *                                        Updates the data only if stored value and value to be set are different.
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
     * @name            getDateReviewed ()
     *                                  Returns the value of date_reviewed property.
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
     * @name                  setMember ()
     *                                  Sets the member property.
     *                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $member
     *
     * @return          object                $this
     */
    public function setMember($member) {
        if(!$this->setModified('member', $member)->isModified()) {
            return $this;
        }
		$this->member = $member;
		return $this;
    }

    /**
     * @name            getMember ()
     *                            Returns the value of member property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->member
     */
    public function getMember() {
        return $this->member;
    }

    /**
     * @name                  setStatus ()
     *                                  Sets the status property.
     *                                  Updates the data only if stored value and value to be set are different.
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
     *                            Returns the value of status property.
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