<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        14.12.2015
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
     * @var \DateTime
     */
    public $date_added;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $member;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
     */
    private $post;

	/**
	 * @param \BiberLtd\Bundle\BlogBundle\Entity\BlogPost $post
	 *
	 * @return $this
	 */
    public function setPost(\BiberLtd\Bundle\BlogBundle\Entity\BlogPost $post) {
        if(!$this->setModified('post', $post)->isModified()) {
            return $this;
        }
		$this->post = $post;
		return $this;
    }

	/**
	 * @return mixed
	 */
    public function getBlogPosts() {
        return $this->blog_posts;
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