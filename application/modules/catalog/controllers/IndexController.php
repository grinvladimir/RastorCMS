<?php

class Catalog_IndexController extends Rastor_Controller_Action {

    public function showlistAction() {
        $catalogModel = new Catalog_Model_Catalog();

        $this->view->menu = $catalogModel->getNavigation($this->view->url(), $this->_getLocale()->getLanguage());
    }

    public function indexAction() {
        $catalogModel = new Catalog_Model_Catalog();
        $productModel = new Catalog_Model_Product();

        $id = $this->_getParam('id');
        $page = $this->_getParam('page');

        $catalog = $catalogModel->getDbTable()->getRecordByUri($id);

        if ($catalog) {
            $catalog = $catalogModel->buildParams($catalog, $this->_getLocale()->getLanguage());
            $catalogModel->buildHead($catalog, $this->view);
            $id = $catalog->id;
            
            $this->view->catalog = $catalog;
        } else {
            $this->view->headTitle($this->_getTranslator()->_('Каталог продукции'));
        }

        $this->view->paginator = $productModel->getPaginator($page, $this->_itemsPerPage, $this->_pageRange, $this->_getLocale()->getLanguage(), array(
            'order' => 'id',
            'catalog_id' => $id
                ));
    }

    public function productAction() {
        $productModel = new Catalog_Model_Product();

        $id = $this->_getParam('id');

        $product = $productModel->getDbTable()->getEnableRecord($id);

        if ($product) {
            $product = $productModel->buildParams($product, $this->_getLocale()->getLanguage());
            $productModel->buildHead($product, $this->view);

            $this->view->product = $product;
        } else {
            throw new Exception('Product not found');
        }
    }

    public function specialAction() {
        $productModel = new Catalog_Model_Product();
        $this->view->products = $productModel->buildParams($productModel->getDbTable()->getSpecialProducts(), $this->_getLocale()->getLanguage(), true);
    }
    
    public function newAction() {
        $productModel = new Catalog_Model_Product();
        $this->view->products = $productModel->buildParams($productModel->getDbTable()->getNewProducts(), $this->_getLocale()->getLanguage(), true);
    }

    public function cartAction() {
        $cart = new Catalog_Model_Cart();

        $this->view->items = $cart->getItems();
        $this->view->price = $cart->getPrice();
        $this->view->count = $cart->getCount();
    }
    
    public function addproductAction() {
        $this->_helper->layout()->disableLayout();

        $id = $this->_getParam('id', 0);

        $cart = new Catalog_Model_Cart();
        $cart->addProduct($id);

        $this->view->count = $cart->getCount();
    }

    public function deleteproductAction() {
        $this->_helper->layout()->disableLayout();

        $id = $this->_getParam('id', 0);

        $cart = new Catalog_Model_Cart();
        $cart->deleteProduct($id);

        $this->view->count = $cart->getCount();
    }

    public function fullcartdataAction() {
        $this->_helper->layout()->disableLayout();
        $cart = new Catalog_Model_Cart();
        
        $cart->setProductsCounts($this->_getAllParams());

        $this->view->items = $cart->getItems();
        $this->view->price = $cart->getPrice();
        $this->view->count = $cart->getCount();
    }
    
    public function cartviewAction() {
        $productModel = new Catalog_Model_Product();
        $cart = new Catalog_Model_Cart();
        $orderModel = new Catalog_Model_Order();

        $this->view->items = $productModel->buildParams($cart->getItems(), $this->_getLocale()->getLanguage(), true);
        $this->view->price = $cart->getPrice();
        $this->view->count = $cart->getCount();

        $this->view->headTitle($this->_getTranslator()->_('Корзина'));

        $form = new Catalog_Form_Order();

        if (!$this->getRequest()->isPost()) {
            $this->view->form = $form;
        } else {
            if (!$form->isValid($_POST)) {
                $this->view->form = $form;
            } else {
                $formData = $form->getValues();
                
                $cart = new Catalog_Model_Cart();

                $cartData = '<table><tr><th>Название</th><th>Количество</th><th>Цена</th></tr>';
                foreach ($cart->getItems() as $item) {
                    $cartData .='<tr><td>' . $item->name . '</td><td>' . $item->_count . '</td><td>' . ($item->_count * $item->price) . '</td></tr>';
                }
                $cartData .= '</table>';

                $usersDbTable = new Core_Model_DbTable_Users();
                $list = $usersDbTable->getAdminMailList();

                $info = 'Контактное лицо: ' . $formData['name'] . '<br/>
						Контактный телефон: ' . $formData['phone'] . '<br/>
						E-mail: ' . $formData['email'] . '<br/>
                                                Примечание: ' . $formData['message'] . '<br/>
                                                Стоимость: ' . $cart->getPrice() . ' р.';

                $orderModel->add($cart, $info);
                
                $form_data = $form->getValues();
                foreach ($list as $item) {
                    $mail = new Zend_Mail('utf-8');
                    $mail->setBodyHtml($info . 'Товары:<br/>' . $cartData);
                    $mail->setFrom($formData['email'], $formData['name']);
                    $mail->addTo($item->email, $item->login);
                    $mail->setSubject('Заказ на сайте');
                    $mail->send();
                }

                $cart->clear();
                $this->view->form = 'Спасибо за заказ, наш консультант свяжется с вами.';
            }
        }
    }

}