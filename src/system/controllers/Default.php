<?php

class DefaultController extends Controller {

    public function init() {
        parent::init();
        
        $this->actions = array(
            'captcha' => 'actions/CaptchaAction.php',
        );
    }

	public function indexAction() {
		//Yii::app()->request->redirect(url($this->module->id . '/Main'));
        $this->redirect('/main/index');
	}
	
	public function loginAction() {
        /**
         * 如果已经登录，进入管理中心
         */
        if($this->user->isLogin()) {
            $this->redirect('/main/index');
        }
        
		if(isset($_POST['Login-Form'])) {
            $user_name = $_POST['Login-Form']['user_name'];
            $user_pwd = $_POST['Login-Form']['user_pwd'];
            $captcha = $_POST['Login-Form']['captcha'];
			if(($ret = UserModel::inst()->login($user_name, $user_pwd, $captcha)) == UserModel::MSG_SUCCESS) {
				//记录操作日志
				$message = '管理员{user_name}[{user_id}][{group_name}]登录了系统后台(IP:{user_ip})';
				$data = array(
					'user_id' => $this->user->id,
					'group_name' => $this->user->group_name,
					'data' => $_POST,
				);
                
				UserLogsModel::inst()->add('Admin/Login', $this->user->id, 'Login', 'Success', $message, $data);
				
				$this->redirect('/main/index');
			} else {
				//记录操作日志
                if($ret == UserModel::MSG_ERROR_PASSWORD_INCORRECT) {
                    $user_id = UserModel::inst()->getUserId($_POST['Login-Form']['user_name']);
                } else {
                    $user_id = 0;
                }
				$message = '管理员{user_name}登录系统后台失败(IP:{user_ip})';
				$data = array(
					'user_name' => $_POST['Login-Form']['user_name'],
					'data' => $_POST,
				);
				UserLogsModel::inst()->add('Admin/Login', $user_id, 'Login', 'Failure', $message, $data);
				
			}
		}
        
	}
	
	public function logoutAction() {
		$this->user->logout();
		
		$this->redirect('/default/login');
	}
}
