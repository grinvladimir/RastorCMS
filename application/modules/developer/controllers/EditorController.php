<?php

class Developer_EditorController extends Rastor_Controller_Cms_ActionSimple {

    public function editAction() {
        Core_View_Helper_CmsTitle::getTitle('Редактирование файла');
        
        $this->view->headScript()->appendFile('/design/cms/codemirror/lib/codemirror.js')
                ->appendFile('/design/cms/codemirror/mode/xml/xml.js')
                ->appendFile('/design/cms/codemirror/mode/javascript/javascript.js')
                ->appendFile('/design/cms/codemirror/mode/css/css.js')
                ->appendFile('/design/cms/codemirror/mode/clike/clike.js')
                ->appendFile('/design/cms/codemirror/mode/php/php.js')
                ->appendFile('/design/cms/codemirror/lib/util/dialog.js')
                ->appendFile('/design/cms/codemirror/lib/util/searchcursor.js')
                ->appendFile('/design/cms/codemirror/lib/util/search.js');

        $this->view->headLink()->appendStylesheet('/design/cms/codemirror/lib/codemirror.css');

        $filename = $this->_getParam('file');

        $this->view->filename = $filename;
        
        if (is_readable($filename)) {
            if (@chmod($filename, 0666)) {
                $this->view->notWritable = false;
            } else if (!is_writable($filename)) {
                $this->view->notWritable = true;
            } else {
                $this->view->notWritable = false;
            }

            $this->view->content = file_get_contents($this->_getParam('file'));
        } else {
            $this->view->notFound = true;
        }
    }
    
    public function saveAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $filename = $this->_getParam('filename');
        $data = $this->_getParam('data');
        
        if (file_put_contents($filename, $data)){
            echo '0';
        } else {
            echo '-1';
        }
    }
}

