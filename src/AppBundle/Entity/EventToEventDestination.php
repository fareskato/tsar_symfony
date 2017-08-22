<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Tag
 *
 * @ORM\Table(name="event_to_event_destination")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class EventToEventDestination
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Event")
     * @ORM\JoinColumn(name="event", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $event;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Destination")
     * @ORM\JoinColumn(name="destination", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $destination;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", length=11, nullable=true)
     */
    protected $position;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return EventToEventDestination
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
     * @return EventToEventDestination
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param mixed $destination
     * @return EventToEventDestination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return EventToEventDestination
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }


}

