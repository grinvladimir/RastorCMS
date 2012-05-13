<?php

return array(
    'routes' => array(
        'article' => new Zend_Controller_Router_Route_Regex(
                'article/(.+)\.htm',
                array(
                    'module' => 'articles',
                    'controller' => 'index',
                    'action' => 'view'
                ),
                array(
                    1 => 'uri'
                ),
                'article/%s.htm'
        ),
        'articles' => new Zend_Controller_Router_Route(
                'articles/:page',
                array(
                    'module' => 'articles',
                    'controller' => 'index',
                    'action' => 'index',
                    'page' => 1
                )
        ),
    ),
    'acl' => array(
        'resources' => array(
            //new Zend_Acl_Resource('content_index'),
            //new Zend_Acl_Resource('content_cms')
        ),
        'allow' => array(
            //array('moderator', 'content_cms', null),
            //array(null, 'content_index', null)
        ),
        'deny' => array()
    ),
    'cmsMenu' => array(
        array(
            'label' => 'Статьи',
            'uri' => '#',
            'order' => 3,
            'pages' => array(
                array(
                    'label' => 'Список статей',
                    'module' => 'articles',
                    'controller' => 'cms',
                    'action' => 'showlist',
                ),
                array(
                    'label' => 'Создать статью',
                    'module' => 'articles',
                    'controller' => 'cms',
                    'action' => 'add',
                )
            )
        )
    ),
    'model' => 'Articles_Model_Article'
);
