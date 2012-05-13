<?php

abstract class Rastor_Application_Module_Bootstrap extends Zend_Application_Module_Bootstrap {

    protected $_routes;
    protected $_acl;
    protected $_cmsMenu = array();
    protected $_model;

    /**
     * Constructor
     *
     * @param  Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application
     * @return void
     */
    public function __construct($application) {
        $this->_setModuleParams(include 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . strtolower($this->getModuleName()) . DIRECTORY_SEPARATOR . 'config.php');

        parent::__construct($application);
    }

    protected function _setModuleParams(array $config) {
        if (isset($config['acl'])) {
            $this->_acl = $config['acl'];
        }

        if (isset($config['routes'])) {
            $this->_routes = $config['routes'];
        }
        
        if (isset($config['cmsMenu'])) {
            $this->_cmsMenu = $config['cmsMenu'];
        }
        
        if (isset($config['model'])) {
            $this->_model = $config['model'];
        }
    }

    protected function _initAcl() {
        $acl = Zend_Registry::get('Acl');

        if (isset($this->_acl)) {
            if (isset($this->_acl['resources'])) {
                foreach ($this->_acl['resources'] as $resource) {
                    $acl->addResource($resource);
                }
            }
            if (isset($this->_acl['allow'])) {
                foreach ($this->_acl['allow'] as $data) {
                    $acl->allow($data[0], $data[1], $data[2]);
                }
            }
            if (isset($this->_acl['deny'])) {
                foreach ($this->_acl['deny'] as $data) {
                    $acl->deny($data[0], $data[1], $data[2]);
                }
            }
        }
    }

    protected function _initRouter() {
        $router = Zend_Registry::get('Router');

        if (count($this->_routes)) {
            $router->addRoutes($this->_routes);
        }
    }
    
    protected function _initCmsMenu() {
        $cmsMenu = Zend_Registry::get('CmsMenu');

        Zend_Registry::set('CmsMenu', array_merge($cmsMenu, $this->_cmsMenu));
    }
    
    protected function _initModelList(){
        if (strlen($this->_model)) {
            $modelList = Zend_Registry::get('ModelList');
            $modelList[] = $this->_model;
            Zend_Registry::set('ModelList', $modelList);
        }
    }

}