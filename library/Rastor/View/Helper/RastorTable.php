<?php

class Rastor_View_Helper_RastorTable extends Zend_View_Helper_Abstract {

    protected static $_tableObject = '';
    
    public static function setTableObject($object){
        self::$_tableObject = $object;
    }

    public function RastorTable() {
        return "<div class=\"gtable\"></div><script type=\"text/javascript\">$('.gtable').RastorTable(".self::$_tableObject.");</script>";
    }
}