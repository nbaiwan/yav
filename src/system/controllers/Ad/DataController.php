<?php

class DataController extends SysController
{
	public function actionIndex()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			//保存修改
			if(!is_array($_POST['Data']['ad_data_rank'])) $_POST['Data']['ad_data_rank'] = array();
			foreach($_POST['Data']['ad_data_rank'] as $_k=>$_v) {
				$flag = Yii::app()->db->createCommand()->update('{{ad_data}}',
					array(
						'ad_data_rank' => ($_POST['Data']['ad_data_rank'][$_k]) ? intval($_POST['Data']['ad_data_rank'][$_k]) : 255,
						'ad_data_is_show' => isset($_POST['Data']['ad_data_is_show'][$_k]) ? intval($_POST['Data']['ad_data_is_show'][$_k]) : 0,
					),
					'ad_data_id=:ad_data_id',
					array(':ad_data_id'=>$_k)
				);
				if($flag) {
					//记录操作日志
					$message = '{user_name}修改了广告素材({ad_data_subject})排序信息';
					$data = array(
						'user_id' => Yii::app()->user->id,
						'user_name' => Yii::app()->user->name,
						'ad_data_subject' => AdData::get_subject_by_id($_k),
						'addons_data' => $_POST,
					);
					AdminLogs::add(Yii::app()->user->id, 'Ad/Data', $_k, 'Modify', 'Success', $message, $data);
				}
			}
			AdData::update_cache();
			
			//$this->refresh();
			
