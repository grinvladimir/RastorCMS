<?php

class Developer_ViewController extends Rastor_Controller_Cms_ActionTable {

    protected $_modelClassName = 'Developer_Model_TemplateEditor';
    protected $_tableButtons = array('edit');
    protected $_tableParams = array('filename', 'datetime', 'ext');
    protected $_tableColumns = array('Файл', 'Последние изменение', 'Расширение');
    protected $_tableColumnsWidth = array(0, 180, 100);
    protected $_tableOptions = array(
        'rebuildParams' => false,
        'removeParams' => false,
        'pageRange' => 3
    );
    protected $_tableOrderEnabled = false;

}

