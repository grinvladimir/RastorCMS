<?php

class Rastor_Filter_LanguageParams implements Zend_Filter_Interface {

    protected $_language = '';

    public function __construct($language) {
        $this->_language = $language;
    }

    public function filter($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->filter($value);
            }
        } else if (is_object($data)) {
            $array = (array) $data;
            foreach ($array as $key => $value) {
                if (substr($key, -strlen($this->_language)) == $this->_language) {
                    $data->{substr($key, 0, -strlen($this->_language) - 1)} = $value;
                }
            }
        }

        return $data;
    }

}