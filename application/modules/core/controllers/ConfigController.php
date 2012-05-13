<?php

class Core_ConfigController extends Rastor_Controller_Cms_ActionSimple {

    public function init() {
        parent::init();
        $this->_helper->layout->setLayout('admin');
    }

    public function changepasswordAction() {
        Core_View_Helper_CmsTitle::getTitle();
        $form = new Core_Form_ChangePassword();

        if (!$this->getRequest()->isPost()) {
            $this->view->form = $form;
        } else {
            if (!$form->isValid($_POST)) {
                $this->view->form = $form;
            } else {
                $formData = $form->getValues();
                $formData['password'] = md5($formData['password']);
                unset($formData['password_confirm']);
                unset($formData['old_password']);

                $usersDb = new Core_Model_DbTable_Users();
                $userData = $this->_getAuth()->getIdentity();

                if ($usersDb->update($formData, $userData->id)) {
                    $messager = new Rastor_Controller_Cms_Messager();
                    $messager->setAction('successfully_changed');
                } else {
                    $messager = new Rastor_Controller_Cms_Messager();
                    $messager->setAction('not_changed');
                }

                $this->_redirect(Rastor_Url::get('admin', array('module' => 'core', 'controller' => 'config', 'action' => 'changepassword')));
            }
        }
    }

    public function cmsconfigAction() {
        Core_View_Helper_CmsTitle::getTitle();
        $form = new Core_Form_CmsConfig();

        if (!$this->getRequest()->isPost()) {
            $this->view->form = $form;
        } else {
            if (!$form->isValid($_POST)) {
                $this->view->form = $form;
            } else {
                $formData = $form->getValues();

                $usersDb = new Core_Model_DbTable_Users();

                $userData = $this->_getAuth()->getIdentity();
                
                if ($usersDb->update($formData, $userData->id)) {
                    $messager = new Rastor_Controller_Cms_Messager();
                    $messager->setAction('successfully_changed');

                    $userData = $usersDb->getRecord($userData->id);
                    $this->_getAuth()->setIdentity($userData);
                } else {
                    $messager = new Rastor_Controller_Cms_Messager();
                    $messager->setAction('not_changed');
                }

                $this->_redirect(Rastor_Url::get('admin', array('module' => 'core', 'controller' => 'config', 'action' => 'cmsconfig')));
            }
        }
    }

}