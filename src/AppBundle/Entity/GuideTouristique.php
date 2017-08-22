<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="guide_touristique")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class GuideTouristique
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
     * Langue
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookLangue")
     * @ORM\JoinColumn(name="langue", referencedColumnName="id", onDelete="SET NULL")
     */
    private $langue;

    /**
     * Type de guide
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookTypeGuide")
     * @ORM\JoinColumn(name="type_guide", referencedColumnName="id", onDelete="SET NULL")
     */
    private $type_guide;

    /**
     * Durée
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookDuree")
     * @ORM\JoinColumn(name="duree", referencedColumnName="id", onDelete="SET NULL")
     */
    private $duree;

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

    /**
     * @return mixed
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * @param mixed $ville
     * @return GuideTouristique
     */
    public function setVille($ville)
    {
        $this->ville = $ville;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * @param mixed $langue
     * @return GuideTouristique
     */
    public function setLangue($langue)
    {
        $this->langue = $langue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeGuide()
    {
        return $this->type_guide;
    }

    /**
     * @param mixed $type_guide
     * @return GuideTouristique
     */
    public function setTypeGuide($type_guide)
    {
        $this->type_guide = $type_guide;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * @param mixed $duree
     * @return GuideTouristique
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;
        return $this;
    }













    public function getClass(){
        return 'GuideTouristique';
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

