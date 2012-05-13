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
class Rastor_Form extends Zend_Form {

    protected $_locales;
    protected $_locale;

    public function __construct() {
        parent::__construct();

        $translator = Zend_Registry::get('Zend_Translate');
        $this->setTranslator($translator);
        
        $this->_locale = Zend_Registry::get('Zend_Locale');

        $config = Zend_Registry::get('config');
        $this->_locales = $config->locales;
    }

    public function setValues($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($this->hasElement($key)) {
                    $this->getElement($key)->setValue($value);
                }
            }
        } else if ($data instanceof stdClass) {
            $data = (Array) $data;

            foreach ($data as $key => $value) {
                if ($this->hasElement($key)) {
                    $this->getElement($key)->setValue($value);
                }
            }
        }
    }

    /**
     * Get Auth
     * 
     * @return Zend_Locale 
     */
    public function getLocale(){
        return $this->_locale;
    }
    
    public function getLanguage(){
        return $this->_locale->getLanguage();
    }
    
    public function hasElement($name) {
        return array_key_exists($name, $this->_elements);
    }

    public function getLocales() {
        return $this->_locales;
    }

    public function getParmLang($param, $lang) {
        return $param . '_' . $lang;
    }

}