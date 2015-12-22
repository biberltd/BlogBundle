<?php
/**
 * @author		Can Berkol
 * @author		Murat Ünal
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
 *     name="tags_of_blog_post",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idxNTagsOfBlogPostDateAdded", columns={"date_added"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUTagsOfBlogPost", columns={"post","tag"})}
 * )
 */
class TagsOfBlogPost extends CoreEntity
{
    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostTag")
     * @ORM\JoinColumn(name="tag", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\BlogBundle\Entity\BlogPostTag
     */
    private $tag;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
     */
    private $post;

	/**
	 * @param \BiberLtd\Bundle\BlogBundle\Entity\BlogPost $blog_post
	 *
	 * @return $this
	 */
    public function setPost(\BiberLtd\Bundle\BlogBundle\Entity\BlogPost $blog_post) {
        if(!$this->setModified('post', $blog_post)->isModified()) {
            return $this;
        }
		$this->post = $blog_post;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
	 */
    public function getPost() {
        return $this->post;
    }

	/**
	 * @param \BiberLtd\Bundle\BlogBundle\Entity\BlogPostTag $tag
	 *
	 * @return $this
	 */
    public function setTag(\BiberLtd\Bundle\BlogBundle\Entity\BlogPostTag $tag) {
        if(!$this->setModified('tag', $tag)->isModified()) {
            return $this;
        }
		$this->tag = $tag;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\BlogBundle\Entity\BlogPostTag
	 */
    public function getTag() {
        return $this->tag;
    }
}