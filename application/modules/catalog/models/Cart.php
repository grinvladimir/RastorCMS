<?php

class Catalog_Model_Cart {

    private $_cart;
    private $_items;
    private $_price;
    private $_count;

    function __construct() {
        $this->_cart = new Zend_Session_Namespace('Rastor_Cart');

        if ($this->_cart->count === null) {
            $this->_items = array();
            $this->genPrice();
            $this->save();
        } else {
            $this->_items = $this->_cart->items;
            $this->_price = $this->_cart->price;
            $this->_count = $this->_cart->count;
        }
    }

    public function clear() {
        $this->_items = array();
        $this->genPrice();
        $this->save();
    }

    public function addProduct($id) {
        $productDbTable = new Catalog_Model_DbTable_Product();
        $product = $productDbTable->getEnableRecord($id);

        if (isset($product->id)) {
            if ($this->inCart($id)) {
                $this->incCount($id);
            } else {
                $product->_count = 1;
                $this->_items[] = $product;
            }
            $this->genPrice();
            $this->save();
        }
    }

    public function deleteProduct($id) {
        foreach ($this->_items as $key => $value) {
            if ($id == $value->id) {
                unset($this->_items[$key]);
                $this->genPrice();
                $this->save();
            }
        }
    }

    public function getPrice() {
        return $this->_price;
    }

    public function getItems() {
        return $this->_items;
    }

    public function getCount() {
        return $this->_count;
    }

    public function setProductsCounts($array){
        foreach ($array as $key => $value) {
            $this->setProductCount($key, $value);
        }
        $this->genPrice();
        $this->save();
    }
    
    public function setProductCount($id, $count) {
        $count = (int)$count;
        if (is_int($count) && ($count > 0)) {
            foreach ($this->_items as $key => $value) {
                if ($id == $value->id) {
                    $this->_items[$key]->_count = $count;
                }
            }
        }
    }

    private function incCount($id) {
        foreach ($this->_items as $item) {
            if ($id == $item->id) {
                $item->_count = $item->_count + 1;
            }
        }
    }

    private function inCart($id) {
        foreach ($this->_items as $item) {
            if ($id == $item->id) {
                return true;
            }
        }

        return false;
    }

    private function genPrice() {
        $price = 0;
        $count = 0;
        foreach ($this->_items as $item) {
            $price = $price + $item->price * $item->_count;
            $count = $count + $item->_count;
        }

        $this->_price = $price;
        $this->_count = $count;
    }

    private function save() {
        $this->_cart->items = $this->_items;
        $this->_cart->price = $this->_price;
        $this->_cart->count = $this->_count;
    }

}