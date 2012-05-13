<?php

/**
 * Auth class
 *
 * GlobalVision CMS
 * @author Budjak Orest
 * @copyright 2011-2012 Budjak Orest (rastor.name)
 * @license http://www.php.net/license/3_01.txt 
 * @version 1.0
 */

class Rastor_Auth extends Zend_Auth {

    private $_defaultRole = 'guest';

    /**
     * Returns an instance of Zend_Auth
     *
     * Singleton pattern implementation
     *
     * @return Rastor_Auth
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getRole() {
        if (!$this->hasIdentity()) {
            return $this->_defaultRole;
        } else {
            return $this->getIdentity()->role;
        }
    }

    public function getAccessLevel() {
        if (!$this->hasIdentity() || (!isset($this->getIdentity()->accessLevel))) {
            return Rastor_Acl_Role::getDefaultAccesLevel();
        } else {
            return $this->getIdentity()->accessLevel;
        }
    }

    public function setIdentity($data) {
        $storage = $this->getStorage();
        
        if (isset($data->role)) {
            $acl = Zend_Registry::get('Acl');
            $data->accessLevel = $acl->getAccessLevel($data->role);
        }

        $storage->write($data);
    }

}
