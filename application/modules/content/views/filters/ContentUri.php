<?php

class Content_View_Filter_Content_Uri implements Zend_Filter_Interface {

    protected $_userUri = '';
    protected $_name = '';

    public function __construct($options = array()) {
        if (!empty($options['generatedUri'])) {
            $this->_generatedUri = $options['generatedUri'];
        }

        if (!empty($options['name'])) {
            $this->_name = $options['name'];
        }
    }

    public function filter($value) {
        Zend_Debug::dump($this->name);
        die;
    }

}
