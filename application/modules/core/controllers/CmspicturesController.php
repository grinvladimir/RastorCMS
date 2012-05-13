<?php

class Core_CmspicturesController extends Zend_Controller_Action {

    private $config;

    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        define('NO_DEBUG', true);
        
        $this->config = Zend_Registry::get('config');
    }

    public function cropAction() {
        $params = $this->_getParam('params');
        $params = explode(':', $params);

        $data = array();

        if (count($params) == 7) {
            $ext = strtolower(pathinfo($params[0], PATHINFO_EXTENSION));
            $baseName = strtolower(pathinfo($params[0], PATHINFO_BASENAME));
            $thumbFileName = Core_Model_Pictures::makeThumb($baseName, $params[5], $params[6], $params[1], $params[2], $params[3], $params[4]);
            if ($thumbFileName) {
                $data['filename'] = $this->config->rastor->pictures->path . $thumbFileName;
            } else {
                $data['error'] = 'Saving file error!';
            }
        } else {
            $data['error'] = 'Bad request!';
        }

        $json = Zend_Json::encode($data);
        echo $json;
    }

    public function deleteAction(){
        $filename = $this->_getParam('filename');
        @unlink($this->config->rastor->pictures->uploadPath . pathinfo($filename, PATHINFO_BASENAME));
    }
    
    public function uploadAction() {
        $form = new Core_Form_PictureUpload();

        $data = array();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {

                $file = $form->upload_file->getFileInfo();

                $ext = strtolower(pathinfo($_FILES['upload_file']['name'], PATHINFO_EXTENSION));
                $baseName = substr(md5(uniqid(rand(), 1)), 0, 16) . '.' . $ext;

                while (file_exists($this->config->rastor->pictures->uploadPath . $baseName)) {
                    $baseName = substr(md5(uniqid(rand(), 1)), 0, 16) . '.' . $ext;
                }

                $form->upload_file->addFilter('Rename', $this->config->rastor->pictures->uploadPath . $baseName);
                $form->upload_file->receive();

                $imageData = getimagesize($this->config->rastor->pictures->uploadPath . $baseName);
                
                $data = array(
                    'filename' => $this->config->rastor->pictures->path . $baseName,
                    'width' => $imageData[0],
                    'height' => $imageData[1]
                );
            }
        }
        $data['error'] = '';
        foreach ($form->upload_file->getMessages() as $error) {
            $data['error'] .= $error . PHP_EOL;
        }
        $json = Zend_Json::encode($data);
        echo $json;
    }

}

?>