<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Tag
 *
 * @ORM\Table(name="extension_to_day")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class ExtensionToDay
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Extension")
     * @ORM\JoinColumn(name="extension", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $extension;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Day")
     * @ORM\JoinColumn(name="day", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $day;

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
     * @return ExtensionToDay
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return ExtensionToDay
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     * @return ExtensionToDay
     */
    public function setDay($day)
    {
        $this->day = $day;
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
     * @return ExtensionToDay
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }


}

