<?php
/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{

    private $__config;
    
    public function _initBootstrap() {
        $this->__config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set("config", $this->__config);
    }
    
    /*
    public function _initIncludePath() {
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->__config->application->library);
    }
    */
    
    public function _initErrors() {
        if($this->__config->application->showErrors) {
            error_reporting(E_ALL & ~E_NOTICE);
            ini_set('display_errors', 'On');
        }
    }
    
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
        $router = Yaf_Dispatcher::getInstance()->getRouter();
        
        //$router->addRoute('movie', $route);
        //$route = new Yaf_Route_Map(true, "_");
        //$router->addRoute('map_defaut', $route);
        //$router->addConfig($this->__config->routes);
    }
    
    /**
     * 初始化组件
     */
    public function _initComponent(Yaf_Dispatcher $dispatcher) {
        $components = $this->__config->component->toArray();
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
    
    /**
     * 初始化Layout插件
     */
    public function _initLayout(Yaf_Dispatcher $dispatcher) {
        /**
         * 初始化布局插件, 布局文件：views/main/frame.html
         */
        /*
        $layout = new LayoutPlugin('frame.html', APP_PATH . DS . 'views/main');
        Yaf_Registry::set('layout', $layout);
        $dispatcher->registerPlugin($layout);
        */
    }
}

?>
