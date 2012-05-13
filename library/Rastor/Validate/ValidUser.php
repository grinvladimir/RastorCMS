<?php

class Rastor_Validate_ValidUser extends Zend_Validate_Abstract {

    const NOT_FOUND = 'userNotFound';

    protected $_messageTemplates = array(
        self::NOT_FOUND => 'Incorrect login or password'
    );
    protected $_dbTable;
    protected $_contextKey;

    public function __construct(Zend_Db_Table_Abstract $dbTable, $key) {
        $this->_dbTable = $dbTable;
        $this->_contextKey = $key;
    }

    public function isValid($value, $context = null) {

        $value = (string) $value;

        if (is_array($context)) {
            if (isset($context[$this->_contextKey])) {
                $select = $this->_dbTable->select()
                        ->where('login = ?', $context[$this->_contextKey])
                        ->where('password = ?', md5($value));

                if ($this->_dbTable->fetchRow($select) !== NULL) {
                    return true;
                }
            }
        } else if (is_string($context)) {
            $select = $this->_dbTable->select()
                    ->where('login = ?', $context)
                    ->where('password = ?', md5($value));

            if ($this->_dbTable->fetchRow($select) !== NULL) {
                return true;
            }
        }

        $this->_error(self::NOT_FOUND);

        return false;
    }

}

?>
