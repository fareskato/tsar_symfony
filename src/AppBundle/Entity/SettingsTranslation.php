<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity
 */
Class SettingsTranslation {

	use ORMBehaviors\Translatable\Translation;

  /**
   * @ORM\Column(type="string", length=255,  nullable=TRUE)
   */
  private $value;

  /**
   * @ORM\Column(type="text", nullable=TRUE)
   */
  private $description;

  /**
   * @ORM\Column(type="text", nullable=TRUE)
   */
  private $body;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="slug", type="string", length=255, nullable = true)
	 */
	private $slug;

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param mixed $value
	 * @return SettingsTranslation
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param mixed $description
	 * @return SettingsTranslation
	 */
	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * @param mixed $body
	 * @return SettingsTranslation
	 */
	public function setBody($body)
	{
		$this->body = $body;
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
	 * @return SettingsTranslation
	 */
	public function setSlug($slug)
	{
		$this->slug = $slug;
		return $this;
	}




}

?>