<?php

class Menu_Model_MenuItem extends Rastor_Model_Abstract {

    protected $_dbTableClassName = 'Menu_Model_DbTable_MenuItem';

    public function getJSONMenuList() {
        $records = $this->getDbTable()->getRecords();

        $config = Zend_Registry::get('config');
        $mainLanguage = $config->locales->key();
        $info = $this->getDbTable()->info();

        $auth = Rastor_Auth::getInstance();
        $authData = $auth->getIdentity();

        if (strlen($authData->table_lang) && (array_key_exists($authData->table_lang, $config->locales->toArray()))) {
            $language = $authData->table_lang;
        } else if (strlen($authData->lang) && (array_key_exists($authData->lang, $config->locales->toArray()))) {
            $language = $authData->lang;
        } else {
            $language = $mainLanguage;
        }

        foreach ($records as $key => $value) {
            $records[$key]->name = $value->{$this->getParmLang('name', $language)};
        }

        return Zend_Json::encode($records);
    }

    public function getMenuLinks($language) {
        $classes = Zend_Registry::get('ModelList');
        $result = array(
            '' => 'Нет',
            'route=default' => 'Главная'
        );
        foreach ($classes as $class) {
            $obj = new $class;
            if (method_exists($obj, 'getMenuLinks')) {
                $result = array_merge($result, $obj->getMenuLinks($language));
            }
        }
        return $result;
    }

    private function getTreeArray($items, $parentId, $level, $language) {
        $array = array();
        foreach ($items as $item) {
            if (($parentId == $item->parent_id) && ($level == $item->depth)) {
                $childrens = $this->getTreeArray($items, $item->id, $level + 1, $language);
                if (count($childrens)) {
                    $item->childrens = $childrens;
                }
                $item->name = $item->{Functions::getParmLang('name', $language)};
                $array[] = $item;
            }
        }
        return $array;
    }

    private function isActive($item, $url, $genUrl) {
        if (strlen($item->module) && strlen($item->controller)) {
            return (($item->module == $this->module) && ($item->controller == $this->controller));
        }
        if (strlen($item->module)) {
            return ($item->module == $this->module);
        }

        return ($url == $genUrl);
    }

    private function getUrl($item) {
        if (strlen($item->route)) {
            $result = array();
            $params = explode(';', $item->route_params);
            foreach ($params as $value) {
                $record = explode(':', $value);
                if (count($record) == 2) {
                    $result[$record[0]] = $record[1];
                }
            }
            
            return Rastor_Url::get($item->route, $result);
        }

        if (strlen($item->url)) {
            return $item->url;
        }

        return Rastor_Url::get('default');
    }

    private function getNavigationTreeArray($items, $parentId, $level, $url, $language) {
        $array = array();

        foreach ($items as $item) {
            if (($parentId == $item->parent_id) && ($level == $item->depth)) {
                $childrens = $this->getNavigationTreeArray($items, $item->id, $level + 1, $url, $language);

                $genUrl = $this->getUrl($item);
                $active = $this->isActive($item, $url, $genUrl);
                $item = array(
                    'label' => $item->{$this->getParmLang('name', $language)},
                    'uri' => $genUrl,
                    'active' => $active
                );

                if (count($childrens)) {
                    $item['pages'] = $childrens;
                }
                $array[] = $item;
            }
        }

        return $array;
    }

    public function getNavigationArray($url, $language) {
        $this->module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
        $this->controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();

        $items = $this->getDbTable()->getEnableRecords();

        return $this->getNavigationTreeArray($items, 0, 0, $url, $language);
    }

    public function getInsertParams($href, $url) {
        $result = array(
            'href' => '',
            'module' => '',
            'controller' => '',
            'route' => '',
            'params' => '',
            'model' => ''
        );
        
        if (empty($url)){
            $result['href'] = $href;
            $params = explode('|', $href);
            foreach ($params as $value) {
                $record = explode('=', $value);
                if (count($record) == 2) {
                    switch ($record[0]) {
                        case 'module':
                            $result['module'] = $record[1];
                            break;
                        case 'controller':
                            $result['controller'] = $record[1];
                            break;
                        case 'route':
                            $result['route'] = $record[1];
                            break;
                        case 'model':
                            $result['model'] = $record[1];
                            break;
                        case 'params':
                            $result['params'] = $record[1];
                            break;
                        default :
                            throw new Exception('Bad href');
                    }
                }
            }
        }
        return $result;
    }

    public function rebuildMenuItems() {
        $records = $this->getDbTable()->getRecords();

        foreach ($records as $record) {
            if (strlen($record->model)) {
                if (method_exists($record->model, 'getMenuItemParams')) {
                    $params = array();
                    $paramsList = explode(';', $record->params);
                    foreach ($paramsList as $value) {
                        $param = explode(':', $value);
                        if (count($param) == 2) {
                            $params[$param[0]] = $param[1];
                        }
                    }

                    $obj = new $record->model;
                    $this->getDbTable()->update($obj->getMenuItemParams($params), $record->id);
                }
            }
        }
    }

}
