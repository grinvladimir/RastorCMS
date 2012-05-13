<?php

class Catalog_Form_Order extends Rastor_Form {

    public function __construct() {
        parent::__construct();

        $this->setAction('')
                ->setMethod('post');

        $name = $this->createElement('text', 'name', array('label' => 'Ваше имя'));
        $name->addDecorator('errors', array('class' => 'error'))
                ->setRequired();

        $email = $this->createElement('text', 'email', array('label' => 'Ваш e-mail'));
        $email->addDecorator('errors', array('class' => 'error'))
                ->setRequired();

        $phone = $this->createElement('text', 'phone', array('label' => 'Ваш телефон'));
        $phone->addDecorator('errors', array('class' => 'error'))
                ->setRequired();

        $message = $this->createElement('textarea', 'message', array('label' => 'Примечание'));
        $message->addDecorator('errors', array('class' => 'error'))
                ->setRequired(false);
        
        $this->addElements(array($name, $email, $phone, $message));

        $submit = $this->createElement('submit', 'submit', array('disableLoadDefaultDecorators' => true, 'required' => true, 'label' => 'Оформить'));
        $submit->addDecorator('viewHelper')
                ->addDecorator('errors')
                ->setAttrib('class', 'button submit')
                ->addDecorator('htmlTag', array('tag' => 'p'));

        $this->addElement($submit);
    }

}
