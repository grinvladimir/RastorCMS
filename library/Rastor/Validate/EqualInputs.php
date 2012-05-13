<?php

class Rastor_Validate_EqualInputs extends Zend_Validate_Abstract {

    const NOT_EQUAL = 'stringsNotEqual';

    protected $_messageTemplates = array(
        self::NOT_EQUAL => 'Strings are not equal'
    );
    
    protected $_contextKey;

    public function __construct($key) {
        $this->_contextKey = $key;
    }

    public function isValid($value, $context = null) {

        $value = (string) $value;

        if (is_array($context)) {
            if (isset($context[$this->_contextKey]) && ($value === $context[$this->_contextKey])) {
                return true;
            }
        } else if (is_string($context) && ($value === $context)) {
            return true;
        }

        $this->_error(self::NOT_EQUAL);

        return false;
    }

}