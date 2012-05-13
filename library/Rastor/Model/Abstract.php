<?php

abstract class Rastor_Model_Abstract {

    protected $_dbTable;
    protected $_dbTableClassName = '';

    public function __construct() {
        if (class_exists($this->_dbTableClassName)) {
            $this->_dbTable = new $this->_dbTableClassName;
        } else {
            throw new Exception('DbTable class "'.$this->_dbTableClassName.'" not found');
        }
    }

    /**
     * Return DbTable
     * 
     * @return Rastor_Model_DbTable_Abstract
     */
    public function getDbTable() {
        return $this->_dbTable;
    }

    public function getParmLang($param, $lang) {
        return $param . '_' . $lang;
    }

    function buildHead($record, $view, $noTitle = false) {
        if (!$noTitle) {
            if (isset($record->title) && strlen($record->title)) {
                $view->headTitle($record->title);
            } else if (isset($record->name) && strlen($record->name)) {
                $view->headTitle($record->name);
            }
        }
        if (isset($record->keywords) && isset($record->description)) {
            $view->headMeta()->appendName('keywords', $record->keywords);
            $view->headMeta()->appendName('description', $record->description);
        }
    }

    /**
     * Get new params for one of language
     * 
     * @param type $data
     * @return type
     */
    public function buildParams($data, $language, $many = false) {
        if ($many) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->buildParams($value, $language);
            }
        } else if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (substr($key, -strlen($language)) == $language) {
                    $data[substr($key, 0, -strlen($language) - 1)] = $value;
                }
            }
        } else if (is_object($data)) {
            $array = (array) $data;
            foreach ($array as $key => $value) {
                if (substr($key, -strlen($language)) == $language) {
                    $data->{substr($key, 0, -strlen($language) - 1)} = $value;
                }
            }
        }

        return $data;
    }
    
    public function getPaginator($page = 1, $itemsPerPage = 10, $pageRange = 5, $language = '', $options = array()) {
        $paginator = new Zend_Paginator($this->getDbTable()->getPaginatorAdapter($options));

        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage)
                ->setPageRange($pageRange)
                ->setFilter(new Rastor_Filter_LanguageParams($language, true));

        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

        return $paginator;
    }

}
