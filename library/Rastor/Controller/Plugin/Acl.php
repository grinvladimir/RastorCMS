<?php

class Rastor_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract {

    protected $_acl;
    protected $_errorModule = 'default';
    protected $_errorController = 'error';
    protected $_errorAction = 'error';

    public function __construct(Zend_Acl $acl, Array $options = array()) {
        $this->_acl = $acl;

        if (isset($options['module'])) {
            $this->_errorModule = $options['module'];
        }
        if (isset($options['controller'])) {
            $this->_errorController = $options['controller'];
        }
        if (isset($options['action'])) {
            $this->_errorAction = $options['action'];
        }
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $params = $request->getParams();
        
        $auth = Rastor_Auth::getInstance();
        $role = $auth->getRole();
        
        if (($this->_acl->has($params['module'] . '_' . $params['controller']) &&
                (!$this->_acl->isAllowed($role, $params['module'] . '_' . $params['controller'], $params['action'])))) {

                $error = new ArrayObject();
                $error->exception = new Rastor_Controller_Plugin_Exception_AccessDenied('Access Denied');
                $error->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER;
                $error->request = $request;
                $request->setParam('error_handler', $error)
                        ->setModuleName($this->_errorModule)
                        ->setControllerName($this->_errorController)
                        ->setActionName($this->_errorAction)
                        ->setDispatched(true);
        }
    }

}

?>