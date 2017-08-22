<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 *
 * @ORM\Table(name="destination")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Destination
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
     * @var boolean
     *
     * @ORM\Column(name="master_destination", type="boolean", length=1)
     */
    private $master_destination = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="present_in_list", type="boolean", length=1)
     */
    private $present_in_list = false;



    /**
     * Many Users have One Address.
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookTypeDestination")
     * @ORM\JoinColumn(name="type_destination_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $type_destination;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookDomain")
     * @ORM\JoinTable(name="destination_to_domain",
     *      joinColumns={@ORM\JoinColumn(name="destination_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="domain_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $type_domain;

	/**
	 * Many Users have Many Users.
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Destination", mappedBy="parent")
	 */
	private $children;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Destination", inversedBy="children")
     * @ORM\JoinTable(name="destination_to_parent",
     *      joinColumns={@ORM\JoinColumn(name="destination_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="destination_parent_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $parent;

    /**
     * Many Users have One Address.
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location")
     * @ORM\JoinColumn(name="location", referencedColumnName="id", onDelete="SET NULL")
     */
    private $location;

    /**
     * Many Users have One Address.
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
     * @ORM\JoinColumn(name="image_background", referencedColumnName="id", onDelete="SET NULL")
     */
    private $image_background;

    /**
     * Many Users have One Address.
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
     * @ORM\JoinColumn(name="image", referencedColumnName="id", onDelete="SET NULL")
     */
    private $image;

    /**
     * Many Users have One Address.
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
     * @ORM\JoinColumn(name="image_panorama", referencedColumnName="id", onDelete="SET NULL")
     */
    private $image_panorama;

    /**
     * Many Users have One Address.
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
     * @ORM\JoinColumn(name="image_header", referencedColumnName="id", onDelete="SET NULL")
     */
    private $image_header;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Files")
     * @ORM\JoinTable(name="destination_to_files",
     *      joinColumns={@ORM\JoinColumn(name="destination_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="files_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $image_others;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BookEtiquette")
     * @ORM\JoinColumn(name="etiquette", referencedColumnName="id", onDelete="SET NULL")
     */
    private $etiquette;

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
    public function getTypeDestination()
    {
        return $this->type_destination;
    }

    /**
     * @param mixed $type_destination
     * @return Destination
     */
    public function setTypeDestination($type_destination)
    {
        $this->type_destination = $type_destination;
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
     * @return Destination
     */
    public function setTypeDomain($type_domain)
    {
        $this->type_domain = $type_domain;
        return $this;
    }

    /**
     * @return bool
     */
    public function getMasterDestination()
    {
        return $this->master_destination;
    }

    /**
     * @param bool $master_destination
     * @return Destination
     */
    public function setMasterDestination($master_destination)
    {
        $this->master_destination = $master_destination;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPresentInList()
    {
        return $this->present_in_list;
    }

    /**
     * @param mixed $present_in_list
     * @return Destination
     */
    public function setPresentInList($present_in_list)
    {
        $this->present_in_list = $present_in_list;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     * @return Destination
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
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
     * @return mixed
     */
    public function setLocation($location)
    {
        $this->location = $location;
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
     * @return Destination
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getImagePanorama()
    {
        return $this->image_panorama;
    }

    /**
     * @param mixed $image_panorama
     */
    public function setImagePanorama($image_panorama)
    {
        $this->image_panorama = $image_panorama;
    }

    /**
     * @return mixed
     */
    public function getImageHeader()
    {
        return $this->image_header;
    }

    /**
     * @param mixed $image_header
     */
    public function setImageHeader($image_header)
    {
        $this->image_header = $image_header;
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
     * @return Destination
     */
    public function setImageOthers($image_others)
    {
        $this->image_others = $image_others;
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
     * @return Destination
     */
    public function setEtiquette($etiquette)
    {
        $this->etiquette = $etiquette;
        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * @param mixed $children
	 * @return Destination
	 */
	public function setChildren($children)
	{
		$this->children = $children;
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

