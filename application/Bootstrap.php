<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    /**
     * Init autoloader
     *
     * @return Zend_Application_Module_Autoloader
     */
    protected function _initAutoload() {
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $autoLoader->setFallbackAutoloader(true);

        return $autoLoader;
    }

    /**
     * Init Config
     *
     * @return Zend_Config 
     */
    protected function _initConfig() {
        $config = new Zend_Config($this->getOptions());

        $registry = Zend_Registry::getInstance();
        $registry->set('config', $config);

        return $config;
    }

    /**
     * Init Request
     *
     * @return Zend_Controller_Request_Http 
     */
    protected function _initRequest() {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');

        $request = new Zend_Controller_Request_Http();
        $request->setBaseUrl($this->_options['baseUrl']);
        $front->setRequest($request);

        return $request;
    }

    /**
     * Init View
     *
     * @return Zend_View 
     */
    protected function _initView() {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        $layout = Zend_Layout::startMvc($this->_options);
        
        $front->registerPlugin(new Rastor_Controller_Plugin_Layouts($this->_options));

        $view = $layout->getView();

        $view->baseUrl = $this->_options['baseUrl'];
        $view->addHelperPath('Rastor/View/Helper', 'Rastor_View_Helper_');

        $view->doctype('XHTML1_STRICT');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');

        $layout->setView($view);

        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);

        return $view;
    }

    /**
     * Init Locale
     * 
     * @return void 
     */
    protected function _initLocale() {
        $this->bootstrap('Config');
        $front = $this->getResource('FrontController');
        $front->registerPlugin(new Rastor_Translate_Plugin());

        define('ROUTE_LANG', '_lang');
    }

    /**
     * Init Router
     * 
     * @return Zend_Controller_Router_Rewrite 
     */
    protected function _initRouter() {
        $router = new Zend_Controller_Router_Rewrite();
        $router->removeDefaultRoutes();

        Zend_Registry::set('Router', $router);

        return $router;
    }

    /**
     * Init Acl
     * 
     * @return Zend_Acl
     */
    protected function _initAcl() {
        $this->bootstrap('Config');
        $config = $this->getResource('Config');

        $acl = new Rastor_Acl();

        $acl->addRole(new Rastor_Acl_Role('member', 3));

        Zend_Registry::set('Acl', $acl);
        Zend_Registry::set('CmsMenu', array());
        Zend_Registry::set('ModelList', array());

        return $acl;
    }

    /**
     * Init Modeules
     * 
     */
    protected function _initPlugins() {
        $this->bootstrap('Router');
        $this->bootstrap('Modules');

        $front = $this->getResource('FrontController');
        $acl = $this->getResource('Acl');
        $router = $this->getResource('Router');

        $front->registerPlugin(new Rastor_Controller_Plugin_Acl($acl, array(
                    'module' => 'index',
                    'controller' => 'error',
                    'action' => 'error',
                )));

        $front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
                    'module' => 'index',
                    'controller' => 'error',
                    'action' => 'error',
                )));

        $front->setRouter($router);
    }

    protected function _initZFDebug() {
        $this->bootstrap('Db');
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');

        if (APPLICATION_ENV == 'development') {
            $options = $this->getOption('zfdebug');
            $zfdebug = new Rastor_Controller_Plugin_Debug($options);
            $front->registerPlugin($zfdebug);
        }
    }

}