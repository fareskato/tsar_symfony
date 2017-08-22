<?php
/**
 * Created by PhpStorm.
 * User: fares
 * Date: 19.07.2017
 * Time: 18:18
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Event
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
     * E-mail de la personne en charge
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Files")
     * @ORM\JoinTable(name="event_to_files",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="files_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $image_others;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\EventToDay", mappedBy="event")
     * @ORM\JoinColumn(name="day", referencedColumnName="event")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $day;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location" )
     * @ORM\JoinColumn(name="location", referencedColumnName="id", onDelete="SET NULL")
     */
    private $location;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Destination")
     * @ORM\JoinTable(name="event_content_to_destination",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="destination_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $related_content;


    /**
     * Flexibilité du prix
     * @var boolean
     *
     * @ORM\Column(name="price_flexibility", type="boolean", length=1)
     */
    private $price_flexibility = 0;

    /**
     * Tarif affiché
     * @var string
     *
     * @ORM\Column(name="price_displayed", type="string", length=255, nullable=true)
     */
    private $price_displayed;


    /**
     * Type d'événement
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookTypeEvent")
     * @ORM\JoinColumn(name="event_type", referencedColumnName="id", onDelete="SET NULL")
     */
    private $event_type;

    // Mini Groups
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
     * @ORM\JoinTable(name="event_to_minigroup",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="minigroup_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $event_minigroup;


    /**
     * Produit Lié
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\EventToRelatedProduct", mappedBy="event")
     */
    private $related_product;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Destination")
     * @ORM\JoinTable(name="event_to_destination",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="destination_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $destination;

    /**
     * @var  bigint
     *
     * @ORM\Column(name="start", type="bigint", length=11, nullable=true)
     */
    private $start;

    /**
     * @var bigint
     *
     * @ORM\Column(name="end", type="bigint", length=11, nullable=true)
     */
    private $end;

    /**
     * Повтор
     * @var boolean
     *
     * @ORM\Column(name="repeat_date", type="boolean", length=1)
     */
    private $repeat = 0;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\EventToDate", mappedBy="event")
     * @ORM\JoinColumn(name="event_schedule", referencedColumnName="event")
     * @ORM\OrderBy({"date_start" = "ASC"})
     */
    private $event_schedule;

    /**
     * JSON Массив расписаний.
     * @var string
     *
     * @ORM\Column(name="event_schedule_string", type="text", nullable=true)
     */
    private $event_schedule_string;

    /**
     * Исключить даты
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Date")
     * @ORM\JoinTable(name="event_to_date_exclude",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="date_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $exclude_dates_list;

    /**
     * Добавить даты
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Date")
     * @ORM\JoinTable(name="event_to_date_include",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="date_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $include_dates_list;

	/**
	 * @var boolean
	 * @ORM\Column(name="favorite", type="boolean", length=1, nullable=true)
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
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     */
    public function setImageBackground($image_background)
    {
        $this->image_background = $image_background;
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
     */
    public function setImage($image)
    {
        $this->image = $image;
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
     */
    public function setImageMiniature($image_miniature)
    {
        $this->image_miniature = $image_miniature;
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
     * @return Event
     */
    public function setImageOthers($image_others)
    {
        $this->image_others = $image_others;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDay()
    {
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
     */
    public function setDay($day)
    {
        $this->day = $day;
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
     */
    public function setLocation($location)
    {
        $this->location = $location;
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
     */
    public function setRelatedContent($related_content)
    {
        $this->related_content = $related_content;
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
     * @return Event
     */
    public function setPriceFlexibility($price_flexibility)
    {
        $this->price_flexibility = $price_flexibility;
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
     */
    public function setPriceDisplayed($price_displayed)
    {
        $this->price_displayed = $price_displayed;
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
     * @return Event
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
     */
    public function setMinigroupPrixEuros($minigroup_prix_euros)
    {
        $this->minigroup_prix_euros = $minigroup_prix_euros;
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
     */
    public function setMinigroupPrixRubles($minigroup_prix_rubles)
    {
        $this->minigroup_prix_rubles = $minigroup_prix_rubles;
    }

    /**
     * @return mixed
     */
    public function getEventMinigroup()
    {
        return $this->event_minigroup;
    }

    /**
     * @param mixed $event_minigroup
     */
    public function setEventMinigroup($event_minigroup)
    {
        $this->event_minigroup = $event_minigroup;
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
     * @return Event
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRelatedProduct()
    {
        if($this->related_product) {
            if ($this->related_product->getVisit()) {
                return $this->related_product->getVisit();
            }
            if ($this->related_product->getExtension()) {
                return $this->related_product->getExtension();
            }
            if ($this->related_product->getVoyage()) {
                return $this->related_product->getVoyage();
            }
            return NULL;
        }
        return NULL;
    }

    /**
     * @param mixed $related_product
     * @return Event
     */
    public function setRelatedProduct($related_product)
    {
        $this->related_product = $related_product;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEventType()
    {
        return $this->event_type;
    }

    /**
     * @param mixed $event_type
     * @return Event
     */
    public function setEventType($event_type)
    {
        $this->event_type = $event_type;
        return $this;
    }

    /**
     * @return bigint
     */
    public function getStart()
    {
        $start = substr($this->start, 6, 2).'.'.substr($this->start, 4, 2).'.'.substr($this->start, 0, 4).' '.substr($this->start, 8, 2).':'.substr($this->start, 10, 2);
        return $start;
    }

    /**
     * @param bigint $start
     * @return Event
     */
    public function setStart($start)
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @return bigint
     */
    public function getEnd()
    {
        $end = substr($this->end, 6, 2).'.'.substr($this->end, 4, 2).'.'.substr($this->end, 0, 4).' '.substr($this->end, 8, 2).':'.substr($this->end, 10, 2);
        return $end;
    }

    /**
     * @param bigint $end
     * @return Event
     */
    public function setEnd($end)
    {
        $this->end = $end;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRepeat()
    {
        return $this->repeat;
    }

    /**
     * @param bool $repeat
     * @return Event
     */
    public function setRepeat($repeat)
    {
        $this->repeat = $repeat;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEventSchedule()
    {
        return $this->event_schedule;
    }

    /**
     * @param mixed $event_schedule
     * @return Event
     */
    public function setEventSchedule($event_schedule)
    {
        $this->event_schedule = $event_schedule;
        return $this;
    }

    /**
     * @return string
     */
    public function getEventScheduleString()
    {
        return $this->event_schedule_string;
    }

    /**
     * @param string $event_schedule_string
     * @return Event
     */
    public function setEventScheduleString($event_schedule_string)
    {
        $this->event_schedule_string = $event_schedule_string;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExcludeDatesList()
    {
        return $this->exclude_dates_list;
    }

    /**
     * @param mixed $exclude_dates_list
     * @return Event
     */
    public function setExcludeDatesList($exclude_dates_list)
    {
        $this->exclude_dates_list = $exclude_dates_list;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIncludeDatesList()
    {
        return $this->include_dates_list;
    }

    /**
     * @param mixed $include_dates_list
     * @return Event
     */
    public function setIncludeDatesList($include_dates_list)
    {
        $this->include_dates_list = $include_dates_list;
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
	 * @return Event
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
        return 'Event';
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