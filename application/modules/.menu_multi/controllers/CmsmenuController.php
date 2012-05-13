<?php

class Menu_CmsmenuController extends Rastor_Controller_Cms_ActionTable {

    protected $_modelClassName = 'Menu_Model_Menu';
    protected $_tableParams = array('id', 'name');
    protected $_treeTranslations = array(
        'removeOne' => 'Удалить запись?',
        'removeOneConfirm' => 'Вы действительно хотите удалить запись?',
        'editTitle' => 'Редактировать',
        'removeTitle' => 'Удалить',
        'buttonYes' => 'Да',
        'buttonNo' => 'Нет'
    );

    public function getTreeTranslations() {
        $view = Zend_Layout::getMvcInstance()->getView();

        $translator = Zend_Registry::get('Zend_Translate');

        foreach ($this->_treeTranslations as $key => $value) {
            $this->_treeTranslations[$key] = $translator->_($value);
        }

        $view->treeTranslations = Zend_Json::encode($this->_treeTranslations);

        return $this->_treeTranslations;
    }

    public function addAction() {
        Core_View_Helper_CmsTitle::getTitle('Новое меню');
        $form = new Menu_Form_CmsMenu();

        if (!$this->getRequest()->isPost()) {
            $this->view->form = $form;
        } else {
            if (!$form->isValid($_POST)) {
                $this->view->form = $form;
            } else {
                $formData = $form->getValues();

                if ($this->getModel()->getDbTable()->insert($formData)) {
                    $messager = new Rastor_Controller_Cms_Messager();
                    $messager->setAction('successfully_added');
                    $this->_redirect(Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cmsmenu', 'action' => 'showlist')));
                }
            }
        }
    }

    public function editAction() {
        Core_View_Helper_CmsTitle::getTitle('Редактирование меню');
        $this->getTreeTranslations();
        $messager = new Rastor_Controller_Cms_Messager();
        $messager->getJSONMessage();

        $id = $this->_getParam('id', 0);
        $this->view->id = $id;

        if ($data = $this->getModel()->getDbTable()->getRecord($id)) {
            $form = new Menu_Form_CmsMenu();
            $form->setValues($data);
            $form->getElement('submit')->setLabel('Сохранить');

            $menuItemModel = new Menu_Model_MenuItem();
            $this->view->list = $menuItemModel->getJSONMenuList($id);

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
                    $this->_redirect(Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cmsmenu', 'action' => 'showlist')));
                }
            }
        } else {
            $messager = new Rastor_Controller_Cms_Messager();
            $messager->setAction('not_found');
            $this->_redirect(Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cmsmenu', 'action' => 'showlist')));
        }
    }

    public function treedataAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $id = $this->_getParam('id', 0);
        
        $menuItemModel = new Menu_Model_MenuItem();
        echo $menuItemModel->getJSONMenuList($id);
    }

    public function saveAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $menuItemModel = new Menu_Model_MenuItem();

        $id = $this->_getParam('id', 0);
        $data = $this->_getParam('data');

        $sort = 1;
        foreach ($data as $value) {
            if ($value['depth'] >= 0) {
                if ($value['parent_id'] == 'none') {
                    $updateData = array(
                        'id' => $value['item_id'],
                        'parent_id' => 0,
                        'depth' => $value['depth'],
                        'sort' => $sort
                    );
                } else {
                    $updateData = array(
                        'id' => $value['item_id'],
                        'parent_id' => $value['parent_id'],
                        'depth' => $value['depth'],
                        'sort' => $sort
                    );
                }
                $sort++;

                $menuItemModel->getDbTable()->update($updateData, $value['item_id']);
            }
        }
    }

}