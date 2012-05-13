<?php

class Menu_Model_Menu extends Rastor_Model_TableAbstract {

    protected $_dbTableClassName = 'Menu_Model_DbTable_Menu';

    protected function _getEditUrl($record) {
        return Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cmsmenu', 'action' => 'edit', 'id' => $record->id));
    }
    
}
