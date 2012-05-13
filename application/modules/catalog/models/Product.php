<?php

class Catalog_Model_Product extends Rastor_Model_TableAbstract {

    protected $_dbTableClassName = 'Catalog_Model_DbTable_Product';

    protected function _getEditUrl($record) {
        return Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cmsproduct', 'action' => 'edit', 'id' => $record->id));
    }
    
    protected function _getViewUrl($record) {
        return Rastor_Url::get('product', array('id' => $record->id));
    }

    protected function _getRecordParam($record, $param) {
        $catalogModel = new Catalog_Model_Catalog();

        switch ($param) {
            case 'catalog_id':
                return $catalogModel->getFullName($record->catalog_id, $this->_tableLanguage);
            case 'enable':
                return $record->enable ? "+" : "-";
            case 'datetime':
                return date('d.m.Y H:i:s', $record->$param);
            case 'date':
                return date('d.m.Y', $record->$param);
            default:
                return $record->$param;
        }
    }

}
