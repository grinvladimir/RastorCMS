<?php

class Core_AdminController extends Rastor_Controller_Cms_ActionSimple {

    public function init() {
        parent::init();
        $this->_helper->layout->setLayout('admin');
    }

    public function indexAction() {
        if (($this->_getAuth()->hasIdentity()) && ($this->_getAuth()->getAccessLevel() < 3)) {
            Core_View_Helper_CmsTitle::getTitle();
        } else {
            $this->_forward('login');
        }
    }

    public function loginAction() {
        Core_View_Helper_CmsTitle::getTitle('Авторизация');
        $this->_helper->_layout->setLayout('adminauth');

        $usersDb = new Core_Model_DbTable_Users();
        $form = new Core_Form_Login();

        if (!$this->getRequest()->isPost()) {
            $this->view->form = $form;
        } else {
            if (!$form->isValid($_POST)) {
                sleep(1);
                $this->view->form = $form;
            } else {
                $this->view->form = $form;
                $login = $form->getValue('login');
                $password = $form->getValue('password');
                $authAdapter = new Core_Model_Auth();
                $result = $authAdapter->getAuthResult($login, $password);
                if ($result->isValid()) {
                    $data = $authAdapter->getResultRowObject(null, 'password');
                    $this->_getAuth()->setIdentity($data);
                    $this->_redirect(Rastor_Url::get('admin', array()));
                }
            }
        }
    }

    public function logoutAction() {
        $this->_getAuth()->clearIdentity();
        $this->_redirect(Rastor_Url::get('admin', array()));
    }

    public function cmsmenuAction() {
        $menuArray = Zend_Registry::get('CmsMenu');

        $this->view->menu = new Zend_Navigation($menuArray);
    }

    public function breadcrumbAction() {
        
    }
    
    public function userpanelAction(){
        $this->view->login = $this->_userData->login;
    }

}

