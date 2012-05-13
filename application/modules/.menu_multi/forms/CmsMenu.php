<?php

class Menu_Form_CmsMenu extends Rastor_Form {

    public function __construct(Array $data = array()) {
        parent::__construct();

        $this->setAction('')
                ->setMethod('post');

        $name = $this->createElement('text', 'name', array('label' => 'Название'));
        $name->addDecorator('errors', array('class' => 'error msg'))
                ->setAttrib('class', 'big');

        $this->addElement($name);

        $auth = Rastor_Auth::getInstance();

        if ($auth->getIdentity()->accessLevel == 0) {
            $enable = $this->createElement('checkbox', 'protected', array('required' => false, 'label' => 'Защищенная запись'));
            $enable->addDecorator('errors', array('class' => 'error msg'));
            $this->addElement($enable);
        }

        $newItem = $this->createElement('button', 'new_item', array('disableLoadDefaultDecorators' => true, 'required' => false, 'label' => 'Добавить новый елемент'));
        $newItem->addDecorator('viewHelper')
                ->addDecorator('errors')
                ->setAttrib('class', 'tree_holder')
                ->addDecorator('htmlTag', array('tag' => 'p'));

        $this->addElement($newItem);
        
        $submit = $this->createElement('submit', 'submit', array('disableLoadDefaultDecorators' => true, 'required' => true, 'label' => 'Создать'));
        $submit->addDecorator('viewHelper')
                ->addDecorator('errors')
                ->addDecorator('htmlTag', array('tag' => 'p'));

        $this->addElement($submit);
    }

}
