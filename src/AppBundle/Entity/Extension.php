<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="extension")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Extension
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
     * @ORM\JoinTable(name="extension_to_files",
     *      joinColumns={@ORM\JoinColumn(name="extension_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="files_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $image_other;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookDomain")
     * @ORM\JoinTable(name="extension_to_domain",
     *      joinColumns={@ORM\JoinColumn(name="extension_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="domain_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $type_domain;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount_days", type="integer", length=11, nullable=true)
     */
    private $amount_days;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookTypeExtension")
     * @ORM\JoinColumn(name="type_extension", referencedColumnName="id", onDelete="SET NULL")
     */
    private $type_extension;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Destination")
     * @ORM\JoinColumn(name="starting_point", referencedColumnName="id", onDelete="SET NULL")
     */
    private $starting_point;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ExtensionToExtensionDestination", mappedBy="extension")
     * @ORM\JoinColumn(name="extension", referencedColumnName="extension")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $extension;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookSeason")
     * @ORM\JoinTable(name="extension_to_season",
     *      joinColumns={@ORM\JoinColumn(name="extension_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="season_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $season;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookTypeRecreation")
     * @ORM\JoinTable(name="extension_to_recreation",
     *      joinColumns={@ORM\JoinColumn(name="extension_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="recreation_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $extension_recreation;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Destination")
     * @ORM\JoinTable(name="extension_content_to_destination",
     *      joinColumns={@ORM\JoinColumn(name="extension_id", referencedColumnName="id", onDelete="cascade")},
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ExtensionToDay", mappedBy="extension")
     * @ORM\JoinColumn(name="day", referencedColumnName="extension")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $day;

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
     * @ORM\JoinTable(name="extension_to_minigroup",
     *      joinColumns={@ORM\JoinColumn(name="extension_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="minigroup_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $extension_minigroup;


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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Extension
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return Extension
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
     * @return Extension
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
     * @return Extension
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
     * @return Extension
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
     * @return Extension
     */
    public function setAmountDays($amount_days)
    {
        $this->amount_days = $amount_days;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeExtension()
    {
        return $this->type_extension;
    }

    /**
     * @param mixed $type_extension
     * @return Extension
     */
    public function setTypeExtension($type_extension)
    {
        $this->type_extension = $type_extension;
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
     * @return Extension
     */
    public function setStartingPoint($starting_point)
    {
        $this->starting_point = $starting_point;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        //return $this->extension;
        $array = array();
        if(!empty($this->extension)) {
            foreach ($this->extension->toArray() as $value) {
                $array[$value->getPosition()] = $value->getDestination();
            }
        }
        return $array;
    }

    /**
     * @param mixed $extension
     * @return Extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
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
     * @return Extension
     */
    public function setSeason($season)
    {
        $this->season = $season;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtensionRecreation()
    {
        return $this->extension_recreation;
    }

    /**
     * @param mixed $extension_recreation
     * @return Extension
     */
    public function setExtensionRecreation($extension_recreation)
    {
        $this->extension_recreation = $extension_recreation;
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
     * @return Extension
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
     * @return Extension
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
     * @return Extension
     */
    public function setDay($day)
    {
        $this->day = $day;
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
     * @return Extension
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
     * @return Extension
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
     * @return Extension
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
     * @return Extension
     */
    public function setMinigroupPrixRubles($minigroup_prix_rubles)
    {
        $this->minigroup_prix_rubles = $minigroup_prix_rubles;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtensionMinigroup()
    {
        return $this->extension_minigroup;
    }

    /**
     * @param mixed $extension_minigroup
     * @return Extension
     */
    public function setExtensionMinigroup($extension_minigroup)
    {
        $this->extension_minigroup = $extension_minigroup;
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
     * @return Extension
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
     * @return Extension
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
     * @return Extension
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
     * @return Extension
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
     * @return Extension
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
     * @return Extension
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
     * @return Extension
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
     * @return Extension
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
     * @return Extension
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
     * @return Extension
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
	 * @return Extension
	 */
	public function setFavorite($favorite)
	{
		$this->favorite = $favorite;
		return $this;
	}



    public function getPrice(){
        return $this->minigroup_prix_rubles;
    }

    public function getClass(){
        return 'Extension';
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

