<?php
/**
 * Created by PhpStorm.
 * User: oalti
 * Date: 02/08/2017
 * Time: 1:01 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Tag
 *
 * @ORM\Table(name="book_vehicle_types")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */

class BookVehicleTypes {
  use ORMBehaviors\Translatable\Translatable;

  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   * @return BookBannerList
   */
  public function setId($id)
  {
    $this->id = $id;
    return $this;
  }



  public function __call($method, $args)
  {
    if (!method_exists(self::getTranslationEntityClass(), $method)) {
      $method = 'get' . ucfirst($method);
    }
    if (!method_exists($this,$method)) {
      return null;
    }
    return $this->proxyCurrentLocaleTranslation($method, $args);
  }
}