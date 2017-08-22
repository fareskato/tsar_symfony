<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="combination_hotels")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class CombinationHotels
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Hotel")
     * @ORM\JoinTable(name="combination_hotel_to_hotel",
     *      joinColumns={@ORM\JoinColumn(name="combination_hotel_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="hotel_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $combination_hotel;

    /**
     * Offre spéciale
     * @var boolean
     *
     * @ORM\Column(name="special_offer", type="boolean", length=1)
     */
    private $special_offer = 0;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return CombinationHotels
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
     * @return CombinationHotels
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
     * @return CombinationHotels
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
     * @return CombinationHotels
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
     * @return CombinationHotels
     */
    public function setExcelDescription($excel_description)
    {
        $this->excel_description = $excel_description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCombinationHotel()
    {
        return $this->combination_hotel;
    }

    /**
     * @param mixed $combination_hotel
     * @return CombinationHotels
     */
    public function setCombinationHotel($combination_hotel)
    {
        $this->combination_hotel = $combination_hotel;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSpecialOffer()
    {
        return $this->special_offer;
    }

    /**
     * @param bool $special_offer
     * @return CombinationHotels
     */
    public function setSpecialOffer($special_offer)
    {
        $this->special_offer = $special_offer;
        return $this;
    }













    public function getClass(){
        return 'CombinationHotels';
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

