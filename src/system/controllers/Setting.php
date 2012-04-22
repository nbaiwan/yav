<?php

class SettingController extends SysController
{
	public $defaultAction ='Base';
	
	/**
	 * 基本设置
	 */
	public function baseAction()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(empty($_POST['Setting']) || !is_array($_POST['Setting'])) {
				$this->redirect('/setting/base');
			}
			foreach($_POST['Setting'] as $_k => $_v) {
				$this->db->update(
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
			$message = '{user_name}修改了基本设置';
			$data = array(
				'addons_data' => $_POST['Setting'],
			);
			UserLogsModel::inst()->add('Setting/Base', '', 'Modify', 'success', $message, $data);
			
			SettingModel::inst()->updateCache();
		}
		
		$settings = SettingModel::inst()->getSettingsByGroup('base');
		$this->getView()->assign(
            array(
                'settings'=>$settings,
            )
        );
	}

	/**
	 * 缓存设置
	 */
	public function cacheAction()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(empty($_POST['Setting']) || !is_array($_POST['Setting'])) {
				$this->redirect('/setting/cache');
			}
			foreach($_POST['Setting'] as $_k => $_v) {
				$this->db->update(
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
			$message = '{user_name}修改了缓存设置';
			$data = array(
				'addons_data' => $_POST['Setting'],
			);
			UserLogsModel::inst()->add('Setting/Cache', '', 'Modify', 'success', $message, $data);
			
			SettingModel::inst()->updateCache();
		}
		
		$settings = SettingModel::inst()->getSettingsByGroup('cache');
		$this->getView()->assign(
            array(
                'settings'=>$settings,
            )
        );
	}
	
	/**
	 * 其他设置
	 */
	public function otherAction()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(empty($_POST['Setting']) || !is_array($_POST['Setting'])) {
				$this->redirect('/setting/other');
			}
			foreach($_POST['Setting'] as $_k => $_v) {
				$this->db->update(
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
			$message = '{user_name}修改了其他设置';
			$data = array(
				'addons_data' => $_POST['Setting'],
			);
			UserLogsModel::inst()->add('Setting/Other', '', 'Modify', 'success', $message, $data);
			
			SettingModel::inst()->updateCache();
		}
		
		$settings = SettingModel::inst()->getSettingsByGroup('other');
		$this->getView()->assign(
            array(
                'settings'=>$settings,
            )
        );
	}
}
