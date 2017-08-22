<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

use Symfony\Component\Intl\Intl;

/**
 * Tag
 *
 * @ORM\Table(name="location")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Location
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
     * @ORM\Column(name="postal_code", type="string", length=255, nullable=true)
     */
    private $postal_code;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @var string
     * @ORM\Column(name="latitude", type="string", length=255, nullable=true)
     */
    private $latitude;

    /**
     * @var string
     * @ORM\Column(name="longitude", type="string", length=255, nullable=true)
     */
    private $longitude;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return Location
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPostalCode()
	{
		return $this->postal_code;
	}

	/**
	 * @param string $postal_code
	 * @return Location
	 */
	public function setPostalCode($postal_code)
	{
		$this->postal_code = $postal_code;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCountry()
	{
		return $this->country;
	}

	public function getCountryName()
	{
		$countryName = $this->country;
		if ($countryName && $countryNewName = Intl::getRegionBundle()->getCountryName(strtoupper($countryName))) {
			return $countryNewName;
		}
		return $countryName;
	}

	/**
	 * @param string $country
	 * @return Location
	 */
	public function setCountry($country)
	{
		$this->country = $country;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLatitude()
	{
		return $this->latitude;
	}

	/**
	 * @param string $latitude
	 * @return Location
	 */
	public function setLatitude($latitude)
	{
		$this->latitude = $latitude;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLongitude()
	{
		return $this->longitude;
	}

	/**
	 * @param string $longitude
	 * @return Location
	 */
	public function setLongitude($longitude)
	{
		$this->longitude = $longitude;
		return $this;
	}







// Translations

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

