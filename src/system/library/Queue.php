<?php

class Queue {
	protected static $__email_key = 'queue.email';
	
	protected static $__mail_hwnd = null;
	
	public static function emailPush($to, $subject, $message) {
	    if (trim($to) == '' || !Common::isEmail($to)) {
	        return false;
	    }
		$data = array(
				'to' => $to,
				'subject' => $subject,
				'message' => $message,
		);
		
		Yaf_Registry::get('queue')->lpush(self::$__email_key, json_encode($data));
	}
	
	public static function emailPop() {
		$data = Yaf_Registry::get('queue')->rpop(self::$__email_key);
		
		if($data) {
			$data = json_decode($data, true);
			if(is_array($data)) {
				return $data;
			}
		}
		
		return array();
	}
	
	public static function emailLen() {
		$queue_len = Yaf_Registry::get('queue')->llen(self::$__email_key);
		
		return $queue_len ? $queue_len : 0;
	}
	
	public static function sendMail($params) {
		//$mailer->AddReplyTo('service@vip.kuaibo.com');
        if (self::$__mail_hwnd === null) {
            include APP_PATH . '/library/phpmailer/class.phpmailer.php';
            self::$__mail_hwnd = new PHPMailer();
            self::$__mail_hwnd->IsMail(); // Send By Mail Function
            
            /*
            include APP_PATH . '/library/phpmailer/class.smtp.php';
            
            self::$__mail_hwnd->Host = 'smtp.qvod.com';
            self::$__mail_hwnd->IsSMTP();  // Send By SMTP Server
            self::$__mail_hwnd->SMTPDebug = 1;
            self::$__mail_hwnd->SMTPAuth = true;
            self::$__mail_hwnd->Username = 'vipservice@qvod.com';
            self::$__mail_hwnd->Password = 'vip321';;
            self::$__mail_hwnd->From = 'vipservice@qvod.com';
            self::$__mail_hwnd->FromName = '快播会员服务中心';
            */
        } else {
            self::$__mail_hwnd->ClearAddresses();
        }
        
		self::$__mail_hwnd->From = 'service@vip.kuaibo.com';
		self::$__mail_hwnd->FromName = '快播会员服务中心';
		self::$__mail_hwnd->CharSet = 'UTF-8';
		self::$__mail_hwnd->Encoding = 'base64';
	    self::$__mail_hwnd->IsHTML(true);
	    
		self::$__mail_hwnd->AddAddress($params['to']);
		self::$__mail_hwnd->Subject = $params['subject'];
		self::$__mail_hwnd->Body = $params['message'];
		//$mailer->AltBody = $params['message'];
		
		if(!self::$__mail_hwnd->Send()) {
			Common::log("邮件发送失败：" . self::$__mail_hwnd->ErrorInfo);
            
            return false;
		} else {
			return true;
		}
	}
}