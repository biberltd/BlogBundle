<?php
/**
 * @name        BlogModerator
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
 *     name="blog_moderator",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idx_n_blog_moderator_date_added", columns={"date_added"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_blog_moderator", columns={"moderator","blog","category"})}
 * )
 */
class BlogModerator extends CoreEntity
{
    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="moderator", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $member;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\Blog", inversedBy="blog_moderators")
     * @ORM\JoinColumn(name="blog", referencedColumnName="id", nullable=false)
     */
    private $blog;

    /** 
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory",
     *     inversedBy="blog_moderators"
     * )
     * @ORM\JoinColumn(name="category", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog_post_category;

    /**
     * @name                  setBlog ()
     *                                Sets the blog property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog
     *
     * @return          object                $this
     */
    public function setBlog($blog) {
        if(!$this->setModified('blog', $blog)->isModified()) {
            return $this;
        }
		$this->blog = $blog;
		return $this;
    }

    /**
     * @name            getBlog ()
     *                          Returns the value of blog property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog
     */
    public function getBlog() {
        return $this->blog;
    }

    /**
     * @name                  setBlogPostCategory ()
     *                                            Sets the blog_post_category property.
     *                                            Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_category
     *
     * @return          object                $this
     */
    public function setBlogPostCategory($blog_post_category) {
        if(!$this->setModified('blog_post_category', $blog_post_category)->isModified()) {
            return $this;
        }
		$this->blog_post_category = $blog_post_category;
		return $this;
    }

    /**
     * @name            getBlogPostCategory ()
     *                                      Returns the value of blog_post_category property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_category
     */
    public function getBlogPostCategory() {
        return $this->blog_post_category;
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
    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Murat Ünal
 * 13.09.2013
 * **************************************
 * A getBlog()
 * A getBlogPostCategory()
 * A getDateAdded()
 * A getMember()
 * A setBlog()
 * A setBlogPostCategory()
 * A setDateAdded()
 * A setMember()
 *
 */