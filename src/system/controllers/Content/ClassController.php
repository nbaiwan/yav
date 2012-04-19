<?php

class ClassController extends SysController
{
	
	/**
	 * 添加档案栏目
	 */
	public function actionCreate($id = 0)
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Class']) || !is_array($_POST['Class'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存档案栏目信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Class']['class_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('档案栏目名称不能为空', self::MSG_ERROR, true);
			}
			
			if($_POST['Class']['content_model_id'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('内容模型不能为空', self::MSG_ERROR, true);
			}
			
			if(empty($_POST['Class']['class_identify'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('档案栏目标识不能为空', self::MSG_ERROR, true);
			}
			
			$flag = Yii::app()->db->createCommand()->insert('{{content_archives_classes}}',
				array(
					'class_id' => 0,
					'class_name' => $_POST['Class']['class_name'],
					'class_parent_id' => isset($_POST['Class']['class_parent_id']) ? intval($_POST['Class']['class_parent_id']) : 0,
					'content_model_id' => $_POST['Class']['content_model_id'],
					'class_identify' => $_POST['Class']['class_identify'],
					'class_is_default' => isset($_POST['Class']['class_is_default']) ? intval($_POST['Class']['class_is_default']) : 0,
					'class_default' => $_POST['Class']['class_default'],
					'class_is_part' => isset($_POST['Class']['class_is_part']) ? intval($_POST['Class']['class_is_part']) : ContentArchivesClass::STAT_PART_COVER_CLASS,
					'class_tempindex' => $_POST['Class']['class_tempindex'],
					'class_templist' => $_POST['Class']['class_templist'],
					'class_temparticle' => $_POST['Class']['class_temparticle'],
					'class_seo_keywords' => $_POST['Class']['class_seo_keywords'],
					'class_seo_description' => $_POST['Class']['class_seo_description'],
					'class_is_show' => isset($_POST['Class']['class_is_show']) ? intval($_POST['Class']['class_is_show']) : 0,
					'class_rank' => isset($_POST['Class']['class_rank']) ? intval($_POST['Class']['class_rank']) : 255,
					'class_is_system' => 0,
					'class_status' => ContentArchivesClass::STAT_STATUS_NORMAL,
					'class_lasttime' => $_SERVER['REQUEST_TIME'],
					'class_dateline' => $_SERVER['REQUEST_TIME'],
				)
			);
			
			$content_model_id = 0;
			$class_name = $_POST['Class']['class_name'];
			if($flag) {
				$content_model_id = Yii::app()->db->getLastInsertID();
				//更新缓存
				ContentArchivesClass::update_cache();
				
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}添加了档案栏目({classes_name})';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'classes_name' => $class_name,
					'data' => array('content_model_id'=>$content_model_id),
				);
				AdminLogs::add($user->id, 'Content/Class', $content_model_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => url($this->module->id. '/Content/Class/Index'),
					);
					$this->message('添加档案栏目完成', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}添加档案栏目{classes_name}失败';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'classes_name' => $class_name,
					'data' => array('server'=>$_POST['Class']),
				);
				AdminLogs::add($user->id, 'Content/Class', $content_model_id, 'Insert', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('添加档案栏目失败', self::MSG_ERROR, true);
			}
		}
		
		//
		$class = array(
			'class_id' => 0,
			'class_name' => '',
			'class_parent_id' => $id,
			'class_identify' => '',
			'class_is_default' => 0,
			'class_default' => 'index.html',
			'class_is_part' => ContentArchivesClass::STAT_PART_COVER_CLASS,
			'class_tempindex' => '',
			'class_templist' => '',
			'class_temparticle' => '',
			'class_seo_keywords' => '',
			'class_seo_description' => '',
			'class_is_show' => 1,
			'class_rank' => 255,
		);
		
		//
		$classes = ContentArchivesClass::get_classes_by_cache();
		
		//
		$models = ContentModel::get_models_by_cache();
		
		$this->render('create',
			array(
				'class' => $class,
				'parent_class_name' => ContentArchivesClass::get_class_name_by_id($id),
				'models' => $models,
			)
		);
	}
	
	/**
	 * 更新档案栏目
	 * @param mixed $id
	 */
	public function actionUpdate($id)
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Class']) || !is_array($_POST['Class'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存档案栏目信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Class']['class_name'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('档案栏目名称不能为空', self::MSG_ERROR, true);
			}
			
			if($_POST['Class']['content_model_id'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('内容模型不能为空', self::MSG_ERROR, true);
			}
			
			if(empty($_POST['Class']['class_identify'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('档案栏目标识不能为空', self::MSG_ERROR, true);
			}
			
			
			$flag = Yii::app()->db->createCommand()->update('{{content_archives_classes}}',
				array(
					'class_name' => $_POST['Class']['class_name'],
					'content_model_id' => $_POST['Class']['content_model_id'],
					'class_identify' => $_POST['Class']['class_identify'],
					'class_is_default' => isset($_POST['Class']['class_is_default']) ? intval($_POST['Class']['class_is_default']) : 0,
					'class_default' => $_POST['Class']['class_default'],
					'class_is_part' => isset($_POST['Class']['class_is_part']) ? intval($_POST['Class']['class_is_part']) : ContentArchivesClass::STAT_PART_COVER_CLASS,
					'class_tempindex' => $_POST['Class']['class_tempindex'],
					'class_templist' => $_POST['Class']['class_templist'],
					'class_temparticle' => $_POST['Class']['class_temparticle'],
					'class_seo_keywords' => $_POST['Class']['class_seo_keywords'],
					'class_seo_description' => $_POST['Class']['class_seo_description'],
					'class_is_show' => isset($_POST['Class']['class_is_show']) ? intval($_POST['Class']['class_is_show']) : 0,
					'class_rank' => isset($_POST['Class']['class_rank']) ? intval($_POST['Class']['class_rank']) : 255,
					'class_lasttime' => $_SERVER['REQUEST_TIME'],
				),
				'class_id=:class_id',
				array(':class_id'=>$id)
			);
			
			if($flag) {
				$class_name = $_POST['Class']['class_name'];
				//更新缓存
				ContentArchivesClass::update_cache();
				
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}修改了档案栏目({classes_name})信息';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'classes_name' => $class_name,
					'data' => array('content_model_id'=>$id),
				);
				AdminLogs::add($user->id, 'Content/Class', $id, 'Modify', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => url($this->module->id. '/Content/Class/Index'.($page ? '/'.$page : '')),
					);
					$this->message('保存档案栏目成功', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}修改档案栏目({classes_name})信息失败';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'classes_name' => $class_name,
					'data' => array('server'=>$_POST['Class']),
				);
				AdminLogs::add($user->id, 'Content/Class', $id, 'Modify', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存档案栏目信息失败', self::MSG_ERROR, true);
			}
		}
		
		$class = ContentArchivesClass::get_class_by_id($id, false);
		if(empty($class)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => url($this->module->id . '/Content/Class/Index'),
			);
			$this->message('档案栏目不存在或已被删除', self::MSG_ERROR, true);
		}
		
		//
		$models = ContentModel::get_models_by_cache();
		
		$this->render('update',
			array(
				'class' => $class,
				'models' => $models,
			)
		);
	}
	
	/**
	 * 删除档案栏目
	 * @param mixed $id 档案栏目编号
	 */
	public function actionDelete($id)
	{
		
		$class = ContentArchivesClass::get_class_by_id($id, false);
		if(empty($class)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => $this->forward,
			);
			$this->message('档案栏目不存在或已被删除', self::MSG_ERROR, true);
		}
		
		if($class['class_is_system'] == 1) {
			$this->redirect[] = array(
				'text' => '',
				'href' => $this->forward,
			);
			$this->message('系统档案栏目不允许删除', self::MSG_ERROR, true);
		}
		
		Yii::app()->db->createCommand()->update('{{content_archives_classes}}',
			array(
				'class_status' => 0,
			),
			'class_id=:class_id',
			array(':class_id'=>$id)
		);
		ContentArchivesClass::update_cache();
			
		//记录操作日志
		$user = Yii::app()->user;
		$message = '{user_name}删除了档案栏目{classes_name}';
		$data = array(
			'user_id' => $user->id,
			'user_name' => $user->name,
			'classes_name' => $class['class_name'],
			'data' => array('class_id'=>$id),
		);
		AdminLogs::add($user->id, 'Content/Class', $id, 'Delete', 'success', $message, $data);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				'href' => $this->forward,
			);
			$this->message('档案栏目删除成功', self::MSG_SUCCESS, true);
			//$this->redirect(array('Content/Class/Index'));
		}
	}
	
	/**
	 * 档案栏目管理
	 */
	public function actionIndex()
	{
		if($_SERVER['REQUEST_METHOD']=='POST') {
			//保存修改
			if(!is_array($_POST['class_name'])) $_POST['class_name'] = array();
			foreach($_POST['class_name'] as $_k=>$_v) {
				$flag = Yii::app()->db->createCommand()->update('{{content_archives_classes}}',
					array(
						'class_name' => $_v,
						'class_identify' => $_POST['class_identify'][$_k],
						'content_model_id' => $_POST['content_model_id'][$_k],
						'class_is_show' => $_POST['class_is_show'][$_k],
						'class_rank' => $_POST['class_rank'][$_k],
						'class_lasttime' => $_SERVER['REQUEST_TIME'],
					),
					'class_id=:class_id',
					array(':class_id'=>$_k)
				);
				if($flag) {
					//记录操作日志
					$user = Yii::app()->user;
					$message = '{user_name}修改了档案栏目{classes_name}';
					$data = array(
						'user_id' => $user->id,
						'user_name' => $user->name,
						'classes_name' => $_v,
						'data' => $_POST,
					);
					AdminLogs::add($user->id, 'Content/Class', $_k, 'Modify', 'success', $message, $data);
				}
			}
			
			//添加新记录
			if(!is_array($_POST['class_name_new'])) $_POST['class_name_new'] = array();
			foreach($_POST['class_name_new'] as $_k=>$_v) {
				if(is_array($_v)) {
					foreach($_v as $__k=>$__v) {
						$flag = Yii::app()->db->createCommand()->insert('{{content_archives_classes}}',
							array(
								'class_id' => 0,
								'class_parent_id' => $_k,
								'class_name' => $__v,
								'content_model_id' => $_POST['content_model_id_new'][$_k][$__k],
								'class_identify' => $_POST['class_identify_new'][$_k][$__k],
								'class_is_default' => 0,
								'class_default' => 'index.html',
								'class_is_part' => ContentArchivesClass::STAT_PART_COVER_CLASS,
								'class_tempindex' => '',
								'class_templist' => '',
								'class_temparticle' => '',
								'class_seo_keywords' => '',
								'class_seo_description' => '',
								'class_status' => ContentArchivesClass::STAT_STATUS_NORMAL,
								'class_is_show' => isset($_POST['class_is_show_new'][$_k][$__k]) ? $_POST['class_is_show_new'][$_k][$__k] : 0,
								'class_rank' => $_POST['class_rank_new'][$_k][$__k],
								'class_lasttime' => $_SERVER['REQUEST_TIME'],
								'class_dateline' => $_SERVER['REQUEST_TIME'],
							)
						);
						if($flag) {
							//记录操作日志
							$user = Yii::app()->user;
							$message = '{user_name}添加了游戏子栏目{classes_name}';
							$data = array(
								'user_id' => $user->id,
								'user_name' => $user->name,
								'classes_name' => $__v,
								'data' => $_POST,
							);
							AdminLogs::add($user->id, 'Content/Class', Yii::app()->db->getLastInsertID(), 'Insert', 'success', $message, $data);
						}
					}
				} else {
					$flag = Yii::app()->db->createCommand()->insert('{{content_archives_classes}}',
						array(
							'class_id' => 0,
							'class_parent_id' => 0,
							'class_name' => $_v,
							'content_model_id' => $_POST['content_model_id_new'][$_k],
							'class_identify' => $_POST['class_identify_new'][$_k],
							'class_is_default' => 0,
							'class_default' => 'index.html',
							'class_is_part' => ContentArchivesClass::STAT_PART_COVER_CLASS,
							'class_tempindex' => '',
							'class_templist' => '',
							'class_temparticle' => '',
							'class_seo_keywords' => '',
							'class_seo_description' => '',
							'class_status' => ContentArchivesClass::STAT_STATUS_NORMAL,
							'class_is_show' => isset($_POST['class_is_show_new'][$_k]) ? $_POST['class_is_show_new'][$_k] : 0,
							'class_rank' => $_POST['class_rank_new'][$_k],
							'class_lasttime' => $_SERVER['REQUEST_TIME'],
							'class_dateline' => $_SERVER['REQUEST_TIME'],
						)
					);
					if($flag) {
						//记录操作日志
						$user = Yii::app()->user;
						$message = '{user_name}添加了档案栏目{classes_name}';
						$data = array(
							'user_id' => $user->id,
							'user_name' => $user->name,
							'classes_name' => $_v,
							'data' => $_POST,
						);
						AdminLogs::add($user->id, 'Content/Class', Yii::app()->db->getLastInsertID(), 'Insert', 'success', $message, $data);
					}
				}
			}
			ContentArchivesClass::update_cache();
			
			$this->refresh();
		}
		
		//
		$classes = ContentArchivesClass::get_classes_by_cache();
		
		//
		$models = ContentModel::get_models_by_cache();
		
		$this->render('index',array(
			'classes' => $classes,
			'models' => $models,
		));
	}
}
