<?php

return array(
    'routes' => array(
        'admin' => new Zend_Controller_Router_Route(
                'admin/:module/:controller/:action/*',
                array(
                    'module' => 'core',
                    'controller' => 'admin',
                    'action' => 'index'
                )
        ),
        'default' => new Zend_Controller_Router_Route_Static(
                '',
                array(
                    'module' => 'index',
                    'controller' => 'index',
                    'action' => 'index'
                )
        )
    ),
    'acl' => array(
        'resources' => array(
            new Zend_Acl_Resource('core_index')
        ),
        'allow' => array(
            array('admin', 'core_index', null),
            array(null, 'core_index', 'login'),
        ),
        'deny' => array(
        )
    ),
    'cmsMenu' => array(
        array(
            'label' => 'Главная',
            'module' => 'core',
            'controller' => 'admin',
            'action' => 'index',
            'order' => 1
        ),
        array(
            'label' => 'Настройки',
            'uri' => '#',
            'order' => 99,
            'pages' => array(
                array(
                    'label' => 'Изменение пароля',
                    'module' => 'core',
                    'controller' => 'config',
                    'action' => 'changepassword',
                    'order' => 1
                ),
                array(
                    'label' => 'Настройки Cms',
                    'module' => 'core',
                    'controller' => 'config',
                    'action' => 'cmsconfig',
                    'order' => 2
                )
            )
        ),
        array(
            'label' => 'Выход',
            'module' => 'core',
            'controller' => 'admin',
            'action' => 'logout',
            'order' => 100
        ),
    )
);
