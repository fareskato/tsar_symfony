<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="train")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Train
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookTrainPeriod")
     * @ORM\JoinColumn(name="period", referencedColumnName="id", onDelete="SET NULL")
     */
    private $period;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookTrainCategorie")
     * @ORM\JoinColumn(name="categorie", referencedColumnName="id", onDelete="SET NULL")
     */
    private $categorie;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookTrainTypeCustomer")
     * @ORM\JoinColumn(name="type_customer", referencedColumnName="id", onDelete="SET NULL")
     */
    private $type_customer;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Train
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
     * @return Train
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
     * @return Train
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
     * @return Train
     */
    public function setTypeGroup($type_group)
    {
        $this->type_group = $type_group;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param mixed $period
     * @return Train
     */
    public function setPeriod($period)
    {
        $this->period = $period;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * @param mixed $categorie
     * @return Train
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeCustomer()
    {
        return $this->type_customer;
    }

    /**
     * @param mixed $type_customer
     * @return Train
     */
    public function setTypeCustomer($type_customer)
    {
        $this->type_customer = $type_customer;
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
     * @return Train
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
     * @return Train
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
     * @return Train
     */
    public function setExcelDescription($excel_description)
    {
        $this->excel_description = $excel_description;
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
     * @return Train
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
     * @return Train
     */
    public function setCityArrival($city_arrival)
    {
        $this->city_arrival = $city_arrival;
        return $this;
    }












    public function getClass(){
        return 'Train';
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

