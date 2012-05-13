<?php

class Core_Model_DbTable_Users extends Rastor_Model_DbTable_Abstract {

    protected $_name = 'users';
    protected $_primary = 'id';
    protected $_sequence = true;
    
    
    public function getAdminMailList() {
        $select = $this->getAdapter()->select()
                        ->from($this->_name, array('login', 'email'))
                        ->where('role = ?', 'admin');
        
        return $this->getAdapter()->fetchAll($select, null, Zend_Db::FETCH_OBJ);
    }
    
    public function canAuth($login, $password) {
        $select = $this->getAdapter()->select()
                        ->from($this->_name, 'id')
                        ->where('login = ?', $login)
                        ->where('password = ?', md5($password))
                        ->limit(1);
        
        if (count($this->getAdapter()->fetchAll($select))) {
            return true;
        }
        return false;
    }
}

?>
