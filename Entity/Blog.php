<?php
/**
 * @name        Blog
 * @package		BiberLtd\Core\BlogBundle
 *
 * @author		Can Berkol
 * @author		Murat Ünal
 *
 * @version     1.0.2
 * @date        25.04.2015
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
use BiberLtd\Bundle\CoreBundle\CoreEntity;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="blog",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxNBlogDateAdded", columns={"date_added"}),
 *         @ORM\Index(name="idxNBlogDateUpdated", columns={"date_updated"}),
 *         @ORM\Index(name="idxNBlogDateRemoved", columns={})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUBlogId", columns={"id"})}
 * )
 */
class Blog extends CoreLocalizableEntity{
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
	 * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
	 */
	private $count_posts;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	public $date_removed;

	/**
	 * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogLocalization", mappedBy="blog")
	 */
	protected $localizations;

	/**
	 * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogModerator", mappedBy="blog")
	 */
	private $moderators;

	/**
	 * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost", mappedBy="blog")
	 */
	private $posts;

	/**
	 * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
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
	 * @name           setModerators()
	 *                 Sets the blog_moderators property.
	 *                 Updates the data only if stored value and value to be set are different.
	 *
	 * @author         Can Berkol
	 *
	 * @since          1.0.2
	 * @version        1.0.2
	 *
	 * @use            $this->setModified()
	 *
	 * @param          mixed $blog_moderators
	 *
	 * @return         object                $this
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
	 *                  Returns the value of blog_moderators property.
	 *
	 * @author          Can Berkol
	 *
	 * @since           1.0.2
	 * @version         1.0.2
	 *
	 * @return          mixed           $this->blog_moderators
	 */
	public function getModerators() {
		return $this->moderators;
	}

	/**
	 * @name            setBlogPosts ()
	 *                  Sets the blog_posts property.
	 *                  Updates the data only if stored value and value to be set are different.
	 *
	 * @author          Can Berkol
	 *
	 * @since           1.0.2
	 * @version         1.0.2
	 *
	 * @use             $this->setModified()
	 *
	 * @param           mixed 				$posts
	 *
	 * @return          object              $this
	 */
	public function setBlogPosts($posts) {
		if(!$this->setModified('posts', $posts)->isModified()) {
			return $this;
		}
		$this->posts = $posts;
		return $this;
	}

	/**
	 * @name            getPosts()
	 *                  Returns the value of blog_posts property.
	 *
	 * @author          Can Berkol
	 *
	 * @since           1.0.2
	 * @version         1.0.2
	 *
	 * @return          mixed           $this->blog_posts
	 */
	public function getPosts() {
		return $this->posts;
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
	 * @name            setSite ()
	 *                  Sets the site property.
	 *                  Updates the data only if stored value and value to be set are different.
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
	 *                  Returns the value of site property.
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
 * v1.0.2                      25.04.2015
 * TW #3568845
 * Can Berkol
 * **************************************
 * Major changes !!
 *
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