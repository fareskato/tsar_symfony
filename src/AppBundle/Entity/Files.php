<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;


/**
 * User
 *
 * @ORM\Table(name="files")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Files
{
	use ORMBehaviors\Translatable\Translatable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

	/**
	 * Many Users have One Address.
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
	 */
    private $user_id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="file_name", type="string", length=255, nullable=TRUE)
	 */
    private $file_name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="url", type="string", length=255)
	 */
    private $url;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="mime", type="string", length=255, nullable=TRUE)
	 */
    private $mime;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="active", type="boolean", length=1)
	 */
    private $active = 1;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="type", type="string", length=255, nullable=TRUE)
	 */
    private $type;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return Files
	 */
	public function setId($id)
	{
		$this->id = $id;
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
	 * @return Files
	 */
	public function setUserId($user_id)
	{
		$this->user_id = $user_id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFileName()
	{
		return $this->file_name;
	}

	/**
	 * @param string $file_name
	 * @return Files
	 */
	public function setFileName($file_name)
	{
		$this->file_name = $file_name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 * @return Files
	 */
	public function setUrl($url)
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMime()
	{
		return $this->mime;
	}

	/**
	 * @param string $mime
	 * @return Files
	 */
	public function setMime($mime)
	{
		$this->mime = $mime;
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
	 * @return Files
	 */
	public function setActive($active)
	{
		$this->active = $active;
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
	 * @return Files
	 */
	public function setType($type)
	{
		$this->type = $type;
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

