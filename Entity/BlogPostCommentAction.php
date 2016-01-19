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
 *     name="blog_post_comment_action",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idxNBlogPostCommentActionDateAdded", columns={"date_added"})},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idxUBlogPostCommentActionId", columns={"id"}),
 *         @ORM\UniqueConstraint(name="idxUBlogPostCommentAction", columns={"comment","member","post"})
 *     }
 * )
 */
class BlogPostCommentAction extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=15)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"l"})
     * @var string
     */
    private $action;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $member;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment")
     * @ORM\JoinColumn(name="comment", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment
     */
    private $blog_post_comment;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
     */
    private $blog_post;

    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function setAction(string $action) {
        if(!$this->setModified('action', $action)->isModified()) {
            return $this;
        }
		$this->action = $action;
		return $this;
    }

    /**
     * @return string
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * @param \BiberLtd\Bundle\BlogBundle\Entity\BlogPost $blog_post
     *
     * @return $this
     */
    public function setBlogPost(\BiberLtd\Bundle\BlogBundle\Entity\BlogPost $blog_post) {
        if(!$this->setModified('blog_post', $blog_post)->isModified()) {
            return $this;
        }
		$this->blog_post = $blog_post;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
     */
    public function getBlogPost() {
        return $this->blog_post;
    }

    /**
     * @param \BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment $blog_post_comment
     *
     * @return $this
     */
    public function setBlogPostComment(\BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment $blog_post_comment) {
        if(!$this->setModified('blog_post_comment', $blog_post_comment)->isModified()) {
            return $this;
        }
		$this->blog_post_comment = $blog_post_comment;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment
     */
    public function getBlogPostComment() {
        return $this->blog_post_comment;
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

}