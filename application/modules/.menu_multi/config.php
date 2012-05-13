<?php

return array(
    'routes' => array(),
    'acl' => array(
        'resources' => array(
            new Zend_Acl_Resource('menu_index')
        ),
        'allow' => array(
            array(null, 'menu_index', null)
        ),
        'deny' => array()
    ),
    'cmsMenu' => array(
        array(
            'label' => 'Меню',
            'uri' => '#',
            'order' => 2,
            'pages' => array(
                array(
                    'label' => 'Список меню',
                    'module' => 'menu',
                    'controller' => 'cmsmenu',
                    'action' => 'showlist',
                ),
                array(
                    'label' => 'Создать меню',
                    'module' => 'menu',
                    'controller' => 'cmsmenu',
                    'action' => 'add',
                )
            )
        )
    )
);
