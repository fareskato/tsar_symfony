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
class ChauffeurTranslation  {

  use ORMBehaviors\Translatable\Translation;
}