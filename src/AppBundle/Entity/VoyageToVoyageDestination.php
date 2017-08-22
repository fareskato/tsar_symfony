<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Tag
 *
 * @ORM\Table(name="voyage_to_voyage_destination")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class VoyageToVoyageDestination
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Voyage")
     * @ORM\JoinColumn(name="voyage", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $voyage;

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
     * @return VoyageToVoyageDestination
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return VoyageToVoyageDestination
     */
    public function setVoyage($voyage)
    {
        $this->voyage = $voyage;
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
     * @return VoyageToVoyageDestination
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
     * @return VoyageToVoyageDestination
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }


}

