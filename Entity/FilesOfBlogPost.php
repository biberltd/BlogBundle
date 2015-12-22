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
 *     name="files_of_blog_post", 
 *     indexes={@ORM\Index(name="idx_n_files_of_blog_post_date_added", columns={"date_added"})}, 
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_files_of_blog_post", columns={"file","post"})}
 * )
 */
class FilesOfBlogPost extends CoreEntity
{
    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false)
     * @var integer
     */
    private $sort_order;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false)
     * @var \BiberLtd\Bundle\BlogBundle\Entity\BlogPost
     */
    private $post;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\FileManagementBundle\Entity\File")
     * @ORM\JoinColumn(name="file", referencedColumnName="id", nullable=false)
     * @var \BiberLtd\Bundle\FileManagementBundle\Entity\File
     */
    private $file;
    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     * @var string
     */
    private $type;
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var integer
     */
    private $count_view;

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
     * @param \BiberLtd\Bundle\FileManagementBundle\Entity\File $file
     *
     * @return $this
     */
    public function setFile(\BiberLtd\Bundle\FileManagementBundle\Entity\File $file) {
        if(!$this->setModified('file', $file)->isModified()) {
            return $this;
        }
        $this->file = $file;
        return $this;
    }

    /**
     * @return \BiberLtd\Bundle\FileManagementBundle\Entity\File
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * @param int $sort_order
     *
     * @return $this
     */
    public function setSortOrder(\integer $sort_order) {
        if(!$this->setModified('sort_order', $sort_order)->isModified()) {
            return $this;
        }
        $this->sort_order = $sort_order;
        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder() {
        return $this->sort_order;
    }

    /**
     * @return int
     */
    public function getCountView()
    {
        return $this->count_view;
    }

    /**
     * @param int $count_view
     *
     * @return $this
     */
    public function setCountView(\integer $count_view)
    {
        if (!$this->setModified('count_view', $count_view)->isModified()) {
            return $this;
        }
        $this->count_view = $count_view;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(\string $type)
    {
        if (!$this->setModified('type', $type)->isModified()) {
            return $this;
        }
        $this->type = $type;
        return $this;
    }
}