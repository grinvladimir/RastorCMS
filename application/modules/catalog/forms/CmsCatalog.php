<?php

class Catalog_Form_CmsCatalog extends Rastor_Form {

    public function __construct() {
        parent::__construct();

        $this->setAction('')
                ->setMethod('post');

        foreach ($this->getLocales() as $lang => $locale) {
            $name = $this->createElement('text', $this->getParmLang('name', $lang), array('label' => $this->getTranslator()->_('Название') . ' (' . $lang . ')'));
            $name->addDecorator('errors', array('class' => 'error msg'))
                    ->setAttrib('class', 'big');

            $this->addElement($name);
        }

        foreach ($this->getLocales() as $lang => $locale) {
            $content = $this->createElement('textarea', $this->getParmLang('content', $lang), array('label' => $this->getTranslator()->_('Контент') . ' (' . $lang . ')'));
            $content->addDecorator('errors', array('class' => 'error msg'))
                    ->setAttrib('class', 'wyswisg')
                    ->setRequired(false);

            $this->addElement($content);

            $info[] = $this->getParmLang('content', $lang);
        }

        $uri = $this->createElement('text', 'uri', array('label' => 'Uri'));
        $uri->addDecorator('errors', array('class' => 'error msg'))
                ->setAttrib('class', 'big')
                ->addFilter(new Rastor_Filter_TranslitUrl());

        $enable = $this->createElement('checkbox', 'enable', array('required' => false, 'label' => 'Активность'));
        $enable->addDecorator('errors', array('class' => 'error msg'));

        $this->addElements(array($uri, $enable));

        $submit = $this->createElement('submit', 'submit', array('disableLoadDefaultDecorators' => true, 'required' => true, 'label' => 'Создать'));
        $submit->addDecorator('viewHelper')
                ->addDecorator('errors')
                ->addDecorator('htmlTag', array('tag' => 'p'));

        $this->addElement($submit);
    }

}
