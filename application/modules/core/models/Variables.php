<?php

class Core_Model_Variables extends Rastor_Model_Abstract {

    protected $_dbTableClassName = 'Content_Model_DbTable_Variables';

    public function get($name) {
        $record = $this->getDbTable()->getRecord($name);

        if (isset($record->value)) {
            return $record->value;
        } else {
            return '';
        }
    }

    public function is($name) {
        return (boolean) $this->variables->getRecord($name);
    }

    public function set($name, $value) {
        $data = array('id' => $name, 'value' => $value);

        if (!$this->is($name)) {
            return $this->getDbTable()->insert($data);
        } else {
            return $this->getDbTable()->update($data, $name);
        }
    }

}
