<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;


/**
 * User
 *
 * @ORM\Table(name="front_slider")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class FrontSlider
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
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=255, nullable = true)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=255, nullable = true, columnDefinition="ENUM('desktop','mobile')")
     */
    private $position;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BookDomain")
     * @ORM\JoinTable(name="front_slider_to_domain",
     *      joinColumns={@ORM\JoinColumn(name="frontslider_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="domain_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $type_domain;

    /**
     * @var integer
     *
     * @ORM\Column(name="reorder", type="integer", length=11)
     */
    private $reorder = 1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="external", type="boolean", length=1)
     */
    private $external = 0;

    /**
     * Many Users have One Address.
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files")
     * @ORM\JoinColumn(name="image", referencedColumnName="id", onDelete="SET NULL")
     */
    private $image;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }
    /**
     * @param mixed $color
     * @return FrontSlider
     */
    public function setColor($color)
    {
        $this->color = $color;
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
     * @return FrontSlider
     */
    public function setTypeDomain($type_domain)
    {
        $this->type_domain = $type_domain;
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
     * @return FrontSlider
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     * @return FrontSlider
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return int
     */
    public function getReorder()
    {
        return $this->reorder;
    }

    /**
     * @param int $reorder
     * @return FrontSlider
     */
    public function setReorder($reorder)
    {
        $this->reorder = $reorder;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExternal()
    {
        return $this->external;
    }

    /**
     * @param bool $external
     * @return FrontSlider
     */
    public function setExternal($external)
    {
        $this->external = $external;
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
