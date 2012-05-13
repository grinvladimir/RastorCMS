<?php

/**
 * Controller action abstract class for cms
 *
 * GlobalVision CMS
 * @author Budjak Orest
 * @copyright 2011-2012 Budjak Orest (rastor.name)
 * @license http://www.php.net/license/3_01.txt 
 * @version 1.0
 */
abstract class Rastor_Controller_Cms_Action extends Rastor_Controller_Action {

    protected $_languages;
    protected $_mainLanguage;
    protected $_modelClassName = '';
    protected $_model;

    public function init() {
        parent::init();

        $this->_languages = array_flip($this->_config->locales->toArray());
        $this->_mainLanguage = $this->_config->locales->key();

        $this->_helper->layout->setLayout('admin');
        
        Core_View_Helper_CmsTitle::setNavigation(new Zend_Navigation(Zend_Registry::get('CmsMenu')));
        Core_View_Helper_CmsTitle::setDocumentTitle('GlobalVisionCMS 5.0');

        if (class_exists($this->_modelClassName)) {
            $this->_model = new $this->_modelClassName;
        } else {
            throw new Exception('Model "' . $this->_modelClassName . '" not found');
        }
    }

    /**
     * Get Model
     * 
     * @return Rastor_Model_Abstract 
     */
    public function getModel() {
        return $this->_model;
    }

}

?>
