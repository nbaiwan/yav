<?php
/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{
    /**
     * 初始化视图
     */
    public function _initView(Yaf_Dispatcher $dispatcher) {
        //defined("TPL_PATH") || define("TPL_PATH", APP_PATH . DS . 'views' . DS);
    }
    
    /**
     * 初始化路由
     */
    public function _initRoute(Yaf_Dispatcher $dispatcher) {
        //$router = Yaf_Dispatcher::getInstance()->getRouter();
        
		//$router->addRoute('movie', $route);
    }
    
    /**
     * 初始化组件
     */
    public function _initComponent(Yaf_Dispatcher $dispatcher) {
        $components = Yaf_Application::app()->getConfig()->component->toArray();
        foreach ($components as $_k => $_v) {
            if (isset($_v['class'])) {
                $component = new $_v['class'];
                //$component = $_v['class']::getInstance();
                if (isset($_v['params']) && is_array($_v['params'])) {
                    foreach ($_v['params'] as $__k => $__v) {
                        $component->$__k = $__v;
                    }
                }
                if (method_exists($component, 'init')) {
                    $component->init();
                }
                Yaf_Registry::set($_k, $component);
            }
        }
    }
}

?>
