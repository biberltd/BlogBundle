<?php
/**
 * @author		Can Berkol
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
 *     name="categories_of_blog_post",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idxNCategoriesOfBlogPostDateAdded", columns={"date_added"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUCategoriesOfBlogPost", columns={"post","category"})}
 * )
 */
class CategoriesOfBlogPost extends CoreEntity
{
    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /**
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"y"})
     * @var string
     */
    private $is_primary;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default":1,"unsigned":true})
     */
    private $sort_order;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory")
     * @ORM\JoinColumn(name="category", referencedColumnName="id", nullable=false)
     * @var \BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory
     */
    private $category;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false)
     * @var \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
     */
    private $post;

    /**
     * @param \BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory $category
     *
     * @return $this
     */
    public function setCategory(\BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory $category) {
        if(!$this->setModified('category', $category)->isModified()) {
            return $this;
        }
		$this->category = $category;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\BlogBundle\Entity\BlogPostCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @param \BiberLtd\Bundle\BlogBundle\Entity\BlogPost $post
     *
     * @return $this
     */
    public function setPost(\BiberLtd\Bundle\BlogBundle\Entity\BlogPost $post) {
        if(!$this->setModified('post', $post)->isModified()) {
            return $this;
        }
		$this->post = $post;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
     */
    public function getPost() {
        return $this->post;
    }

    /**
     * @param string $is_primary
     *
     * @return $this
     */
    public function setIsPrimary(string $is_primary) {
        if(!$this->setModified('is_primary', $is_primary)->isModified()) {
            return $this;
        }
		$this->is_primary = $is_primary;
		return $this;
    }

    /**
     * @return string
     */
    public function getIsPrimary() {
        return $this->is_primary;
    }
    /**
     * @param int $sortOrder
     *
     * @return $this
     */
    public function setSortOrder(int $sortOrder) {
        if(!$this->setModified('sort_order', $sortOrder)->isModified()) {
            return $this;
        }
		$this->sort_order = $sortOrder;
		return $this;
    }

    /**
     * @return string
     */
    public function getSortOrder() {
        return $this->sort_order;
    }
}