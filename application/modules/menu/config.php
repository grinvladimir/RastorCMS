<?php

return array(
    'routes' => array(),
    'acl' => array(
        'resources' => array(
            new Zend_Acl_Resource('menu_index'),
            new Zend_Acl_Resource('menu_cms')
        ),
        'allow' => array(
            array('moderator', 'menu_cms', null),
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
                    'label' => 'Список пунктов меню',
                    'module' => 'menu',
                    'controller' => 'cms',
                    'action' => 'show',
                ),
                array(
                    'label' => 'Создать пункт меню',
                    'module' => 'menu',
                    'controller' => 'cms',
                    'action' => 'add',
                )
            )
        )
    )
);
