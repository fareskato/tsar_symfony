<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="visa")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Visa
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookTypeVisa")
     * @ORM\JoinColumn(name="type_visa", referencedColumnName="id", onDelete="SET NULL")
     */
    private $type_visa;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookVisaUrgence")
     * @ORM\JoinColumn(name="urgence", referencedColumnName="id", onDelete="SET NULL")
     */
    private $urgence;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookVisaPeriod")
     * @ORM\JoinColumn(name="visa_period", referencedColumnName="id", onDelete="SET NULL")
     */
    private $visa_period;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Visa
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
     * @return Visa
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getTypeVisa()
    {
        return $this->type_visa;
    }

    /**
     * @param mixed $type_visa
     * @return Visa
     */
    public function setTypeVisa($type_visa)
    {
        $this->type_visa = $type_visa;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrgence()
    {
        return $this->urgence;
    }

    /**
     * @param mixed $urgence
     * @return Visa
     */
    public function setUrgence($urgence)
    {
        $this->urgence = $urgence;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisaPeriod()
    {
        return $this->visa_period;
    }

    /**
     * @param mixed $visa_period
     * @return Visa
     */
    public function setVisaPeriod($visa_period)
    {
        $this->visa_period = $visa_period;
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
     * @return Visa
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
     * @return Visa
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
     * @return Visa
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
     * @return Visa
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
     * @return Visa
     */
    public function setExcelDescription($excel_description)
    {
        $this->excel_description = $excel_description;
        return $this;
    }










    public function getClass(){
        return 'Visa';
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

