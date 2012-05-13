<?php

class Catalog_CmsController extends Rastor_Controller_Cms_Action {

    protected $_modelClassName = 'Catalog_Model_Catalog';

    public function addAction() {
        Core_View_Helper_CmsTitle::getTitle('Новый каталог');
        $id = $this->_getParam('id');

        $form = new Catalog_Form_CmsCatalog();

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
                    $this->_redirect(Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cms', 'action' => 'show')));
                }
            }
        }
    }

    public function editAction() {
        Core_View_Helper_CmsTitle::getTitle('Редактирование каталога');
        $id = $this->_getParam('id');

        if ($data = $this->getModel()->getDbTable()->getRecord($id)) {
            $form = new Catalog_Form_CmsCatalog();
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
                        Rastor_Callback::callback();
                    } else {
                        $messager = new Rastor_Controller_Cms_Messager();
                        $messager->setAction('not_changed');
                    }
                    $this->_redirect(Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cms', 'action' => 'show')));
                }
            }
        } else {
            $messager = new Rastor_Controller_Cms_Messager();
            $messager->setAction('not_found');
            $this->_redirect(Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cms', 'action' => 'show')));
        }
    }

    public function showAction() {
        Core_View_Helper_CmsTitle::getTitle();

        $tree = new Core_Model_Tree(array(
                    'saveUrl' => Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cms', 'action' => 'save')),
                    'reloadUrl' => Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cms', 'action' => 'treedata')),
                    'removeUrl' => Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cms', 'action' => 'remove')),
                    'editUrl' => substr(Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cms', 'action' => 'edit', 'id' => '0')), 0, -1),
                    'addUrl' => Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cms', 'action' => 'add'))
                ));

        $this->view->json = $tree->getJSONObject();
    }

    public function saveAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

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

                $this->getModel()->getDbTable()->update($updateData, $value['item_id']);
            }
        }
    }

    public function treedataAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        echo $this->getModel()->getJSONMenuList();
    }

    public function removeAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_getParam('id');

        $messager = new Rastor_Controller_Cms_Messager();
        $this->getModel()->getDbTable()->recursiveDelete($id, true);
        echo $messager->getJSONMessage('successfully_deleted', array());
        Rastor_Callback::callback();
    }

}