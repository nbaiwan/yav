<?php



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
        $this->_cache->expire($id, $this->_s->expire);
        return $this->_cache->get($this->calculateKey($id));
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
        return "{$this->_s['prefix']}{$id}";
    }
}