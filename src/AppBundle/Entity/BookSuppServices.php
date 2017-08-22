<?php
/**
 * Created by PhpStorm.
 * User: oalti
 * Date: 20/07/2017
 * Time: 12:11 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Table(name="book_supp_services")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */

class BookSuppServices {
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