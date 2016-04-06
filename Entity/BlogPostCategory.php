<?php
/**
 * @name        BlogPostCategory
 * @package		BiberLtd\Core\BlogBundle
 *
 * @author		Can Berkol
 * @author		Murat Ünal
 *
 * @version     1.0.1
 * @date        26.04.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
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
     */
    private $id;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_updated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
	public $date_removed;

    /**
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogModerator", mappedBy="category")
     */
    private $moderators;

    /** 
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory", mappedBy="parents")
     */
    private $children;

    /** 
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategoryLocalization",
     *     mappedBy="post_category",cascade={"persist"}
     * )
     * 
     */
    protected $localizations;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     */
    private $site;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\Blog")
     * @ORM\JoinColumn(name="blog", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $parents;

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
     * @name            setBlog ()
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
     * @name            setModerators()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_moderators
     *
     * @return          object                $this
     */
    public function setModerators($blog_moderators) {
        if(!$this->setModified('moderators', $blog_moderators)->isModified()) {
            return $this;
        }
		$this->moderators = $blog_moderators;
		return $this;
    }

    /**
     * @name            getModerators()
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          mixed           $this->blog_moderators
     */
    public function getModerators() {
        return $this->moderators;
    }

    /**
     * @name            setChildren ()
     *                  Sets the children property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $children
     *
     * @return          object                $this
     */
    public function setChildren($children) {
        if(!$this->setModified('children', $children)->isModified()) {
            return $this;
        }
		$this->children = $children;
		return $this;
    }

    /**
     * @name            getChildren ()
     *                  Returns the value of children property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->children
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * @name            setParents ()
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $parents
     *
     * @return          object                $this
     */
    public function setParents($parents) {
        if(!$this->setModified('parents', $parents)->isModified()) {
            return $this;
        }
		$this->parents = $parents;
		return $this;
    }

    /**
     * @name            getParents ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->parents
     */
    public function getParents() {
        return $this->parents;
    }

    /**
	 * @name            setSite ()
	 *                          *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $site
     *
     * @return          object                $this
     */
    public function setSite($site) {
        if(!$this->setModified('site', $site)->isModified()) {
            return $this;
        }
		$this->site = $site;
		return $this;
    }

    /**
     * @name            getSite ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->site
     */
    public function getSite() {
        return $this->site;
    }

}
/**
 * Change Log:
 * **************************************
 * v1.0.1  					   26.04.2015
 * TW #3568845
 * Can Berkol
 * **************************************
 * A getModerators()
 * A setModerators()
 *
 * **************************************
 * v1.0.0                      Murat Ünal
 * 10.10.2013
 * **************************************
 * A getBlog()
 * A getBlogModerators()
 * A getChildren()
 * A getDateAdded()
 * A getId()
 * A getLocalizations()
 * A getParents()
 * A getSite()
 *
 * A setBlog()
 * A setBlogModerators()
 * A setChildren()
 * A setDateAdded()
 * A setLocalizations()
 * A setParents()
 * A setSite()
 *
 */
