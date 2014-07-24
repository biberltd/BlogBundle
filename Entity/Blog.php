<?php
/**
 * @name        Blog
 * @package		BiberLtd\Core\BlogBundle
 *
 * @author		Murat Ünal
 *
 * @version     1.0.1
 * @date        09.10.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Core\Bundles\BlogBundle\Entity;
use BiberLtd\Core\CoreLocalizableEntity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreEntity;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="blog",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_n_blog_date_created", columns={"date_created"}),
 *         @ORM\Index(name="idx_n_blog_date_updated", columns={"date_updated"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_blog_id", columns={"id"})}
 * )
 */
class Blog extends CoreLocalizableEntity
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
    private $date_created;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_updated;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $count_posts;

    /** 
     * @ORM\OneToMany(targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogPostTag", mappedBy="blog")
     */
    private $blog_post_tags;

    /** 
     * @ORM\OneToMany(targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogLocalization", mappedBy="blog")
     */
    protected $localizations;

    /** 
     * @ORM\OneToMany(targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogModerator", mappedBy="blog")
     */
    private $blog_moderators;

    /** 
     * @ORM\OneToMany(targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogPostCategory", mappedBy="blog")
     */
    private $blog_post_categories;

    /** 
     * @ORM\OneToMany(targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogPost", mappedBy="blog")
     */
    private $blog_posts;

    /** 
     * 
     */
    private $blog_post_fields;

    /** 
     * 
     */
    private $blog_post_field_contents;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     */
    private $site;
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
     * @name                  setBlogPostCategories ()
     *                                              Sets the blog_post_categories property.
     *                                              Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_categories
     *
     * @return          object                $this
     */
    public function setBlogPostCategories($blog_post_categories) {
        if(!$this->setModified('blog_post_categories', $blog_post_categories)->isModified()) {
            return $this;
        }
		$this->blog_post_categories = $blog_post_categories;
		return $this;
    }

    /**
     * @name            getBlogPostCategories ()
     *                                        Returns the value of blog_post_categories property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_categories
     */
    public function getBlogPostCategories() {
        return $this->blog_post_categories;
    }

    /**
     * @name                  setBlogPostFieldContents ()
     *                                                 Sets the blog_post_field_contents property.
     *                                                 Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_field_contents
     *
     * @return          object                $this
     */
    public function setBlogPostFieldContents($blog_post_field_contents) {
        if(!$this->setModified('blog_post_field_contents', $blog_post_field_contents)->isModified()) {
            return $this;
        }
		$this->blog_post_field_contents = $blog_post_field_contents;
		return $this;
    }

    /**
     * @name            getBlogPostFieldContents ()
     *                                           Returns the value of blog_post_field_contents property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_field_contents
     */
    public function getBlogPostFieldContents() {
        return $this->blog_post_field_contents;
    }

    /**
     * @name                  setBlogPostFields ()
     *                                          Sets the blog_post_fields property.
     *                                          Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_fields
     *
     * @return          object                $this
     */
    public function setBlogPostFields($blog_post_fields) {
        if(!$this->setModified('blog_post_fields', $blog_post_fields)->isModified()) {
            return $this;
        }
		$this->blog_post_fields = $blog_post_fields;
		return $this;
    }

    /**
     * @name            getBlogPostFields ()
     *                                    Returns the value of blog_post_fields property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_fields
     */
    public function getBlogPostFields() {
        return $this->blog_post_fields;
    }

    /**
     * @name                  setBlogPostTags ()
     *                                        Sets the blog_post_tags property.
     *                                        Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_tags
     *
     * @return          object                $this
     */
    public function setBlogPostTags($blog_post_tags) {
        if(!$this->setModified('blog_post_tags', $blog_post_tags)->isModified()) {
            return $this;
        }
		$this->blog_post_tags = $blog_post_tags;
		return $this;
    }

    /**
     * @name            getBlogPostTags ()
     *                                  Returns the value of blog_post_tags property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_tags
     */
    public function getBlogPostTags() {
        return $this->blog_post_tags;
    }

    /**
     * @name                  setBlogPosts ()
     *                                     Sets the blog_posts property.
     *                                     Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_posts
     *
     * @return          object                $this
     */
    public function setBlogPosts($blog_posts) {
        if(!$this->setModified('blog_posts', $blog_posts)->isModified()) {
            return $this;
        }
		$this->blog_posts = $blog_posts;
		return $this;
    }

    /**
     * @name            getBlogPosts ()
     *                               Returns the value of blog_posts property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_posts
     */
    public function getBlogPosts() {
        return $this->blog_posts;
    }

    /**
     * @name                  setCountPosts ()
     *                                      Sets the count_posts property.
     *                                      Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $count_posts
     *
     * @return          object                $this
     */
    public function setCountPosts($count_posts) {
        if(!$this->setModified('count_posts', $count_posts)->isModified()) {
            return $this;
        }
		$this->count_posts = $count_posts;
		return $this;
    }

    /**
     * @name            getCountPosts ()
     *                                Returns the value of count_posts property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->count_posts
     */
    public function getCountPosts() {
        return $this->count_posts;
    }

    /**
     * @name                  setDateCreated ()
     *                                       Sets the date_created property.
     *                                       Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_created
     *
     * @return          object                $this
     */
    public function setDateCreated($date_created) {
        if(!$this->setModified('date_created', $date_created)->isModified()) {
            return $this;
        }
		$this->date_created = $date_created;
		return $this;
    }

    /**
     * @name            getDateCreated ()
     *                                 Returns the value of date_created property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_created
     */
    public function getDateCreated() {
        return $this->date_created;
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
 * v1.0.0                      Murat Ünal
 * 13.09.2013
 * **************************************
 * A getBlogModerators()
 * A getBlogPostCategories()
 * A getBlogPostFieldContents()
 * A getBlogPostTags()
 * A getBlogPosts()
 * A getCountPosts()
 * A getDateCreated()
 * A getDateUpdated()
 * A getId()
 * A getSite()
 * A setBlogModerators()
 * A setBlogPosts_categories()
 * A setBlogPosts_field_contents()
 * A setBlogPosts_tags()
 * A setBlogPosts()
 * A setCountPosts()
 * A setDateCreated()
 * A setDateUpdated()
 * A setSite()
 *
 */