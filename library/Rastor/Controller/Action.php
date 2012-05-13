<?php

/**
 * Controller action
 *
 * GlobalVision CMS
 * @author Budjak Orest
 * @copyright 2011-2012 Budjak Orest (rastor.name)
 * @license http://www.php.net/license/3_01.txt 
 * @version 1.0
 */
class Rastor_Controller_Action extends Zend_Controller_Action {

    protected $_userData;
    protected $_translator;
    protected $_locale;
    protected $_config;
    protected $_itemsPerPage = 10;
    protected $_pageRange = 5;

    public function init() {
        $auth = Zend_Auth::getInstance();
        $this->_userData = $auth->getIdentity();

        $this->_translator = Zend_Registry::get('Zend_Translate');
        $this->_locale = Zend_Registry::get('Zend_Locale');
        $this->_config = Zend_Registry::get('config');
    }

    /**
     * Get Auth
     * 
     * @return Rastor_Auth 
     */
    protected function _getAuth() {
        return Rastor_Auth::getInstance();
    }

    /**
     * Get Zend_Translate
     * 
     * @return Zend_Translate 
     */
    protected function _getTranslator() {
        return $this->_translator;
    }

    /**
     * Get Auth
     * 
     * @return Zend_Locale 
     */
    protected function _getLocale() {
        return $this->_locale;
    }
    
    protected function _getConfig() {
        return $this->_config;
    }

}

?>
