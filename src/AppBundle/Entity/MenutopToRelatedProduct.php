<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Tag
 *
 * @ORM\Table(name="menutop_related_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class MenutopToRelatedProduct
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Visit")
     * @ORM\JoinColumn(name="visit", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $visit;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Extension")
     * @ORM\JoinColumn(name="extension", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $extension;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Voyage")
     * @ORM\JoinColumn(name="voyage", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $voyage;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Hotel")
     * @ORM\JoinColumn(name="hotel", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $hotel;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return EventToRelatedProduct
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     * @return EventToRelatedProduct
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisit()
    {
        return $this->visit;
    }

    /**
     * @param mixed $visit
     * @return EventToRelatedProduct
     */
    public function setVisit($visit)
    {
        $this->visit = $visit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param mixed $extension
     * @return EventToRelatedProduct
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVoyage()
    {
        return $this->voyage;
    }

    /**
     * @param mixed $voyage
     * @return EventToRelatedProduct
     */
    public function setVoyage($voyage)
    {
        $this->voyage = $voyage;
        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getHotel()
	{
		return $this->hotel;
	}

	/**
	 * @param mixed $hotel
	 * @return MenuToRelatedProduct
	 */
	public function setHotel($hotel)
	{
		$this->hotel = $hotel;
		return $this;
	}



}

