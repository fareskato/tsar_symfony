<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="transferts")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Transferts
{
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
     * Téléphone
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=TRUE)
     */
    private $phone;

    /**
     * type
     * @var boolean
     *
     * @ORM\Column(name="type_group", type="boolean", length=1)
     */
    private $type_group = 0;

    /**
     * Custom id
     * @var integer
     *
     * @ORM\Column(name="custom_id", type="integer", nullable=TRUE)
     */
    private $custom_id;

    /**
     * Excel title
     * @var string
     *
     * @ORM\Column(name="excel_title", type="string", length=255, nullable=TRUE)
     */
    private $excel_title;

    /**
     * Excel description
     * @var string
     *
     * @ORM\Column(name="excel_description", type="string", length=255, nullable=TRUE)
     */
    private $excel_description;

    /**
     * Ville.
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location")
     * @ORM\JoinColumn(name="ville", referencedColumnName="id", onDelete="SET NULL")
     */
    private $ville;

    /**
     * Ville de départ.
     * Город отправления
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location")
     * @ORM\JoinColumn(name="departure_city", referencedColumnName="id", onDelete="SET NULL")
     */
    private $departure_city;

    /**
     * Ville d'arrivée.
     * Город прибытия
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location")
     * @ORM\JoinColumn(name="city_arrival", referencedColumnName="id", onDelete="SET NULL")
     */
    private $city_arrival;

    /**
     * Nombre de passagers
     * @var integer
     *
     * @ORM\Column(name="number_passengers", type="integer", nullable=TRUE)
     */
    private $number_passengers;

    /**
     * Type de transfert
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookTypeTransfert")
     * @ORM\JoinColumn(name="type_transfert", referencedColumnName="id", onDelete="SET NULL")
     */
    private $type_transfert;

    /**
     * Véhicule
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookVehicle")
     * @ORM\JoinColumn(name="vehicle", referencedColumnName="id", onDelete="SET NULL")
     */
    private $vehicle;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Transferts
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
     * @return Transferts
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return Transferts
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTypeGroup()
    {
        return $this->type_group;
    }

    /**
     * @param bool $type_group
     * @return Transferts
     */
    public function setTypeGroup($type_group)
    {
        $this->type_group = $type_group;
        return $this;
    }



    /**
     * @return int
     */
    public function getCustomId()
    {
        return $this->custom_id;
    }

    /**
     * @param int $custom_id
     * @return Transferts
     */
    public function setCustomId($custom_id)
    {
        $this->custom_id = $custom_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getExcelTitle()
    {
        return $this->excel_title;
    }

    /**
     * @param string $excel_title
     * @return Transferts
     */
    public function setExcelTitle($excel_title)
    {
        $this->excel_title = $excel_title;
        return $this;
    }

    /**
     * @return string
     */
    public function getExcelDescription()
    {
        return $this->excel_description;
    }

    /**
     * @param string $excel_description
     * @return Transferts
     */
    public function setExcelDescription($excel_description)
    {
        $this->excel_description = $excel_description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * @param mixed $ville
     * @return Transferts
     */
    public function setVille($ville)
    {
        $this->ville = $ville;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDepartureCity()
    {
        return $this->departure_city;
    }

    /**
     * @param mixed $departure_city
     * @return Transferts
     */
    public function setDepartureCity($departure_city)
    {
        $this->departure_city = $departure_city;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCityArrival()
    {
        return $this->city_arrival;
    }

    /**
     * @param mixed $city_arrival
     * @return Transferts
     */
    public function setCityArrival($city_arrival)
    {
        $this->city_arrival = $city_arrival;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumberPassengers()
    {
        return $this->number_passengers;
    }

    /**
     * @param int $number_passengers
     * @return Transferts
     */
    public function setNumberPassengers($number_passengers)
    {
        $this->number_passengers = $number_passengers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeTransfert()
    {
        return $this->type_transfert;
    }

    /**
     * @param mixed $type_transfert
     * @return Transferts
     */
    public function setTypeTransfert($type_transfert)
    {
        $this->type_transfert = $type_transfert;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * @param mixed $vehicle
     * @return Transferts
     */
    public function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;
        return $this;
    }











    public function getClass(){
        return 'Transferts';
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

