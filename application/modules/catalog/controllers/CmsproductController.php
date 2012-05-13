<?php

class Catalog_CmsproductController extends Rastor_Controller_Cms_ActionTable {

    protected $_modelClassName = 'Catalog_Model_Product';
    protected $_tableParams = array('id', 'name', 'catalog_id', 'enable');
    protected $_tableColumns = array('id', 'Название', 'Каталог', 'Активность');
    protected $_tableColumnsWidth = array(40, 0, 200, 100);

    public function addAction() {
        Core_View_Helper_CmsTitle::getTitle('Новая статья');
        $form = new Catalog_Form_CmsProduct();

        $picture = new Core_Model_Pictures(array(
                    'type' => 'picture',
                    'preview' => array(
                        'width' => 100,
                        'height' => 100
                    ),
                    'fixedThumbSize' => true
                ));

        $this->view->json = $picture->getJSONObject();

        if (!$this->getRequest()->isPost()) {
            $this->view->form = $form;
        } else {
            if (!$form->isValid($_POST)) {
                $this->view->form = $form;
            } else {
                $formData = $form->getValues();

                $preview = Zend_Json::decode($formData['preview']);
                unset($formData['preview']);

                if ($this->getModel()->getDbTable()->insert($formData + $preview)) {
                    $messager = new Rastor_Controller_Cms_Messager();
                    $messager->setAction('successfully_added');
                    $this->_redirect(Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cmsproduct', 'action' => 'showlist')));
                }
            }
        }
    }

    public function editAction() {
        Core_View_Helper_CmsTitle::getTitle('Редактирование статьи');
        $id = $this->_getParam('id', 0);

        if ($data = $this->getModel()->getDbTable()->getRecord($id)) {
            $form = new Catalog_Form_CmsProduct();
            $form->setValues($data);
            $form->getElement('submit')->setLabel('Изменить');

            $picture = new Core_Model_Pictures(array(
                        'type' => 'picture',
                        'preview' => array(
                            'width' => 100,
                            'height' => 100
                        ),
                        'fixedThumbSize' => true
                    ));

            $picture->setData(Zend_Json::encode(array(
                        'picture' => $data->picture,
                        'thumb' => $data->thumb
                    )));

            $this->view->json = $picture->getJSONObject();

            if (!$this->getRequest()->isPost()) {
                $this->view->form = $form;
            } else {
                if (!$form->isValid($_POST)) {
                    $this->view->form = $form;
                } else {
                    $formData = $form->getValues();

                    $preview = Zend_Json::decode($formData['preview']);
                    unset($formData['preview']);

                    if ($this->getModel()->getDbTable()->update($formData + $preview, $id)) {
                        $messager = new Rastor_Controller_Cms_Messager();
                        $messager->setAction('successfully_changed');
                    } else {
                        $messager = new Rastor_Controller_Cms_Messager();
                        $messager->setAction('not_changed');
                    }
                    $this->_redirect(Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cmsproduct', 'action' => 'showlist')));
                }
            }
        } else {
            $messager = new Rastor_Controller_Cms_Messager();
            $messager->setAction('not_found');
            $this->_redirect(Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cmsproduct', 'action' => 'showlist')));
        }
    }

}