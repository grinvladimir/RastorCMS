<?php

class Catalog_Model_Catalog extends Rastor_Model_Abstract {

    protected $_dbTableClassName = 'Catalog_Model_DbTable_Catalog';

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

    public function getCatalogUrl($item) {
        if (!empty($item->uri)) {
            return Rastor_Url::get('catalog', array('id' => $item->uri));
        } else if (!empty($item->id)) {
            return Rastor_Url::get('catalog', array('id' => $item->id));
        } else {
            return Rastor_Url::get('default');
        }
    }

    public function getMenuLinks($language) {
        $records = $this->getDbTable()->getRecords();

        $result = array('module=catalog|route=articles' => '*Каталог продукции');
        foreach ($records as $record) {
            $result['model=Catalog_Model_Catalog|params=id:' . $record->id] = $record->{$this->getParmLang('name', $language)};
        }
        $results = array('Каталог продукции' => $result);
        return $results;
    }

    public function getMenuItemParams($params) {
        foreach ($params as $key => $value) {
            if ($key == 'id') {
                $record = $this->getDbTable()->getEnableRecord($value);
                if (isset($record->id)) {
                    if (strlen($record->uri)) {
                        return array(
                            'route' => 'catalog',
                            'route_params' => 'id:' . $record->uri
                        );
                    } else {
                        return array(
                            'route' => 'catalog',
                            'route_params' => 'id:' . $record->id
                        );
                    }
                }
            }
        }

        return array(
            'href' => 'route=default',
            'route' => 'default',
            'params' => '',
            'route_params' => '',
            'module' => '',
            'controller' => '',
            'model' => ''
        );
    }

    private function getNavigationTreeArray($items, $parentId, $level, $url) {
        $array = array();

        foreach ($items as $item) {
            if (($parentId == $item->parent_id) && ($level == $item->depth)) {
                $childrens = $this->getNavigationTreeArray($items, $item->id, $level + 1, $url);

                $genUrl = $this->getCatalogUrl($item);
                $item = array(
                    'label' => $item->name,
                    'uri' => $genUrl,
                    'active' => ($genUrl == $url)
                );

                if (count($childrens)) {
                    $item['pages'] = $childrens;
                }
                $array[] = $item;
            }
        }

        return $array;
    }

    public function getNavigation($url, $language) {
        $items = $this->buildParams($this->getDbTable()->getEnableRecords(), $language, true);

        return new Zend_Navigation($this->getNavigationTreeArray($items, 0, 0, $url));
    }

    protected function getRecordForName($records, $id) {
        foreach ($records as $value) {
            if ($value->id == $id) {
                return $value;
            }
        }

        return false;
    }

    public function getSelectList() {
        $translator = Zend_Registry::get('Zend_Translate');
        $locale = Zend_Registry::get('Zend_Locale');

        $select = array(0 => $translator->_('Нет'));

        $records = $this->getDbTable()->getRecords();
        foreach ($records as $record) {
            $select[$record->id] = $this->getFullName($record->id, $locale->getLanguage(), $records, false);
        }

        return $select;
    }

    public function getFullName($id, $language, $records = null, $htmlSymbols = true) {
        if ($records === null) {
            $records = $this->getDbTable()->getRecords();
        }

        $names = array();

        do {
            $record = $this->getRecordForName($records, $id);

            if (isset($record->parent_id)) {
                $id = $record->parent_id;
                array_unshift($names, $record->{$this->getParmLang('name', $language)});
            }
        } while (($record !== false) && ($id != 0));

        if ($id != 0) {
            $translator = Zend_Registry::get('Zend_Translate');
            if ($htmlSymbols) {
                return '<span style="color: red;">' . $translator->_('Ошибка') . '</span>';
            } else {
                return $translator->_('*Ошибка');
            }
        }

        if ($htmlSymbols) {
            return implode(' &gt; ', $names);
        } else {
            return implode(' > ', $names);
        }
    }

}
