<?php

class Menu_Model_DbTable_MenuItem extends Rastor_Model_DbTable_Abstract {

    protected $_name = 'menu_items';
    protected $_primary = 'id';
    protected $_sequence = true;

    public function getMenuRecords($id) {
        $select = $this->select()
                ->where('menu_id = ?', $id)
                ->order('sort');

        return $this->getAdapter()->fetchAll($select);
    }

    public function getNextSortValue() {
        $select = $this->select()
                ->from($this->_name, array(new Zend_Db_Expr('max(sort) as max')));

        $row = $this->getAdapter()->fetchRow($select);

        return $row->max + 1;
    }

    public function getChildrenRecords($menuId, $id) {
        $select = $this->select()
                ->where('menu_id = ?', $menuId)
                ->where('parent_id = ?', $id)
                ->order('sort');

        return $this->getAdapter()->fetchAll($select);
    }

    public function recursiveDelete($id, $firstCall = false) {
        $record = $this->getRecord($id);

        if ($this->delete($id)) {
            $records = $this->getChildrenRecords($record->menu_id, $id);
            foreach ($records as $value) {
                $this->recursiveDelete($value->id);
            }
        }
    }

}