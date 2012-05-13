<?php

class Menu_Model_MenuItem extends Rastor_Model_TableAbstract {

    protected $_dbTableClassName = 'Menu_Model_DbTable_MenuItem';

    protected function _getEditUrl($record) {
        return Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cmsmenu', 'action' => 'edit', 'id' => $record->id));
    }

    public function getJSONMenuList($id) {
        $records = $this->getDbTable()->getMenuRecords($id);

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

}
