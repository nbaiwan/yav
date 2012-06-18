<?php

class ErrorController extends Yaf_Controller_Abstract {

    public function errorAction() {
        $e = $this->getRequest()->getException();
        assert($e == $e->getCode());
        $this->getView()->assign(
            array(
                "code" => $e->getCode(),
                "message" => $e->getMessage(),
            )
        );
        print_r($e->getMessage());
       exit; 
    }
}
