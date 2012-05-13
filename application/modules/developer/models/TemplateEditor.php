<?php

class Developer_Model_TemplateEditor extends Rastor_Model_TableAbstract {

    protected $_dbTableClassName = 'Developer_Model_Adapter';

    protected function _getTableViewParams($params){
        return $params;
    }

    protected function _getEditUrl($record){
        return Rastor_Url::get('admin', array('module' => 'developer', 'controller' => 'editor', 'action' => 'edit')) . '?file=' . $record->fullfilename;
    }
    
}