			$this->redirect[] = array(
				'text' => '',
				'href' => $this->forward,
			);
			$this->message('修改广告素材排序完成', self::MSG_SUCCESS, true);
		}
		
		$sql = "SELECT `ad_position_id`,`ad_position_name` FROM {{ad_position}} WHERE `ad_position_status`!=:ad_position_status";
		$cmd = Yii::app()->db->createCommand($sql);
		$cmd->execute(array(':ad_position_status'=>AdPosition::STAT_DELETED));
		$pos_arr = $cmd->queryAll();
		$ad_data = AdData::Pages(
				array(
					'allow_cache' => false,
					'ad_position_id' => $_GET['ad_position_id'],
					'search_key' => $_GET['search_key'],
					//'pagesize' => 1,
				)
			);
		foreach($ad_data['rows'] as $k=>$v){
			if($v['ad_data_subject'] == '' && $v['ad_data_relative_id']){
				$relative_info = AdData::get_relative_info($v['ad_position_id'], $v['ad_data_relative_id']);
				$ad_data['rows'][$k]['ad_data_subject'] = $relative_info['ad_data_subject'];
				$ad_data['rows'][$k]['ad_data_link'] = "http://www.wan123.com".$relative_info['ad_data_link'];
			}
		}
		$this->render('index',array(
			'datas' => $ad_data,
			'pos_arr' => $pos_arr,
		));
	}
	
	public function actionUpdate($id, $page = null)
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['F']) || !is_array($_POST['F'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存信息错误', self::MSG_ERROR, true);
			}
			
			/*if($_POST['F']['ad_data_subject'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('名称不能为空', self::MSG_ERROR, true);
			}
			if($_POST['F']['ad_data_link'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('链接不能为空', self::MSG_ERROR, true);
			}*/
			if(count($_POST['F']['ad_data_page'])){
				$page = implode(",",$_POST['F']['ad_data_page']);
			}else{
				$page = "";
			}
			$user = Yii::app()->user;
			$flag = Yii::app()->db->createCommand()->update('{{ad_data}}',
				array(
					'ad_position_id' => $_POST['F']['ad_position_id'],
					'ad_data_type' => $_POST['F']['ad_data_type'],
					'ad_data_page' => $page,
					'ad_data_subject' => $_POST['F']['ad_data_subject'],
					'ad_data_image_md5' => $_POST['F']['ad_data_image_md5'],
					'ad_data_flash_md5' => $_POST['F']['ad_data_flash_md5'],
					'ad_data_link' => $_POST['F']['ad_data_link'],
					'ad_data_html' => $_POST['F']['ad_data_html'],
					'ad_data_expire_start' => strtotime($_POST['F']['ad_data_expire_start']),
					'ad_data_expire_end' => strtotime($_POST['F']['ad_data_expire_end']),
					'ad_data_rank' => $_POST['F']['ad_data_rank'],
					'update_user_id' => $user->id,
					'ad_data_relative_id' => $_POST['F']['ad_data_relative_id'],

				),
				'ad_data_id=:ad_data_id',
				array(':ad_data_id'=>$id)
			);
			if($flag) {
				$ad_data_id = $id;
				$ad_data_subject = $_POST['F']['ad_data_subject'];
				//更新缓存
				AdData::update_cache();
				
				//记录操作日志
				
				$message = '{user_name}修改了广告素材{ad_data_subject}';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'ad_data_subject' => $ad_data_subject,
					'addons_data' => array('ad_data_id'=>$ad_data_id),
				);
				AdminLogs::add($user->id, 'Ad/Data', $ad_data_id, 'Modify', 'success', $message, $data);
			}
			
			if(!isset($_GET['ajax'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => url($this->module->id. '/Ad/Data/Index'),
				);
				$this->message('保存成功', self::MSG_SUCCESS, true);
			}
		}
		
		$data = AdData::get_one_by_id($id,false);
		if(empty($data)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id . '/Ad/Position/Index'),
			);
			$this->message('id不存在或已被删除', self::MSG_ERROR, true);
		}
		//print_r($data);
		$sql = "SELECT `ad_position_id`,`ad_position_name`,`ad_position_relative_type` FROM `ad_position` WHERE `ad_position_status`!=:ad_position_status";
		$cmd = Yii::app()->db->createCommand($sql);
		$cmd->execute(array(':ad_position_status'=>AdPosition::STAT_DELETED));
		$position_arr = $cmd->queryAll();
		if($data['ad_data_expire_start']){
			$data['ad_data_expire_start'] = date("Y-m-d H:i:s",$data['ad_data_expire_start']);
		}else{
			$data['ad_data_expire_start'] = "";
		}
		if($data['ad_data_expire_end']){
			$data['ad_data_expire_end'] = date("Y-m-d H:i:s",$data['ad_data_expire_end']);
		}else{
			$data['ad_data_expire_end'] = "";
		}
		if($data['ad_data_page']){
			$data['ad_data_page'] = explode(",",$data['ad_data_page']);
		}else{
			$data['ad_data_page'] = array();
		}
		$pages = AdData::$PAGE;
		if($data['ad_data_image_md5']){
			$data['image_full_path'] = UploadFile::get_file_path($data['ad_data_image_md5'], 'images');
		}
		$this->render('update',
			array(
				'data' => $data,
				'position_arr' =>$position_arr,
				"pages" => $pages
			)
		);
	}
	public function actionCreate()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['F']) || !is_array($_POST['F'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存信息错误', self::MSG_ERROR, true);
			}
			
			/*if($_POST['F']['ad_data_subject'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('名称不能为空', self::MSG_ERROR, true);
			}
			if($_POST['F']['ad_data_link'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('链接不能为空', self::MSG_ERROR, true);
			}*/

			if(count($_POST['F']['ad_data_page'])){
				$page = implode(",",$_POST['F']['ad_data_page']);
			}else{
				$page = "";
			}
			$user = Yii::app()->user;
			$flag = Yii::app()->db->createCommand()->insert('{{ad_data}}',
				array(
					'ad_data_id' => 0,
					'ad_position_id' => $_POST['F']['ad_position_id'],
					'ad_data_type' => $_POST['F']['ad_data_type'],
					'ad_data_page' => $page,
					'ad_data_subject' => $_POST['F']['ad_data_subject'],
					'ad_data_image_md5' => $_POST['F']['ad_data_image_md5'],
					'ad_data_flash_md5' => $_POST['F']['ad_data_flash_md5'],
					'ad_data_link' => $_POST['F']['ad_data_link'],
					'ad_data_html' => $_POST['F']['ad_data_html'],
					'ad_data_expire_start' => strtotime($_POST['F']['ad_data_expire_start']),
					'ad_data_expire_end' => strtotime($_POST['F']['ad_data_expire_end']),
					'ad_data_rank' => $_POST['F']['ad_data_rank'],
					'ad_data_dateline' => time(),
					'ad_data_status' => AdData::STAT_NORMAL,
					'ad_data_relative_id' => $_POST['F']['ad_data_relative_id'],
					'insert_user_id' => $user->id,
					'update_user_id' => $user->id,
				)
			);
			
			if($flag) {
				$ad_data_id = Yii::app()->db->getLastInsertID();
				$ad_data_subject = $_POST['F']['ad_data_subject'];
				//更新缓存
				AdPosition::update_cache();
				
				//记录操作日志
				
				$message = '{user_name}添加广告素材{ad_data_subject}';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'ad_data_subject' => $ad_data_subject,
					'addons_data' => array('ad_data_id'=>$ad_data_id),
				);
				AdminLogs::add($user->id, 'Ad/Data', $ad_data_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => url($this->module->id. '/Ad/Data/Index'),
					);
					$this->message('添加成功', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}添加广告素材{ad_data_subject}失败';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'ad_data_subject' => $ad_data_subject,
					'addons_data' => array('data'=>$_POST['F']),
				);
				AdminLogs::add($user->id, 'Ad/Data', $ad_data_id, 'Insert', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('添加失败', self::MSG_ERROR, true);
			}
		}
		
		$data = array(
			'ad_data_id' => 0,
			'ad_position_id' => isset($_GET['ad_position_id']) ? $_GET['ad_position_id'] : 0,
			'ad_data_type' => 0,
			'ad_data_page' => '',
			'ad_data_subject' => '',
			'ad_data_image_md5' => '',
			'ad_data_flash_md5' => '',
			'ad_data_link' => '',
			'ad_data_html' => '',
			'ad_data_expire_start' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
			'ad_data_expire_end' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] + 3600 * 24 * 30),
			'ad_data_rank' => 255,
			'ad_data_relative_id' => 0,
		);
		
		$sql = "SELECT `ad_position_id`,`ad_position_name`,`ad_position_relative_type` FROM `ad_position` WHERE `ad_position_status`!=:ad_position_status";
		$cmd = Yii::app()->db->createCommand($sql);
		$cmd->execute(array(':ad_position_status'=>AdPosition::STAT_DELETED));
		$position_arr = $cmd->queryAll();
		
		$pages = AdData::$PAGE;
		$this->render(
			'create',
			array(
				'data' => $data,
				'position_arr' => $position_arr,
				'pages' => $pages
			)
		);
	}
	public function actionDelete($id, $page = null)
	{
		$data = AdData::get_one_by_id($id);
		Yii::app()->db->createCommand()->update('{{ad_data}}',
			array(
				'ad_data_status' => AdData::STAT_DELETED,
			),
			'ad_data_id=:ad_data_id',
			array(':ad_data_id'=>$id)
		);
		//更新缓存
		AdData::update_cache();
			
		//记录操作日志
		$user = Yii::app()->user;
		$message = '{user_name}删除了广告素材{ad_data_subject}';
		$data = array(
			'user_id' => $user->id,
			'user_name' => $user->name,
			'ad_data_subject' => $data['ad_data_subject'],
			'addons_data' => array('ad_data_id'=>$id),
		);
		AdminLogs::add($user->id, 'Ad/Data', $id, 'Delete', 'success', $message, $data);
		if(!isset($_GET['ajax'])) {
			$this->redirect(array('Ad/Data/Index'));
		}
	}
	
	/**
	 * 文件上传
	 */
	public function actionUploadFile()
	{
		// HTTP headers for no cache etc
		header('Content-type: text/plain; charset=UTF-8');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	
		// Settings
		$targetDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "plupload";
		$cleanupTargetDir = false; // Remove old files
		$maxFileAge = 60 * 60; // Temp file age in seconds
	
		// 5 minutes execution time
		@set_time_limit(5 * 60);
		// usleep(5000);
	
		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
		$chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
	
		// Clean the fileName for security reasons
		$fileName = preg_replace('/[^\w\._\s]+/', '', $fileName);
	
		// Create target dir
		if (!file_exists($targetDir)) {
			@mkdir($targetDir);
		}
	
		// Remove old temp files
		if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
			while (($file = readdir($dir)) !== false) {
				$filePath = $targetDir . DIRECTORY_SEPARATOR . $file;
		
				// Remove temp files if they are older than the max age
				if (preg_match('/\\.tmp$/', $file) && (filemtime($filePath) < time() - $maxFileAge)) {
					@unlink($filePath);
				}
			}
			
			closedir($dir);
		} else {
			throw new CHttpException (500, Yii::t('app', "Can't open temporary directory."));
		}
	
		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
	
		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];
		
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = fopen($_FILES['file']['tmp_name'], "rb");
				
					if ($in) {
						while ($buff = fread($in, 4096)) {
							fwrite($out, $buff);
						}
					} else {
						throw new CHttpException (500, Yii::t('app', "Can't open input stream."));
					}
					fclose($out);
					@unlink($_FILES['file']['tmp_name']);
				} else {
					throw new CHttpException (500, Yii::t('app', "Can't open output stream."));
				}
			} else {
				throw new CHttpException (500, Yii::t('app', "Can't move uploaded file."));
			}
		} else {
			// Open temp file
			$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen("php://input", "rb");
			
				if ($in) {
					while ($buff = fread($in, 4096)) {
						fwrite($out, $buff);
					}
				} else {
					throw new CHttpException (500, Yii::t('app', "Can't open input stream."));
				}
			
				fclose($out);
			} else
				throw new CHttpException (500, Yii::t('app', "Can't open output stream."));
		}
	
		// After last chunk is received, process the file
		$ret = array('result' => '1');
		if (intval($chunk) + 1 >= intval($chunks)) {
			
			$originalname = $fileName;
			if (isset($_SERVER['HTTP_CONTENT_DISPOSITION'])) {
				$arr = array();
				preg_match('@^attachment; filename="([^"]+)"@',$_SERVER['HTTP_CONTENT_DISPOSITION'],$arr);
				if (isset($arr[1])) {
					$originalname = $arr[1];
				}
			}
			
			$originalname = $targetDir . DIRECTORY_SEPARATOR . $originalname;
			AdUploadFile::setBasePath($_POST['type']);
			$ret = UploadFile::upload(
					array(
						'file' => $originalname,
						'upload_dir' => isset($_POST['upload_dir']) && $_POST['upload_dir'] ? $_POST['upload_dir'] : 'ad/other',
					)
				);
		}
	
		// Return response
		echo json_encode($ret); exit;
	}
}
