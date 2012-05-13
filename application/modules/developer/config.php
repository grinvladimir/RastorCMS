<?php

return array(
    'acl' => array(
        'resources' => array(
            new Zend_Acl_Resource('developer_view')
        ),
        'allow' => array(
            array('superadmin', 'developer_view', null)
        ),
        'deny' => array()
    ),
    'cmsMenu' => array(
        array(
            'label' => 'Меню разработчика',
            'uri' => '#',
            'order' => 9,
            'resource' => 'developer_view',
            'pages' => array(
                array(
                    'label' => 'Список шаблонов',
                    'module' => 'developer',
                    'controller' => 'view',
                    'action' => 'showlist',
                ),
                array(
                    'label' => 'robots.txt',
                    'module' => 'developer',
                    'controller' => 'editor',
                    'action' => 'edit',
                    'params' => array('file' => 'robots.txt')
                )
            )
        )
    )
);
