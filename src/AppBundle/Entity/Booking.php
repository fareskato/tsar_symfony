<?php
/**
 * Created by PhpStorm.
 * User: oalti
 * Date: 19/07/2017
 * Time: 4:05 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="booking")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Booking {


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
   * Users
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
   * @ORM\JoinColumn(name="assigned_user", referencedColumnName="id", onDelete="SET NULL", nullable=true)
   */
  private $assigned_user;

  /**
   * booked product
   * @ORM\OneToOne(targetEntity="AppBundle\Entity\BookingToRelatedProduct", mappedBy="booking")
   */
  private $booked_product;


  /**
   * @var  bigint
   *
   * @ORM\Column(name="date", type="bigint", length=11, nullable=true)
   */
  private $date;


  /**
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255, nullable = true)
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(name="hotel", type="string", length=255, nullable = true)
   */
  private $hotel;

  /**
   * @var integer
   *
   * @ORM\Column(name="amount_of_people", type="integer", length=11, nullable=true)
   */
  private $amount_of_people;

  /**
   * @var  int
   *
   * @ORM\Column(name="supplement_single", type="integer", length=11, nullable=true)
   */
  private $supplement_single;

  /**
   * @var integer
   *
   * @ORM\Column(name="numbre_de_jours", type="integer", length=11, nullable=true)
   */
  private $numbre_de_jours;

  /**
   * @var  int
   *
   * @ORM\Column(name="numbre_de_nuits", type="integer", length=11, nullable=true)
   */
  private $numbre_de_nuits;

  /**
   * @var integer
   *
   * @ORM\Column(name="numbre_de_chambers", type="integer", length=11, nullable=true)
   */
  private $numbre_de_chambers;

  /**
   * @var integer
   *
   * @ORM\Column(name="prix", type="integer", length=11, nullable=true)
   */
  private $prix;

  /**
   * @var integer
   *
   * @ORM\Column(name="supplement_single_price", type="integer", length=11, nullable=true)
   */
  private $supplement_single_price;

  /**
   * @var string
   *
   * @ORM\Column(name="pdf_link", type="string", length=255, nullable = true)
   */
  private $pdf_link;

  /**
   * @var string
   *
   * @ORM\Column(name="blockjour_order", type="string", length=255, nullable = true)
   */
  private $blockjour_order;

  /**
   * @var boolean
   *
   * @ORM\Column(name="visa", type="boolean", length=1, nullable = true)
   */
  private $visa = 0;

  /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookSuppServices")
   * @ORM\JoinColumn(name="supp_services", referencedColumnName="id", onDelete="SET NULL")
   */
  private $supp_services;

  /**
   * @var string
   *
   * @ORM\Column(name="flight_from", type="string", length=255, nullable = true)
   */
  private $flight_from;

  /**
   * @var string
   *
   * @ORM\Column(name="clarification", type="text", nullable=TRUE)
   */
  private $clarification;


  /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookCivilite")
   * @ORM\JoinColumn(name="civilite", referencedColumnName="id", onDelete="SET NULL")
   */
  private $civilite;

  /**
   * @var string
   *
   * @ORM\Column(name="nom", type="text", nullable=TRUE)
   */
  private $nom;

  /**
   * @var string
   *
   * @ORM\Column(name="prenom", type="text", nullable=TRUE)
   */
  private $prenom;

  /**
   * @var string
   *
   * @ORM\Column(name="phone", type="text", nullable=TRUE)
   */
  private $phone;

  /**
   * @var string
   *
   * @ORM\Column(name="email", type="text", nullable=TRUE)
   */
  private $email;

  /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookOfferOptions")
   * @ORM\JoinColumn(name="offer_options", referencedColumnName="id", onDelete="SET NULL")
   */
  private $offer_options;

  /**
   * @var string
   *
   * @ORM\Column(name="security_key", type="text", nullable=TRUE)
   */
  private $key;

  /**
   * @var string
   *
   * @ORM\Column(name="website_version", type="text", nullable=TRUE)
   */
  private $website_version;

  /**
   * @var string
   *
   * @ORM\Column(name="comment", type="text", nullable=TRUE)
   */
  private $comment;

  /**
   * @var integer
   *
   * @ORM\Column(name="devis_booking_id", type="integer", length=11, nullable=true)
   */
  private $devis_booking_id;


  /**
   * @var string
   *
   * @ORM\Column(name="excel_link", type="text", nullable=TRUE)
   */
  private $excel_link;

  /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookSendDocs")
   * @ORM\JoinColumn(name="send_docs", referencedColumnName="id", onDelete="SET NULL")
   */
  private $send_docs;

  /**
   * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookHotelStars")
   * @ORM\JoinTable(name="booking_to_hotelstars",
   *      joinColumns={@ORM\JoinColumn(name="booking_id", referencedColumnName="id", onDelete="cascade")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="hotelstars_id", referencedColumnName="id", onDelete="cascade")}
   *      )
   */
  private $hotel_stars;


  /**
   * @return mixed
   */
  public function getHotelStars()
  {
    return $this->hotel_stars;
  }

  /**
   * @param mixed $hotel_stars
   * @return Hotel
   */
  public function setHotelStars($hotel_stars)
  {
    $this->hotel_stars = $hotel_stars;
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
   * @return mixed
   */
  public function getAssignedUser() {
    return $this->assigned_user;
  }

  /**
   * @param mixed $assigned_user
   */
  public function setAssignedUser($assigned_user) {
    $this->assigned_user = $assigned_user;
  }

  /**
   * @return \AppBundle\Entity\bigint
   */
  public function getDate() {
      $start = substr($this->date, 6, 2).'.'.substr($this->date, 4, 2).'.'.substr($this->date, 0, 4).' '.substr($this->date, 8, 2).':'.substr($this->date, 10, 2);
      return $start;
  }

  /**
   * @param \AppBundle\Entity\bigint $date
   */
  public function setDate($date) {
    $this->date = $date;
  }

  /**
   * @return string
   */
  public function getHotel() {
    return $this->hotel;
  }

  /**
   * @param string $hotel
   */
  public function setHotel($hotel) {
    $this->hotel = $hotel;
  }

  /**
   * @return int
   */
  public function getAmountOfPeople() {
    return $this->amount_of_people;
  }

  /**
   * @param int $amount_of_people
   */
  public function setAmountOfPeople($amount_of_people) {
    $this->amount_of_people = $amount_of_people;
  }

  /**
   * @return int
   */
  public function getSupplementSingle() {
    return $this->supplement_single;
  }

  /**
   * @param int $supplement_single
   */
  public function setSupplementSingle($supplement_single) {
    $this->supplement_single = $supplement_single;
  }

  /**
   * @return int
   */
  public function getNumbreDeJours() {
    return $this->numbre_de_jours;
  }

  /**
   * @param int $numbre_de_jours
   */
  public function setNumbreDeJours($numbre_de_jours) {
    $this->numbre_de_jours = $numbre_de_jours;
  }

  /**
   * @return int
   */
  public function getNumbreDeNuits() {
    return $this->numbre_de_nuits;
  }

  /**
   * @param int $numbre_de_nuits
   */
  public function setNumbreDeNuits($numbre_de_nuits) {
    $this->numbre_de_nuits = $numbre_de_nuits;
  }

  /**
   * @return int
   */
  public function getNumbreDeChambers() {
    return $this->numbre_de_chambers;
  }

  /**
   * @param int $numbre_de_chambers
   */
  public function setNumbreDeChambers($numbre_de_chambers) {
    $this->numbre_de_chambers = $numbre_de_chambers;
  }

  /**
   * @return int
   */
  public function getPrix() {
    return $this->prix;
  }

  /**
   * @param int $prix
   */
  public function setPrix($prix) {
    $this->prix = $prix;
  }

  /**
   * @return int
   */
  public function getSupplementSinglePrice() {
    return $this->supplement_single_price;
  }

  /**
   * @param int $supplement_single_price
   */
  public function setSupplementSinglePrice($supplement_single_price) {
    $this->supplement_single_price = $supplement_single_price;
  }

  /**
   * @return string
   */
  public function getPdfLink() {
    return $this->pdf_link;
  }

  /**
   * @param string $pdf_link
   */
  public function setPdfLink($pdf_link) {
    $this->pdf_link = $pdf_link;
  }

  /**
   * @return string
   */
  public function getBlockjourOrder() {
    return $this->blockjour_order;
  }

  /**
   * @param string $blockjour_order
   */
  public function setBlockjourOrder($blockjour_order) {
    $this->blockjour_order = $blockjour_order;
  }

  /**
   * @return bool
   */
  public function isVisa() {
    return $this->visa;
  }

  /**
   * @param bool $visa
   */
  public function setVisa($visa) {
    $this->visa = $visa;
  }

  /**
   * @return mixed
   */
  public function getSuppServices() {
    return $this->supp_services;
  }

  /**
   * @param mixed $supp_services
   */
  public function setSuppServices($supp_services) {
    $this->supp_services = $supp_services;
  }

  /**
   * @return string
   */
  public function getFlightFrom() {
    return $this->flight_from;
  }

  /**
   * @param string $flight_from
   */
  public function setFlightFrom($flight_from) {
    $this->flight_from = $flight_from;
  }

  /**
   * @return string
   */
  public function getClarification() {
    return $this->clarification;
  }

  /**
   * @param string $clarification
   */
  public function setClarification($clarification) {
    $this->clarification = $clarification;
  }

  /**
   * @return mixed
   */
  public function getCivilite() {
    return $this->civilite;
  }

  /**
   * @param mixed $civilite
   */
  public function setCivilite($civilite) {
    $this->civilite = $civilite;
  }

  /**
   * @return string
   */
  public function getNom() {
    return $this->nom;
  }

  /**
   * @param string $nom
   */
  public function setNom($nom) {
    $this->nom = $nom;
  }

  /**
   * @return string
   */
  public function getPrenom() {
    return $this->prenom;
  }

  /**
   * @param string $prenom
   */
  public function setPrenom($prenom) {
    $this->prenom = $prenom;
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
  public function getEmail() {
    return $this->email;
  }

  /**
   * @param string $email
   */
  public function setEmail($email) {
    $this->email = $email;
  }

  /**
   * @return mixed
   */
  public function getOfferOptions() {
    return $this->offer_options;
  }

  /**
   * @param mixed $offer_options
   */
  public function setOfferOptions($offer_options) {
    $this->offer_options = $offer_options;
  }

  /**
   * @return string
   */
  public function getKey() {
    return $this->key;
  }

  /**
   * @param string $key
   */
  public function setKey($key) {
    $this->key = $key;
  }

  /**
   * @return string
   */
  public function getWebsiteVersion() {
    return $this->website_version;
  }

  /**
   * @param string $website_version
   */
  public function setWebsiteVersion($website_version) {
    $this->website_version = $website_version;
  }

  /**
   * @return string
   */
  public function getComment() {
    return $this->comment;
  }

  /**
   * @param string $comment
   */
  public function setComment($comment) {
    $this->comment = $comment;
  }

  /**
   * @return int
   */
  public function getDevisBookingId() {
    return $this->devis_booking_id;
  }

  /**
   * @param int $devis_booking_id
   */
  public function setDevisBookingId($devis_booking_id) {
    $this->devis_booking_id = $devis_booking_id;
  }

  /**
   * @return string
   */
  public function getExcelLink() {
    return $this->excel_link;
  }

  /**
   * @param string $excel_link
   */
  public function setExcelLink($excel_link) {
    $this->excel_link = $excel_link;
  }

  /**
   * @return mixed
   */
  public function getSendDocs() {
    return $this->send_docs;
  }

  /**
   * @param mixed $send_docs
   */
  public function setSendDocs($send_docs) {
    $this->send_docs = $send_docs;
  }

  /**
   * @return mixed
   */
  public function getBookedProduct()
  {
    if($this->booked_product) {
        if ($this->booked_product->getVisit()) {
            return $this->booked_product->getVisit();
        }
        if ($this->booked_product->getExtension()) {
            return $this->booked_product->getExtension();
        }
        if ($this->booked_product->getVoyage()) {
            return $this->booked_product->getVoyage();
        }
        if ($this->booked_product->getEvent()) {
            return $this->booked_product->getEvent();
        }
      return NULL;
    }
    return NULL;
  }

  /**
   * @param mixed $booked_product
   * @return Event
   */
  public function setBookedProduct($booked_product)
  {
    $this->booked_product = $booked_product;
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

