<?php
/**
 * @name        BlogPostComment
 * @package		BiberLtd\Core\BlogBundle
 *
 * @author		Can Berkol
 * @author		Murat Ünal
 *
 * @version     1.0.1
 * @date        26.04.2015
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
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUBlogPostCommentId", columns={"id"})}
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
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     */
    private $count_likes;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     */
    private $count_dislikes;

    /**
     * @ORM\OneToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment", inversedBy="parent")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=false, unique=true)
     */
    private $comment;

    /**
     * @ORM\OneToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostComment", mappedBy="comment")
     */
    private $parent;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="author", referencedColumnName="id", onDelete="SET NULL")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost", inversedBy="comments")
     */
    private $post;

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
     * @name            setPost()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @use             $this->setModified()
     *
     * @param           mixed 		$post
     *
     * @return          object      $this
     */
    public function setPost($post) {
        if(!$this->setModified('post', $post)->isModified()) {
            return $this;
        }
		$this->post = $post;
		return $this;
    }

    /**
     * @name            getPost()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          mixed           $this->blog_post
     */
    public function getPost() {
        return $this->post;
    }

    /**
     * @name            setComment
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @use             $this->setModified()
     *
     * @param           mixed $blog_post_comment
     *
     * @return          object                $this
     */
    public function setComment($blog_post_comment) {
        if(!$this->setModified('comment', $blog_post_comment)->isModified()) {
            return $this;
        }
		$this->comment = $blog_post_comment;
		return $this;
    }

    /**
     * @name            getComment()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          mixed           $this->blog_post_comment
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * @name            setParent()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @use             $this->setModified()
     *
     * @param           mixed 			$parent
     *
     * @return          object          $this
     */
    public function setParent($parent) {
        if(!$this->setModified('parent', $parent)->isModified()) {
            return $this;
        }
		$this->parent = $parent;
		return $this;
    }

    /**
     * @name            getParent()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
	 * @return          mixed
	 */
    public function getBlogPostCommentParent() {
        return $this->parent;
    }

    /**
     * @name            setCountDislikes ()
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
     *                  Returns the value of count_dislikes property.
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
     * @name            setCountLikes ()
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
     *                  Returns the value of count_likes property.
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
     * @name            setDatePublished()
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
     * @name           setEmail ()
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
     * @name            setMember()
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
    public function setAuthor($member) {
        if(!$this->setModified('author', $member)->isModified()) {
            return $this;
        }
		$this->author = $member;
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
     * @name            setName ()
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
     *                  Returns the value of name property.
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
     *                  Returns the value of url property.
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
 * v1.0.1  					   26.04.2015
 * TW #3568845
 * Can Berkol
 * **************************************
 * Major changes!!
 *
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