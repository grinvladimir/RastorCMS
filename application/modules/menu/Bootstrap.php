<?php

class Menu_Bootstrap extends Rastor_Application_Module_Bootstrap {
    
    protected function _initCallBack(){
        Rastor_Callback::getInstance()->addCallback('Menu_Model_MenuItem', 'rebuildMenuItems');
    }
    
}