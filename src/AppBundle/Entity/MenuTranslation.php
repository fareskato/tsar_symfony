<?php

/*
$categoryEntity = $this->em->getRepository('Acme\DemoBundle\Entity\Category');
$categories = $categoryEntity->childrenHierarchy();
*/


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity
 */
class MenuTranslation
{
	use ORMBehaviors\Translatable\Translation;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="slug", type="string", length=255, nullable = true)
	 */
	private $slug;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="active", type="boolean", length=1)
	 */
	private $active = 0;

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return MenuTranslation
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * @param string $slug
	 * @return MenuTranslation
	 */
	public function setSlug($slug)
	{
		$this->slug = $slug;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isActive()
	{
		return $this->active;
	}

	/**
	 * @param bool $active
	 * @return MenuTranslation
	 */
	public function setActive($active)
	{
		$this->active = $active;
		return $this;
	}



}

