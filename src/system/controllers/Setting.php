<?php

class SettingController extends SysController
{
	public $defaultAction ='Base';
	
	/**
	 * 基本设置
	 */
	public function actionBase()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(empty($_POST['Setting']) || !is_array($_POST['Setting'])) {
				$this->redirect(array('Setting/Base'));
			}
			foreach($_POST['Setting'] as $_k => $_v) {
				Yii::app()->db->createCommand()->update(
					'{{setting}}',
					array(
						'setting_value' => $_v,
					), 
					'setting_identify=:setting_identify',
					array(
						':setting_identify' => $_k,
					)
				);
			}
			
			//记录操作日志
			$user = Yii::app()->user;
			$message = '{user_name}修改了基本设置';
			$data = array(
				'user_id' => $user->id,
				'user_name' => $user->name,
				'addons_data' => $_POST['Setting'],
			);
			AdminLogs::add($user->id, 'Setting/Base', '', 'Modify', 'success', $message, $data);
			
			Setting::update_cache();
		}
		
		$settings = Setting::get_settings_by_group('base');
		$this->render('base',array(
			'settings'=>$settings,
		));
	}

	/**
	 * 缓存设置
	 */
	public function actionCache()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(empty($_POST['Setting']) || !is_array($_POST['Setting'])) {
				$this->redirect(array('Setting/Cache'));
			}
			foreach($_POST['Setting'] as $_k => $_v) {
				Yii::app()->db->createCommand()->update(
					'{{setting}}',
					array(
						'setting_value' => $_v,
					), 
					'setting_identify=:setting_identify',
					array(
						':setting_identify' => $_k,
					)
				);
			}
			
			//记录操作日志
			$user = Yii::app()->user;
			$message = '{user_name}修改了缓存设置';
			$data = array(
				'user_id' => $user->id,
				'user_name' => $user->name,
				'addons_data' => $_POST['Setting'],
			);
			AdminLogs::add($user->id, 'Setting/Cache', '', 'Modify', 'success', $message, $data);
			
			Setting::update_cache();
		}
		
		$settings = Setting::get_settings_by_group('cache');
		$this->render('cache',array(
			'settings'=>$settings,
		));
	}
	
	/**
	 * 其他设置
	 */
	public function actionOther()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(empty($_POST['Setting']) || !is_array($_POST['Setting'])) {
				$this->redirect(array('Setting/Other'));
			}
			foreach($_POST['Setting'] as $_k => $_v) {
				Yii::app()->db->createCommand()->update(
					'{{setting}}',
					array(
						'setting_value' => $_v,
					), 
					'setting_identify=:setting_identify',
					array(
						':setting_identify' => $_k,
					)
				);
			}
			
			//记录操作日志
			$user = Yii::app()->user;
			$message = '{user_name}修改了其他设置';
			$data = array(
				'user_id' => $user->id,
				'user_name' => $user->name,
				'addons_data' => $_POST['Setting'],
			);
			AdminLogs::add($user->id, 'Setting/Other', '', 'Modify', 'success', $message, $data);
			
			Setting::update_cache();
		}
		
		$settings = Setting::get_settings_by_group('other');
		$this->render('other',array(
			'settings'=>$settings,
		));
	}
}
