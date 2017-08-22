<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="assurance")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Assurance
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
     * Type d'assurance
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookTypeDassurance")
     * @ORM\JoinColumn(name="type_dassurance", referencedColumnName="id", onDelete="SET NULL")
     */
    private $type_dassurance;

    /**
     * Durée de l'assurance
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookDurationInsurance")
     * @ORM\JoinColumn(name="duration_insurance", referencedColumnName="id", onDelete="SET NULL")
     */
    private $duration_insurance;

    /**
     * Prix total du séjour
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookTotalPriceStay")
     * @ORM\JoinColumn(name="total_price_stay", referencedColumnName="id", onDelete="SET NULL")
     */
    private $total_price_stay;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Assurance
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
     * @return Assurance
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
     * @return Assurance
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
     * @return Assurance
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
     * @return Assurance
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
     * @return Assurance
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
     * @return Assurance
     */
    public function setExcelDescription($excel_description)
    {
        $this->excel_description = $excel_description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeDassurance()
    {
        return $this->type_dassurance;
    }

    /**
     * @param mixed $type_dassurance
     * @return Assurance
     */
    public function setTypeDassurance($type_dassurance)
    {
        $this->type_dassurance = $type_dassurance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDurationInsurance()
    {
        return $this->duration_insurance;
    }

    /**
     * @param mixed $book_duration_insurance
     * @return Assurance
     */
    public function setDurationInsurance($book_duration_insurance)
    {
        $this->duration_insurance = $book_duration_insurance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalPriceStay()
    {
        return $this->total_price_stay;
    }

    /**
     * @param mixed $total_price_stay
     * @return Assurance
     */
    public function setTotalPriceStay($total_price_stay)
    {
        $this->total_price_stay = $total_price_stay;
        return $this;
    }












    public function getClass(){
        return 'Assurance';
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

