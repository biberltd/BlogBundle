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
use BiberLtd\Bundle\CoreBundle\CoreLocalizableEntity;
use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="blog_post_category",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxNBlogPostCategoryDateAdded", columns={"date_added"}),
 *         @ORM\Index(name="idxNBlogPostCategoryDateUpdated", columns={"date_updated"}),
 *         @ORM\Index(name="idxNBlogPostCategoryDateRemoved", columns={"date_removed"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUBlogPostCategoryId", columns={"id"})}
 * )
 */
class BlogPostCategory extends CoreLocalizableEntity
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
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogModerator", mappedBy="category")
     * @var array
     */
    private $moderators;

    /** 
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory", mappedBy="parents")
     * @var array
     */
    private $children;

    /** 
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategoryLocalization",
     *     mappedBy="post_category"
     * )
<<<<<<< HEAD
     * @var array
=======
     * 
>>>>>>> c16988b65157239621309d5468e2493309930d0a
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
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var array
     */
    private $parents;

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
	 * @param array $blog_moderators
	 *
	 * @return $this
	 */
    public function setModerators(array $blog_moderators) {
	    $validCollection = [];
	    foreach($blog_moderators as $moderator){
		    if($moderator instanceof \BiberLtd\Bundle\BlogBundle\Entity\BlogModeratorr){
			     $validCollection[] = $moderator;
		    }
	    }
        if(!$this->setModified('moderators', $validCollection)->isModified()) {
            return $this;
        }
		$this->moderators = $validCollection;
		return $this;
    }

	/**
	 * @return array
	 */
    public function getModerators() {
        return $this->moderators;
    }

	/**
	 * @param array $children
	 *
	 * @return $this
	 */
    public function setChildren(array $children) {
	    $validCollection = [];
	    foreach($children as $item){
		    if($item instanceof \BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory){
			    $validCollection[] = $item;
		    }
	    }
        if(!$this->setModified('children', $validCollection)->isModified()) {
            return $this;
        }
		$this->children = $validCollection;
		return $this;
    }

	/**
	 * @return array
	 */
    public function getChildren() {
        return $this->children;
    }

	/**
	 * @param array $parents
	 *
	 * @return $this
	 */
    public function setParents(array $parents) {
	    $validCollection = [];
	    foreach($parents as $item){
		    if($item instanceof \BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory){
			    $validCollection[] = $item;
		    }
	    }
        if(!$this->setModified('parents', $validCollection)->isModified()) {
            return $this;
        }
		$this->parents = $validCollection;
		return $this;
    }

	/**
	 * @return array
	 */
    public function getParents() {
        return $this->parents;
    }

	/**
	 * @param \BiberLtd\Bundle\SiteManagementBundle\Entity\Site $site
	 *
	 * @return $this
	 */
    public function setSite(\BiberLtd\Bundle\SiteManagementBundle\Entity\Site $site) {
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