<?php
/**
 * Created by PhpStorm.
 * User: oalti
 * Date: 20/07/2017
 * Time: 12:12 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;


/**
 ** @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class BookSendDocsTranslation {

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
  public function getDescription() {
    return $this->description;
  }

  /**
   * @param string $description
   */
  public function setDescription($description) {
    $this->description = $description;
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
   * @return BookSendDocs
   */
  public function setName($name)
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return int
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @return string
   */
  public function getLocale() {
    return $this->locale;
  }

  /**
   * @param string $locale
   */
  public function setLocale($locale) {
    $this->locale = $locale;
  }

  /**
   * @return mixed
   */
  public function getTranslatable() {
    return $this->translatable;
  }

  /**
   * @param mixed $translatable
   */
  public function setTranslatable($translatable) {
    $this->translatable = $translatable;
  }



}