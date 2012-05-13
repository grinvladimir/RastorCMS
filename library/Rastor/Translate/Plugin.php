<?php

class Rastor_Translate_Plugin extends Zend_Controller_Plugin_Abstract {

    private $_locales;
    private $_mainLanguage;
    private $_modules;
    private $_moduleDirectory;

    function __construct() {
        $config = Zend_Registry::get('config');

        $this->_locales = $config->locales->toArray();
        $this->_mainLanguage = $config->locales->key();
        $this->_modules = $config->resources->modules->toArray();
        $this->_moduleDirectory = $config->resources->frontController->moduleDirectory;
    }

    public function routeStartup(Zend_Controller_Request_Abstract $request) {
        $router = Zend_Controller_Front::getInstance()->getRouter();

        $routeLang = new Zend_Controller_Router_Route(
                        ':lang',
                        null,
                        array('lang' => '[a-z]{2}')
        );

        $newRoutes = array();
        $oldRoutes = array();
        foreach ($router->getRoutes() as $name => $route) {
            if ($name != 'admin') {
                $newRoutes[$name . ROUTE_LANG] = $routeLang->chain($route);
            }
            $oldRoutes[$name] = $route;
            $router->removeRoute($name);
        }

        $router->addRoutes($oldRoutes + $newRoutes);
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request) {

        Zend_Registry::set('request_params', $request->getParams());

        $router = Zend_Controller_Front::getInstance()->getRouter();
        $routeName = $router->getCurrentRouteName();

        if ($routeName == 'admin') {
            $namespace = new Zend_Session_Namespace('Zend_Auth');

            if (isset($namespace->storage->locale) && array_key_exists($namespace->storage->locale, $this->_locales)) {
                $locale = new Zend_Locale($this->_locales[$namespace->storage->locale]);
            } else {
                $locale = new Zend_Locale($this->_locales[$this->_mainLanguage]);
            }
        } else {
            $language = $request->getParam('lang', 'none');
            if (!array_key_exists($language, $this->_locales)) {
                $locale = new Zend_Locale('auto');

                if (!array_key_exists($locale->getLanguage(), $this->_locales)) {
                    $locale = new Zend_Locale($this->_locales[$this->_mainLanguage]);
                }
            } else {
                $router->setGlobalParam('lang', $language);
                $locale = new Zend_Locale($this->_locales[$language]);
            }

            if (($this->_mainLanguage == $language) && (substr($routeName, -strlen(ROUTE_LANG)) == ROUTE_LANG)) {
                $routeName = substr($routeName, 0, strlen($routeName) - strlen(ROUTE_LANG));
                $this->getResponse()->setRedirect($router->assemble($request->getParams(), $routeName));
            }
        }

        Rastor_Url::initialize($locale->getLanguage(), $this->_mainLanguage);

        $translate = new Zend_Translate(
                        'array',
                        'application' . DIRECTORY_SEPARATOR . 'locale',
                        $locale->getLanguage(),
                        array(
                            'scan' => Zend_Translate::LOCALE_FILENAME
                        )
        );

        foreach ($this->_modules as $module) {
            $source = $this->_moduleDirectory . $module . DIRECTORY_SEPARATOR . 'locale' . DIRECTORY_SEPARATOR . $locale->getLanguage() . '.php';
            if (file_exists($source)) {
                $translate->addTranslation($source, $locale->getLanguage());
            }
        }

        Zend_Validate_Abstract::setDefaultTranslator($translate);
        Zend_Registry::set('Zend_Locale', $locale);
        Zend_Registry::set('Zend_Translate', $translate);
        
        $view = Zend_Layout::getMvcInstance()->getView();
        $view->headMeta()->appendHttpEquiv('Content-Language', $locale->toString());
    }

}