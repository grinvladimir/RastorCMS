<?php

return array(
    'routes' => array(
        'catalog' => new Zend_Controller_Router_Route(
                'catalog/:id/*',
                array(
                    'module' => 'catalog',
                    'controller' => 'index',
                    'action' => 'index',
                    'id' => 0,
                    'page' => 1
                )
        ),
        'product' => new Zend_Controller_Router_Route(
                'product/:id',
                array(
                    'module' => 'catalog',
                    'controller' => 'index',
                    'action' => 'product'
                )
        ),
        'cart' => new Zend_Controller_Router_Route(
                'cart',
                array(
                    'module' => 'catalog',
                    'controller' => 'index',
                    'action' => 'cartview'
                )
        ),
        'addproduct' => new Zend_Controller_Router_Route(
                'addproduct',
                array(
                    'module' => 'catalog',
                    'controller' => 'index',
                    'action' => 'addproduct'
                )
        ),
        'deleteproduct' => new Zend_Controller_Router_Route(
                'deleteproduct',
                array(
                    'module' => 'catalog',
                    'controller' => 'index',
                    'action' => 'deleteproduct'
                )
        )
    ),
    'acl' => array(
        'resources' => array(
        //new Zend_Acl_Resource('menu_index'),
        //new Zend_Acl_Resource('menu_cms')
        ),
        'allow' => array(
        //array('moderator', 'menu_cms', null),
        //array(null, 'menu_index', null)
        ),
        'deny' => array()
    ),
    'cmsMenu' => array(
        array(
            'label' => 'Каталог продукции',
            'uri' => '#',
            'order' => 4,
            'pages' => array(
                array(
                    'label' => 'Список каталогов',
                    'module' => 'catalog',
                    'controller' => 'cms',
                    'action' => 'show',
                ),
                array(
                    'label' => 'Добавиь каталог',
                    'module' => 'catalog',
                    'controller' => 'cms',
                    'action' => 'add',
                ),
                array(
                    'label' => 'Список продуктов',
                    'module' => 'catalog',
                    'controller' => 'cmsproduct',
                    'action' => 'showlist',
                ),
                array(
                    'label' => 'Добавить продукт',
                    'module' => 'catalog',
                    'controller' => 'cmsproduct',
                    'action' => 'add',
                ),
                array(
                    'label' => 'Список заказов',
                    'module' => 'catalog',
                    'controller' => 'cmsorder',
                    'action' => 'showlist',
                ),
            )
        )
    ),
    'model' => 'Catalog_Model_Catalog'
);
