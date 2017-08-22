<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Settings
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
Class Settings {

	use ORMBehaviors\Translatable\Translatable;

  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer", nullable=TRUE)
   */
  protected $id;

  /**
   * @ORM\Column(type="string", length=255, nullable=TRUE)
   */
  private $name;

  /**
   * @ORM\Column(type="string", nullable=TRUE)
   */
  private $settings_category;

  /**
   * Many Users have One Address.
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
   * @ORM\JoinColumn(name="image", referencedColumnName="id", onDelete="SET NULL")
   */
  private $image;

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 * @return Settings
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param mixed $name
	 * @return Settings
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSettingsCategory()
	{
		return $this->settings_category;
	}

	/**
	 * @param mixed $settings_category
	 * @return Settings
	 */
	public function setSettingsCategory($settings_category)
	{
		$this->settings_category = $settings_category;
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
	 * @return Settings
	 */
	public function setImage($image)
	{
		$this->image = $image;
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

?>