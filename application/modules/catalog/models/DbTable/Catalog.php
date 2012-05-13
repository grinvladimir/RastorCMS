<?php

class Catalog_Model_DbTable_Catalog extends Rastor_Model_DbTable_Abstract {

    protected $_name = 'catalog_items';
    protected $_primary = 'id';
    protected $_sequence = true;

    public function getRecords() {
        $select = $this->select()
                ->order('sort');
        return $this->getAdapter()->fetchAll($select);
    }
    
    public function getEnableRecords() {
        $select = $this->select()
                ->where('enable = 1')
                ->order('sort');
        return $this->getAdapter()->fetchAll($select);
    }
    
    public function getNextSortValue() {
        $select = $this->select()
                ->from($this->_name, array(new Zend_Db_Expr('max(sort) as max')));

        $row = $this->getAdapter()->fetchRow($select);

        return $row->max + 1;
    }
    
    function getRecordByUri($uri) {
        $select = $this->select()
                ->where('uri = ?', (String)$uri)
                ->orWhere('id = ?', $uri)
                ->where('enable = ?', 1);
        
        return $this->getAdapter()->fetchRow($select);
    }


    public function getChildrenRecords($id) {
        $select = $this->select()
                ->where('parent_id = ?', $id)
                ->order('sort');

        return $this->getAdapter()->fetchAll($select);
    }

    public function recursiveDelete($id) {
        $record = $this->getRecord($id);

        if ($this->delete($id)) {
            $records = $this->getChildrenRecords($id);
            foreach ($records as $value) {
                $this->recursiveDelete($value->id);
            }
        }
    }

}