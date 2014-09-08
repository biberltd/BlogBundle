<?php
/**
 * @name        BlogPostField
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
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreEntity;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="blog_post_field", 
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"}, 
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_blog_post_field_id", columns={"id"})}
 * )
 */
class BlogPostField extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /** 
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostFieldLocalization",
     *     mappedBy="blog_post_field"
     * )
     */
    protected $localizations;

    /** 
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostFieldContent",
     *     mappedBy="blog_post_field"
     * )
     */
    private $blog_post_field_contents;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\Blog", inversedBy="blog_post_fields")
     * @ORM\JoinColumn(name="blog", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog;

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
     * @name                  setNotes ()
     *                                 Sets the notes property.
     *                                 Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $notes
     *
     * @return          object                $this
     */
    public function setNotes($notes) {
        if(!$this->setModified('notes', $notes)->isModified()) {
            return $this;
        }
		$this->notes = $notes;
		return $this;
    }

    /**
     * @name            getNotes ()
     *                           Returns the value of notes property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->notes
     */
    public function getNotes() {
        return $this->notes;
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
 * A getBlogPostFieldContents()
 * A getId()
 * A getNotes()
 * A getSite()
 *
 * A setBlog()
 * A setBlog_field_contents()
 * A setNotes()
 * A setSite()
 *
 */
