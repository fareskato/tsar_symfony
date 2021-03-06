<?php
/**
 * Created by PhpStorm.
 * User: fares
 * Date: 13.07.2017
 * Time: 13:44
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;



/**
 ** @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class BookDriverDurationTranslation
{
    use ORMBehaviors\Translatable\Translation;
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=TRUE)
     */
    private $description;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return BookDriverDuration
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return BookDriverDuration
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

}