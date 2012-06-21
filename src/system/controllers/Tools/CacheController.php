<?php

class CacheController extends SystemController
{
	public function actionIndex($step = 0)
	{
		if($step == 2) {
			$this->render('step2', array(
					'type' => isset($_GET['type']) ? implode('_', $_GET['type']) : '',
				)
			);	
		} else if($step == 3) {
			$this->render('step3');
		} else {
			$this->render('index');	
		}
	}
	
	/*
	 * 更新缓存
	 */
	public function actionUpdateCache()
	{
		$cache = Yii::app()->cache;
		
		if(!isset($_GET['type'])) exit;
		$_type = explode('_', $_GET['type']);
		
		//清空缓存数据
		if(in_array('all', $_type)) {
			$cache->flush();
			
			$this->clear_dir_files(Yii::getPathOfAlias('webroot.assets'));
		} else {
			//更新系统设置缓存
			if(in_array('setting', $_type)) {
				Setting::updateCache();
			}
			
			//更新权限缓存
			if(in_array('purview', $_type)) {
				Purview::updateCache();
			}
			
			//更新角色缓存
			if(in_array('role', $_type)) {
				Role::updateCache();
			}
			
			//清空临时文件
			if(in_array('tempfile', $_type)) {
				$this->clear_dir_files(Yii::getPathOfAlias('webroot.assets'));
			}
		}
	}
	
	protected function clear_dir_files($clear_dir)
	{
		echo $clear_dir;exit;
		$handle = opendir($clear_dir);
		if($handle) {
			
			while(($file = readdir($handle)) !== FALSE) {
				if($file == '.' || $file == '..' || $file == '.svn') {
					continue;
				}
				
				$wait_del_file = $clear_dir . DIRECTORY_SEPARATOR . $file;
				
				echo$wait_del_file, '<br />';
				if(is_dir($wait_del_file)) {
					//删除目录及目录下的文件
					$this->clear_dir_files($wait_del_file);
					@rmdir($wait_del_file);
				} else {
					//删除文件
					@unlink($wait_del_file);
				}
			}
			//关闭目录句柄
			closedir($handle);
		}
	}
}