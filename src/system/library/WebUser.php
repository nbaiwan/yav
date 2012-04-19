<?php
// this file must be stored in:
// protected/components/WebUser.php
 
class WebUser extends Component {
    
    //private $_logined = false;
    
    public $keyPrefix = 'sowel.session.user.';
    
    private $_s = null;
	
	public function init() {
		parent::init();
		
        $this->_s = Yaf_Registry::get('session');
        $this->_s->open();
	}

	public function login($user, $duration=0) {
		//parent::login($identity, $duration);
		
		$this->_logined = true;
		
		$this->id = $user['user_id'];
		$this->name = $user['user_name'];
		$this->realname = $user['realname'];
		$this->email = $user['email'];
        $this->group_id = $user['group_id'];
	}
	
	public function logout($destroySession=true) {
		parent::logout($destroySession);
	}
	
	public function isLogin() {
		return $this->_logined;
	}
	
	public function __get($name) {
        return $this->_s[$name];
	}
	
	public function __set($name, $value) {
        return $this->_s[$name] = $value;
	}
}
?>