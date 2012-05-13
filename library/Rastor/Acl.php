<?php

/**
 * Acl
 *
 * GlobalVision CMS
 * @author Budjak Orest
 * @copyright 2011-2012 Budjak Orest (rastor.name)
 * @license http://www.php.net/license/3_01.txt 
 * @version 1.0
 */

class Rastor_Acl extends Zend_Acl {
    
    public function __construct() {
        $this->addRole(new Rastor_Acl_Role('guest', Rastor_Acl_Role::getDefaultAccesLevel()));
        $this->addRole(new Rastor_Acl_Role('moderator', 2));
        $this->addRole(new Rastor_Acl_Role('admin', 1), 'moderator');
        $this->addRole(new Rastor_Acl_Role('superadmin', 0), 'moderator');
    }


    public function getAccessLevelList(){
        $list = $this->_getRoleRegistry()->getRoles();
        
        $result = array();
        foreach ($list as $key => $value) {
            $result[$key] = $value['instance']->getAccessLevel();
        }
        
        return $result;
    }
    
    public function getAccessLevel($roleId) {
        if (method_exists($this->getRole($roleId), 'getAccessLevel')){
            return $this->getRole($roleId)->getAccessLevel();
        } else {
            return Rastor_Acl_Role::getDefaultAccesLevel();
        }
    }
    
}
