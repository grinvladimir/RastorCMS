<?php

/**
 * Abstract table class
 *
 * Rastor CMS
 * @author Budjak Orest
 * @copyright 2011 Budjak Orest (rastor.name)
 * @license http://www.php.net/license/3_01.txt
 * @version 0.9
 */
abstract class Rastor_Model_DbTable_Abstract extends Zend_Db_Table_Abstract {

    public function getRecords() {
        $select = $this->select();
        return $this->getAdapter()->fetchAll($select);
    }

    public function getEnableRecords() {
        $select = $this->select()
                ->where('enable = 1');
        return $this->getAdapter()->fetchAll($select);
    }

    public function getRecord($id) {
        $select = $this->select()
                ->where('id = ?', $id);
        return $this->getAdapter()->fetchRow($select);
    }

    public function getEnableRecord($id) {
        $select = $this->select()
                ->where('id = ?', $id)
                ->where('enable = 1');
        return $this->getAdapter()->fetchRow($select);
    }

    public function delete($id) {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        return parent::delete($where);
    }

    public function update($data, $id) {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        return parent::update($data, $where);
    }

    /**
     * Returns an instance of a Zend_Db_Table_Select object.
     *
     * @return Zend_Db_Table_Select
     */
    protected function _getRastorTableSelect($requestParams) {
        return $this->select();
    }

    /**
     * Returns an instance of a Zend_Paginator_Adapter_DbSelect data special for RastorTable js
     *
     * @return Zend_Paginator_Adapter_DbSelect
     */
    function getRastorTablePaginatorAdapter($order = '', $orderDirection = null, $requestParams = array()) {
        $select = $this->_getRastorTableSelect($requestParams);
        
        if (strlen($order)) {
            if ($orderDirection == 0) {
                $select->order($order);
            } else {
                $select->order($order . ' desc');
            }
        }

        return new Zend_Paginator_Adapter_DbSelect($select);
    }
    
    function isProtected($id) {
        $select = $this->select()
                ->from($this->_name, array('protected'))
                ->where('id = ?', $id);
        
        $result = $this->getAdapter()->fetchRow($select);

        return (isset($result->protected) && ($result->protected));
    }
    
    protected function _getPaginatorSelect($options){
        $select = $this->select()
                ->where('enable = 1');
        
        if (isset($options['order'])){
            $select->order($options['order']);
        }
        
        return $select;
    }
    
    function getPaginatorAdapter($options) {
        $select = $this->_getPaginatorSelect($options);

        return new Zend_Paginator_Adapter_DbSelect($select);
    }

}

?>
