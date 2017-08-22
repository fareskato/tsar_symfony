<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * ExtensionTranslation
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class ExtensionTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=TRUE)
     */
    private $name;

  /**
   * @var string
   *
   * @ORM\Column(name="headline_liste", type="string", length=255, nullable=true)
   */
  private $headline_liste;

    /**
     * @var string
     *
     * @ORM\Column(name="body_summary", type="text", nullable=TRUE)
     */
    private $body_summary;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=TRUE)
     */
    private $body;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", length=1)
     */
    private $active = 0;


    /**
     * Texte sous le prix
     * @var string
     *
     * @ORM\Column(name="text_under_price", type="string", length=255, nullable=TRUE)
     */
    private $text_under_price;

    /**
     * Détails du service
     * @var string
     *
     * @ORM\Column(name="service_details", type="string", length=255, nullable=TRUE)
     */
    private $service_details;

    /**
     * External booking link label
     * @var string
     *
     * @ORM\Column(name="external_booking_link_label", type="string", length=255, nullable=true)
     */
    private $external_booking_link_label;

    /**
     * Conditions de Vente
     * @var string
     *
     * @ORM\Column(name="conditions_sale", type="text", nullable=TRUE)
     */
    private $conditions_sale;


  /**
   * @var string
   *
   * @ORM\Column(name="slug", type="string", length=255, nullable=true)
   */
  private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="minigroup_name", type="string", length=255, nullable=true)
     */
    private $minigroup_name;


  /**
   * @return string
   */
  public function getSlug()
  {
    return $this->slug;
  }

  /**
   * @param string $slug
   * @return HotelTranslation
   */
  public function setSlug($slug)
  {
    $this->slug = $slug;
    return $this;
  }

  /**
   * @return string
   */
  public function getHeadlineListe() {
    return $this->headline_liste;
  }

  /**
   * @param string $headline_liste
   */
  public function setHeadlineListe($headline_liste) {
    $this->headline_liste = $headline_liste;
  }

  /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ExtensionTranslation
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getBodySummary()
    {
        return $this->body_summary;
    }

    /**
     * @param string $body_summary
     * @return ExtensionTranslation
     */
    public function setBodySummary($body_summary)
    {
        $this->body_summary = $body_summary;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return ExtensionTranslation
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return ExtensionTranslation
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return string
     */
    public function getTextUnderPrice()
    {
        return $this->text_under_price;
    }

    /**
     * @param string $text_under_price
     * @return ExtensionTranslation
     */
    public function setTextUnderPrice($text_under_price)
    {
        $this->text_under_price = $text_under_price;
        return $this;
    }

    /**
     * @return string
     */
    public function getServiceDetails()
    {
        return $this->service_details;
    }

    /**
     * @param string $service_details
     * @return ExtensionTranslation
     */
    public function setServiceDetails($service_details)
    {
        $this->service_details = $service_details;
        return $this;
    }

    /**
     * @return string
     */
    public function getExternalBookingLinkLabel()
    {
        return $this->external_booking_link_label;
    }

    /**
     * @param string $external_booking_link_label
     * @return ExtensionTranslation
     */
    public function setExternalBookingLinkLabel($external_booking_link_label)
    {
        $this->external_booking_link_label = $external_booking_link_label;
        return $this;
    }

    /**
     * @return string
     */
    public function getConditionsSale()
    {
        return $this->conditions_sale;
    }

    /**
     * @param string $conditions_sale
     * @return ExtensionTranslation
     */
    public function setConditionsSale($conditions_sale)
    {
        $this->conditions_sale = $conditions_sale;
        return $this;
    }

    /**
     * @return string
     */
    public function getMinigroupName()
    {
        return $this->minigroup_name;
    }

    /**
     * @param string $minigroup_name
     * @return ExtensionTranslation
     */
    public function setMinigroupName($minigroup_name)
    {
        $this->minigroup_name = $minigroup_name;
        return $this;
    }


}

