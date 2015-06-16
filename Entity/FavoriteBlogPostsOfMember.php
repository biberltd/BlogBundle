<?php
/**
 * @name        FavoriteBlogPostsOfMember
 * @package		BiberLtd\Core\BlogBundle
 *
 * @author		CaN Berkol
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
 *     name="favorite_blog_posts_of_member",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idxNFavoriteBlogPostOfMemberDateAdded", columns={"date_added"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUFavoriteBlogPostOfMember", columns={"member","post"})}
 * )
 */
class FavoriteBlogPostsOfMember extends CoreEntity
{
    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $member;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $post;

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
     * @param           mixed 		$post
     *
     * @return          object                $this
     */
    public function setPost($post) {
        if(!$this->setModified('post', $post)->isModified()) {
            return $this;
        }
		$this->post = $post;
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
     * @return          mixed           $this->blog_posts
     */
    public function getBlogPosts() {
        return $this->blog_posts;
    }

    /**
     * @name            setMember ()
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
 * v1.0.1  					   26.04.2015
 * TW #3568845
 * Can Berkol
 * **************************************
 * Major changes!!
 *
 * **************************************
 * v1.0.1                     Murat Ünal
 * 10.10.2013
 * **************************************
 * A getBlogPosts()
 * A getDateAdded()
 * A getMember()
 *
 * A setBlogPosts()
 * A setDateAdded()
 * A setMember()
 *
 */