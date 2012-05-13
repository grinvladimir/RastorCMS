<?php

class Catalog_Model_Order extends Rastor_Model_TableAbstract {

    protected $_dbTableClassName = 'Catalog_Model_DbTable_Order';
    protected $_statuses = array(
        0 => 'Новый',
        1 => 'Обработан',
        2 => 'Отклонен'
    );

    public function __construct() {
        parent::__construct();
        $this->getStatuses();
    }

    protected function _getEditUrl($record) {
        return Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cmsorderproducts', 'action' => 'showlist', 'id' => $record->id));
    }

    public function getStatuses() {
        $translator = Zend_Registry::get('Zend_Translate');

        foreach ($this->_statuses as $key => $value) {
            $this->_statuses[$key] = $translator->_($value);
        }

        return $this->_statuses;
    }

    protected function _getRecordParam($record, $param) {
        switch ($param) {
            case 'status':
                return $this->_statuses[$record->status];
            case 'date':
                return date('d.m.Y H:i:s', $record->$param);
            default:
                return $record->$param;
        }
    }

    public function add(Catalog_Model_Cart $cart, $info) {
        $orderProductsDbTable = new Catalog_Model_DbTable_OrderProducts();
        
        $order = array(
            'date' => time(),
            'info' => $info
        );

        $orderId = $this->getDbTable()->insert($order);
        
        foreach ($cart->getItems() as $item) {
            $orderProductsDbTable->insert(array(
                'order_id' => $orderId,
                'product_id' => $item->id,
                'count' => $item->_count
            ));
        }
    }

}
