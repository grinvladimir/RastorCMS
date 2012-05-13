<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Rastor_Acl_Role extends Zend_Acl_Role {

    private $_accessLevel;
    private static $_defaultAccessLevel = 100;

    public function __construct($roleId, $accessLevel = null) {
        parent::__construct($roleId);

        if (is_int($accessLevel)) {
            $this->_accessLevel = $accessLevel;
        } else {
            $this->_accessLevel = self::$_defaultAccessLevel;
        }
    }

    public static function setDefaultAccesLevel($level) {
        self::$_defaultAccessLevel = $level;
    }

    public static function getDefaultAccesLevel() {
        return self::$_defaultAccessLevel;
    }

    public function getAccessLevel() {
        return $this->_accessLevel;
    }

}

?>
