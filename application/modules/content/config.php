<?php

return array(
    'routes' => array(
        'content' => new Zend_Controller_Router_Route_Regex(
                '(.+)\.html',
                array(
                    'module' => 'content',
                    'controller' => 'index',
                    'action' => 'index'
                ),
                array(
                    1 => 'uri'
                ),
                '%s.html'
        ),
        'contacts' => new Zend_Controller_Router_Route(
                'contacts',
                array(
                    'module' => 'content',
                    'controller' => 'index',
                    'action' => 'contacts'
                )
        ),
        'error' => new Zend_Controller_Router_Route(
                'error',
                array(
                    'module' => 'content',
                    'controller' => 'index',
                    'action' => 'error'
                )
        )
    ),
    'acl' => array(
        'resources' => array(
            new Zend_Acl_Resource('content_index'),
            new Zend_Acl_Resource('content_cms')
        ),
        'allow' => array(
            array('moderator', 'content_cms', null),
            array(null, 'content_index', null)
        ),
        'deny' => array()
    ),
    'cmsMenu' => array(
        array(
            'label' => 'Материалы',
            'uri' => '#',
            'order' => 2,
            'pages' => array(
                array(
                    'label' => 'Список материалов',
                    'module' => 'content',
                    'controller' => 'cms',
                    'action' => 'showlist',
                ),
                array(
                    'label' => 'Создать материл',
                    'module' => 'content',
                    'controller' => 'cms',
                    'action' => 'add',
                )
            )
        )
    ),
    'model' => 'Content_Model_Content'
);
