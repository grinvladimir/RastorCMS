<?php

class Catalog_Model_OrderProducts extends Rastor_Model_TableAbstract {

    protected $_dbTableClassName = 'Catalog_Model_DbTable_OrderProducts';

    protected function _getViewUrl($record) {
        return Rastor_Url::get('product', array('id' => $record->id));
    }
    
    protected function _getEditUrl($record) {
        return Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cmsproduct', 'action' => 'edit', 'id' => $record->id));
    }

    protected function _getRecordParam($record, $param) {
        switch ($param) {
            case 'name':
                return $record->{$this->getParmLang('name', $this->_tableLanguage)};
            default:
                return $record->$param;
        }
    }

}
