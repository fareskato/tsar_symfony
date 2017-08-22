<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
/**
 *
 * @ORM\Table(name="day")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Day
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
     * Label du bloc-jour (automatique)
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=TRUE)
     */
    private $label;

	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Files")
	 * @ORM\JoinTable(name="day_to_files",
	 *      joinColumns={@ORM\JoinColumn(name="day_id", referencedColumnName="id", onDelete="cascade")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="files_id", referencedColumnName="id", onDelete="cascade")}
	 *      )
	 */
    private $images;

	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Destination")
	 * @ORM\JoinTable(name="day_to_destination",
	 *      joinColumns={@ORM\JoinColumn(name="day_id", referencedColumnName="id", onDelete="cascade")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="destination_id", referencedColumnName="id", onDelete="cascade")}
	 *      )
	 */
	private $destination;


	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Hotel")
	 * @ORM\JoinTable(name="day_to_hotel",
	 *      joinColumns={@ORM\JoinColumn(name="day_id", referencedColumnName="id", onDelete="cascade")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="hotel_id", referencedColumnName="id", onDelete="cascade")}
	 *      )
	 */
	private $hotel;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\DayToProduct", mappedBy="day")
     * @ORM\JoinColumn(name="day_produit", referencedColumnName="day")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $day_produit;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Day")
     * @ORM\JoinTable(name="alternative_day_to_day",
     *      joinColumns={@ORM\JoinColumn(name="alternative_day_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="day_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $alternative_day;

    public function __construct()
    {
        $this->day_produit = new ArrayCollection();
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
     * @return Day
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
     * @return Day
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $images
     * @return Day
     */
    public function setImages($images)
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param mixed $destination
     * @return Day
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHotel()
    {
        return $this->hotel;
    }

    /**
     * @param mixed $hotel
     * @return Day
     */
    public function setHotel($hotel)
    {
        $this->hotel = $hotel;
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
     * @return Day
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
     * @return Day
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
     * @return Day
     */
    public function setTransferThree($transfer_three)
    {
        $this->transfer_three = $transfer_three;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDayProduit()
    {
        $array=array();
        foreach($this->day_produit->toArray() as $value){
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
            }elseif($value->getProductPacks()){
                $array[$value->getPosition()]=$value->getProductPacks();
            }
        }
        return $array;
    }

    /**
     * @param mixed $day_produit
     * @return Day
     */
    public function setDayProduit($day_produit)
    {
        $this->day_produit = $day_produit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAlternativeDay()
    {
        return $this->alternative_day;
    }

    /**
     * @param mixed $alternative_day
     * @return Day
     */
    public function setAlternativeDay($alternative_day)
    {
        $this->alternative_day = $alternative_day;
        return $this;
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

