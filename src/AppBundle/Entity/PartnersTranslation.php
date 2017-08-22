<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class PartnersTranslation
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
	 * @var string
	 *
	 * @ORM\Column(name="keywords", type="text", nullable=TRUE)
	 */
	private $keywords;


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return Partners
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
	 * @return Partners
	 */
	public function setSlug($slug)
	{
		$this->slug = $slug;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getKeywords()
	{
		return $this->keywords;
	}

	/**
	 * @param string $keywords
	 * @return Partners
	 */
	public function setKeywords($keywords)
	{
		$this->keywords = $keywords;
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
	 * @return Partners
	 */
	public function setActive($active)
	{
		$this->active = $active;
		return $this;
	}
}

