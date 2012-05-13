<?php

class Rastor_View_Helper_RastorMessager extends Zend_View_Helper_Abstract {

    public function RastorMessager($selector) {
        $messager = new Rastor_Controller_Cms_Messager();
        $data = $messager->getJSONMessage();
        return '<script type="text/javascript">$("' . $selector . '").RastorMessager({data: ' . $data . '});</script>';
    }

}
