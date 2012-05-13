<?php

class Content_IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $contentModel = new Content_Model_Content();
        
        $uri = $this->_getParam('uri');
        $content = $contentModel->getDbAdapter()->getRecordByUri($uri.'.html');
        if ($content){
            Zend_Debug::dump($content);
        } else {
            throw new Exception('Content not found');
        }
    }

}

