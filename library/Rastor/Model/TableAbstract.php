<?php

abstract class Rastor_Model_TableAbstract extends Rastor_Model_Abstract {

    protected $_defaultItemCountPerPage = 10;
    protected $_tableLanguage = '';

    /**
     * Get data for table
     * 
     * @param array $records
     * @param array $viewparams
     * @return string 
     */
    public function getTableData($viewparams, $options = array(), $requestParams = array()) {
        $this->_getTableLanguage();
        
        if ((isset($options['rebuildParams'])) && ($options['rebuildParams'] == true)) {
            $viewparams = $this->_getTableViewParams($viewparams, $options['removeParams']);
        }

        if (isset($viewparams[$options['order']]) && ($this->_canOrder($viewparams[$options['order']]))) {
            $order = $viewparams[$options['order']];
        } else {
            if ($options['sort']) {
                $order = 'sort';
            } else {
                $order = '';
            }
        }

        $paginator = $this->_getRastorTablePaginator($order, $options['orderDirection'], $requestParams);
        $paginator->setCurrentPageNumber($options['page'])
                ->setItemCountPerPage($this->_getItemCountPerPage())
                ->setPageRange($options['pageRange']);

        $pages = $paginator->getPages();
        $pages->pagesInRange = array_values($pages->pagesInRange);
        $result->pages = $pages;

        $result->data = array();
        foreach ($paginator->getIterator() as $record) {
            $list = array();
            foreach ($viewparams as $param) {
                $list[] = $this->_getRecordParam($record, $param);
            }
            $result->data[] = $list;
        }

        $result->viewLinks = array();
        $result->editLinks = array();
        foreach ($paginator->getIterator() as $record) {
            $result->viewLinks[] = $this->_getViewUrl($record);
            $result->editLinks[] = $this->_getEditUrl($record);
        }
        
        return Zend_Json::encode($result);
    }

    protected function _getTableLanguage(){
        $config = Zend_Registry::get('config');
        $mainLanguage = $config->locales->key();
        
        $auth = Rastor_Auth::getInstance();
        $authData = $auth->getIdentity();

        if (strlen($authData->table_lang) && (array_key_exists($authData->table_lang, $config->locales->toArray()))) {
            $this->_tableLanguage = $authData->table_lang;
        } else if (strlen($authData->lang) && (array_key_exists($authData->lang, $config->locales->toArray()))) {
            $this->_tableLanguage = $authData->lang;
        } else {
            $this->_tableLanguage = $mainLanguage;
        }
        
        return $this->_tableLanguage;
    }


    protected function _getItemCountPerPage() {
        $auth = Rastor_Auth::getInstance();
        $authData = $auth->getIdentity();

        if (isset($authData->table_rows) && (is_numeric($authData->table_rows))) {
            return $authData->table_rows;
        }

        return $this->_defaultItemCountPerPage;
    }

    protected function _getRecordParam($record, $param) {
        switch ($param) {
            case 'enable':
                return $record->enable ? "+" : "-";
            case 'datetime':
                return date('d.m.Y H:i:s', $record->$param);
            case 'date':
                return date('d.m.Y', $record->$param);
            default:
                return $record->$param;
        }
    }

    protected function _canOrder($param){
        $info = $this->getDbTable()->info();
        return in_array($param, $info['cols']);
    }
    
    protected function _getTableViewParams($params, $remove = true) {
        $info = $this->getDbTable()->info();

        foreach ($params as $key => $param) {
            if (!in_array($param, $info['cols'])) {
                if (!in_array($this->getParmLang($param, $this->_tableLanguage), $info['cols'])) {
                    if ($remove) {
                        unset($params[$key]);
                    }
                } else {
                    $params[$key] = $this->getParmLang($param, $this->_tableLanguage);
                }
            }
        }

        return $params;
    }

    protected function _getRastorTablePaginator($order, $orderDirection, $requestParams) {
        return new Zend_Paginator($this->getDbTable()->getRastorTablePaginatorAdapter($order, $orderDirection, $requestParams));
    }

    public function setSortValue($value, $id) {
        return $this->getDbTable()->update(array('sort' => $value), $id);
    }

    protected function _getViewUrl($record) {
        return '';
    }

    protected function _getEditUrl($record) {
        return '';
    }

}
