<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity
 */
class EventTranslation
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
     * @ORM\Column(name="introduction", type="text", nullable=TRUE)
     */
    private $introduction;
    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=TRUE)
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(name="body_summary", type="text", nullable=TRUE)
     */
    private $body_summary;

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
     * Conditions de Vente
     * @var string
     *
     * @ORM\Column(name="conditions_sale", type="text", nullable=TRUE)
     */
    private $conditions_sale;

    /**
     * @var string
     *
     * @ORM\Column(name="headline_liste", type="string", length=255, nullable=TRUE)
     */
    private $headline_liste;

    /**
     * Horaires affichés
     * @var string
     *
     * @ORM\Column(name="show_times", type="string", length=255, nullable=TRUE)
     */
    private $show_times;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", length=1)
     */
    private $active = 0;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return EventTranslation
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * @param string $introduction
     * @return EventTranslation
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;
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
     * @return EventTranslation
     */
    public function setBody($body)
    {
        $this->body = $body;
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
     * @return EventTranslation
     */
    public function setBodySummary($body_summary)
    {
        $this->body_summary = $body_summary;
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
     * @return EventTranslation
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
     * @return EventTranslation
     */
    public function setServiceDetails($service_details)
    {
        $this->service_details = $service_details;
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
     * @return EventTranslation
     */
    public function setConditionsSale($conditions_sale)
    {
        $this->conditions_sale = $conditions_sale;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeadlineListe()
    {
        return $this->headline_liste;
    }

    /**
     * @param string $headline_liste
     * @return EventTranslation
     */
    public function setHeadlineListe($headline_liste)
    {
        $this->headline_liste = $headline_liste;
        return $this;
    }

    /**
     * @return string
     */
    public function getShowTimes()
    {
        return $this->show_times;
    }

    /**
     * @param string $show_times
     * @return EventTranslation
     */
    public function setShowTimes($show_times)
    {
        $this->show_times = $show_times;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return EventTranslation
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
     * @return EventTranslation
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }







}

