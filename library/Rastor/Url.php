<?php

/**
 * Class for generate Url
 *
 * GlobalVision CMS
 * @author Budjak Orest
 * @copyright 2011-2012 Budjak Orest (rastor.name)
 * @license http://www.php.net/license/3_01.txt 
 * @version 1.0
 */
class Rastor_Url {

    private static $_language;
    private static $_mainLanguage;

    public static function initialize($language, $mainLanguage) {
        self::$_language = $language;
        self::$_mainLanguage = $mainLanguage;
    }

    public static function get($routeName, $params = array()) {
        if ($routeName != 'admin') {
            if (self::$_mainLanguage == self::$_language) {
                if (substr($routeName, -strlen(ROUTE_LANG)) == ROUTE_LANG) {
                    $routeName = substr($routeName, 0, strlen($routeName) - strlen(ROUTE_LANG));
                }
            } else {
                if (substr($routeName, -strlen(ROUTE_LANG)) != ROUTE_LANG) {
                    $routeName = $routeName . ROUTE_LANG;
                }
            }
        }

        return Zend_Layout::getMvcInstance()->getView()->url($params, $routeName, true);
    }

    public static function getLang($routeName, $params = array(), $lang) {
        if ($routeName != 'admin') {
            if (self::$_mainLanguage == $lang) {
                if (substr($routeName, -strlen(ROUTE_LANG)) == ROUTE_LANG) {
                    $routeName = substr($routeName, 0, strlen($routeName) - strlen(ROUTE_LANG));
                }
            } else {
                if (substr($routeName, -strlen(ROUTE_LANG)) != ROUTE_LANG) {
                    $routeName = $routeName . ROUTE_LANG;
                }
            }
        }

        return Zend_Layout::getMvcInstance()->getView()->url($params, $routeName, true);
    }

}
