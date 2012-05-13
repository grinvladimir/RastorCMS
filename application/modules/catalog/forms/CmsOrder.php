<?php

class Catalog_Form_CmsOrder extends Rastor_Form {

    public function __construct() {
        parent::__construct();

        $this->setAction('')
                ->setMethod('post');
        

        $info = $this->createElement('textarea', 'info', array('label' => 'Информация о заказчике'));
        $info->addDecorator('errors', array('class' => 'error msg'))
                ->setAttrib('class', 'wyswisg')
                ->setRequired();

        $model = new Catalog_Model_Order();
        
        $status = $this->createElement('select', 'status', array('label' => 'Статус заказа'));
        $status->addDecorator('errors', array('class' => 'error msg'))
                ->setMultiOptions($model->getStatuses());

        $this->addElements(array($info, $status));

        $submit = $this->createElement('submit', 'submit', array('disableLoadDefaultDecorators' => true, 'required' => true, 'label' => 'Создать'));
        $submit->addDecorator('viewHelper')
                ->addDecorator('errors')
                ->addDecorator('htmlTag', array('tag' => 'p'));

        $this->addElement($submit);
    }

}
