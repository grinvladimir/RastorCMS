<?php

class Catalog_Form_CmsProduct extends Rastor_Form {

    public function __construct(Array $data = array()) {
        parent::__construct();

        $this->setAction('')
                ->setMethod('post');

        $info = array();
        $seo = array();

        foreach ($this->getLocales() as $lang => $locale) {
            $name = $this->createElement('text', $this->getParmLang('name', $lang), array('label' => $this->getTranslator()->_('Название') . ' (' . $lang . ')'));
            $name->addDecorator('errors', array('class' => 'error msg'))
                    ->setAttrib('class', 'big');

            $this->addElement($name);

            $info[] = $this->getParmLang('name', $lang);
        }

        foreach ($this->getLocales() as $lang => $locale) {
            $smallcontent = $this->createElement('textarea', $this->getParmLang('smallcontent', $lang), array('label' => $this->getTranslator()->_('Краткое описание') . ' (' . $lang . ')'));
            $smallcontent->addDecorator('errors', array('class' => 'error msg'))
                    ->setAttrib('class', 'wyswisg')
                    ->setRequired();

            $this->addElement($smallcontent);

            $info[] = $this->getParmLang('smallcontent', $lang);
        }

        foreach ($this->getLocales() as $lang => $locale) {
            $content = $this->createElement('textarea', $this->getParmLang('content', $lang), array('label' => $this->getTranslator()->_('Контент') . ' (' . $lang . ')'));
            $content->addDecorator('errors', array('class' => 'error msg'))
                    ->setAttrib('class', 'wyswisg')
                    ->setRequired();

            $this->addElement($content);

            $info[] = $this->getParmLang('content', $lang);
        }

        $model = new Catalog_Model_Catalog();

        $catalogId = $this->createElement('select', 'catalog_id', array('label' => 'Каталог'));
        $catalogId->addDecorator('errors', array('class' => 'error msg'))
                ->setMultiOptions($model->getSelectList());
        
        $old_price = $this->createElement('text', 'old_price', array('label' => 'Цена без скидки'));
        $old_price->addDecorator('errors', array('class' => 'error msg'));
        
        $price = $this->createElement('text', 'price', array('label' => 'Цена'));
        $price->addDecorator('errors', array('class' => 'error msg'));

        $preview = $this->createElement('hidden', 'preview', array('required' => false));

        $special = $this->createElement('checkbox', 'special', array('required' => false, 'label' => 'Специальное предложение'));
        $special->addDecorator('errors', array('class' => 'error msg'));
        
        $exist = $this->createElement('checkbox', 'exist', array('required' => false, 'label' => 'Наличие'));
        $exist->addDecorator('errors', array('class' => 'error msg'));
        
        $enable = $this->createElement('checkbox', 'enable', array('required' => false, 'label' => 'Активность'));
        $enable->addDecorator('errors', array('class' => 'error msg'));


        $this->addElements(array(
            $catalogId,
            $old_price,
            $price,
            $preview,
            $special,
            $exist,
            $enable
        ));

        $auth = Rastor_Auth::getInstance();

        if ($auth->getIdentity()->accessLevel == 0) {
            $protected = $this->createElement('checkbox', 'protected', array('required' => false, 'label' => 'Защищенная запись'));
            $protected->addDecorator('errors', array('class' => 'error msg'));
            
            $this->addElement($enable);
        }

        foreach ($this->getLocales() as $lang => $locale) {
            $title = $this->createElement('text', $this->getParmLang('title', $lang), array('label' => 'title' . ' (' . $lang . ')'));
            $title->setAttrib('class', 'big')
                    ->setRequired(false);

            $keywords = $this->createElement('text', $this->getParmLang('keywords', $lang), array('label' => 'keywords' . ' (' . $lang . ')'));
            $keywords->setAttrib('class', 'big')
                    ->setRequired(false);

            $description = $this->createElement('text', $this->getParmLang('description', $lang), array('label' => 'description' . ' (' . $lang . ')'));
            $description->setAttrib('class', 'big')
                    ->setRequired(false);

            $seo[] = $this->getParmLang('title', $lang);
            $seo[] = $this->getParmLang('keywords', $lang);
            $seo[] = $this->getParmLang('description', $lang);

            $this->addElements(array($title, $keywords, $description));
        }

        $this->addDisplayGroup(array_merge($info, array('catalog_id', 'uri', 'old_price', 'price', 'preview', 'special', 'exist', 'enable', 'protected')), 'info')
                ->addDisplayGroup($seo, 'seo')
                ->setDisplayGroupDecorators(array('FormElements', 'fieldset'));

        $this->getDisplayGroup('seo')->setLegend('SEO');
        $this->getDisplayGroup('info')->setLegend('Информация');

        $submit = $this->createElement('submit', 'submit', array('disableLoadDefaultDecorators' => true, 'required' => true, 'label' => 'Создать'));
        $submit->addDecorator('viewHelper')
                ->addDecorator('errors')
                ->setAttrib('class', 'submit_margin')
                ->addDecorator('htmlTag', array('tag' => 'p'));

        $this->addElement($submit);
    }

}
