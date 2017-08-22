<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="voyage")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Voyage
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
     * Label du programme (automatique)
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=TRUE)
     */
    private $label;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
     * @ORM\JoinColumn(name="image_background", referencedColumnName="id", onDelete="SET NULL")
     */
    private $image_background;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
     * @ORM\JoinColumn(name="image_thumbnail", referencedColumnName="id", onDelete="SET NULL")
     */
    private $image_thumbnail;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Files")
     * @ORM\JoinTable(name="voyage_to_files",
     *      joinColumns={@ORM\JoinColumn(name="voyage_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="files_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $image_other;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookDomain")
     * @ORM\JoinTable(name="voyage_to_domain",
     *      joinColumns={@ORM\JoinColumn(name="voyage_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="domain_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $type_domain;

    /**
     * Nombre de jours fixes
     * @var integer
     *
     * @ORM\Column(name="amount_days", type="integer", length=11, nullable=true)
     */
    private $amount_days;

    /**
     * Nombre de jours additionnels
     * @var integer
     *
     * @ORM\Column(name="extra_days", type="integer", length=11, nullable=true)
     */
    private $extra_days;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookTypeVoyage")
     * @ORM\JoinColumn(name="type_voyage", referencedColumnName="id", onDelete="SET NULL")
     */
    private $type_voyage;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Destination")
     * @ORM\JoinColumn(name="starting_point", referencedColumnName="id", onDelete="SET NULL")
     */
    private $starting_point;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\VoyageToVoyageDestination", mappedBy="voyage")
     * @ORM\JoinColumn(name="voyage", referencedColumnName="voyage")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $voyage;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookSeason")
     * @ORM\JoinTable(name="voyage_to_season",
     *      joinColumns={@ORM\JoinColumn(name="voyage_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="season_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $season;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookTypeRecreation")
     * @ORM\JoinTable(name="voyage_to_recreation",
     *      joinColumns={@ORM\JoinColumn(name="voyage_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="recreation_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $voyage_recreation;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Destination")
     * @ORM\JoinTable(name="voyage_content_to_destination",
     *      joinColumns={@ORM\JoinColumn(name="voyage_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="destination_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $related_content;

    /**
     * Many Users have One Address.
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location" )
     * @ORM\JoinColumn(name="location", referencedColumnName="id", onDelete="SET NULL")
     */
    private $location;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\VoyageToDay", mappedBy="voyage")
     * @ORM\JoinColumn(name="day", referencedColumnName="voyage")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $day;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\VoyageToExtraDay", mappedBy="voyage")
     * @ORM\JoinColumn(name="day", referencedColumnName="voyage")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $extra_days_block;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mini_groupe", type="boolean", length=1)
     */
    private $mini_groupe = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="promoted_fronpage", type="boolean", length=1)
     */
    private $promoted_fronpage = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="minigroup_promotion_weight", type="integer", length=11, nullable=true)
     */
    private $minigroup_promotion_weight;

    /**
     * @var integer
     *
     * @ORM\Column(name="minigroup_prix_euros", type="integer", length=11, nullable=true)
     */
    private $minigroup_prix_euros;

    /**
     * @var integer
     *
     * @ORM\Column(name="minigroup_prix_rubles", type="integer", length=11, nullable=true)
     */
    private $minigroup_prix_rubles;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Minigroup")
     * @ORM\JoinTable(name="voyage_to_minigroup",
     *      joinColumns={@ORM\JoinColumn(name="voyage_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="minigroup_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $voyage_minigroup;

    /**
     * Produit tarifé
     * @var boolean
     *
     * @ORM\Column(name="tariffed_product", type="boolean", length=1)
     */
    private $tariffed_product = 0;

    /**
     * Flexibilité du prix
     * @var boolean
     *
     * @ORM\Column(name="price_flexibility", type="boolean", length=1)
     */
    private $price_flexibility = 0;

    /**
     * Produit 365 Lié
     * @var string
     *
     * @ORM\Column(name="product_365", type="string", length=255, nullable=true)
     */
    private $product_365;

    /**
     * External booking
     * @var boolean
     *
     * @ORM\Column(name="external_booking", type="boolean", length=1)
     */
    private $external_booking = 0;

    /**
     * External booking link
     * @var string
     *
     * @ORM\Column(name="external_booking_link", type="string", length=255, nullable=true)
     */
    private $external_booking_link;

    /**
     * Tarif affiché
     * @var string
     *
     * @ORM\Column(name="price_displayed", type="string", length=255, nullable=true)
     */
    private $price_displayed;

    /**
     * Prix en euro
     * @var integer
     *
     * @ORM\Column(name="prix_euro", type="integer", length=11, nullable=true)
     */
    private $prix_euro;

    /**
     * Prix en rouble
     * @var integer
     *
     * @ORM\Column(name="prix_rouble", type="integer", length=11, nullable=true)
     */
    private $prix_rouble;

    /**
     * Mise à jour auto du tarif affiché
     * @var boolean
     *
     * @ORM\Column(name="auto_update_price", type="boolean", length=1)
     */
    private $auto_update_price = 0;

	/**
	 * @var boolean
	 * @ORM\Column(name="favorite", type="boolean", length=1)
	 */
	private $favorite;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Assurance")
     * @ORM\JoinColumn(name="assurance", referencedColumnName="id", onDelete="SET NULL")
     */
    private $assurance;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Visa")
     * @ORM\JoinColumn(name="visa", referencedColumnName="id", onDelete="SET NULL")
     */
    private $visa;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\CombinationHotels")
     * @ORM\JoinTable(name="voyage_to_combination_hotel",
     *      joinColumns={@ORM\JoinColumn(name="voyage_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="combination_hotel_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $combination_hotel;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Voyage
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
     * @return Voyage
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
     * @return Voyage
     */
    public function setImageBackground($image_background)
    {
        $this->image_background = $image_background;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImageThumbnail()
    {
        return $this->image_thumbnail;
    }

    /**
     * @param mixed $image_thumbnail
     * @return Voyage
     */
    public function setImageThumbnail($image_thumbnail)
    {
        $this->image_thumbnail = $image_thumbnail;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImageOther()
    {
        return $this->image_other;
    }

    /**
     * @param mixed $image_other
     * @return Voyage
     */
    public function setImageOther($image_other)
    {
        $this->image_other = $image_other;
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
     * @return Voyage
     */
    public function setTypeDomain($type_domain)
    {
        $this->type_domain = $type_domain;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAmountDays()
    {
        return $this->amount_days;
    }

    /**
     * @param mixed $amount_days
     * @return Voyage
     */
    public function setAmountDays($amount_days)
    {
        $this->amount_days = $amount_days;
        return $this;
    }

    /**
     * @return int
     */
    public function getExtraDays()
    {
        return $this->extra_days;
    }

    /**
     * @param int $extra_days
     * @return Voyage
     */
    public function setExtraDays($extra_days)
    {
        $this->extra_days = $extra_days;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getTypeVoyage()
    {
        return $this->type_voyage;
    }

    /**
     * @param mixed $type_voyage
     * @return Voyage
     */
    public function setTypeVoyage($type_voyage)
    {
        $this->type_voyage = $type_voyage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartingPoint()
    {
        return $this->starting_point;
    }

    /**
     * @param mixed $starting_point
     * @return Voyage
     */
    public function setStartingPoint($starting_point)
    {
        $this->starting_point = $starting_point;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVoyage()
    {
        //return $this->voyage;
        $array = array();
        if(!empty($this->voyage)) {
            foreach ($this->voyage->toArray() as $value) {
                $array[$value->getPosition()] = $value->getDestination();
            }
        }
        return $array;
    }

    /**
     * @param mixed $voyage
     * @return Voyage
     */
    public function setVoyage($voyage)
    {
        $this->voyage = $voyage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param mixed $season
     * @return Voyage
     */
    public function setSeason($season)
    {
        $this->season = $season;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVoyageRecreation()
    {
        return $this->voyage_recreation;
    }

    /**
     * @param mixed $voyage_recreation
     * @return Voyage
     */
    public function setVoyageRecreation($voyage_recreation)
    {
        $this->voyage_recreation = $voyage_recreation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRelatedContent()
    {
        return $this->related_content;
    }

    /**
     * @param mixed $related_content
     * @return Voyage
     */
    public function setRelatedContent($related_content)
    {
        $this->related_content = $related_content;
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
     * @return Voyage
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDay()
    {
        //print_r(get_class_methods($this->day->toArray()[0])); exit;
        $array = array();
        if(!empty($this->day)) {
            foreach ($this->day->toArray() as $value) {
                $array[$value->getPosition()] = $value->getDay();
            }
        }
        return $array;
    }

    /**
     * @param mixed $day
     * @return Voyage
     */
    public function setDay($day)
    {
        $this->day = $day;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtraDaysBlock()
    {
        $array = array();
        if(!empty($this->extra_days_block)) {
            foreach ($this->extra_days_block->toArray() as $value) {
                $array[$value->getPosition()] = $value->getDay();
            }
        }
        return $array;
    }

    /**
     * @param mixed $extra_days_block
     * @return Voyage
     */
    public function setExtraDaysBlock($extra_days_block)
    {
        $this->extra_days_block = $extra_days_block;
        return $this;
    }



    /**
     * @return bool
     */
    public function isMiniGroupe()
    {
        return $this->mini_groupe;
    }

    /**
     * @param bool $mini_groupe
     * @return Voyage
     */
    public function setMiniGroupe($mini_groupe)
    {
        $this->mini_groupe = $mini_groupe;
        return $this;
    }


    /**
     * @return int
     */
    public function getMinigroupPromotionWeight()
    {
        return $this->minigroup_promotion_weight;
    }

    /**
     * @param int $minigroup_promotion_weight
     * @return Voyage
     */
    public function setMinigroupPromotionWeight($minigroup_promotion_weight)
    {
        $this->minigroup_promotion_weight = $minigroup_promotion_weight;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinigroupPrixEuros()
    {
        return $this->minigroup_prix_euros;
    }

    /**
     * @param int $minigroup_prix_euros
     * @return Voyage
     */
    public function setMinigroupPrixEuros($minigroup_prix_euros)
    {
        $this->minigroup_prix_euros = $minigroup_prix_euros;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinigroupPrixRubles()
    {
        return $this->minigroup_prix_rubles;
    }

    /**
     * @param int $minigroup_prix_rubles
     * @return Voyage
     */
    public function setMinigroupPrixRubles($minigroup_prix_rubles)
    {
        $this->minigroup_prix_rubles = $minigroup_prix_rubles;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVoyageMinigroup()
    {
        return $this->voyage_minigroup;
    }

    /**
     * @param mixed $voyage_minigroup
     * @return Voyage
     */
    public function setVoyageMinigroup($voyage_minigroup)
    {
        $this->voyage_minigroup = $voyage_minigroup;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPromotedFronpage()
    {
        return $this->promoted_fronpage;
    }

    /**
     * @param bool $promoted_fronpage
     * @return Voyage
     */
    public function setPromotedFronpage($promoted_fronpage)
    {
        $this->promoted_fronpage = $promoted_fronpage;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTariffedProduct()
    {
        return $this->tariffed_product;
    }

    /**
     * @param bool $tariffed_product
     * @return Voyage
     */
    public function setTariffedProduct($tariffed_product)
    {
        $this->tariffed_product = $tariffed_product;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPriceFlexibility()
    {
        return $this->price_flexibility;
    }

    /**
     * @param bool $price_flexibility
     * @return Voyage
     */
    public function setPriceFlexibility($price_flexibility)
    {
        $this->price_flexibility = $price_flexibility;
        return $this;
    }

    /**
     * @return string
     */
    public function getProduct365()
    {
        return $this->product_365;
    }

    /**
     * @param string $product_365
     * @return Voyage
     */
    public function setProduct365($product_365)
    {
        $this->product_365 = $product_365;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExternalBooking()
    {
        return $this->external_booking;
    }

    /**
     * @param bool $external_booking
     * @return Voyage
     */
    public function setExternalBooking($external_booking)
    {
        $this->external_booking = $external_booking;
        return $this;
    }

    /**
     * @return string
     */
    public function getExternalBookingLink()
    {
        return $this->external_booking_link;
    }

    /**
     * @param string $external_booking_link
     * @return Voyage
     */
    public function setExternalBookingLink($external_booking_link)
    {
        $this->external_booking_link = $external_booking_link;
        return $this;
    }

    /**
     * @return string
     */
    public function getPriceDisplayed()
    {
        return $this->price_displayed;
    }

    /**
     * @param string $price_displayed
     * @return Voyage
     */
    public function setPriceDisplayed($price_displayed)
    {
        $this->price_displayed = $price_displayed;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrixEuro()
    {
        return $this->prix_euro;
    }

    /**
     * @param int $prix_euro
     * @return Voyage
     */
    public function setPrixEuro($prix_euro)
    {
        $this->prix_euro = $prix_euro;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrixRouble()
    {
        return $this->prix_rouble;
    }

    /**
     * @param int $prix_rouble
     * @return Voyage
     */
    public function setPrixRouble($prix_rouble)
    {
        $this->prix_rouble = $prix_rouble;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAutoUpdatePrice()
    {
        return $this->auto_update_price;
    }

    /**
     * @param bool $auto_update_price
     * @return Voyage
     */
    public function setAutoUpdatePrice($auto_update_price)
    {
        $this->auto_update_price = $auto_update_price;
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
	 * @return Voyage
	 */
	public function setFavorite($favorite)
	{
		$this->favorite = $favorite;
		return $this;
	}

	public function getPrice(){
		return $this->minigroup_prix_rubles;
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
     * @return Voyage
     */
    public function setAssurance($assurance)
    {
        $this->assurance = $assurance;
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
     * @return Voyage
     */
    public function setVisa($visa)
    {
        $this->visa = $visa;
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
     * @return Voyage
     */
    public function setCombinationHotel($combination_hotel)
    {
        $this->combination_hotel = $combination_hotel;
        return $this;
    }













    public function getClass(){
        return 'Voyage';
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

