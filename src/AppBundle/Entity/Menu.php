<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Table(name="menu")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 */
class Menu
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
	 * @var string
	 *
	 * @ORM\Column(name="type", type="string", length=255, nullable = true, columnDefinition="ENUM('category','separator','url')")
	 */
	private $type = 'url';

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="external", type="boolean", length=1)
	 */
	private $external = 0;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="class", type="string", length=255, nullable = true)
	 */
	private $class;

	/**
	 * @Gedmo\TreeLeft
	 * @ORM\Column(name="lft", type="integer")
	 */
	private $lft;

	/**
	 * @Gedmo\TreeLevel
	 * @ORM\Column(name="lvl", type="integer")
	 */
	private $lvl;

	/**
	 * @Gedmo\TreeRight
	 * @ORM\Column(name="rgt", type="integer")
	 */
	private $rgt;

	/**
	 * @Gedmo\TreeRoot
	 * @ORM\Column(name="root", type="integer", nullable=true)
	 */
	private $root;

	/**
	 * @Gedmo\TreeParent
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Menu", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $parent;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Menu", mappedBy="parent")
	 * @ORM\OrderBy({"reorder" = "ASC"})
	 */
	private $children;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="reorder", type="integer", length=11)
	 */
	private $reorder = 0;

	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookDomain")
	 * @ORM\JoinTable(name="menu_to_domain",
	 *      joinColumns={@ORM\JoinColumn(name="menu_id", referencedColumnName="id", onDelete="cascade")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="domain_id", referencedColumnName="id", onDelete="cascade")}
	 *      )
	 */
	private $type_domain;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return Menu
	 */
	public function setId($id)
	{
		$this->id = $id;
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
	 * @return Menu
	 */
	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isExternal()
	{
		return $this->external;
	}

	/**
	 * @param bool $external
	 * @return Menu
	 */
	public function setExternal($external)
	{
		$this->external = $external;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 * @param string $class
	 * @return Menu
	 */
	public function setClass($class)
	{
		$this->class = $class;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLft()
	{
		return $this->lft;
	}

	/**
	 * @param mixed $lft
	 * @return Menu
	 */
	public function setLft($lft)
	{
		$this->lft = $lft;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLvl()
	{
		return $this->lvl;
	}

	/**
	 * @param mixed $lvl
	 * @return Menu
	 */
	public function setLvl($lvl)
	{
		$this->lvl = $lvl;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getRgt()
	{
		return $this->rgt;
	}

	/**
	 * @param mixed $rgt
	 * @return Menu
	 */
	public function setRgt($rgt)
	{
		$this->rgt = $rgt;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getRoot()
	{
		return $this->root;
	}

	/**
	 * @param mixed $root
	 * @return Menu
	 */
	public function setRoot($root)
	{
		$this->root = $root;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * @param mixed $parent
	 * @return Menu
	 */
	public function setParent($parent)
	{
		$this->parent = $parent;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * @param mixed $children
	 * @return Menu
	 */
	public function setChildren($children)
	{
		$this->children = $children;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getReorder()
	{
		return $this->reorder;
	}

	/**
	 * @param int $reorder
	 * @return Menu
	 */
	public function setReorder($reorder)
	{
		$this->reorder = $reorder;
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
	 * @return Menu
	 */
	public function setTypeDomain($type_domain)
	{
		$this->type_domain = $type_domain;
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

