<?php
/**
 * @name        BlogPostModerationReply
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
 *     name="blog_post_moderation_reply",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idx_n_blog_post_moderation_reply_date_replied", columns={"date_replied"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_blog_post_moderation_reply_id", columns={"id"})}
 * )
 */
class BlogPostModerationReply extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=15)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_replied;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $comment;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $sent_from;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $is_read;

    /** 
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogPostModeration",
     *     inversedBy="blog_post_moderation_replies"
     * )
     * @ORM\JoinColumn(name="moderation", referencedColumnName="id", nullable=false)
     */
    private $blog_post_moderation;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="author", referencedColumnName="id", nullable=false)
     */
    private $member;
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
     * @name                  setBlogPostModeration ()
     *                                              Sets the blog_post_moderation property.
     *                                              Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_moderation
     *
     * @return          object                $this
     */
    public function setBlogPostModeration($blog_post_moderation) {
        if(!$this->setModified('blog_post_moderation', $blog_post_moderation)->isModified()) {
            return $this;
        }
		$this->blog_post_moderation = $blog_post_moderation;
		return $this;
    }

    /**
     * @name            getBlogPostModeration ()
     *                                        Returns the value of blog_post_moderation property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_moderation
     */
    public function getBlogPostModeration() {
        return $this->blog_post_moderation;
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
     * @name                  setDateReplied ()
     *                                       Sets the date_replied property.
     *                                       Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_replied
     *
     * @return          object                $this
     */
    public function setDateReplied($date_replied) {
        if(!$this->setModified('date_replied', $date_replied)->isModified()) {
            return $this;
        }
		$this->date_replied = $date_replied;
		return $this;
    }

    /**
     * @name            getDateReplied ()
     *                                 Returns the value of date_replied property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_replied
     */
    public function getDateReplied() {
        return $this->date_replied;
    }

    /**
     * @name                  set İsRead()
     *                            Sets the is_read property.
     *                            Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $is_read
     *
     * @return          object                $this
     */
    public function setIsRead($is_read) {
        if(!$this->setModified('is_read', $is_read)->isModified()) {
            return $this;
        }
		$this->is_read = $is_read;
		return $this;
    }

    /**
     * @name            get İsRead()
     *                      Returns the value of is_read property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->is_read
     */
    public function getIsRead() {
        return $this->is_read;
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
     * @name                  setSentFrom ()
     *                                    Sets the sent_from property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $sent_from
     *
     * @return          object                $this
     */
    public function setSentFrom($sent_from) {
        if(!$this->setModified('sent_from', $sent_from)->isModified()) {
            return $this;
        }
		$this->sent_from = $sent_from;
		return $this;
    }

    /**
     * @name            getSentFrom ()
     *                              Returns the value of sent_from property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->sent_from
     */
    public function getSentFrom() {
        return $this->sent_from;
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Murat Ünal
 * 13.09.2013
 * **************************************
 * A getBlogPostModeration()
 * A getComment()
 * A getDateReplied()
 * A getId()
 * A getIsRead()
 * A getMember()
 * A getSentFrom()
 *
 * A setBlogPostModeration()
 * A setComment()
 * A setDateReplied()
 * A setIsRead()
 * A setMember()
 * A setSentFrom()
 *
 */