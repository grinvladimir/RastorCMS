<?php

class Rastor_Filter_UriBuilder implements Zend_Filter_Interface {

    protected $_userUri;
    protected $_name;

    public function __construct($options = array()) {
        if (!empty($options['userUri'])) {
            $this->_userUri = $options['userUri'];
        }

        if (!empty($options['name'])) {
            $this->_name = $options['name'];
        }
    }

    public function filter($value) {
        $translitUriFilter = new Rastor_Filter_TranslitUrl();
        
        if (strlen($this->_userUri->getValue())) {
            return $translitUriFilter->filter($this->_userUri->getValue());
        } else {
            return $translitUriFilter->filter($this->_name->getValue());
        }
    }

}