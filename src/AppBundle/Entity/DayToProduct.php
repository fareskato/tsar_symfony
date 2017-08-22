<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Tag
 *
 * @ORM\Table(name="day_to_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class DayToProduct
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Day")
     * @ORM\JoinColumn(name="day", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $day;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProductPacks")
     * @ORM\JoinColumn(name="product_packs", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $product_packs;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Visa")
     * @ORM\JoinColumn(name="visa", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $visa;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Train")
     * @ORM\JoinColumn(name="train", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $train;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Assurance")
     * @ORM\JoinColumn(name="assurance", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $assurance;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TicketsDeMusee")
     * @ORM\JoinColumn(name="tickets_de_musee", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $tickets_de_musee;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\GuideTouristique")
     * @ORM\JoinColumn(name="guide_touristique", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $guide_touristique;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AutreProduct")
     * @ORM\JoinColumn(name="autre_produit", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $autre_produit;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", length=11, nullable=true)
     */
    protected $position;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ProductPacksToProduct
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     * @return DayToProduct
     */
    public function setDay($day)
    {
        $this->day = $day;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getProductPacks()
    {
        return $this->product_packs;
    }

    /**
     * @param mixed $product_packs
     * @return ProductPacksToProduct
     */
    public function setProductPacks($product_packs)
    {
        $this->product_packs = $product_packs;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisa()
    {
        return $this->visa;
    }

    /**
     * @param mixed $visa
     * @return ProductPacksToProduct
     */
    public function setVisa($visa)
    {
        $this->visa = $visa;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTrain()
    {
        return $this->train;
    }

    /**
     * @param mixed $train
     * @return ProductPacksToProduct
     */
    public function setTrain($train)
    {
        $this->train = $train;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAssurance()
    {
        return $this->assurance;
    }

    /**
     * @param mixed $assurance
     * @return ProductPacksToProduct
     */
    public function setAssurance($assurance)
    {
        $this->assurance = $assurance;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return ProductPacksToProduct
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTicketsDeMusee()
    {
        return $this->tickets_de_musee;
    }

    /**
     * @param mixed $tickets_de_musee
     * @return ProductPacksToProduct
     */
    public function setTicketsDeMusee($tickets_de_musee)
    {
        $this->tickets_de_musee = $tickets_de_musee;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGuideTouristique()
    {
        return $this->guide_touristique;
    }

    /**
     * @param mixed $guide_touristique
     * @return ProductPacksToProduct
     */
    public function setGuideTouristique($guide_touristique)
    {
        $this->guide_touristique = $guide_touristique;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAutreProduit()
    {
        return $this->autre_produit;
    }

    /**
     * @param mixed $autre_produit
     * @return ProductPacksToProduct
     */
    public function setAutreProduit($autre_produit)
    {
        $this->autre_produit = $autre_produit;
        return $this;
    }
}

