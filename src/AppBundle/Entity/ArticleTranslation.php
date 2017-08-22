<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity
 */
class ArticleTranslation
{

	use ORMBehaviors\Translatable\Translation;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="slug", type="string", length=255, nullable=true)
	 */
	private $slug;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="body_summary", type="text", nullable=TRUE)
	 */
	private $body_summary;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="body", type="text", nullable=TRUE)
	 */
	private $body;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="keywords", type="text", nullable=TRUE)
	 */
	private $keywords;

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
	 */
	public function setName($name)
	{
		$this->name = $name;
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
	 */
	public function setSlug($slug)
	{
		$this->slug = $slug;
	}

	/**
	 * @return string
	 */
	public function getBodySummary()
	{
		return $this->body_summary;
	}

	/**
	 * @param string $body_summary
	 */
	public function setBodySummary($body_summary)
	{
		$this->body_summary = $body_summary;
	}

	/**
	 * @return string
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * @param string $body
	 */
	public function setBody($body)
	{
		$this->body = $body;
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
	 */
	public function setKeywords($keywords)
	{
		$this->keywords = $keywords;
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
	 */
	public function setActive($active)
	{
		$this->active = $active;
	}





}

