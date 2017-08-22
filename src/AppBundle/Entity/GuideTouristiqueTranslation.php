<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * VisaTranslation
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class GuideTouristiqueTranslation
{
    use ORMBehaviors\Translatable\Translation;



    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", length=1)
     */
    private $active = 0;

    /**
     * Nom du visa
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=TRUE)
     */
    private $name;

    /**
     * Informations supplémentaires
     * @var string
     *
     * @ORM\Column(name="informations_supplementaires", type="text", nullable=TRUE)
     */
    private $informations_supplementaires;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="commentaires", type="text", nullable=TRUE)
     */
    private $commentaires;

    /**
     *  Information utile
     * @var string
     *
     * @ORM\Column(name="information_utile", type="text", nullable=TRUE)
     */
    private $information_utile;


     /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return VisitTranslation
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return VisaTranslation
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }

    /**
     * @param string $commentaires
     * @return VisaTranslation
     */
    public function setCommentaires($commentaires)
    {
        $this->commentaires = $commentaires;
        return $this;
    }

    /**
     * @return string
     */
    public function getInformationsSupplementaires()
    {
        return $this->informations_supplementaires;
    }

    /**
     * @param string $informations_supplementaires
     * @return VisaTranslation
     */
    public function setInformationsSupplementaires($informations_supplementaires)
    {
        $this->informations_supplementaires = $informations_supplementaires;
        return $this;
    }

    /**
     * @return string
     */
    public function getInformationUtile()
    {
        return $this->information_utile;
    }

    /**
     * @param string $information_utile
     * @return VisaTranslation
     */
    public function setInformationUtile($information_utile)
    {
        $this->information_utile = $information_utile;
        return $this;
    }


}

