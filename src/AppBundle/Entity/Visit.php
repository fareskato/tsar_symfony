<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="visit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Visit
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookDomain")
     * @ORM\JoinTable(name="visit_to_domain",
     *      joinColumns={@ORM\JoinColumn(name="visit_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="domain_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $type_domain;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookSeason")
     * @ORM\JoinTable(name="visit_to_season",
     *      joinColumns={@ORM\JoinColumn(name="visit_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="season_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $season;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookTypeRecreation")
     * @ORM\JoinTable(name="visit_to_recreation",
     *      joinColumns={@ORM\JoinColumn(name="visit_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="recreation_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $recreation;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Destination")
     * @ORM\JoinColumn(name="ville", referencedColumnName="id", onDelete="SET NULL")
     */
    private $ville;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookDuration")
     * @ORM\JoinColumn(name="visit_duration", referencedColumnName="id", onDelete="SET NULL")
     */
    private $visit_duration;

    /**
     * @var integer
     *
     * @ORM\Column(name="number_hours_visit", type="integer", nullable=TRUE)
     */
    private $number_hours_visit;

    /**
     * Principale
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
     * @ORM\JoinColumn(name="image", referencedColumnName="id", onDelete="SET NULL")
     */
    private $image;

    /**
     * Arrière-plan
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
     * @ORM\JoinColumn(name="image_background", referencedColumnName="id", onDelete="SET NULL")
     */
    private $image_background;


    /**
     * Vignette liste
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
     * @ORM\JoinColumn(name="image_miniature", referencedColumnName="id", onDelete="SET NULL")
     */
    private $image_miniature;

    /**
     * Autres photos
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Files")
     * @ORM\JoinTable(name="visit_to_files",
     *      joinColumns={@ORM\JoinColumn(name="visit_id", referencedColumnName="id", onDelete="cascade")},
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Destination")
     * @ORM\JoinTable(name="visit_to_travel_points",
     *      joinColumns={@ORM\JoinColumn(name="visit_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="destination_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $travel_points;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mini_groupe", type="boolean", length=1)
     */
    private $mini_groupe = 0;

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
     * @ORM\JoinTable(name="visit_to_minigroup",
     *      joinColumns={@ORM\JoinColumn(name="visit_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="minigroup_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $visit_minigroup;


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
     * @return Visit
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return Visit
     */
    public function setTypeDomain($type_domain)
    {
        $this->type_domain = $type_domain;
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
     * @return Visit
     */
    public function setSeason($season)
    {
        $this->season = $season;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecreation()
    {
        return $this->recreation;
    }

    /**
     * @param mixed $recreation
     * @return Visit
     */
    public function setRecreation($recreation)
    {
        $this->recreation = $recreation;
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
     * @return Visit
     */
    public function setVille($ville)
    {
        $this->ville = $ville;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisitDuration()
    {
        return $this->visit_duration;
    }

    /**
     * @param mixed $visit_duration
     * @return Visit
     */
    public function setVisitDuration($visit_duration)
    {
        $this->visit_duration = $visit_duration;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumberHoursVisit()
    {
        return $this->number_hours_visit;
    }

    /**
     * @param int $number_hours_visit
     * @return Visit
     */
    public function setNumberHoursVisit($number_hours_visit)
    {
        $this->number_hours_visit = $number_hours_visit;
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
     * @return Visit
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
     * @return Visit
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
     * @return Visit
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
     * @return Visit
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
     * @return Visit
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTravelPoints()
    {
        return $this->travel_points;
    }

    /**
     * @param mixed $travel_points
     * @return Visit
     */
    public function setTravelPoints($travel_points)
    {
        $this->travel_points = $travel_points;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isMiniGroupe()
    {
        return $this->mini_groupe;
    }

    /**
     * @param mixed $mini_groupe
     * @return Visit
     */
    public function setMiniGroupe($mini_groupe)
    {
        $this->mini_groupe = $mini_groupe;
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
     * @return Visit
     */
    public function setMinigroupPrixEuros($minigroup_prix_euros)
    {
        $this->minigroup_prix_euros = $minigroup_prix_euros;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinigroupPrixRubles()
    {
        return $this->minigroup_prix_rubles;
    }

    /**
     * @param mixed $minigroup_prix_rubles
     * @return Visit
     */
    public function setMinigroupPrixRubles($minigroup_prix_rubles)
    {
        $this->minigroup_prix_rubles = $minigroup_prix_rubles;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisitMinigroup()
    {
        return $this->visit_minigroup;
    }

    /**
     * @param mixed $visit_minigroup
     * @return Visit
     */
    public function setVisitMinigroup($visit_minigroup)
    {
        $this->visit_minigroup = $visit_minigroup;
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
     * @return Visit
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
     * @return Visit
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
     * @return Visit
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
     * @return Visit
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
     * @return Visit
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
     * @return Visit
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
     * @return Visit
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
     * @return Visit
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
     * @return Visit
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
	 * @return Visit
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
        return 'Visit';
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

