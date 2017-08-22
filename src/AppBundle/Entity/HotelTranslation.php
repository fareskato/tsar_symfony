<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Tag
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class HotelTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=TRUE)
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
	 * @ORM\Column(name="headline", type="string", length=255, nullable=true)
	 */
	private $headline;

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
	 * @ORM\Column(name="type_of_hotel", type="string", length=255, nullable=true)
	 */
	private $type_of_hotel;

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
     * @return HotelTranslation
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
     * @return HotelTranslation
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * @param string $headline
     * @return HotelTranslation
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;
        return $this;
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
     * @return HotelTranslation
     */
    public function setBodySummary($body_summary)
    {
        $this->body_summary = $body_summary;
        return $this;
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
     * @return HotelTranslation
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeOfHotel()
    {
        return $this->type_of_hotel;
    }

    /**
     * @param string $type_of_hotel
     * @return HotelTranslation
     */
    public function setTypeOfHotel($type_of_hotel)
    {
        $this->type_of_hotel = $type_of_hotel;
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
     * @return HotelTranslation
     */
    public function setActive($active)
    {
        $this->active = $active;
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
     * @return HotelTranslation
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
        return $this;
    }


}