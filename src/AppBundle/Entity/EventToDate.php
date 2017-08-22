<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Tag
 *
 * @ORM\Table(name="event_to_date")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class EventToDate
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
    private $event;

    /**
     * @var  bigint
     *
     * @ORM\Column(name="date_start", type="bigint", length=11, nullable=true)
     */
    private $date_start;

    /**
     * @var  bigint
     *
     * @ORM\Column(name="date_stop", type="bigint", length=11, nullable=true)
     */
    private $date_stop;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return EventToDate
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param int $event
     * @return EventToDate
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return bigint
     */
    public function getDateStart()
    {
        $date=substr($this->date_start, 6, 2).'.'.substr($this->date_start, 4, 2).'.'.substr($this->date_start, 0, 4).' '.substr($this->date_start, 8, 2).':'.substr($this->date_start, 10, 2);
        return $date;
    }

	public function getStart() {
		return $this->date_start;
	}

	public function getMonthStart($date = false) {
		if ($date) {
			$date = substr($this->date_start, 0, 4).'-'.substr($this->date_start, 4, 2).'-'.substr($this->date_start, 6, 2);
		} else {
			$date = substr($this->date_start, 0, 4).'-'.substr($this->date_start, 4, 2);
		}
		return $date;
	}

    /**
     * @param bigint $date
     * @return EventToDate
     */
    public function setDateStart($date)
    {
        $this->date_start = $date;
        return $this;
    }

    /**
     * @return bigint
     */
    public function getDateStop()
    {
        $date=substr($this->date_stop, 6, 2).'.'.substr($this->date_stop, 4, 2).'.'.substr($this->date_stop, 0, 4).' '.substr($this->date_stop, 8, 2).':'.substr($this->date_stop, 10, 2);
        return $date;

    }

	public function getStop() {
		return $this->date_stop;
	}

    /**
     * @param bigint $date_stop
     * @return EventToDate
     */
    public function setDateStop($date_stop)
    {
        $this->date_stop = $date_stop;
        return $this;
    }


}

