<?php

class Articles_CmsController extends Rastor_Controller_Cms_ActionTable {

    protected $_modelClassName = 'Articles_Model_Article';
    protected $_tableParams = array('id', 'name', 'enable');
    protected $_tableColumns = array('id', 'Название', 'Активность');
    protected $_tableColumnsWidth = array(40, 0, 100);

    public function addAction() {
        Core_View_Helper_CmsTitle::getTitle('Новая статья');
        $form = new Articles_Form_CmsArticle();
        $form->removeElement('uri');

        $picture = new Core_Model_Pictures(array(
                    'type' => 'picture',
                    'width' => 100,
                    'height' => 100
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
                    $this->_redirect(Rastor_Url::get('admin', array('module' => 'articles', 'controller' => 'cms', 'action' => 'showlist')));
                }
            }
        }
    }

    public function editAction() {
        Core_View_Helper_CmsTitle::getTitle('Редактирование статьи');
        $id = $this->_getParam('id', 0);

        if ($data = $this->getModel()->getDbTable()->getRecord($id)) {
            $form = new Articles_Form_CmsArticle();
            $form->setValues($data);
            $form->getElement('submit')->setLabel('Изменить');

            $picture = new Core_Model_Pictures(array(
                        'type' => 'picture',
                        'width' => 100,
                        'height' => 100
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
                        Rastor_Callback::callback();
                    } else {
                        $messager = new Rastor_Controller_Cms_Messager();
                        $messager->setAction('not_changed');
                    }
                    $this->_redirect(Rastor_Url::get('admin', array('module' => 'articles', 'controller' => 'cms', 'action' => 'showlist')));
                }
            }
        } else {
            $messager = new Rastor_Controller_Cms_Messager();
            $messager->setAction('not_found');
            $this->_redirect(Rastor_Url::get('admin', array('module' => 'articles', 'controller' => 'cms', 'action' => 'showlist')));
        }
    }

}