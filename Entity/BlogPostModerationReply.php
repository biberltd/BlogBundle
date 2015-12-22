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
     * @var int
     */
    private $id;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    private $date_replied;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $comment;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"a"})
     * @var string
     */
    private $sent_from;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"n"})
     * @var string
     */
    private $is_read;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostModeration", inversedBy="replies")
     * @ORM\JoinColumn(name="moderation", referencedColumnName="id", nullable=false)
     * @var \BiberLtd\Bundle\BlogBundle\Entity\BlogPostModeration
     */
    private $moderation;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="author", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $member;

	/**
	 * @return mixed
	 */
    public function getId(){
        return $this->id;
    }

	/**
	 * @param \BiberLtd\Bundle\BlogBundle\Entity\BlogPostModeration $moderation
	 *
	 * @return $this
	 */
    public function setModeration(\BiberLtd\Bundle\BlogBundle\Entity\BlogPostModeration $moderation) {
        if(!$this->setModified('moderation', $moderation)->isModified()) {
            return $this;
        }
		$this->moderation = $moderation;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\BlogBundle\Entity\BlogPostModeration
	 */
    public function getModeration() {
        return $this->moderation;
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
	 * @param \DateTime $date_replied
	 *
	 * @return $this
	 */
    public function setDateReplied(\DateTime $date_replied) {
        if(!$this->setModified('date_replied', $date_replied)->isModified()) {
            return $this;
        }
		$this->date_replied = $date_replied;
		return $this;
    }

	/**
	 * @return \DateTime
	 */
    public function getDateReplied() {
        return $this->date_replied;
    }

	/**
	 * @param $is_read
	 *
	 * @return $this
	 */
    public function setIsRead($is_read) {
        if(!$this->setModified('is_read', $is_read)->isModified()) {
            return $this;
        }
		$this->is_read = $is_read;
		return $this;
    }

	/**3
	 * @return string
	 */
    public function getIsRead() {
        return $this->is_read;
    }

	/**
	 * @param \BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member
	 *
	 * @return $this
	 */
    public function setMember(\BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member) {
        if(!$this->setModified('member', $member)->isModified()) {
            return $this;
        }
		$this->member = $member;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
	 */
    public function getMember() {
        return $this->member;
    }

	/**
	 * @param string $sent_from
	 *
	 * @return $this
	 */
    public function setSentFrom(\string $sent_from) {
        if(!$this->setModified('sent_from', $sent_from)->isModified()) {
            return $this;
        }
		$this->sent_from = $sent_from;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getSentFrom() {
        return $this->sent_from;
    }
}