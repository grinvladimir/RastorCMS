<?php

class Rastor_Validate_EqualPasswords extends Rastor_Validate_EqualInputs {

    protected $_messageTemplates = array(
        self::NOT_EQUAL => 'Passwords do not match'
    );

}

?>
