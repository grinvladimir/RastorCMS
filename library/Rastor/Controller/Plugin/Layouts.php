<?php

class Rastor_Controller_Plugin_Layouts extends Zend_Controller_Plugin_Abstract {

    protected $_modulesLayouts;
    protected $_defaulLayout;

    public function __construct($array = array()) {
        if (is_array($array) && count($array['resources']['modules']) > 0) {
            $this->_modulesLayouts = $array['resources']['modules'];
        } else {
            $this->_modulesLayouts = array();
        }

        if (isset($array['dafaultLayout'])) {
            $this->_defaulLayout = $array['dafaultLayout'];
        } else {
            $this->_defaulLayout = false;
        }
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $moduleName = $request->getParam('module');
        $layout = Zend_Layout::getMvcInstance();
        
        if (in_array($moduleName, $this->_modulesLayouts)) {
            $layout->setLayout($moduleName);
        } else if ($this->_defaulLayout) {
            $layout->setLayout($this->_defaulLayout);
        } else {
            throw new Exception('Can`t set layout!');
        }
    }

}

?>