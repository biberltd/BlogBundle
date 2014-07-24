<?php
/**
 * @name        BlogPost
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

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="blog_post",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_n_blog_post_date_added", columns={"date_added"}),
 *         @ORM\Index(name="idx_n_blog_post_date_published", columns={"date_published"}),
 *         @ORM\Index(name="idx_n_blog_post_date_approved", columns={"date_approved"}),
 *         @ORM\Index(name="idx_n_blog_post_date_unpublished", columns={"date_unpublished"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_blog_post_id", columns={"id"})}
 * )
 */
class BlogPost extends CoreLocalizableEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $type;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $status;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_approved;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_published;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_unpublished;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $count_view;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $count_like;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $count_dislike;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $count_comment;

    /** 
     * @ORM\OneToMany(targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogPostModeration", mappedBy="blog_post")
     */
    private $blog_post_moderations;

    /** 
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogPostLocalization",
     *     mappedBy="blog_post"
     * )
     */
    protected $localizations;

    /** 
     * @ORM\OneToMany(targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogPostComment", mappedBy="blog_post")
     */
    private $blog_post_comments;

    /** 
     * @ORM\OneToMany(targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\BlogPostAction", mappedBy="blog_post")
     */
    private $blog_post_actions;

    /** 
     * 
     */
    private $blog_post_field_contents;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\FileManagementBundle\Entity\File")
     * @ORM\JoinColumn(name="preview_image", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $file;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\BlogBundle\Entity\Blog", inversedBy="blog_posts")
     * @ORM\JoinColumn(name="blog", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $blog;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     */
    private $site;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="author", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $author;
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
     * @name                  setBlogPostActions ()
     *                                           Sets the blog_post_actions property.
     *                                           Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_actions
     *
     * @return          object                $this
     */
    public function setBlogPostActions($blog_post_actions) {
        if(!$this->setModified('blog_post_actions', $blog_post_actions)->isModified()) {
            return $this;
        }
		$this->blog_post_actions = $blog_post_actions;
		return $this;
    }

    /**
     * @name            getBlogPostActions ()
     *                                     Returns the value of blog_post_actions property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_actions
     */
    public function getBlogPostActions() {
        return $this->blog_post_actions;
    }

    /**
     * @name                  setBlogPostComments ()
     *                                            Sets the blog_post_comments property.
     *                                            Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_comments
     *
     * @return          object                $this
     */
    public function setBlogPostComments($blog_post_comments) {
        if(!$this->setModified('blog_post_comments', $blog_post_comments)->isModified()) {
            return $this;
        }
		$this->blog_post_comments = $blog_post_comments;
		return $this;
    }

    /**
     * @name            getBlogPostComments ()
     *                                      Returns the value of blog_post_comments property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_comments
     */
    public function getBlogPostComments() {
        return $this->blog_post_comments;
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
     * @name                  setBlogPostModerations ()
     *                                               Sets the blog_post_moderations property.
     *                                               Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_moderations
     *
     * @return          object                $this
     */
    public function setBlogPostModerations($blog_post_moderations) {
        if(!$this->setModified('blog_post_moderations', $blog_post_moderations)->isModified()) {
            return $this;
        }
		$this->blog_post_moderations = $blog_post_moderations;
		return $this;
    }

    /**
     * @name            getBlogPostModerations ()
     *                                         Returns the value of blog_post_moderations property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_moderations
     */
    public function getBlogPostModerations() {
        return $this->blog_post_moderations;
    }

    /**
     * @name                  setCountComment ()
     *                                        Sets the count_comment property.
     *                                        Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $count_comment
     *
     * @return          object                $this
     */
    public function setCountComment($count_comment) {
        if(!$this->setModified('count_comment', $count_comment)->isModified()) {
            return $this;
        }
		$this->count_comment = $count_comment;
		return $this;
    }

    /**
     * @name            getCountComment ()
     *                                  Returns the value of count_comment property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->count_comment
     */
    public function getCountComment() {
        return $this->count_comment;
    }

    /**
     * @name                  setCountDislike ()
     *                                        Sets the count_dislike property.
     *                                        Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $count_dislike
     *
     * @return          object                $this
     */
    public function setCountDislike($count_dislike) {
        if(!$this->setModified('count_dislike', $count_dislike)->isModified()) {
            return $this;
        }
		$this->count_dislike = $count_dislike;
		return $this;
    }

    /**
     * @name            getCountDislike ()
     *                                  Returns the value of count_dislike property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->count_dislike
     */
    public function getCountDislike() {
        return $this->count_dislike;
    }

    /**
     * @name                  setCountLike ()
     *                                     Sets the count_like property.
     *                                     Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $count_like
     *
     * @return          object                $this
     */
    public function setCountLike($count_like) {
        if(!$this->setModified('count_like', $count_like)->isModified()) {
            return $this;
        }
		$this->count_like = $count_like;
		return $this;
    }

    /**
     * @name            getCountLike ()
     *                               Returns the value of count_like property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->count_like
     */
    public function getCountLike() {
        return $this->count_like;
    }

    /**
     * @name                  setCountView ()
     *                                     Sets the count_view property.
     *                                     Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $count_view
     *
     * @return          object                $this
     */
    public function setCountView($count_view) {
        if(!$this->setModified('count_view', $count_view)->isModified()) {
            return $this;
        }
		$this->count_view = $count_view;
		return $this;
    }

    /**
     * @name            getCountView ()
     *                               Returns the value of count_view property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->count_view
     */
    public function getCountView() {
        return $this->count_view;
    }

    /**
     * @name                  setDateApproved ()
     *                                        Sets the date_approved property.
     *                                        Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_approved
     *
     * @return          object                $this
     */
    public function setDateApproved($date_approved) {
        if(!$this->setModified('date_approved', $date_approved)->isModified()) {
            return $this;
        }
		$this->date_approved = $date_approved;
		return $this;
    }

    /**
     * @name            getDateApproved ()
     *                                  Returns the value of date_approved property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_approved
     */
    public function getDateApproved() {
        return $this->date_approved;
    }

    /**
     * @name                  setDatePublished ()
     *                                         Sets the date_published property.
     *                                         Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_published
     *
     * @return          object                $this
     */
    public function setDatePublished($date_published) {
        if(!$this->setModified('date_published', $date_published)->isModified()) {
            return $this;
        }
		$this->date_published = $date_published;
		return $this;
    }

    /**
     * @name            getDatePublished ()
     *                                   Returns the value of date_published property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_published
     */
    public function getDatePublished() {
        return $this->date_published;
    }

    /**
     * @name                  setDateUnpublished ()
     *                                           Sets the date_unpublished property.
     *                                           Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_unpublished
     *
     * @return          object                $this
     */
    public function setDateUnpublished($date_unpublished) {
        if(!$this->setModified('date_unpublished', $date_unpublished)->isModified()) {
            return $this;
        }
		$this->date_unpublished = $date_unpublished;
		return $this;
    }

    /**
     * @name            getDateUnpublished ()
     *                                     Returns the value of date_unpublished property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_unpublished
     */
    public function getDateUnpublished() {
        return $this->date_unpublished;
    }

    /**
     * @name                  setFile ()
     *                                Sets the file property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $file
     *
     * @return          object                $this
     */
    public function setFile($file) {
        if(!$this->setModified('file', $file)->isModified()) {
            return $this;
        }
		$this->file = $file;
		return $this;
    }

    /**
     * @name            getFile ()
     *                          Returns the value of file property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->file
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * @name            setAuthor()
     *                  Sets the author property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed                 $author
     *
     * @return          object                $this
     */
    public function setAuthor($author) {
        if(!$this->setModified('author', $author)->isModified()) {
            return $this;
        }
		$this->author = $author;
		return $this;
    }

    /**
     * @name            getAuthor()
     *                  Returns the value of member property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->member
     */
    public function getAuthor() {
        return $this->author;
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

    /**
     * @name                  setStatus ()
     *                                  Sets the status property.
     *                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $status
     *
     * @return          object                $this
     */
    public function setStatus($status) {
        if(!$this->setModified('status', $status)->isModified()) {
            return $this;
        }
		$this->status = $status;
		return $this;
    }

    /**
     * @name            getStatus ()
     *                            Returns the value of status property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->status
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @name                  setType ()
     *                                Sets the type property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $type
     *
     * @return          object                $this
     */
    public function setType($type) {
        if(!$this->setModified('type', $type)->isModified()) {
            return $this;
        }
		$this->type = $type;
		return $this;
    }

    /**
     * @name            getType ()
     *                          Returns the value of type property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->type
     */
    public function getType() {
        return $this->type;
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Murat Ünal
 * 13.09.2013
 * **************************************
 * A getBlog()
 * A getBlogPostActions()
 * A getBlogPostComments()
 * A getBlogPostFieldContents()
 * A getBlogPostModerations()
 * A get_posts()
 * A getCountComment()
 * A getCountDislike()
 * A getCountLike()
 * A getCountView()
 * A getDateAdded()
 * A getDateApproved()
 * A getDatePublished()
 * A getDateUnpublished()
 * A getFile()
 * A getLocalizations()
 * A getId()
 * A getMember()
 * A getSite()
 * A getStatus()
 * A getType()
 *
 * A setBlog()
 * A setBlogPostActions()
 * A setBlogPostComments()
 * A setBlogPostFieldContents()
 * A setBlogPostModerations()
 * A set_posts()
 * A setCountComment()
 * A setCountDislike()
 * A setCountLike()
 * A setCountView()
 * A setDateAdded()
 * A setDateApproved()
 * A setDatePublished()
 * A setDateUnpublished()
 * A setFile()
 * A setLocalizations()
 * A setMember()
 * A setSite()
 * A setStatus()
 * A setType()

 *
 */