<?php
/**
 * 文件说明
 * @version $Id: Common.php Mar 27, 2012 10:10:39 AM
 * @author Jacky Zhang <zhangbaolin@qvod.com>
 * @copyright 深圳市快播科技有限公司
 */

class Common
{
	/**
	 * 返回客户端的IP地址
	 * @param boolean $is_int 所否返回整形IP地址
	 * @return int/string $ip 用户IP地址 
	 */
	public static function getIp($is_int = false)
	{
		//$ip = false;
		if(!empty($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER["REMOTE_ADDR"];
		} else if(! empty($_SERVER["HTTP_CLIENT_IP"])){
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		
		if(! empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			if($ip){
				array_unshift($ips, $ip);
				$ip = FALSE;
			}
			for($i = 0; $i < count($ips); $i ++){
				if(! preg_match("/^(0|10|127|172\.16|192\.168)\./", $ips[$i])){
					$ip = $ips[$i];
					break;
				}
			}
		}
		$ip = $ip ? $ip : $_SERVER['REMOTE_ADDR'];
		$ip = $ip ? $ip : '0.0.0.0';
		
		return $is_int ? bindec(decbin(ip2long($ip))) : $ip;
	}
	
	/**
	 * 数据库连接密码二次加密函数
	 * @param string $db_pwd 未加密前到密码
	 * @return string $new_db_pwd 返回加密后到MYSQL密码
	 */
	public static function mysql_db_encrypt($db_pwd) {
		$config_file = APP_PATH . '/config/mysql_db_salt.ini';
		$salt = preg_replace("/\r\n|\r|\n/", "", @file_get_contents($config_file));
		$new_db_pwd = md5(md5($db_pwd) . $salt);
        // echo "{$db_pwd}, {$salt}, {$new_db_pwd}";
	
		return $new_db_pwd;
	}
}