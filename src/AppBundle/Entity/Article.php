<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Article
{

	use ORMBehaviors\Translatable\Translatable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookDomain")
	 * @ORM\JoinTable(name="article_to_domain",
	 *      joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id", onDelete="cascade")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="domain_id", referencedColumnName="id", onDelete="cascade")}
	 *      )
	 */
	private $type_domain;

	/**
	 * Many Users have One Address.
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $user_id = null;

	/**
	 * Many Users have One Address.
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
	 * @ORM\JoinColumn(name="image", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $image  = null;


	/**
	 * @var integer
	 *
	 * @ORM\Column(name="created", type="integer", length=11, nullable=TRUE)
	 */
	private $created;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="changed", type="integer", length=11, nullable=TRUE)
	 */
	private $changed;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return Article
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTypeDomain()
	{
		return $this->type_domain;
	}

	/**
	 * @param mixed $type_domain
	 * @return Article
	 */
	public function setTypeDomain($type_domain)
	{
		$this->type_domain = $type_domain;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getUserId()
	{
		return $this->user_id;
	}

	/**
	 * @param mixed $user_id
	 * @return Article
	 */
	public function setUserId($user_id)
	{
		$this->user_id = $user_id;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getImage()
	{
		return $this->image;
	}

	/**
	 * @param mixed $image
	 * @return Article
	 */
	public function setImage($image)
	{
		$this->image = $image;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @param int $created
	 * @return Article
	 */
	public function setCreated($created)
	{
		$this->created = $created;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getChanged()
	{
		return $this->changed;
	}

	/**
	 * @param int $changed
	 * @return Article
	 */
	public function setChanged($changed)
	{
		$this->changed = $changed;
		return $this;
	}




	public function __call($method, $args)
	{
		if (!method_exists(self::getTranslationEntityClass(), $method)) {
			$method = 'get' . ucfirst($method);
		}
		if (!method_exists($this,$method)) {
			return null;
		}
		return $this->proxyCurrentLocaleTranslation($method, $args);
	}
}

