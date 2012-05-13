<?php

class Catalog_CmsorderproductsController extends Rastor_Controller_Cms_ActionTable {

    protected $_modelClassName = 'Catalog_Model_OrderProducts';
    protected $_tableParams = array('oid', 'name', 'count', 'price', 'total_price');
    protected $_tableColumns = array('id', 'Название', 'Количество', 'Цена', 'Всего');
    protected $_tableColumnsWidth = array(40, 0, 100, 80, 80);
    protected $_tableOptions = array(
        'rebuildParams' => false,
        'removeParams' => true,
        'pageRange' => 3
    );

    public function showlistAction() {
        Core_View_Helper_CmsTitle::setTitle('Редактирование заказа');
        $orderModel = new Catalog_Model_Order();
        
        $id = $this->_getParam('id');
        $this->_tableRequestParams = array('id' => $id);
        
        $object = $this->_getJSONTableObject();
        Rastor_View_Helper_RastorTable::setTableObject($object);
        
        $this->view->totalPrice = $this->getModel()->getDbTable()->getTotalPrice($id);
        
        if ($data = $orderModel->getDbTable()->getRecord($id)) {
            $form = new Catalog_Form_CmsOrder();
            $form->getElement('submit')->setLabel('Сохранить');
            $form->setValues($data);

            if (!$this->getRequest()->isPost()) {
                $this->view->form = $form;
            } else {
                if (!$form->isValid($_POST)) {
                    $this->view->form = $form;
                } else {
                    $formData = $form->getValues();

                    if ($orderModel->getDbTable()->update($formData, $id)) {
                        $messager = new Rastor_Controller_Cms_Messager();
                        $messager->setAction('successfully_changed');
                    } else {
                        $messager = new Rastor_Controller_Cms_Messager();
                        $messager->setAction('not_changed');
                    }
                    $this->_redirect(Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cmsorderproducts', 'action' => 'showlist', 'id' => $id)));
                }
            }
        } else {
            $messager = new Rastor_Controller_Cms_Messager();
            $messager->setAction('not_found');
            $this->_redirect(Rastor_Url::get('admin', array('module' => 'catalog', 'controller' => 'cmsorder', 'action' => 'showlist')));
        }
    }
    
}