<?php
/**
 * @name        BlogPostModerationReply
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
 *     name="blog_post_moderation_reply",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idxNBlogPostModerationDateReplied", columns={"date_replied"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUBlogPostModerationReplyId", columns={"id"})}
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
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"a"})
     */
    private $sent_from;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"n"})
     */
    private $is_read;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostModeration", inversedBy="replies")
     * @ORM\JoinColumn(name="moderation", referencedColumnName="id", nullable=false)
     */
    private $moderation;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="author", referencedColumnName="id", nullable=false, onDelete="CASCADE")
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
     * @name            setModeration()
     *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @use             $this->setModified()
     *
     * @param           mixed $moderation
     *
     * @return          object                $this
     */
    public function setModeration($moderation) {
        if(!$this->setModified('moderation', $moderation)->isModified()) {
            return $this;
        }
		$this->moderation = $moderation;
		return $this;
    }

    /**
     * @name            getModeration()
     *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          mixed           $this->moderation
     */
    public function getModeration() {
        return $this->moderation;
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
     * @name            getComment()
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
     * @name            setDateReplied()
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
     * @name            getDateReplied()
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
     * @name            setIsRead()
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
     * @name            getIsRead()
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
     * @name            setMember()
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
     * @name            getMember()
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
     * @name            setSentFrom()
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
     * @name            getSentFrom()
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