<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Tag
 *
 * @ORM\Table(name="location_ip")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class LocationIp
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="ip_start", type="integer", length=11, nullable=true)
	 */
	private $ip_start;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="ip_end", type="integer", length=11, nullable=true)
	 */
	private $ip_end;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="ip_range", type="string", length=255, nullable = true)
	 */
	private $ip_range;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="country", type="string", length=255, nullable = true)
	 */
	private $country;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="city_id", type="integer", length=11, nullable=true)
	 */
	private $city_id;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return LocationIp
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getIpStart()
	{
		return $this->ip_start;
	}

	/**
	 * @param int $ip_start
	 * @return LocationIp
	 */
	public function setIpStart($ip_start)
	{
		$this->ip_start = $ip_start;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getIpEnd()
	{
		return $this->ip_end;
	}

	/**
	 * @param int $ip_end
	 * @return LocationIp
	 */
	public function setIpEnd($ip_end)
	{
		$this->ip_end = $ip_end;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getIpRange()
	{
		return $this->ip_range;
	}

	/**
	 * @param string $ip_range
	 * @return LocationIp
	 */
	public function setIpRange($ip_range)
	{
		$this->ip_range = $ip_range;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * @param string $country
	 * @return LocationIp
	 */
	public function setCountry($country)
	{
		$this->country = $country;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCityId()
	{
		return $this->city_id;
	}

	/**
	 * @param int $city_id
	 * @return LocationIp
	 */
	public function setCityId($city_id)
	{
		$this->city_id = $city_id;
		return $this;
	}



}

