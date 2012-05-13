<?php

class Core_Form_Login extends Zend_Form {

    public function __construct(Array $data = array()) {
        parent::__construct();
        
        $this->setAction('')
                ->setMethod('post');

        $login = $this->createElement('text', 'login', array('required' => true, 'label' => 'Логин'));
        $login->addDecorator('errors', array('class' => 'error msg'))
                ->addValidator('stringLength', false, array(2, 20));

        $password = $this->createElement('password', 'password', array('required' => true, 'label' => 'Пароль'));
        $password->addDecorator('errors', array('class' => 'error msg'))
                ->addValidator('StringLength', false, array(4))
                ->addValidator(new Rastor_Validate_ValidUser(new Core_Model_DbTable_Users(), 'login'));

        $submit = $this->createElement('submit', 'login_buttton', array('required' => false, 'label' => 'Вход'));
        $submit->addDecorator('errors');
        
        $this->addElements(array(
            $login,
            $password,
            $submit
        ));
        
        if (count($data)){
            $this->getElement('login')->setValue($data->username);
        }
    }

}
