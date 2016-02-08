<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;


class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Flats', array('route' => 'homepage'));
        $menu->addChild('Houses', array('route' => 'security_login_form'));
        $menu->addChild('Acres', array('route' => 'user_registration'))->setAttribute('divider_append', true);
        $menu->addChild('Rent', array('route' => 'user_registration'));

        $menu['Rent']->addChild('FlatsRent', array())->setAttribute('dropdown', true)
        ->addChild('One room', array('route' => 'homepage'))->getParent()
        ->addChild('Two room', array('route' => 'homepage'))->getParent();

        $menu['Rent']->addChild('Commerce', array('route' => 'homepage'));




        return $menu;
    }
}