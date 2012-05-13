<?php

class Menu_IndexController extends Rastor_Controller_Action {

    public function showAction() {
        $menuModel = new Menu_Model_MenuItem();
        
        $this->view->menu = new Zend_Navigation($menuModel->getNavigationArray($this->view->url(), $this->_getLocale()->getLanguage()));
    }

}

