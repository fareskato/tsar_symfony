<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Tag
 *
 * @ORM\Table(name="extension_to_extension_destination")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class ExtensionToExtensionDestination
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
     * @return ExtensionToExtensionDestination
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
     * @return ExtensionToExtensionDestination
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
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
     * @return ExtensionToExtensionDestination
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
     * @return ExtensionToExtensionDestination
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }


}

