<?php
/**
 * CCacheSession缓存会话类
 * @version $Id: library/CCacheSession.php Apr 17, 2012 11:37:02 AM
 * @author Jacky Zhang <myself.fervor@gmail.com>
 * @copyright 启航网络科技
 */

class CCacheSession {
    private $_cache = null;
    private $_s = null;
    
    public function __construct($session) {
        $this->_s = $session;
    }

    public function open() {
        $this->_cache = Yaf_Registry::get($this->_s->cacheId);
        
        return true;
    }
    
    public function close() {
        
        return true;
    }
    
    public function read($id) {
        $sessionKey = $this->calculateKey($id);
        $this->_cache->expire($sessionKey, $this->_s->expire);
        $data = $this->_cache->get($sessionKey);
        
        return $data===false ? '' : $data;
    }
    
    public function write($id, $value) {
        $this->_cache->set($this->calculateKey($id), $value, $this->_s['expire']);
    }
    
    public function destroy($id) {
        return $this->_cache->del($this->calculateKey($id));
    }
    
    public function gc($maxLifetime) {
        return true;
    }
    
    public function calculateKey($id) {
        return "{$this->_s->prefix}{$id}";
    }
}