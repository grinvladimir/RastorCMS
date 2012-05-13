<?php

class Content_Model_DbTable_Content extends Rastor_Model_DbTable_Abstract {

    protected $_name = 'contents';
    protected $_primary = 'id';
    protected $_sequence = true;

    /**
     * Get recod by uri
     * 
     * @param string $uri
     * @return stdClass 
     */
    function getRecordByUri($uri) {
        $select = $this->select()
                ->where('uri = ?', $uri)
                ->where('enable = 1');
        return $this->getAdapter()->fetchRow($select);
    }

}