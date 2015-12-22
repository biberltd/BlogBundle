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
 *     name="related_blog_post",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idxNRelatedBlogPostDateAdded", columns={"date_added"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxURelatedBlogPost", columns={"post","related_post"})}
 * )
 */
class RelatedBlogPost extends CoreEntity
{
    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
     */
    private $post;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="related_post", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\BlogBundle\Entity\RelatedBlogPost
     */
    private $related_post;

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
    public function getBlogPost() {
        return $this->post;
    }

	/**
	 * @param \BiberLtd\Bundle\BlogBundle\Entity\RelatedBlogPost $related_post
	 *
	 * @return $this
	 */
    public function setRelatedPost(\BiberLtd\Bundle\BlogBundle\Entity\RelatedBlogPost $related_post) {
        if(!$this->setModified('related_post', $related_post)->isModified()) {
            return $this;
        }
		$this->related_post = $related_post;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\BlogBundle\Entity\RelatedBlogPost
	 */
    public function getRelatedPost() {
        return $this->related_post;
    }
}