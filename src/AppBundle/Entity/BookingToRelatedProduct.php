<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Tag
 *
 * @ORM\Table(name="booking_to_related_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class BookingToRelatedProduct
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
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Booking", inversedBy="booked_product")
     * @ORM\JoinColumn(name="booking", referencedColumnName="id")
     */
    protected $booking;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Event")
     * @ORM\JoinColumn(name="event", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $event;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return BookingToRelatedProduct
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBooking()
    {
        return $this->booking;
    }

    /**
     * @param mixed $booking
     * @return BookingToRelatedProduct
     */
    public function setBooking($booking)
    {
        $this->booking = $booking;
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
     * @return BookingToRelatedProduct
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
     * @return BookingToRelatedProduct
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
     * @return BookingToRelatedProduct
     */
    public function setVoyage($voyage)
    {
        $this->voyage = $voyage;
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
     * @return BookingToRelatedProduct
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }



}

