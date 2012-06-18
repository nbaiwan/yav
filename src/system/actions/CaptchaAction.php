<?php

class CaptchaAction extends Yaf_Action_Abstract {
    public $backColor = null;
    
    public $fontColor = null;
    
    public $fixedVerifyCode = null;
    
    /**
     * Runs the action.
     */
    public function execute() {
        $captchaCode = Captcha::inst()->getVerifyCode(true);
        // we add a random 'v' parameter so that FireFox can refresh the image
        // when src attribute of image tag is changed
        //echo $this->getController()->createUrl($this->getId(),array('v' => uniqid()));
        Captcha::inst()->renderImage(Captcha::inst()->getVerifyCode());
        
        exit;
    }

}
