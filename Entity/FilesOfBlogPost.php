<?php
/**
 * @name        FilesOfBlogPost
 * @package		BiberLtd\Bundle\CoreBundle\BlogBundle
 *
 * @author		Murat Ünal
 *
 * @version     1.0.0
 * @date        15.09.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
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
     */
    public $date_added;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $sort_order;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\BlogBundle\Entity\BlogPost")
     * @ORM\JoinColumn(name="post", referencedColumnName="id", nullable=false)
     */
    private $post;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\FileManagementBundle\Entity\File")
     * @ORM\JoinColumn(name="file", referencedColumnName="id", nullable=false)
     */
    private $file;
    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $type;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $count_view;

    /**
     * @name            setPost ()
     *                  Sets the blog_post property.
     *                  Updates the data only if stored value and value to be set are different.
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
    public function setPost($post) {
        if(!$this->setModified('post', $post)->isModified()) {
            return $this;
        }
        $this->post = $post;
        return $this;
    }

    /**
     * @name            getPost ()
     *                  Returns the value of blog_post property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->blog_post
     */
    public function getPost() {
        return $this->post;
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
     * @name                  setSortOrder ()
     *                                     Sets the sort_order property.
     *                                     Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $sort_order
     *
     * @return          object                $this
     */
    public function setSortOrder($sort_order) {
        if(!$this->setModified('sort_order', $sort_order)->isModified()) {
            return $this;
        }
        $this->sort_order = $sort_order;
        return $this;
    }

    /**
     * @name            getSortOrder ()
     *                               Returns the value of sort_order property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->sort_order
     */
    public function getSortOrder() {
        return $this->sort_order;
    }

    /**
     * @name        getCountView ()
     *
     * @author      Said İmamoğlu
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      mixed
     */
    public function getCountView()
    {
        return $this->count_view;
    }

    /**
     * @name        setCountView ()
     *
     * @author      Said İmamoğlu
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       mixed $count_view
     *
     * @return      $this
     */
    public function setCountView($count_view)
    {
        if (!$this->setModifiled('count_view', $count_view)->isModified()) {
            return $this;
        }
        $this->count_view = $count_view;
        return $this;
    }

    /**
     * @name        getType ()
     *
     * @author      Said İmamoğlu
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @name        setType ()
     *
     * @author      Said İmamoğlu
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       mixed $type
     *
     * @return      $this
     */
    public function setType($type)
    {
        if (!$this->setModifiled('type', $type)->isModified()) {
            return $this;
        }
        $this->type = $type;
        return $this;
    }
    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

}
/**
 * Change Log:
 * **************************************
 * v1.0.1                      Said İmamoğlu
 * 15.01.2015
 *
 * **************************************
 * A getType()
 * A setType()
 * A getCountView()
 * A setCountView()
 *
 * **************************************
 * v1.0.0                      Murat Ünal
 * 15.09.2013
 * **************************************
 * A getDateAdded()
 * A getFile()
 * A getSortOrder()
 *
 * A setDateAdded()
 * A setFile()
 * A setSortOrder()
 *
 */
