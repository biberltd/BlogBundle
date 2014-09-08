<?php
/**
 * @name        BlogPostCommentAct,on
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
namespace BiberLtd\Bundle\BlogBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreEntity;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="blog_post_comment_action",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idx_n_blog_post_comment_action_date_added", columns={"date_added"})},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idx_u_blog_post_comment_action_id", columns={"id"}),
 *         @ORM\UniqueConstraint(name="idx_u_blog_post_comment_member", columns={"comment","member","post"})
 *     }
 * )
 */
class BlogPostCommentAction extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=15)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $action;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $member;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment")
     * @ORM\JoinColumn(name="comment", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog_post_comment;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog_post;
    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

    /**
     * @name            getId()
     *                  Gets $id property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          integer          $this->id
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @name                  setAction ()
     *                                  Sets the action property.
     *                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $action
     *
     * @return          object                $this
     */
    public function setAction($action) {
        if(!$this->setModified('action', $action)->isModified()) {
            return $this;
        }
		$this->action = $action;
		return $this;
    }

    /**
     * @name            getAction ()
     *                            Returns the value of action property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->action
     */
    public function getAction() {
        return $this->action;
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
     * @name                  setBlogPostComment ()
     *                                           Sets the blog_post_comment property.
     *                                           Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_comment
     *
     * @return          object                $this
     */
    public function setBlogPostComment($blog_post_comment) {
        if(!$this->setModified('blog_post_comment', $blog_post_comment)->isModified()) {
            return $this;
        }
		$this->blog_post_comment = $blog_post_comment;
		return $this;
    }

    /**
     * @name            getBlogPostComment ()
     *                                     Returns the value of blog_post_comment property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_comment
     */
    public function getBlogPostComment() {
        return $this->blog_post_comment;
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

}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Murat Ünal
 * 13.09.2013
 * **************************************
 * A getAction()
 * A getBlogPost()
 * A getBlogPostComment()
 * A getDateAdded()
 * A getId()
 * A getMember()
 *
 * A setAction()
 * A setBlogPost()
 * A setBlogPostComment()
 * A setDateAdded()
 * A setMember()
 *
 */