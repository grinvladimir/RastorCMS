<?php

class Content_Form_Contacts extends Rastor_Form {

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
        
        $topic = $this->createElement('text', 'topic', array('label' => 'Тема'));
        $topic->addDecorator('errors', array('class' => 'error'))
                ->setRequired();

        $message = $this->createElement('textarea', 'message', array('label' => 'Сообщение'));
        $message->addDecorator('errors', array('class' => 'error'))
                ->setRequired(false);
        
        $this->addElements(array($name, $email, $phone, $topic, $message));

        $submit = $this->createElement('submit', 'submit', array('disableLoadDefaultDecorators' => true, 'required' => true, 'label' => 'Отправить'));
        $submit->addDecorator('viewHelper')
                ->addDecorator('errors')
                ->setAttrib('class', 'button submit')
                ->addDecorator('htmlTag', array('tag' => 'p'));

        $this->addElement($submit);
    }

}
