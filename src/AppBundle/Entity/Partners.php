<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Tag
 *
 * @ORM\Table(name="partners")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Partners
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
     * @ORM\Column(name="link", type="string", length=255, nullable = true)
     */
    private $link;

	/**
	 * Many Users have One Address.
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
	 * @ORM\JoinColumn(name="image", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $image;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="reorder", type="integer", length=11)
	 */
	private $reorder = 1;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return Partners
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * @param string $link
	 * @return Partners
	 */
	public function setLink($link)
	{
		$this->link = $link;
		return $this;
	}

	public function getImage()
	{
		return $this->image;
	}

	/**
	 * @param mixed $image
	 * @return Partners
	 */
	public function setImage($image)
	{
		$this->image = $image;
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
	 * @return Partners
	 */
	public function setReorder($reorder)
	{
		$this->reorder = $reorder;
		return $this;
	}





// Translations
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

