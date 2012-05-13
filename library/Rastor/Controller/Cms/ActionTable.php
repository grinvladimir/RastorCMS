<?php

/**
 * Controller action abstract class for cms
 *
 * GlobalVision CMS
 * @author Budjak Orest
 * @copyright 2011-2012 Budjak Orest (rastor.name)
 * @license http://www.php.net/license/3_01.txt 
 * @version 1.0
 */
abstract class Rastor_Controller_Cms_ActionTable extends Rastor_Controller_Action {

    protected $_languages;
    protected $_mainLanguage;
    protected $_modelClassName = '';
    protected $_model;
    protected $_tableSortable = false;
    protected $_tableOrderEnabled = true;
    protected $_tableParams = array('id', 'name', 'enable');
    protected $_tableOptions = array(
        'rebuildParams' => true,
        'removeParams' => true,
        'pageRange' => 3
    );
    protected $_tableButtons = array('view', 'edit', 'remove');
    protected $_tableColumns = array('id');
    protected $_tableColumnsWidth = array(0);
    protected $_tableRequestParams = array();
    protected $_tableMessageShowTime = 3000;
    protected $_tableTranslations = array(
        'removeOne' => 'Удалить запись?',
        'removeOneConfirm' => 'Вы действительно хотите удалить запись?',
        'removeMany' => 'Удалить записи?',
        'removeManyConfirm' => 'Вы действительно хотите удалить выбраные записи?',
        'removeChecked' => 'Удалить отмеченные',
        'reload' => 'Обновить',
        'saveChanges' => 'Сохранить изменения',
        'viewTitle' => 'Смотреть',
        'editTitle' => 'Редактировать',
        'sortTitle' => 'Сортировать',
        'backToSort' => 'Вернуться к сортировке',
        'removeTitle' => 'Удалить',
        'buttonYes' => 'Да',
        'buttonNo' => 'Нет'
    );

    public function init() {
        parent::init();

        $this->_languages = array_flip($this->_config->locales->toArray());
        $this->_mainLanguage = $this->_config->locales->key();

        $this->_helper->layout->setLayout('admin');

        Core_View_Helper_CmsTitle::setNavigation(new Zend_Navigation(Zend_Registry::get('CmsMenu')));
        Core_View_Helper_CmsTitle::setDocumentTitle('GlobalVisionCMS 5.0');

        if (class_exists($this->_modelClassName)) {
            $this->_model = new $this->_modelClassName;
        } else {
            throw new Exception('Model "' . $this->_modelClassName . '" not found');
        }
    }

    public function getTableTranslations() {
        $translator = Zend_Registry::get('Zend_Translate');

        foreach ($this->_tableTranslations as $key => $value) {
            $this->_tableTranslations[$key] = $translator->_($value);
        }

        return $this->_tableTranslations;
    }

    public function getTableColumns() {
        $translator = Zend_Registry::get('Zend_Translate');

        foreach ($this->_tableColumns as $key => $value) {
            $this->_tableColumns[$key] = $translator->_($value);
        }

        return $this->_tableColumns;
    }

    protected function _getTableRemoveUrl() {
        return Rastor_Url::get('admin', array(
                    'module' => $this->getRequest()->getModuleName(),
                    'controller' => $this->getRequest()->getControllerName(),
                    'action' => 'remove'
                ));
    }

    protected function _getTableReloadUrl() {
        return Rastor_Url::get('admin', array(
                    'module' => $this->getRequest()->getModuleName(),
                    'controller' => $this->getRequest()->getControllerName(),
                    'action' => 'tabledata'
                ));
    }

    protected function _getTableSortUrl() {
        return Rastor_Url::get('admin', array(
                    'module' => $this->getRequest()->getModuleName(),
                    'controller' => $this->getRequest()->getControllerName(),
                    'action' => 'sort'
                ));
    }

    protected function _getJSONTableObject() {
        $this->getTableTranslations();
        $this->getTableColumns();

        $messager = new Rastor_Controller_Cms_Messager();
        $message = $messager->getAsArray();

        $result = array(
            'sortable' => $this->_tableSortable,
            'orderEnabled' => $this->_tableOrderEnabled,
            'buttons' => $this->_tableButtons,
            'columns' => $this->_tableColumns,
            'colWidth' => $this->_tableColumnsWidth,
            'colWidth' => $this->_tableColumnsWidth,
            'msgShowTime' => $this->_tableMessageShowTime,
            'startMsg' => $message,
            'reloadUrl' => $this->_getTableReloadUrl(),
            'requestParams' => $this->_tableRequestParams,
            'translation' => $this->_tableTranslations
        );

        if (in_array('remove', $this->_tableButtons)) {
            $result['removeUrl'] = $this->_getTableRemoveUrl();
        }

        if ($this->_tableSortable) {
            $result['sortUrl'] = $this->_getTableSortUrl();
        }

        return Zend_Json::encode($result);
    }

    public function showlistAction() {
        Core_View_Helper_CmsTitle::getTitle();

        $object = $this->_getJSONTableObject();
        Rastor_View_Helper_RastorTable::setTableObject($object);
    }

    public function sortAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        define('NO_DEBUG', true);

        $ids = $this->_getParam('ids');
        if (is_array($ids)) {
            foreach ($ids as $key => $value) {
                $this->getModel()->setSortValue($key, $value);
            }
        }
    }

    public function tabledataAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        define('NO_DEBUG', true);

        $tableOptions = array(
            'page' => $this->_getParam('page', 1),
            'order' => $this->_getParam('order', -1),
            'orderDirection' => $this->_getParam('orderdirection', 0),
            'sort' => $this->_getParam('sort', 0)
        );

        $this->_tableRequestParams = $this->_getParam('requestparams', array());

        echo $this->getModel()->getTableData($this->_tableParams, array_merge($tableOptions, $this->_tableOptions), $this->_tableRequestParams);
    }

    public function removeAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        define('NO_DEBUG', true);

        $ids = $this->_getParam('ids');
        $messager = new Rastor_Controller_Cms_Messager();
        if (is_array($ids)) {
            $deleted = 0;
            foreach ($ids as $id) {
                switch ($this->_getAuth()->getAccessLevel()) {
                    case 0: if ($this->getModel()->getDbTable()->delete($id)) {
                            $deleted++;
                        }
                        break;
                    case 1: if ((!$this->getModel()->getDbTable()->isProtected($id)) && ($this->getModel()->getDbTable()->delete($id))) {
                            $deleted++;
                        }
                        break;
                }
            }
            echo $messager->getJSONMessage('successfully_deleted_count', array('deleted' => $deleted, 'all' => count($ids)));
        } else if (is_numeric($ids)) {
            switch ($this->_getAuth()->getAccessLevel()) {
                case 0: if ($this->getModel()->getDbTable()->delete($ids)) {
                        echo $messager->getJSONMessage('successfully_deleted', array());
                    }
                    break;
                case 1: if ((!$this->getModel()->getDbTable()->isProtected($ids)) && ($this->getModel()->getDbTable()->delete($ids))) {
                        echo $messager->getJSONMessage('successfully_deleted', array());
                    } else {
                        echo $messager->getJSONMessage('no_access', array());
                    }
                    break;
                default : echo $messager->getJSONMessage('no_access', array());
            }
        }
        Rastor_Callback::callback();
    }

    /**
     * Get Model
     * 
     * @return Rastor_Model_Abstract 
     */
    public function getModel() {
        return $this->_model;
    }

}

?>
