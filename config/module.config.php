<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'CorePeople\CtrlController\Users' =>
                'CorePeople\CtrlController\UsersController',
        )
    ),

    'router' => array(
        'routes' => array(
            'ctrl-users' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/ctrl/users[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => 'add|edit',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'CorePeople\CtrlController\Users',
                        'action' => 'index'
                    )
                )
            ),
        ),
    ),

    'navigation' => array(
        'control' => array(
            array(
                'label' => 'Пользователи',
                'route' => 'ctrl-users',
            ),
        )
    )
);
