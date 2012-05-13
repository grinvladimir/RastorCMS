<?php

class Menu_CmsmenuitemController extends Rastor_Controller_Cms_Action {

    protected $_modelClassName = 'Menu_Model_MenuItem';

    public function addAction() {
        Core_View_Helper_CmsTitle::getTitle('Новый пункт меню');
        $id = $this->_getParam('id');

        $form = new Menu_Form_CmsMenuItem();
        $form->getElement('menu_id')->setValue($id);

        if (!$this->getRequest()->isPost()) {
            $this->view->form = $form;
        } else {
            if (!$form->isValid($_POST)) {
                $this->view->form = $form;
            } else {
                $formData = $form->getValues();

                $formData['sort'] = $this->getModel()->getDbTable()->getNextSortValue();

                if ($this->getModel()->getDbTable()->insert($formData)) {
                    $messager = new Rastor_Controller_Cms_Messager();
                    $messager->setAction('successfully_added');
                    $this->_redirect(Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cmsmenu', 'action' => 'edit', 'id' => $id)));
                }
            }
        }
    }

    public function editAction() {
        Core_View_Helper_CmsTitle::getTitle('Редактирование пункта меню');
        $id = $this->_getParam('id');

        if ($data = $this->getModel()->getDbTable()->getRecord($id)) {
            $form = new Menu_Form_CmsMenuItem();
            $form->getElement('submit')->setLabel('Сохранить');
            $form->setValues($data);

            if (!$this->getRequest()->isPost()) {
                $this->view->form = $form;
            } else {
                if (!$form->isValid($_POST)) {
                    $this->view->form = $form;
                } else {
                    $formData = $form->getValues();

                    if ($this->getModel()->getDbTable()->update($formData, $id)) {
                        $messager = new Rastor_Controller_Cms_Messager();
                        $messager->setAction('successfully_changed');
                    } else {
                        $messager = new Rastor_Controller_Cms_Messager();
                        $messager->setAction('not_changed');
                    }
                    $this->_redirect(Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cmsmenu', 'action' => 'edit', 'id' => $formData['menu_id'])));
                }
            }
        } else {
            $messager = new Rastor_Controller_Cms_Messager();
            $messager->setAction('not_found');
            $this->_redirect(Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cmsmenu', 'action' => 'showlist')));
        }
    }

    public function removeAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_getParam('id');
        
        $messager = new Rastor_Controller_Cms_Messager();
        $this->getModel()->getDbTable()->recursiveDelete($id, true);
        echo $messager->getJSONMessage('successfully_deleted', array());
    }

}