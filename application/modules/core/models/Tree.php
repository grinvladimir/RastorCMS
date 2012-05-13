<?php

class Core_Model_Tree {

    protected $_params = array(
        'nestedSortable' => array(
            'disableNesting' => 'no-nest',
            'forcePlaceholderSize' => true,
            'handle' => 'div',
            'helper' => 'clone',
            'items' => 'li',
            'maxLevels' => 10,
            'opacity' => '.6',
            'placeholder' => 'placeholder',
            'revert' => 250,
            'tabSize' => 25,
            'tolerance' => 'pointer',
            'toleranceElement' => '> div'
        ),
        'msgShowTime' => 3000,
        'startMsg' => '',
        'removeUrl' => '',
        'reloadUrl' => '',
        'editUrl' => '',
        'saveUrl' => '',
        'data' => '',
        'tranlation' => array()
    );
    protected $_translations = array(
        'removeOne' => 'Удалить запись?',
        'removeOneConfirm' => 'Вы действительно хотите удалить запись?',
        'editTitle' => 'Редактировать',
        'removeTitle' => 'Удалить',
        'buttonYes' => 'Да',
        'buttonNo' => 'Нет',
        'buttonAdd' => 'Добавить новую запись'
    );

    public function __construct($params = array()) {
        $this->_params = array_merge($this->_params, $params);
    }

    public function getTranslations() {
        $view = Zend_Layout::getMvcInstance()->getView();

        $translator = Zend_Registry::get('Zend_Translate');

        foreach ($this->_translations as $key => $value) {
            $this->_translations[$key] = $translator->_($value);
        }

        return $this->_translations;
    }

    public function getJSONObject() {
        $this->getTranslations();

        $messager = new Rastor_Controller_Cms_Messager();
        $message = $messager->getAsArray();

        $result = array_merge($this->_params, array(
            'translation' => $this->_translations,
            'startMsg' => $message
                ));

        return Zend_Json::encode($result);
    }

}