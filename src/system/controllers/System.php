<?php
/**
 *
 */

class SystemController extends Controller {
    const MSG_SUCCESS = 1;
    const MSG_ERROR = 2;
    const MSG_INFORMATION = 4;
    
    protected $redirect = array();
    
    public function init() {
    
        if(!$this->user->isLogin()) {
            $this->redirect('/default/login');
        }
        
        parent::init();
    }
    
    public function message($message) {
        $this->getView()->assign(
            array(
                'message' => $message,
                'redirect' => $this->redirect,
            )
        );
        
        $this->getView()->display('common/message.html');
        exit;
    }
}

?>
