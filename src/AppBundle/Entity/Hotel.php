<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Hotel
 *
 * @ORM\Table(name="hotel")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Hotel
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
     * Label
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=TRUE)
     */
    private $label;

    /**
     * Catégorie de chambre
     * @var string
     *
     * @ORM\Column(name="categorie", type="string", length=255, nullable=TRUE)
     */
    private $categorie;

	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookDomain")
	 * @ORM\JoinTable(name="hotel_to_domain",
	 *      joinColumns={@ORM\JoinColumn(name="hotel_id", referencedColumnName="id", onDelete="cascade")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="domain_id", referencedColumnName="id", onDelete="cascade")}
	 *      )
	 */
	private $type_domain;


	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Destination")
	 * @ORM\JoinTable(name="hotel_to_destination",
	 *      joinColumns={@ORM\JoinColumn(name="hotel_id", referencedColumnName="id", onDelete="cascade")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="destination_id", referencedColumnName="id", onDelete="cascade")}
	 *      )
	 */
	private $destination;

	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookMetro")
	 * @ORM\JoinTable(name="hotel_to_metro",
	 *      joinColumns={@ORM\JoinColumn(name="hotel_id", referencedColumnName="id", onDelete="cascade")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="metro_id", referencedColumnName="id", onDelete="cascade")}
	 *      )
	 */
	private $metro;

	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookHotelStars")
	 * @ORM\JoinTable(name="hotel_to_hotelstars",
	 *      joinColumns={@ORM\JoinColumn(name="hotel_id", referencedColumnName="id", onDelete="cascade")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="hotelstars_id", referencedColumnName="id", onDelete="cascade")}
	 *      )
	 */
	private $hotel_stars;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookServices")
     * @ORM\JoinTable(name="hotel_to_service",
     *      joinColumns={@ORM\JoinColumn(name="hotel_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="service_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $hotel_service;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookAccessInternet")
	 * @ORM\JoinColumn(name="hotel_internet", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $hotel_internet;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookEtiquette")
	 * @ORM\JoinColumn(name="etiquette", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $etiquette;

	/**
	 * @var boolean
	 * @ORM\Column(name="favorite", type="boolean", length=1)
	 */
	private $favorite;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="number_of_rooms", type="integer", length=11, nullable=true)
	 */
	private $number_of_rooms;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
	 * @ORM\JoinColumn(name="image", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $image;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
	 * @ORM\JoinColumn(name="image_background", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $image_background;


	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
	 * @ORM\JoinColumn(name="image_miniature", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $image_miniature;

	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Files")
	 * @ORM\JoinTable(name="hotel_to_files",
	 *      joinColumns={@ORM\JoinColumn(name="hotel_id", referencedColumnName="id", onDelete="cascade")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="files_id", referencedColumnName="id", onDelete="cascade")}
	 *      )
	 */
	private $image_others;

	/**
	 * Many Users have One Address.
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location")
	 * @ORM\JoinColumn(name="location", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $location;

    /**
     * Custom id
     * @var integer
     *
     * @ORM\Column(name="excel_custom_id", type="integer", nullable=TRUE)
     */
    private $excel_custom_id;

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
     * @return Hotel
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
     * @return Hotel
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * @param string $categorie
     * @return Hotel
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getTypeDomain()
    {
        return $this->type_domain;
    }

    /**
     * @param mixed $type_domain
     * @return Hotel
     */
    public function setTypeDomain($type_domain)
    {
        $this->type_domain = $type_domain;
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
     * @return Hotel
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMetro()
    {
        return $this->metro;
    }

    /**
     * @param mixed $metro
     * @return Hotel
     */
    public function setMetro($metro)
    {
        $this->metro = $metro;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHotelStars()
    {
        return $this->hotel_stars;
    }

    /**
     * @param mixed $hotel_stars
     * @return Hotel
     */
    public function setHotelStars($hotel_stars)
    {
        $this->hotel_stars = $hotel_stars;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHotelService()
    {
        return $this->hotel_service;
    }

    /**
     * @param mixed $hotel_service
     * @return Hotel
     */
    public function setHotelService($hotel_service)
    {
        $this->hotel_service = $hotel_service;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHotelInternet()
    {
        return $this->hotel_internet;
    }

    /**
     * @param mixed $hotel_internet
     * @return Hotel
     */
    public function setHotelInternet($hotel_internet)
    {
        $this->hotel_internet = $hotel_internet;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEtiquette()
    {
        return $this->etiquette;
    }

    /**
     * @param mixed $etiquette
     * @return Hotel
     */
    public function setEtiquette($etiquette)
    {
        $this->etiquette = $etiquette;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfRooms()
    {
        return $this->number_of_rooms;
    }

    /**
     * @param int $number_of_rooms
     * @return Hotel
     */
    public function setNumberOfRooms($number_of_rooms)
    {
        $this->number_of_rooms = $number_of_rooms;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     * @return Hotel
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImageBackground()
    {
        return $this->image_background;
    }

    /**
     * @param mixed $image_background
     * @return Hotel
     */
    public function setImageBackground($image_background)
    {
        $this->image_background = $image_background;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImageMiniature()
    {
        return $this->image_miniature;
    }

    /**
     * @param mixed $image_miniature
     * @return Hotel
     */
    public function setImageMiniature($image_miniature)
    {
        $this->image_miniature = $image_miniature;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImageOther()
    {
        return $this->image_others;
    }

    /**
     * @param mixed $image_others
     * @return Hotel
     */
    public function setImageOthers($image_others)
    {
        $this->image_others = $image_others;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     * @return Hotel
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

	/**
	 * @return bool
	 */
	public function isFavorite()
	{
		return $this->favorite;
	}



	/**
	 * @param bool $favorite
	 * @return Hotel
	 */
	public function setFavorite($favorite)
	{
		$this->favorite = $favorite;
		return $this;
	}

    /**
     * @return int
     */
    public function getExcelCustomId()
    {
        return $this->excel_custom_id;
    }

    /**
     * @param int $excel_custom_id
     * @return Hotel
     */
    public function setExcelCustomId($excel_custom_id)
    {
        $this->excel_custom_id = $excel_custom_id;
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
     * @return Hotel
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
     * @return Hotel
     */
    public function setExcelDescription($excel_description)
    {
        $this->excel_description = $excel_description;
        return $this;
    }





    public function getClass(){
        return 'Hotel';
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