<?php
/**
 * @author		Can Berkol
 * @author		Murat Ünal
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        10.12.2015
 */
namespace BiberLtd\Bundle\BlogBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="blog_moderator",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idxNBlogModeratorDateAdded", columns={"date_added"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUBlogModerator", columns={"moderator","blog","category"})}
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
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $member;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\Blog", inversedBy="moderators")
     * @ORM\JoinColumn(name="blog", referencedColumnName="id", nullable=false)
     * @var \BiberLtd\Bundle\BlogBundle\Entity\Blog
     */
    private $blog;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory", inversedBy="moderators")
     * @ORM\JoinColumn(name="category", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory
     */
    private $category;

    /**
     * @param \BiberLtd\Bundle\BlogBundle\Entity\Blog $blog
     *
     * @return $this
     */
    public function setBlog(\BiberLtd\Bundle\BlogBundle\Entity\Blog $blog) {
        if(!$this->setModified('blog', $blog)->isModified()) {
            return $this;
        }
		$this->blog = $blog;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\BlogBundle\Entity\Blog
     */
    public function getBlog() {
        return $this->blog;
    }

    /**
     * @param \BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory $category
     *
     * @return $this
     */
    public function setCategory(\BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory $category) {
        if(!$this->setModified('category', $category)->isModified()) {
            return $this;
        }
		$this->category = $category;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory
     */
    public function getCategory() {
        return $this->category;
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