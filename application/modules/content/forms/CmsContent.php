<?php

class Content_Form_CmsContent extends Rastor_Form {

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
            $content = $this->createElement('textarea', $this->getParmLang('content', $lang), array('label' => $this->getTranslator()->_('Контент') . ' (' . $lang . ')'));
            $content->addDecorator('errors', array('class' => 'error msg'))
                    ->setAttrib('class', 'wyswisg')
                    ->setRequired();

            $this->addElement($content);

            $info[] = $this->getParmLang('content', $lang);
        }

        $userUri = $this->createElement('text', 'user_uri', array('label' => 'Uri'));
        $userUri->addDecorator('errors', array('class' => 'error msg'))
                ->setAttrib('class', 'big')
                ->addFilter(new Rastor_Filter_TranslitUrl());

        $enable = $this->createElement('checkbox', 'enable', array('required' => false, 'label' => 'Активность'));
        $enable->addDecorator('errors', array('class' => 'error msg'));

        $this->addElements(array(
            $enable,
            $userUri
        ));

        $uri = $this->createElement('hidden', 'uri', array('disableLoadDefaultDecorators' => true));
        $uri->addDecorator('viewHelper')
                ->addFilter(new Rastor_Filter_UriBuilder(array(
                            'name' => $this->getElement($this->getParmLang('name', $this->getLanguage())),
                            'userUri' => $this->getElement('user_uri')
                        )));
        
        $this->addElement($uri);
        
        $auth = Rastor_Auth::getInstance();

        if ($auth->getIdentity()->accessLevel == 0) {
            $enable = $this->createElement('checkbox', 'protected', array('required' => false, 'label' => 'Защищенная запись'));
            $enable->addDecorator('errors', array('class' => 'error msg'));
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

        $this->addDisplayGroup(array_merge($info, array('user_uri', 'enable', 'protected')), 'info')
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
