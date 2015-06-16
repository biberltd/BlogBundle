<?php
/**
 * @name        BlogPostAction
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
use BiberLtd\Bundle\CoreBundle\CoreEntity;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="blog_post_action",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxUBlogPostActionID", columns={"id"}),
 *         @ORM\Index(name="idxNBlogPostActionDateAdded", columns={"date_added"})
 *     }
 * )
 */
class BlogPostAction extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=15)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"v"})
     */
    private $action;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost", inversedBy="actions")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $post;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $member;
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
     * @name            setAction ()
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
     * @name            setPost ()
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
     * @name            getPost ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          mixed           $this->blog_post
     */
    public function getPost() {
        return $this->post;
    }

    /**
     * @name            setMember ()
     *                  Sets the member property.
     *                  Updates the data only if stored value and value to be set are different.
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
 * v1.0.1                      26.04.2015
 * TW #3568845
 * Can Berkol
 * **************************************
 * A getPost()
 * A setPost()
 * D getBlogPost()
 * D setBlogPost()
 *
 * **************************************
 * v1.0.0                      Murat Ünal
 * 13.09.2013
 * **************************************
 * A getAction()
 * A get_blot_post()
 * A getDateAdded()
 * A getId()
 * A getMember()
 * A setAction()
 * A setBlogPost()
 * A setDateAdded()
 * A setMember()
 *
 */