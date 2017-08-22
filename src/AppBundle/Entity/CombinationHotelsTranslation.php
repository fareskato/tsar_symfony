<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * VisaTranslation
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class CombinationHotelsTranslation
{
    use ORMBehaviors\Translatable\Translation;



    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", length=1)
     */
    private $active = 0;

    /**
     * Nom du visa
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=TRUE)
     */
    private $name;

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return CombinationHotelsTranslation
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return CombinationHotelsTranslation
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }



}

