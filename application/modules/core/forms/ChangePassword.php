<?php

class Core_Form_ChangePassword extends Rastor_Form {

    public function __construct(Array $data = array()) {
        parent::__construct();
        
        $this->setAction('')
                ->setMethod('post');

        $auth = Rastor_Auth::getInstance();
        $userData = $auth->getIdentity();
        
        $oldPassword = $this->createElement('password', 'old_password', array('label' => 'Старый пароль'));
        $oldPassword->addDecorator('errors', array('class' => 'error msg'))
                ->addValidator('stringLength', false, array(4, 32))
                ->addValidator(new Rastor_Validate_OldPassword(new Core_Model_DbTable_Users(), $userData->login))
                ->setRequired();

        $password = $this->createElement('password', 'password', array('label' => 'Новый пароль'));
        $password->addDecorator('errors', array('class' => 'error msg'))
                ->addValidator('stringLength', false, array(4, 32))
                ->setRequired();

        $passwordConfirm = $this->createElement('password', 'password_confirm', array('label' => 'Подтверждение пароля'));
        $passwordConfirm->addDecorator('errors', array('class' => 'error msg'))
                ->addValidator(new Rastor_Validate_EqualPasswords('password'))
                ->setRequired();

        $submit = $this->createElement('submit', 'submit', array('label' => 'Сохранить', 'disableLoadDefaultDecorators' => true));
        $submit->addDecorator('viewHelper')
                ->addDecorator('htmlTag', array('tag' => 'p'))
                ->setAttrib('class', 'button');
        
        $this->addElements(array(
            $oldPassword,
            $password,
            $passwordConfirm,
            $submit
        ));
        
        if (count($data)){
            $this->setValues($data);
        }
    }

}
