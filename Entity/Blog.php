<?php
/**
 * @author		Can Berkol
 * @author		Murat Ünal
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        10.12.2015
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

	/**
	 * @return mixed
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * @param array $moderators (BiberLtd\MemberberManagementBundle\Entity\Member)
	 *
	 * @return $this
	 */
	public function setModerators(array $moderators) {
		$cCount = count($moderators);
		$validModerators = [];
		for($i=0; $i < $cCount; $i++){
			if($moderators[$i] instanceof BiberLtd\MemberberManagementBundle\Entity\Member){
				$validModerators[] = $moderators[$i];
			}
		}
		unset($moderators);
		if(!$this->setModified('moderators', $validModerators)->isModified()) {
			return $this;
		}
		$this->moderators = $validModerators;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getModerators() {
		return $this->moderators;
	}

	/**
	 * @param $posts
	 *
	 * @return $this
	 */
	public function setBlogPosts($posts) {
		$cCount = count($posts);
		$validPosts = [];
		for($i=0; $i < $cCount; $i++){
			if($posts[$i] instanceof BiberLtd\BlogBundle\Entity\BşogPost){
				$validPosts[] = $validPosts[$i];
			}
		}
		unset($posts);
		if(!$this->setModified('posts', $validPosts)->isModified()) {
			return $this;
		}
		$this->posts = $validPosts;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPosts() {
		return $this->posts;
	}

	/**
	 * @param int $count
	 *
	 * @return $this
	 */
	public function setCountPosts(int $count) {
		if(!$this->setModified('count_posts', $count)->isModified()) {
			return $this;
		}
		$this->count_posts = $count;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCountPosts() {
		return $this->count_posts;
	}

	/**
	 * @param \BiberLtd\Bundle\SiteManagementBundle\Entity\Site $site
	 *
	 * @return $this
	 */
	public function setSite(\BiberLtd\Bundle\SiteManagementBundle\Entity\Site $site) {
		if(!$this->setModified('site', $site)->isModified()) {
			return $this;
		}
		$this->site = $site;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSite() {
		return $this->site;
	}
}