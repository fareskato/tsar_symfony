<?php
/**
 * Created by PhpStorm.
 * User: oalti
 * Date: 02/08/2017
 * Time: 11:20 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class AutreProductTranslation  {

  use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=TRUE)
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return AutreProductTranslation
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }


}