<?php

class Core_Form_PictureUpload extends Zend_Form {
    private $_maxFileSize = 5;
    private $_extensions = 'jpg,png,gif,bmp';

    public function __construct() {
        parent::__construct();

        $this->setAction('')
                ->setMethod('post')
                ->addDecorator('form');

        $image = $this->createElement('file', 'upload_file');
        $image->addValidator('Size', false, $this->_maxFileSize * 1024 * 1024)
                ->addValidator('Extension', false, $this->_extensions);

        $this->addElement($image);
    }

}
