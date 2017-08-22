<?php
/**
 * Created by PhpStorm.
 * User: fares
 * Date: 28.06.2017
 * Time: 11:43
 */

namespace AdminBundle\Menu;


use Knp\Menu\FactoryInterface;

class Builder
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

//        $menu->addChild('Home', array('route' => '/'));

        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        return $menu;
    }
}