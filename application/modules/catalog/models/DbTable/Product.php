<?php

class Catalog_Model_DbTable_Product extends Rastor_Model_DbTable_Abstract {

    protected $_name = 'catalog_products';
    protected $_primary = 'id';
    protected $_sequence = true;

    protected function _getPaginatorSelect($options) {
        $select = parent::_getPaginatorSelect($options);

        if (isset($options['catalog_id'])) {
            $select->where('catalog_id = ?', $options['catalog_id']);
        }

        return $select;
    }

    public function getSpecialProducts() {
        $select = $this->select()
                ->where('enable = ?', 1)
                ->where('special = ?', 1)
                ->limit(3);
        
        return $this->getAdapter()->fetchAll($select);
    }
    
    public function getNewProducts() {
        $select = $this->select()
                ->where('enable = ?', 1)
                ->limit(6);
        
        return $this->getAdapter()->fetchAll($select);
    }

}