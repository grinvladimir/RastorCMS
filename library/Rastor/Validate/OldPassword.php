<?php

class Rastor_Validate_OldPassword extends Zend_Validate_Abstract {

    const INCORECT = 'oldpasswordIncorect';

    protected $_messageTemplates = array(
        self::INCORECT => 'The old password is incorrect'
    );
    
    protected $_dbTable;
    protected $_login;

    public function __construct(Zend_Db_Table_Abstract $dbTable, $login) {
        $this->_dbTable = $dbTable;
        $this->_login = $login;
    }

    public function isValid($value) {
        
        $select = $this->_dbTable->select()
                ->where('login = ?', $this->_login)
                ->where('password = ?', md5($value));
        
        if ($this->_dbTable->fetchRow($select) !== NULL) {
            return true;
        }

        $this->_error(self::INCORECT);

        return false;
    }

}

?>
