<?php

class Rastor_Callback {

    protected static $_instance = null;
    protected static $_callbackArray = array();

    /**
     * Returns an instance of Rastor_Callback
     *
     * Singleton pattern implementation
     *
     * @return Rastor_Callback
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static function addCallback($className, $methodName) {
        self::$_callbackArray[] = array(
            'class' => $className,
            'method' => $methodName
        );
    }

    public static function callback() {
        foreach (self::$_callbackArray as $value) {
            if (method_exists($value['class'], $value['method'])) {
                $obj = new $value['class'];
                $obj->{$value['method']}();
            }
        }
    }
    
}
