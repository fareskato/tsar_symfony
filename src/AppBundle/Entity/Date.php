<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Tag
 *
 * @ORM\Table(name="date")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
class Date
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
     * @ORM\Column(name="date", type="bigint", length=11, nullable=true)
     */
    private $date;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Date
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bigint
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param bigint $date
     * @return Date
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }


}

