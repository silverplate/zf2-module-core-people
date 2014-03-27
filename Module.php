<?php

namespace CorePeople;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                __NAMESPACE__ . '\Mapper\Person' =>
                    __NAMESPACE__ . '\Mapper\Person',
                __NAMESPACE__ . '\Mapper\User' =>
                    __NAMESPACE__ . '\Mapper\User',
            ),
            'factories' => array(
                __NAMESPACE__ . '\Form\User' => function($_sm) {
                    return new Form\User($_sm);
                }
            ),
        );
    }
}
