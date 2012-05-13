<?php

class Rastor_View_Helper_RastorPictures extends Zend_View_Helper_Abstract {

    public function RastorPictures($selector, $JSONObject) {
        return '<script type="text/javascript">$("' . $selector . '").RastorPictures(' . $JSONObject . ');</script>';
    }

}
