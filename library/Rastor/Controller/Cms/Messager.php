<?php

/**
 * Class for send and receive messages. Specifically for CMS interface.
 *
 * GlobalVision CMS
 * @author Budjak Orest
 * @copyright 2011-2012 Budjak Orest (rastor.name)
 * @license http://www.php.net/license/3_01.txt 
 * @version 1.0
 */
class Rastor_Controller_Cms_Messager {

    private $_MESSAGES = array(
        'successfully_added' => array('success', "Данные успешно добавлены!"),
        'successfully_changed' => array('success', "Данные успешно изменены!"),
        'successfully_changed_count' => array('success', "%count% записей успешно изменены!"),
        'successfully_deleted' => array('success', "Данные успешно удалены!"),
        'successfully_deleted_count' => array('success', "%deleted% записей из %all% успешно удалены!"),
        'no_access' => array('warning', "У вас нету прав для совершения данного действия!"),
        'not_changed' => array('information', "Запись не изменилась, так как изменений не было замечено!"),
        'not_found' => array('error', "Запись для редактирования не найдена!")
    );
    private $_namespace;
    private $_options;
    private $_translator;
    public $msg = '';
    public $type = '';

    public function __construct() {
        $this->_namespace = new Zend_Session_Namespace('Rastor_Controller_Cms_Messager');
        $this->_translator = Zend_Registry::get('Zend_Translate');
    }

    public function setAction($action, $options = null) {
        $this->_namespace->action = $action;
        $this->_options = $options;
    }

    public function getMessage($action = null, $options = null) {
        $this->genMessage($action, $options);
        return $this;
    }

    public function getAsArray($action = null, $options = null) {
        $this->genMessage($action, $options);

        return array(
            'msg' => $this->msg,
            'type' => $this->type
        );
    }

    public function getJSONMessage($action = null, $options = null) {
        $this->genMessage($action, $options);

        $response = Zend_Json::encode($this);

        if (!isset($action)) {
            $view = Zend_Layout::getMvcInstance()->getView();
            $view->messager = $response;
        }

        return $response;
    }

    private function getMessageValue($msg) {
        if (count($this->_options)) {
            foreach ($this->_options as $key => $value) {
                $msg = str_replace('%' . $key . '%', $value, $msg);
            }
        }
        return $msg;
    }

    private function genMessage($action = null, $options = null) {
        if (isset($action)) {
            $this->_options = $options;

            if (isset($this->_MESSAGES[$action])) {
                $this->msg = $this->getMessageValue($this->_translator->_($this->_MESSAGES[$action][1]));
                $this->type = $this->_MESSAGES[$action][0];
            }

            $this->_namespace->unsetAll();
        } else if (isset($this->_namespace->action)) {
            if (isset($this->_MESSAGES[$this->_namespace->action])) {
                $this->msg = $this->getMessageValue($this->_translator->_($this->_MESSAGES[$this->_namespace->action][1]));
                $this->type = $this->_MESSAGES[$this->_namespace->action][0];
            }

            $this->_namespace->unsetAll();
        }
    }

}