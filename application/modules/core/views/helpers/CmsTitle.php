<?php

class Core_View_Helper_CmsTitle extends Zend_View_Helper_Abstract {

    private static $_navigation;
    private static $_title = '';
    private static $_documentTitleSeparator = '';

    public static function setNavigation($navigation) {
        self::$_navigation = $navigation;
    }

    public static function setTitle($title) {
        self::$_title = $title;
    }

    public static function setDocumentTitle($separator) {
        self::$_documentTitleSeparator = $separator;
    }

    public function cmsTitle() {
        return self::$_title;
    }

    public static function getTitle($title = '') {
        if (isset($title)) {
            self::setTitle($title);
        }

        $oViewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $oNavigation = $oViewRenderer->view->navigation(new Zend_Navigation(Zend_Registry::get('CmsMenu')));
        $active = $oNavigation->findActive($oNavigation->getContainer());

        $view = Zend_Layout::getMvcInstance()->getView();
        if (strlen(self::$_title)) {
            $view->headTitle(self::$_documentTitleSeparator . ' | ' . self::$_title);
        } else if (isset($active['page'])) {
            $view->headTitle(self::$_documentTitleSeparator . ' | ' . $active['page']->getLabel());
            self::$_title = $active['page']->getLabel();
        } else {
            $view->headTitle(self::$_documentTitleSeparator);
        }
    }

}
