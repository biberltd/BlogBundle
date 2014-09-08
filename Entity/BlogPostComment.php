<?php
/**
 * @name        BlogPostComment
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
 *     name="blog_post_comment",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_blog_post_comment_id", columns={"id"})}
 * )
 */
class BlogPostComment extends CoreEntity
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $date_removed;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_published;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $email;

    /** 
     * @ORM\Column(type="text", nullable=true)
     */
    private $url;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $count_likes;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $count_dislikes;

    /** 
     * @ORM\OneToOne(
     *     targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment",
     *     inversedBy="blog_post_comment_parent"
     * )
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=false, unique=true)
     */
    private $blog_post_comment;

    /** 
     * @ORM\OneToOne(
     *     targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment",
     *     mappedBy="blog_post_comment"
     * )
     */
    private $blog_post_comment_parent;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     */
    private $site;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="author", referencedColumnName="id", onDelete="SET NULL")
     */
    private $member;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost", inversedBy="blog_post_comments")
     */
    private $blog_post;
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
     * @name                  setBlogPost ()
     *                                    Sets the blog_post property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post
     *
     * @return          object                $this
     */
    public function setBlogPost($blog_post) {
        if(!$this->setModified('blog_post', $blog_post)->isModified()) {
            return $this;
        }
		$this->blog_post = $blog_post;
		return $this;
    }

    /**
     * @name            getBlogPost ()
     *                              Returns the value of blog_post property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post
     */
    public function getBlogPost() {
        return $this->blog_post;
    }

    /**
     * @name                  setBlogPostComment ()
     *                                           Sets the blog_post_comment property.
     *                                           Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_comment
     *
     * @return          object                $this
     */
    public function setBlogPostComment($blog_post_comment) {
        if(!$this->setModified('blog_post_comment', $blog_post_comment)->isModified()) {
            return $this;
        }
		$this->blog_post_comment = $blog_post_comment;
		return $this;
    }

    /**
     * @name            getBlogPostComment ()
     *                                     Returns the value of blog_post_comment property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_comment
     */
    public function getBlogPostComment() {
        return $this->blog_post_comment;
    }

    /**
     * @name                  setBlogPostCommentParent ()
     *                                                 Sets the blog_post_comment_parent property.
     *                                                 Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_comment_parent
     *
     * @return          object                $this
     */
    public function setBlogPostCommentParent($blog_post_comment_parent) {
        if(!$this->setModified('blog_post_comment_parent', $blog_post_comment_parent)->isModified()) {
            return $this;
        }
		$this->blog_post_comment_parent = $blog_post_comment_parent;
		return $this;
    }

    /**
     * @name            getBlogPostCommentParent ()
     *                                           Returns the value of blog_post_comment_parent property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post_comment_parent
     */
    public function getBlogPostCommentParent() {
        return $this->blog_post_comment_parent;
    }

    /**
     * @name                  setCountDislikes ()
     *                                         Sets the count_dislikes property.
     *                                         Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $count_dislikes
     *
     * @return          object                $this
     */
    public function setCountDislikes($count_dislikes) {
        if(!$this->setModified('count_dislikes', $count_dislikes)->isModified()) {
            return $this;
        }
		$this->count_dislikes = $count_dislikes;
		return $this;
    }

    /**
     * @name            getCountDislikes ()
     *                                   Returns the value of count_dislikes property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->count_dislikes
     */
    public function getCountDislikes() {
        return $this->count_dislikes;
    }

    /**
     * @name                  setCountLikes ()
     *                                      Sets the count_likes property.
     *                                      Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $count_likes
     *
     * @return          object                $this
     */
    public function setCountLikes($count_likes) {
        if(!$this->setModified('count_likes', $count_likes)->isModified()) {
            return $this;
        }
		$this->count_likes = $count_likes;
		return $this;
    }

    /**
     * @name            getCountLikes ()
     *                                Returns the value of count_likes property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->count_likes
     */
    public function getCountLikes() {
        return $this->count_likes;
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
     * @name                  setEmail ()
     *                                 Sets the email property.
     *                                 Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $email
     *
     * @return          object                $this
     */
    public function setEmail($email) {
        if(!$this->setModified('email', $email)->isModified()) {
            return $this;
        }
		$this->email = $email;
		return $this;
    }

    /**
     * @name            getEmail ()
     *                           Returns the value of email property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->email
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @name                  setMember ()
     *                                  Sets the member property.
     *                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $member
     *
     * @return          object                $this
     */
    public function setMember($member) {
        if(!$this->setModified('member', $member)->isModified()) {
            return $this;
        }
		$this->member = $member;
		return $this;
    }

    /**
     * @name            getMember ()
     *                            Returns the value of member property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->member
     */
    public function getMember() {
        return $this->member;
    }

    /**
     * @name                  setName ()
     *                                Sets the name property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $name
     *
     * @return          object                $this
     */
    public function setName($name) {
        if(!$this->setModified('name', $name)->isModified()) {
            return $this;
        }
		$this->name = $name;
		return $this;
    }

    /**
     * @name            getName ()
     *                          Returns the value of name property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->name
     */
    public function getName() {
        return $this->name;
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

    /**
     * @name                  setUrl ()
     *                               Sets the url property.
     *                               Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $url
     *
     * @return          object                $this
     */
    public function setUrl($url) {
        if(!$this->setModified('url', $url)->isModified()) {
            return $this;
        }
		$this->url = $url;
		return $this;
    }

    /**
     * @name            getUrl ()
     *                         Returns the value of url property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->url
     */
    public function getUrl() {
        return $this->url;
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Murat Ünal
 * 10.10.2013
 * **************************************
 * A getBlog()
 * A getBlogPostComment()
 * A getBlogPostComment_parent()
 * A getCountDislikes()
 * A get_count_kikes()
 * A getDateAdded()
 * A getDatePublished()
 * A getDateRemoved()
 * A getEmail()
 * A getId()
 * A getMember()
 * A getName()
 * A getSite()
 * A getUrl()
 *
 * A setBlog()
 * A setBlogPostComment()
 * A setBlogPostComment_parent()
 * A setCountDislikes()
 * A set_count_kikes()
 * A setDateAdded()
 * A setDatePublished()
 * A setDateRemoved()
 * A setEmail()
 * A setMember()
 * A setName()
 * A setSite()
 * A setUrl()
 *
 */