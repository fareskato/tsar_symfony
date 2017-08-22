<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Tag
 *
 * @ORM\Table(name="minigroup")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Minigroup
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
     * @var integer
     *
     * @ORM\Column(name="prix_eur", type="integer", length=11, nullable=true)
     */
    private $prix_eur;

    /**
     * @var integer
     *
     * @ORM\Column(name="prix_rub", type="integer", length=11, nullable=true)
     */
    private $prix_rub;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Minigroup
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

	/**
	 * @return int
	 */
	public function getStart()
	{
        $start = substr($this->start, 6, 2).'.'.substr($this->start, 4, 2).'.'.substr($this->start, 0, 4).' '.substr($this->start, 8, 2).':'.substr($this->start, 10, 2);
		return $start;
	}

	/**
	 * @param int $start
	 * @return Minigroup
	 */
	public function setStart($start)
	{
		$this->start = $start;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getEnd()
	{
        $end = substr($this->end, 6, 2).'.'.substr($this->end, 4, 2).'.'.substr($this->end, 0, 4).' '.substr($this->end, 8, 2).':'.substr($this->end, 10, 2);
		return $end;
	}

	/**
	 * @param int $end
	 * @return Minigroup
	 */
	public function setEnd($end)
	{
		$this->end = $end;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPrixEur()
	{
		return $this->prix_eur;
	}

	/**
	 * @param int $prix_eur
	 * @return Minigroup
	 */
	public function setPrixEur($prix_eur)
	{
		$this->prix_eur = $prix_eur;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPrixRub()
	{
		return $this->prix_rub;
	}

	/**
	 * @param int $prix_rub
	 * @return Minigroup
	 */
	public function setPrixRub($prix_rub)
	{
		$this->prix_rub = $prix_rub;
		return $this;
	}



}

?>