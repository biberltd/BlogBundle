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
 *     name="blog_post_tag",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxNBlogPostTagAdded", columns={"date_added"}),
 *         @ORM\Index(name="idxNBlogPostTagUpdated", columns={"date_updated"}),
 *         @ORM\Index(name="idxNBlogPostTagRemoved", columns={"date_removed"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUBlogPostTagId", columns={"id"})}
 * )
 */
class BlogPostTag extends  CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     * @var int
     */
    private $count_posts;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_updated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
	public $date_removed;

    /** 
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostTagLocalization", mappedBy="tag")
     * @var array
     */
    protected $localizations;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\SiteManagementBundle\Entity\Site
     */
    private $site;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\Blog")
     * @ORM\JoinColumn(name="blog", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\BlogBundle\Entity\Blog
     */
    private $blog;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false, onDelete="CASCADE")
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
     * @param int $count_posts
     *
     * @return $this
     */
    public function setCountPosts(\int $count_posts) {
        if(!$this->setModified('count_posts', $count_posts)->isModified()) {
            return $this;
        }
		$this->count_posts = $count_posts;
		return $this;
    }

    /**
     * @return int
     */
    public function getCountPosts() {
        return $this->count_posts;
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
     * @param \BiberLtd\Bundle\SiteManagementBundle\Site $site
     *
     * @return $this
     */
    public function setSite(\BiberLtd\Bundle\SiteManagementBundle\Site $site) {
        if(!$this->setModified('site', $site)->isModified()) {
            return $this;
        }
		$this->site = $site;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\SiteManagementBundle\Entity\Site
     */
    public function getSite() {
        return $this->site;
    }
}