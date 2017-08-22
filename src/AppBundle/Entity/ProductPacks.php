<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="product_packs")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class ProductPacks
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
     * Ville.
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location")
     * @ORM\JoinColumn(name="ville", referencedColumnName="id", onDelete="SET NULL")
     */
    private $ville;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Transferts")
     * @ORM\JoinColumn(name="transfer_one", referencedColumnName="id", onDelete="SET NULL")
     */
    private $transfer_one;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Transferts")
     * @ORM\JoinColumn(name="transfer_two", referencedColumnName="id", onDelete="SET NULL")
     */
    private $transfer_two;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Transferts")
     * @ORM\JoinColumn(name="transfer_three", referencedColumnName="id", onDelete="SET NULL")
     */
    private $transfer_three;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProductPacksToProduct", mappedBy="product_packs")
     * @ORM\JoinColumn(name="ajouter_produit", referencedColumnName="product_packs")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $ajouter_produit;

    public function __construct()
    {
        $this->ajouter_produit = new ArrayCollection();
    }
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ProductPacks
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
     * @return ProductPacks
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
     * @return ProductPacks
     */
    public function setVille($ville)
    {
        $this->ville = $ville;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTransferOne()
    {
        return $this->transfer_one;
    }

    /**
     * @param mixed $transfer_one
     * @return ProductPacks
     */
    public function setTransferOne($transfer_one)
    {
        $this->transfer_one = $transfer_one;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTransferTwo()
    {
        return $this->transfer_two;
    }

    /**
     * @param mixed $transfer_two
     * @return ProductPacks
     */
    public function setTransferTwo($transfer_two)
    {
        $this->transfer_two = $transfer_two;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTransferThree()
    {
        return $this->transfer_three;
    }

    /**
     * @param mixed $transfer_three
     * @return ProductPacks
     */
    public function setTransferThree($transfer_three)
    {
        $this->transfer_three = $transfer_three;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAjouterProduit()
    {
        $array=array();
        foreach($this->ajouter_produit->toArray() as $value){
            if($value->getVisa()){
                $array[$value->getPosition()]=$value->getVisa();
            }elseif($value->getTrain()){
                $array[$value->getPosition()]=$value->getTrain();
            }elseif($value->getAssurance()){
                $array[$value->getPosition()]=$value->getAssurance();
            }elseif($value->getTicketsDeMusee()){
                $array[$value->getPosition()]=$value->getTicketsDeMusee();
            }elseif($value->getGuideTouristique()){
                $array[$value->getPosition()]=$value->getGuideTouristique();
            }elseif($value->getAutreProduit()){
                $array[$value->getPosition()]=$value->getAutreProduit();
            }
        }
        return $array;
    }

    /**
     * @param mixed $ajouter_produit
     * @return ProductPacks
     */
    public function setAjouterProduit($ajouter_produit)
    {
        $this->ajouter_produit = $ajouter_produit;
        return $this;
    }










    public function getClass(){
        return 'ProductPacks';
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

