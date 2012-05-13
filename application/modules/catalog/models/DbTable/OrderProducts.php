<?php

class Catalog_Model_DbTable_OrderProducts extends Rastor_Model_DbTable_Abstract {

    protected $_name = 'catalog_orders_products';
    protected $_primary = 'id';
    protected $_sequence = true;

    protected function _getRastorTableSelect($requestParams = array()) {
        $select = $this->select()
                ->from(array('cop' => $this->_name), array(
                    'oid' => 'cop.id',
                    'total_price' => '(cop.count * cp.price)',
                    'count' => 'cop.count'
                ))
                ->setIntegrityCheck(false)
                ->joinRight(array('cp' => 'catalog_products'), 'cp.id = cop.product_id');

        if (isset($requestParams['id'])) {
            $select->where('cop.order_id = ?', $requestParams['id']);
        }

        return $select;
    }

    public function getTotalPrice($id) {
        $select = $this->select()
                ->from(array('cop' => $this->_name), array('total_price' => 'sum(cop.count * cp.price)'))
                ->setIntegrityCheck(false)
                ->joinRight(array('cp' => 'catalog_products'), 'cp.id = cop.product_id')
                ->where('cop.order_id = ?', $id);

        $totalPrice = $this->getAdapter()->fetchRow($select);
        return $totalPrice->total_price;
    }

}