<?php
/**
 * @name        BlogPostCategory
 * @package		BiberLtd\Core\BlogBundle
 *
 * @author		Murat Ünal
 *
 * @version     1.0.1
 * @date        10.10.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Bundle\BlogBundle\Entity;
use BiberLtd\Core\CoreLocalizableEntity;
use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="blog_post_category",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idx_n_blog_post_category_date_added", columns={"date_added"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_blog_post_category_id", columns={"id"})}
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
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogModerator",
     *     mappedBy="blog_post_category"
     * )
     */
    private $blog_moderators;

    /** 
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory", mappedBy="parents")
     */
    private $children;

    /** 
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategoryLocalization",
     *     mappedBy="post_category"
     * )
     */
    protected $localizations;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     */
    private $site;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\Blog", inversedBy="blog_post_categories")
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
     * @name                  setBlogModerators ()
     *                                          Sets the blog_moderators property.
     *                                          Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_moderators
     *
     * @return          object                $this
     */
    public function setBlogModerators($blog_moderators) {
        if(!$this->setModified('blog_moderators', $blog_moderators)->isModified()) {
            return $this;
        }
		$this->blog_moderators = $blog_moderators;
		return $this;
    }

    /**
     * @name            getBlogModerators ()
     *                                    Returns the value of blog_moderators property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_moderators
     */
    public function getBlogModerators() {
        return $this->blog_moderators;
    }

    /**
     * @name                  setChildren ()
     *                                    Sets the children property.
     *                                    Updates the data only if stored value and value to be set are different.
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
     *                              Returns the value of children property.
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
     * @name                  setParents ()
     *                                   Sets the parents property.
     *                                   Updates the data only if stored value and value to be set are different.
     *
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
     *                             Returns the value of parents property.
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
     * @name                  setSite ()
     *                                Sets the site property.
     *                                Updates the data only if stored value and value to be set are different.
     *
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
     *                          Returns the value of site property.
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
 * v1.0.1                      Murat Ünal
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