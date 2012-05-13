<?php

class Catalog_CmsorderController extends Rastor_Controller_Cms_ActionTable {

    protected $_modelClassName = 'Catalog_Model_Order';
    protected $_tableParams = array('id', 'date', 'status');
    protected $_tableColumns = array('id', 'Дата', 'Статус');
    protected $_tableColumnsWidth = array(40, 0, 100);
    protected $_tableButtons = array('edit', 'remove');

}