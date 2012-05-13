<?php

class Core_Model_Auth extends Zend_Auth_Adapter_DbTable {

    protected $_tableName = 'users';
    protected $_identityColumn = 'login';
    protected $_credentialColumn = 'password';
    
    public function getAuthResult($login, $password) {
        $this->setIdentity($login);
        $this->setCredential(md5($password));

        $auth = Zend_Auth::getInstance();
        return $auth->authenticate($this);
    }

}