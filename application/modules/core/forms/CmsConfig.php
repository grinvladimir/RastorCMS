<?php

class Core_Form_CmsConfig extends Rastor_Form {

    public function __construct(Array $data = array()) {
        parent::__construct();

        $this->setAction('')
                ->setMethod('post');

        $auth = Rastor_Auth::getInstance();
        $userData = $auth->getIdentity();

        $config = Zend_Registry::get('config');

        $language = $this->createElement('select', 'lang', array('label' => 'Язык CMS'));
        $language->addDecorator('errors', array('class' => 'error msg'))
                ->setMultiOptions($this->getLocalesTranslationList($config->cmslocales->toArray()))
                ->setValue($userData->lang);

        $tableLanguage = $this->createElement('select', 'table_lang', array('label' => 'Приоритетный язык отображения данных'));
        $tableLanguage->addDecorator('errors', array('class' => 'error msg'))
                ->setMultiOptions($this->getLocalesTranslationList($config->locales->toArray()))
                ->setValue($userData->table_lang);

        $tableRows = $this->createElement('select', 'table_rows', array('label' => 'Максимальное количество записей на странице (для таблицы)'));
        $tableRows->addDecorator('errors', array('class' => 'error msg'))
                ->setMultiOptions(array(
                    10 => 10,
                    25 => 25,
                    50 => 50,
                    100 => 100,
                    200 => 200,
                    0 => 'Все'
                ))
                ->setValue($userData->table_rows);


        $submit = $this->createElement('submit', 'submit', array('label' => 'Сохранить', 'disableLoadDefaultDecorators' => true));
        $submit->addDecorator('viewHelper')
                ->addDecorator('htmlTag', array('tag' => 'p'))
                ->setAttrib('class', 'button');

        $this->addElements(array(
            $language,
            $tableLanguage,
            $tableRows
        ));

        $this->addElement($submit);
        
        if (count($data)) {
            $this->setValues($data);
        }
    }

    function getLocalesTranslationList($array) {
        foreach ($array as $key => $value) {
            $array[$key] = Zend_Locale::getTranslation($key, 'language', $value);
        }

        return $array;
    }

}
