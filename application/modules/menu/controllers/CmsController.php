<?php

class Menu_CmsController extends Rastor_Controller_Cms_Action {

    protected $_modelClassName = 'Menu_Model_MenuItem';

    public function addAction() {
        Core_View_Helper_CmsTitle::getTitle('Новый пункт меню');
        $id = $this->_getParam('id');

        $form = new Menu_Form_CmsMenuItem();

        if (!$this->getRequest()->isPost()) {
            $this->view->form = $form;
        } else {
            if (!$form->isValid($_POST)) {
                $this->view->form = $form;
            } else {
                $formData = $form->getValues();

                $formData['sort'] = $this->getModel()->getDbTable()->getNextSortValue();

                if ($this->getModel()->getDbTable()->insert(array_merge($formData, $this->getModel()->getInsertParams($formData['href'], $formData['url'])))) {
                    $messager = new Rastor_Controller_Cms_Messager();
                    $messager->setAction('successfully_added');
                    $this->getModel()->rebuildMenuItems();
                    $this->_redirect(Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cms', 'action' => 'show')));
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
                    
                    if ($this->getModel()->getDbTable()->update(array_merge($formData, $this->getModel()->getInsertParams($formData['href'], $formData['url'])), $id)) {
                        $messager = new Rastor_Controller_Cms_Messager();
                        $messager->setAction('successfully_changed');
                        $this->getModel()->rebuildMenuItems();
                    } else {
                        $messager = new Rastor_Controller_Cms_Messager();
                        $messager->setAction('not_changed');
                    }
                    $this->_redirect(Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cms', 'action' => 'show')));
                }
            }
        } else {
            $messager = new Rastor_Controller_Cms_Messager();
            $messager->setAction('not_found');
            $this->_redirect(Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cms', 'action' => 'show')));
        }
    }

    public function showAction() {
        Core_View_Helper_CmsTitle::getTitle();

        $tree = new Core_Model_Tree(array(
                    'saveUrl' => Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cms', 'action' => 'save')),
                    'reloadUrl' => Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cms', 'action' => 'treedata')),
                    'removeUrl' => Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cms', 'action' => 'remove')),
                    'editUrl' => substr(Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cms', 'action' => 'edit', 'id' => '0')), 0, -1),
                    'addUrl' => Rastor_Url::get('admin', array('module' => 'menu', 'controller' => 'cms', 'action' => 'add'))
                ));

        $this->view->json = $tree->getJSONObject();
    }

    public function saveAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $menuItemModel = new Menu_Model_MenuItem();
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

    public function treedataAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $menuItemModel = new Menu_Model_MenuItem();
        echo $menuItemModel->getJSONMenuList();
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