<?php
/**
 * 网站前台入口文件
 * 
 * @version 1.0.0: index.php 2012-02-27 17:43
 * @author Jacky Zhang <myself.fervor@gmail.com>
 * @copyright 深圳市快播科技有限公司
 */

define("APP_PATH", dirname(dirname(__FILE__)));

define("DS", DIRECTORY_SEPARATOR);

$app  = new Yaf_Application(APP_PATH . "/config/application.ini");
$app->bootstrap()->run();

?>
