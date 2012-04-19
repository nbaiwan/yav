<?php
/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{
    /**
     * 初始化组件
     */
    public function _initComponent(Yaf_Dispatcher $dispatcher) {
        $components = Yaf_Application::app()->getConfig()->component->toArray();
        foreach ($components as $_k => $_v) {
            if (isset($_v['class'])) {
                $component = new $_v['class'];
                if (isset($_v['params']) && is_array($_v['params'])) {
                    foreach ($_v['params'] as $__k => $__v) {
                        $component->$__k = $__v;
                    }
                    if (method_exists($component, 'init')) {
                        $component->init();
                    }
                }
                Yaf_Registry::set($_k, $component);
            }
        }
    }
    /**
     * 初始化Redis - Cache组件
     */
    /*
    public function _initRedis(Yaf_Dispatcher $dispatcher) {
        $_config = Yaf_Application::app()->getConfig()->redis;
        $_servers = $_config->cache->servers->toArray();
        $_cache = new Cache_CRedisCache();
        $_cache->servers = $_servers;
        
        Yaf_Registry::set('cache', $_cache);
        unset($_cache, $_servers);
        
        $_servers = $_config->queue->servers->toArray();
        $_queue = new Cache_CRedisCache();
        $_queue->servers = $_servers;
        
        Yaf_Registry::set('queue', $_queue);
        unset($_cache, $_queue, $_servers, $_config);
    }*/
    
    /**
     * 初始化DB组件
     */
    /*
    public function _initDataBase(Yaf_Dispatcher $dispatcher) {
        $_config = Yaf_Application::app()->getConfig()->database;
        $_db_dsn       = $_config->dsn;
        $_db_username    = $_config->params->username;
        $_db_password    = $_config->params->password;
        $_db = new Db_CDbConnection($_db_dsn, $_db_username, 
                                        $_db_password);
        
        $_db_params = $_config->params->toArray();
        unset($_db_params['username'], $_db_params['password']);
        if($_db_params && is_array($_db_params)) {
            foreach($_db_params as $_k=>$_v) {
                $_db->$_k = $_v;
            }
        }
        
        Yaf_Registry::set('db', $_db);
        unset($_db, $_db_attributes, $_db_password, $_db_username, $_db_dsn, $_config);
    }
    */
}

?>
