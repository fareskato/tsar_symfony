<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * VisitTranslation
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class TransfertsTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", length=1)
     */
    private $active = 0;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="commentaires", type="text", nullable=TRUE)
     */
    private $commentaires;

    /**
     * Informations supplémentaires
     * @var string
     *
     * @ORM\Column(name="informations_supplementaires", type="text", nullable=TRUE)
     */
    private $informations_supplementaires;

    /**
     *  Information utile
     * @var string
     *
     * @ORM\Column(name="information_utile", type="text", nullable=TRUE)
     */
    private $information_utile;

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return TransfertsTranslation
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
     * @return TransfertsTranslation
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
     * @return TransfertsTranslation
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
     * @return TransfertsTranslation
     */
    public function setInformationUtile($information_utile)
    {
        $this->information_utile = $information_utile;
        return $this;
    }



}

