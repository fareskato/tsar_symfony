<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;


/**
 ** @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class BookingTranslation
{

    use ORMBehaviors\Translatable\Translation;

}

