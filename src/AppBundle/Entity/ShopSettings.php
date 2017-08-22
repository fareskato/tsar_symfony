<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * ShopSettings
 *
 * @ORM\Table(name="shop_settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntityRepository")
 */
Class ShopSettings {

    // Just if U want to use translation on the future A
    // Don't forget to create ShopSettingTranslation entity
//    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=TRUE)
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=TRUE)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255,  nullable=TRUE)
     */
    private $value;


    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }



    // Translation : if needed
    /*
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
    */

}

