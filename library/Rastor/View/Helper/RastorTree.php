<?php

class Rastor_View_Helper_RastorTree extends Zend_View_Helper_Abstract {

    public function RastorTree($selector, $JSONObject) {
        return '<script type="text/javascript">$("' . $selector . '").RastorTree(' . $JSONObject . ');</script>';
    }

}
