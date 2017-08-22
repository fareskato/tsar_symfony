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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="autre_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class AutreProduct {
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
     * Label du visa
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=TRUE)
     */
    private $label;

  /**
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255, nullable=TRUE)
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(name="titre", type="string", length=255, nullable=TRUE)
   */
  private $titre;

  /**
   * Ville.
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location")
   * @ORM\JoinColumn(name="ville", referencedColumnName="id", onDelete="SET NULL")
   */
  private $ville;

  /**
   * @var string
   *
   * @ORM\Column(name="description", type="text", nullable=TRUE)
   */
  private $description;

  /**
   * @var string
   *
   * @ORM\Column(name="nom_en_latin", type="string", length=255, nullable=TRUE)
   */
  private $nom_en_latin;

  /**
   * @var string
   *
   * @ORM\Column(name="nom_en_cyrillique", type="string", length=255, nullable=TRUE)
   */
  private $nom_en_cyrillique;

  /**
   * @var string
   *
   * @ORM\Column(name="phone", type="string", length=255, nullable=TRUE)
   */
  private $phone;

  /**
   * @var string
   *
   * @ORM\Column(name="supp_info", type="string", length=255, nullable=TRUE)
   */
  private $supp_info;

  /**
   * @var string
   *
   * @ORM\Column(name="info_utile", type="string", length=255, nullable=TRUE)
   */
  private $info_utile;

  /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookCurrency")
   * @ORM\JoinColumn(name="currency", referencedColumnName="id", onDelete="SET NULL")
   */
  private $currency;

  /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookGroupType")
   * @ORM\JoinColumn(name="group_type", referencedColumnName="id", onDelete="SET NULL")
   */
  private $group_type;

  /**
   * @var string
   *
   * @ORM\Column(name="excel_custom_id", type="string", length=255, nullable=TRUE)
   */
  private $excel_custom_id;

  /**
   * @var string
   *
   * @ORM\Column(name="excel_title", type="string", length=255, nullable=TRUE)
   */
  private $excel_title;

  /**
   * @var string
   *
   * @ORM\Column(name="excel_description", type="string", nullable=TRUE)
   */
  private $excel_description;

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
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return AutreProduct
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }


  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getTitre() {
    return $this->titre;
  }

  /**
   * @param string $titre
   */
  public function setTitre($titre) {
    $this->titre = $titre;
  }

  /**
   * @return string
   */
  public function getVille() {
    return $this->ville;
  }

  /**
   * @param string $ville
   */
  public function setVille($ville) {
    $this->ville = $ville;
  }

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
  public function getNomEnLatin() {
    return $this->nom_en_latin;
  }

  /**
   * @param string $nom_en_latin
   */
  public function setNomEnLatin($nom_en_latin) {
    $this->nom_en_latin = $nom_en_latin;
  }

  /**
   * @return string
   */
  public function getNomEnCyrillique() {
    return $this->nom_en_cyrillique;
  }

  /**
   * @param string $nom_en_cyrillique
   */
  public function setNomEnCyrillique($nom_en_cyrillique) {
    $this->nom_en_cyrillique = $nom_en_cyrillique;
  }

  /**
   * @return string
   */
  public function getPhone() {
    return $this->phone;
  }

  /**
   * @param string $phone
   */
  public function setPhone($phone) {
    $this->phone = $phone;
  }

  /**
   * @return string
   */
  public function getSuppInfo() {
    return $this->supp_info;
  }

  /**
   * @param string $supp_info
   */
  public function setSuppInfo($supp_info) {
    $this->supp_info = $supp_info;
  }

  /**
   * @return string
   */
  public function getInfoUtile() {
    return $this->info_utile;
  }

  /**
   * @param string $info_utile
   */
  public function setInfoUtile($info_utile) {
    $this->info_utile = $info_utile;
  }

  /**
   * @return mixed
   */
  public function getCurrency() {
    return $this->currency;
  }

  /**
   * @param mixed $currency
   */
  public function setCurrency($currency) {
    $this->currency = $currency;
  }

  /**
   * @return mixed
   */
  public function getGroupType() {
    return $this->group_type;
  }

  /**
   * @param mixed $group_type
   */
  public function setGroupType($group_type) {
    $this->group_type = $group_type;
  }

  /**
   * @return string
   */
  public function getExcelCustomId() {
    return $this->excel_custom_id;
  }

  /**
   * @param string $excel_custom_id
   */
  public function setExcelCustomId($excel_custom_id) {
    $this->excel_custom_id = $excel_custom_id;
  }

  /**
   * @return string
   */
  public function getExcelTitle() {
    return $this->excel_title;
  }

  /**
   * @param string $excel_title
   */
  public function setExcelTitle($excel_title) {
    $this->excel_title = $excel_title;
  }

  /**
   * @return string
   */
  public function getExcelDescription() {
    return $this->excel_description;
  }

  /**
   * @param string $excel_description
   */
  public function setExcelDescription($excel_description) {
    $this->excel_description = $excel_description;
  }




    public function getClass(){
        return 'AutreProduct';
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