<?php

class ArchivesController extends SysController
{
	
	public function actionSearch($search_key = '')
	{
		$_params = array(
			'allow_cache' => false,
			'pagesize' => 999999,
			'search_key' => $search_key,
		);
		$archives = ContentArchives::Pages($_params);
		$_r = array(
			'count' => count($archives['rows']),
			'items' => array(
			),
		);
		foreach($archives['rows'] as $_k=>$_v) {
			$_r['items'][$_v['content_archives_id']] = $_v['content_archives_subject']; 
		}
		
		echo json_encode($_r);
		exit;
	}
	/**
	 * 添加文档
	 */
	public function actionCreate($content_model_id = null)
	{
		
		if(empty($content_model_id)) {
			$_model = ContentModel::get_model_by_id($content_model_id);
			$content_model_id = $_model['content_model_id'];
			//unset($_model);
		} else {
			$_model = ContentModel::get_model_by_id($content_model_id);
		}
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Archive']) || !is_array($_POST['Archive'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('发布文档信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Archive']['content_archives_subject'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('文档标题不能为空', self::MSG_ERROR, true);
			}
			
			if(empty($_POST['Archive']['class_id']) || !is_array($_POST['Archive']['class_id'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请至少选择一个文档栏目', self::MSG_ERROR, true);
			} else {
				foreach($_POST['Archive']['class_id'] as $_k=>$_v) {
					if(empty($_v)) {
						unset($_POST['Archive']['class_id'][$_k]);
					}
				}
				if(empty($_POST['Archive']['class_id'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => 'javascript:history.go(-1);',
					);
					$this->message('请至少选择一个文档栏目', self::MSG_ERROR, true);
				}
			}
			
			/*if($_POST['Archive']['content_archives_body'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('文档内容不能为空', self::MSG_ERROR, true);
			}*/
			
			//自定义属性
			if(isset($_POST['Archive']['content_archives_flag']) && is_array($_POST['Archive']['content_archives_flag'])) {
				$_POST['Archive']['content_archives_flag'] = explode(',', $_POST['Archive']['content_archives_flag']);
			} else {
				$_POST['Archive']['content_archives_flag'] = '';
			}
			
			//发布时间
			$pubdate = ($_POST['Archive']['content_archives_pubtime'] == '') ? $_SERVER['REQUEST_TIME'] : strtotime($_POST['Archive']['content_archives_pubtime']);
			$user = Yii::app()->user;
			$flag = Yii::app()->db->createCommand()->insert('{{content_archives}}',
				array(
					'content_archives_id' => 0,
					'admin_id' => Yii::app()->user->id,
					'content_archives_subject' => $_POST['Archive']['content_archives_subject'],
					'content_archives_color' => $_POST['Archive']['content_archives_color'],
					'content_archives_short_subject' => $_POST['Archive']['content_archives_short_subject'],
					'content_archives_flag' => isset($_POST['Archive']['content_archives_flag']) ? $_POST['Archive']['content_archives_flag'] : '',
					'content_archives_jump_url' => $_POST['Archive']['content_archives_jump_url'],
					'content_archives_thumb' => $_POST['Archive']['content_archives_thumb'],
					'content_archives_source' => $_POST['Archive']['content_archives_source'],
					'content_archives_author' => $_POST['Archive']['content_archives_author'],
					'content_archives_keywords' => $_POST['Archive']['content_archives_keywords'],
					'content_archives_summary' => isset($_POST['Archive']['content_archives_summary']) ? $_POST['Archive']['content_archives_summary'] : '',
					'content_archives_rank' => ($_POST['Archive']['content_archives_rank']) ? intval($_POST['Archive']['content_archives_rank']) : 255,
					'content_archives_status' => ContentArchives::STAT_NORMAL,
					'content_archives_pubtime' => $pubdate,
					'content_archives_lasttime' => $_SERVER['REQUEST_TIME'],
					'content_archives_dateline' => $_SERVER['REQUEST_TIME'],
					'insert_user_id' => $user->user_id,
					'update_user_id' => $user->user_id,
				)
			);
			
			$content_archives_subject = $_POST['Archive']['content_archives_subject'];
			if($flag) {
				$content_archives_id = Yii::app()->db->getLastInsertID();
				
				//文档栏目
				$_POST['Archive']['class_id'] = array_unique($_POST['Archive']['class_id']);
				foreach($_POST['Archive']['class_id'] as $_k=>$_v) {
					Yii::app()->db->createCommand()->insert(
						'{{content_archives_classes_relating}}',
						array(
							'content_archives_id' => $content_archives_id,
							'class_id' => $_v,
						)
					);
				}
				
				//标签处理
				$content_archives_tags = preg_split('/[,|，]/', $_POST['Archive']['content_archives_tags']);
				$content_archives_tags = array_unique($content_archives_tags);
				foreach($content_archives_tags as $_k=>$_v) {
					Yii::app()->db->createCommand()->insert(
						'{{content_archives_tags}}',
						array(
							'content_archives_id' => $content_archives_id,
							'tags_name' => $_v,
						)
					);
				}
				
				//缩略图
				
				//附加表信息
				$_model = ContentModel::get_model_by_id($content_model_id);
				$_columns = ContentModel::get_model_table_by_id($content_model_id);
				$_table_name = "{{content_addons{$_model['content_model_identify']}}}";
				$data = array(
					'content_archives_id' => $content_archives_id,
					'content_channel_id' => 0,
				);
				foreach($_columns as $_k=>$_v) {
					$data[$_v['content_model_field_identify']] = $_POST['Archive'][$_v['content_model_field_identify']];
				}
				
				Yii::app()->db->createCommand()->insert(
					$_table_name,
					$data
				);
				
				//记录操作日志
				
				$message = '{user_name}添加了文档({archive_subject})';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'archive_subject' => $content_archives_subject,
					'data' => array('content_archives_id'=>$content_archives_id),
				);
				AdminLogs::add($user->id, 'Content/Archive', $content_archives_id, 'Insert', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => $this->forward ? $this->forward : url($this->module->id. '/Content/Archives/Index'),
					);
					$this->message('添加文档完成', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}添加文档{archive_subject}失败';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'archive_subject' => $content_archives_subject,
					'data' => array('server'=>$_POST['Archive']),
				);
				AdminLogs::add($user->id, 'Content/Archive', $content_archives_id, 'Insert', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('添加文档失败', self::MSG_ERROR, true);
			}
		}
		
		$archive = array(
			'content_archives_id' => 0,
			'content_archives_subject' => '',
			'content_archives_color' => '',
			'content_archives_short_subject' => '',
			'content_archives_flag' => array(),
			'content_archives_tags' => array(),
			'content_archives_classes' => array(),
			'content_model_colums' => ContentModel::get_model_table_by_id($content_model_id),
			'content_archives_rank' => 255,
			'content_archives_pubtime' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
		);
		
		$_edit_template = $_model['content_model_edit_template'] ? $_model['content_model_edit_template'] : 'default';
		
		$this->render("_{$_edit_template}_create",
			array(
				'_edit_template' => $_edit_template,
				'archive' => $archive,
				'classes' => ContentArchivesClass::get_classes_by_cache(),
			)
		);
	}
	
	
	/**
	 * 采集入库
	 * @param int $id   (collect_list_id)  
	 */
	public function actionPut($id)
	{
		$mixdata = CollectList::get_list_model_by_id($id);//将collect_task collect_list collect_model content_model 数据取出来
		$collect_task = $mixdata['collect_task'];
		$collect_list = $mixdata['collect_list'];
		$collect_model = $mixdata['collect_model'];
		$content_model = $mixdata['content_model'];

		$collect_fields = CollectFields::get_fields_by_model_id($collect_model['collect_model_id']);//可变化的字段
		$tmpfields = $this->ContentReg($collect_list['collect_task_id'], $collect_list['collect_list_url'],$collect_task);
		
		foreach ($tmpfields as $k=>$v){
			$collect = CollectFields::get_fields_by_id($v['identify']);
			$collect_task[$collect['collect_fields_identify']] = $v['value'];
		}
		$archive = array(
			'content_archives_id' => 0,
			'content_archives_subject' => $collect_list['collect_list_title'],
			'content_archives_color' => '',
			'content_archives_short_subject' => '',
			'content_archives_flag' => array(),
			'content_archives_tags' => array(),
			'content_archives_classes' => array(),
			'content_model_colums' => ContentModel::get_model_table_by_id($content_model['content_model_id']),
			'content_archives_rank' => 255,
			'content_archives_pubtime' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
		);
		
		$_model = ContentModel::get_model_by_id($content_model['content_model_id']);
		
		foreach ($collect_fields as $_k=>$_v) {
			$archive[$_v['content_model_field_identify']] = $collect_task[$_v['collect_fields_identify']]; 
		}
		$_edit_template = $_model['content_model_edit_template'] ? $_model['content_model_edit_template'] : 'default';
		
		$this->render("_{$_edit_template}_create",
			array(
				'_edit_template' => $_edit_template,
				'archive' => $archive,
				'classes' => ContentArchivesClass::get_classes_by_cache(),
			)
		);
	}
	/**
	 * 采集
	 * @param int $id  collect_task_id
	 * @url  string  需要读取的文件地址
	 * @task  array  采集任务记录数组
	 */
	public function ContentReg($id, $url,$task){
		$re = CollectTask::read_html($url);
		if($re['success']){
			$content = $re['content'];
		}else{
			echo '<script>alert("'.$re['error'].'");window.close();</script>';
			exit;
		}
		$url_arr = parse_url($url);
		if($url_arr['path']){
			$a = array_filter(explode("/",$url_arr['path']));
			array_pop($a);
			$pa = implode("/",$a)."/";
		}else{
			$pa = "";
		}
		$content = CollectTask::re2ab($content,$url_arr['scheme']."://".$url_arr['host']."/".$pa);
		$template = CollectTemplate::get_template_by_id($task['collect_template_id'],false);
		if($task['collect_task_totalpagereg']){
			$totalpagereg = $task['collect_task_totalpagereg'];
		}elseif($template['collect_template_totalpagereg']){
			$totalpagereg = $template['collect_template_totalpagereg'];
		}else{
			$totalpagereg = "";
		}
		$pagerule = $task['collect_task_pagerule'] ? $task['collect_task_pagerule'] : $template['collect_template_pagerule'];
		$totalpage = 0;
		if($totalpagereg){
			$totalpagereg = preg_replace("/\[.*\]/U","(\d)",$totalpagereg);
			preg_match("/".$totalpagereg."/",$content,$p);
			if(intval($p[1])){
				$totalpage = intval($p[1]);
			}
		}
		$pageurl_arr = array();
		if($totalpage){
			for($i=2;$i<=$totalpage;$i++){
				$pageurl_arr[] = preg_replace("/{$pagerule}\d/",$pagerule.$i,$url);
			}
		}else{ //不是js分页时，则查找分页地址
			$pagestart = $task['collect_task_pagestart'] ? $task['collect_task_pagestart'] : $template['collect_template_pagestart'];
			$pageend = $task['collect_task_pageend'] ? $task['collect_task_pageend'] : $template['collect_template_pageend'];
			if($pagestart && $pageend){
				$page_area_reg = str_replace("/","\/",$pagestart) ."([\s\S]*)". str_replace("/","\/",$pageend);
				preg_match("/{$page_area_reg}/Ui",$content,$mm);
				if($mm[1]){
					preg_match_all('/\<a.*href=["|\'](.*)?["|\'].*\>(.*)\<\/a\>/iU', $content, $arr);
					if(count($arr[1])){
						$links = array_unique($arr[1]);
						sort($links);
						$source_url = preg_replace("/{$pagerule}\d/","",$url);
						if($pagerule){
							foreach($links as $k=>$v){
								if($v != $url){
									$baseurl = preg_replace("/{$pagerule}\d/","",$v);
									if($baseurl == $source_url && $v != $url){
										$pageurl_arr[] = $v;
									}
								}
							}
						}
					}
				}
			}
		}
		if(is_array($pageurl_arr) && count($pageurl_arr)){ //取得所有分页内容
			foreach($pageurl_arr as $v){
				$c = CollectTask::read_html($v);
				if($c['content']){
					$c['content'] = CollectTask::re2ab($c['content'],$url_arr['scheme']."://".$url_arr['host']."/");
					$content .= $c['content'];
				}
			}
		}
		$content = str_replace("none","block",$content); //有些分页是默认不显示内容的
		$filter = array();
		if($task['collect_task_filter']){
			$filter = array_merge($filter,json_decode($task['collect_task_filter']));
		}
		if($template['collect_template_filter']){
			$filter = array_merge($filter,json_decode($template['collect_template_filter']));
		}
		
		$_charset = CollectTask::get_charset();
		$content = mb_convert_encoding($content,"UTF-8","gb2312,gbk,utf-8");
		if(count($filter)){
			foreach($filter as $f){
				$f = preg_replace("/\[.*\]/U","([\s\S]*)",$f);
				$f = str_replace("/","\/",$f);
				$content = preg_replace("/".$f."/Ui"," ",$content);
			}
		}
		
		if($task['collect_task_rulearr']){
			eval('$task_rule_arr = '.$task['collect_task_rulearr'].';');
			$task_rule_arr = array_filter($task_rule_arr);
		}else{
			$task_rule_arr = array();
		}
		if($template['collect_template_fieldsreg']){
			eval('$template_rule_arr = '.$template['collect_template_fieldsreg'].';');
		}
		if(count($task_rule_arr)){
			foreach($task_rule_arr as $k=>$v){
				$template_rule_arr[$k] = $v;
			}
		}
		$template_rule_arr = array_filter($template_rule_arr);
		$fields = array();
		if(count($template_rule_arr) && is_array($template_rule_arr)){
			foreach($template_rule_arr as $k=>$v){
				
				$reg = preg_replace("/\[.*\]/U","([\s\S]*)",$v);
				$reg = str_replace("/","\/",$reg);
				preg_match_all("/".$reg."/U",$content,$ma);
				if(count($ma[1])){
					foreach($ma[1] as $k2=>$v2){
						if(CollectTask::is_image($v2)){
							if($task['collect_task_saveimg']){
								$ma[1][$k2] = CollectTask::save_image($v2);
							}else{
								$ma[1][$k2] = $v2;
							}
						}else{
							if($task['collect_task_saveimg']){
								preg_match_all('/<img.*src=["|\']+(.*)?["|\']+>/Ui', $v2, $arr);//正则出所有图片保存替换
								if(is_array($arr[1]) && count($arr[1])){
									foreach($arr[1] as $img){
										$ma[1][$k2] = str_replace($img,CollectTask::save_image($img),$v2);
									}
								}
							}
						}
					}
					preg_match("/\[(.*)\]/U",$v,$match);
					$fields[] = array('identify'=>$k,'name'=>$match[1],'value'=>$ma[1][0]);
				}
			}
		}
		return $fields;
	}
	
	
	public function actionUpdate($id)
	{
		$archive = ContentArchives::get_archive_by_id($id, false);//dump($archive);exit;
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['Archive']) || !is_array($_POST['Archive'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('发布文档信息错误', self::MSG_ERROR, true);
			}
			
			if($_POST['Archive']['content_archives_subject'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('文档标题不能为空', self::MSG_ERROR, true);
			}
			
			if(empty($_POST['Archive']['class_id']) || !is_array($_POST['Archive']['class_id'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('请至少选择一个文档栏目', self::MSG_ERROR, true);
			} else {
				foreach($_POST['Archive']['class_id'] as $_k=>$_v) {
					if(empty($_v)) {
						unset($_POST['Archive']['class_id'][$_k]);
					}
				}
				if(empty($_POST['Archive']['class_id'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => 'javascript:history.go(-1);',
					);
					$this->message('请至少选择一个文档栏目', self::MSG_ERROR, true);
				}
			}
			
			/*if($_POST['Archive']['content_archives_body'] =='') {
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('文档内容不能为空', self::MSG_ERROR, true);
			}*/
			
			//自定义属性
			if(isset($_POST['Archive']['content_archives_flag']) && is_array($_POST['Archive']['content_archives_flag'])) {
				$_POST['Archive']['content_archives_flag'] = implode(',', $_POST['Archive']['content_archives_flag']);
			} else {
				$_POST['Archive']['content_archives_flag'] = '';
			}
			
			//发布时间
			$pubdate = ($_POST['Archive']['content_archives_pubtime'] == '') ? $_SERVER['REQUEST_TIME'] : strtotime($_POST['Archive']['content_archives_pubtime']);
			$user = Yii::app()->user;
			$flag = Yii::app()->db->createCommand()->update('{{content_archives}}',
				array(
					'content_archives_subject' => $_POST['Archive']['content_archives_subject'],
					'content_archives_color' => $_POST['Archive']['content_archives_color'],
					'content_archives_short_subject' => $_POST['Archive']['content_archives_short_subject'],
					'content_archives_flag' => $_POST['Archive']['content_archives_flag'],
					'content_archives_jump_url' => $_POST['Archive']['content_archives_jump_url'],
					'content_archives_thumb' => $_POST['Archive']['content_archives_thumb'],
					'content_archives_source' => $_POST['Archive']['content_archives_source'],
					'content_archives_author' => $_POST['Archive']['content_archives_author'],
					'content_archives_keywords' => $_POST['Archive']['content_archives_keywords'],
					'content_archives_summary' => $_POST['Archive']['content_archives_summary'],
					'content_archives_rank' => ($_POST['Archive']['content_archives_rank']) ? intval($_POST['Archive']['content_archives_rank']) : 255,
					//'content_archives_status' => ContentArchives::STAT_NORMAL,
					'content_archives_pubtime' => $pubdate,
					'content_archives_lasttime' => $_SERVER['REQUEST_TIME'],
					'update_user_id' => $user->user_id,
				),
				'content_archives_id=:content_archives_id',
				array(':content_archives_id'=>$id)
			);
			
			if($flag) {
				$content_archives_name = $_POST['Archive']['content_archives_name'];
				
				//文档栏目
				$_POST['Archive']['class_id'] = array_unique($_POST['Archive']['class_id']);
				$_sql_addons = "";
				$_params = array(
					':content_archives_id' => $id,
				);
				foreach($_POST['Archive']['class_id'] as $_k=>$_v) {
					$_sql_addons .= $_sql_addons ? " OR class_id=:class_id{$_k}" : "class_id=:class_id{$_k}";
					$_params[":class_id{$_k}"] = $_v;
				}
				//删除多余栏目
				$_sql = "DELETE FROM {{content_archives_classes_relating}} WHERE content_archives_id=:content_archives_id AND NOT ({$_sql_addons})";
				Yii::app()->db->createCommand($_sql)->execute($_params);
				
				//取出现有栏目
				$_sql = "SELECT class_id FROM {{content_archives_classes_relating}} WHERE content_archives_id=:content_archives_id AND ({$_sql_addons})";
				$_classes = Yii::app()->db->createCommand($_sql)->queryColumn($_params);
				//去除已存在库中的栏目
				$_classes = array_diff($_POST['Archive']['class_id'], $_classes);
				
				//栏目入库
				foreach($_classes as $_k=>$_v) {
					Yii::app()->db->createCommand()->insert(
						'{{content_archives_classes_relating}}',
						array(
							'content_archives_id' => $id,
							'class_id' => $_v,
						)
					);
				}
				
				//标签处理
				$_sql_addons = "";
				$_params = array(
					':content_archives_id' => $id,
				);
				$content_archives_tags = preg_split('/[,|，]/', $_POST['Archive']['content_archives_tags']);
				foreach($content_archives_tags as $_k=>$_v) {
					$_sql_addons .= $_sql_addons ? " OR tags_name=:tags_name{$_k}" : "tags_name=:tags_name{$_k}";
					$_params[":tags_name{$_k}"] = $_v;
				}
				//删除多余栏目
				$_sql = "DELETE FROM {{content_archives_tags}} WHERE content_archives_id=:content_archives_id AND NOT ({$_sql_addons})";
				Yii::app()->db->createCommand($_sql)->execute($_params);
				
				//取出现有栏目
				$_sql = "SELECT tags_name FROM {{content_archives_tags}} WHERE content_archives_id=:content_archives_id AND ({$_sql_addons})";
				$_tags = Yii::app()->db->createCommand($_sql)->queryColumn($_params);
				//去除已存在库中的栏目
				$_tags = array_diff($content_archives_tags, $_tags);
				
				//栏目入库
				foreach($_tags as $_k=>$_v) {
					Yii::app()->db->createCommand()->insert(
						'{{content_archives_tags}}',
						array(
							'content_archives_id' => $id,
							'tags_name' => $_v,
						)
					);
				}
				
				//缩略图
				
				//附加表信息
				$_model = ContentModel::get_model_by_id($archive['content_model_id']);
				$_columns = ContentModel::get_model_table_by_id($_model['content_model_id']);
				$_table_name = "{{content_addons{$_model['content_model_identify']}}}";
				$data = array();
				foreach($_columns as $_k=>$_v) {
					if($_v['content_model_field_type'] == ContentModelField::DATA_TYPE_CHECKBOX) {
						$data[$_v['content_model_field_identify']] = serialize($_POST['Archive'][$_v['content_model_field_identify']]);
					} else {
						$data[$_v['content_model_field_identify']] = $_POST['Archive'][$_v['content_model_field_identify']];
					}
				}
				
				Yii::app()->db->createCommand()->update(
					$_table_name,
					$data,
					'content_archives_id=:content_archives_id',
					array(':content_archives_id'=>$id)
				);
				
				//删除缓存
				if(Yii::app()->cache) {
					Yii::app()->cache->delete("content.archive.row.{$id}");
				}
				
				//记录操作日志
				
				$message = '{user_name}修改了文档({archive_subject})信息';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'archive_subject' => $content_archives_name,
					'data' => array('content_archives_id'=>$id),
				);
				AdminLogs::add($user->id, 'Content/Archive', $id, 'Modify', 'success', $message, $data);
				
				if(!isset($_GET['ajax'])) {
					$this->redirect[] = array(
						'text' => '',
						'href' => $this->forward ? $this->forward : url($this->module->id. '/Content/Archives/Index'),
					);
					$this->message('保存文档成功', self::MSG_SUCCESS, true);
				}
			} else {
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}修改文档({archive_subject})信息失败';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'archive_subject' => $content_archives_name,
					'data' => array('server'=>$_POST['Archive']),
				);
				AdminLogs::add($user->id, 'Content/Archive', $id, 'Modify', 'failure', $message, $data);
				
				$this->redirect[] = array(
					'text' => '',
					'href' => 'javascript:history.go(-1);',
				);
				$this->message('保存文档信息失败', self::MSG_ERROR, true);
			}
		}
		
		if(empty($archive)) {
			$this->redirect[] = array(
				'text' => '',
				'href' => $_SERVER['HTTP_REFERER'],//url($this->module->id . '/Content/Archives/Index'),
			);
			$this->message('文档不存在或已被删除', self::MSG_ERROR, true);
		}
		$archive['content_archives_pubtime'] = date('Y-m-d H:i:00', $archive['content_archives_pubtime']);
		$_model = ContentModel::get_model_by_id($archive['content_model_id']);
		
		
		$_edit_template = $_model['content_model_edit_template'] ? $_model['content_model_edit_template'] : 'default';
		
		$this->render("_{$_edit_template}_update",
			array(
				'_edit_template' => $_edit_template,
				'archive' => $archive,
				'classes' => ContentArchivesClass::get_classes_by_cache(),
			)
		);
	}
	
	/**
	 * 删除文档
	 * @param mixed $id 文档编号
	 */
	public function actionDelete($id = null)
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(empty($_POST['Archives']['content_archives_id'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => $_SERVER['HTTP_REFERER'],
				);
				$this->message('文档不存在或已被删除', self::MSG_ERROR, true);
			}
			
			$content_archives_id = $_POST['Archives']['content_archives_id'];
		} else {
			if(empty($id) > 0) {
				$this->redirect[] = array(
					'text' => '',
					'href' => $_SERVER['HTTP_REFERER'],
				);
				$this->message('文档不存在或已被删除', self::MSG_ERROR, true);
			}
			
			$content_archives_id = $id;
		}
		//
		if(!is_array($content_archives_id)) {
			$content_archives_id = array($content_archives_id);
		}
		//
		$_sql_wheel = "";
		$_sql_params = array();
		foreach($content_archives_id as $_k=>$_v) {
			$_sql_wheel .= "content_archives_id=:content_archives_id{$_k} OR ";
			$_sql_params[":content_archives_id{$_k}"] = $_v;
		}
		$_sql_wheel = trim($_sql_wheel, ' OR ');
		
		//
		$_sql = "SELECT content_archives_id, content_archives_subject FROM {{content_archives}} WHERE {$_sql_wheel}";
		$cmd = Yii::app()->db->createCommand($_sql);
		$_r = $cmd->queryAll(true, $_sql_params);
		
		//修改状态为删除
		$content_archives_subject = ContentArchives::get_archive_subject_by_id($id);
		$flag = Yii::app()->db->createCommand()->update('{{content_archives}}',
			array(
				'content_archives_status' => ContentArchives::STAT_DELETED,
			),
			$_sql_wheel,
			$_sql_params
		);
		
		if($flag) {
			foreach($_r as $_k=>$_v) {
				//删除缓存
				if(Yii::app()->cache) {
					Yii::app()->cache->delete("content.archive.row.{$id}");
				}
				
				//记录操作日志
				$user = Yii::app()->user;
				$message = '{user_name}删除了文档({archive_subject})';
				$data = array(
					'user_id' => $user->id,
					'user_name' => $user->name,
					'archive_subject' => $_v['content_archives_subject'],
					'data' => array('content_archives_id'=>$id),
				);
				AdminLogs::add($user->id, 'Content/Archive', $_v['content_archives_id'], 'Delete', 'success', $message, $data);
			}
		}
		
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				'href' => $_SERVER['HTTP_REFERER'],
			);
			$this->message('删除文档完成', self::MSG_SUCCESS, true);
		}
	}
	
	public function actionCommend($id = null)
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(empty($_POST['Archives']['content_archives_id'])) {
				$this->redirect[] = array(
					'text' => '',
					'href' => $_SERVER['HTTP_REFERER'],
				);
				$this->message('文档不存在或已被删除', self::MSG_ERROR, true);
			}
			
			$content_archives_id = $_POST['Archives']['content_archives_id'];
		} else {
			if(empty($id)) {
				$this->redirect[] = array(
					'text' => '',
					'href' => $_SERVER['HTTP_REFERER'],
				);
				$this->message('文档不存在或已被删除', self::MSG_ERROR, true);
			}
			
			$content_archives_id = $id;
		}
		//
		if(!is_array($content_archives_id)) {
			$content_archives_id = array($content_archives_id);
		}
		//
		$_sql_addons = '';
		$_params = array();
		foreach($content_archives_id as $_k=>$_v) {
			$_sql_addons .= ":content_archives_id{$_k},";
			$_params[":content_archives_id{$_k}"] = $_v;
		}
		$_sql_addons = trim($_sql_addons, ',');
		
		$_sql = "SELECT content_archives_id, content_archives_subject, content_archives_flag FROM {{content_archives}} WHERE content_archives_id IN ({$_sql_addons})";
		$cmd = Yii::app()->db->createCommand($_sql);
		$archives = $cmd->queryAll(true, $_params);
		
		foreach($archives as $_k=>$_v) {
			$_v['content_archives_flag'] = $_v['content_archives_flag'] ? explode(',', $_v['content_archives_flag']) : array();
			//
			$_v['content_archives_flag'][] = ContentArchives::STAT_ARCHIVES_FLAG_C;
			$_v['content_archives_flag'] = array_flip($_v['content_archives_flag']);
			$_v['content_archives_flag'] = array_flip($_v['content_archives_flag']);
			sort($_v['content_archives_flag']);
			$_v['content_archives_flag'] = implode(',', $_v['content_archives_flag']);
			//
			$flag = Yii::app()->db->createCommand()->update(
				'{{content_archives}}',
				array(
					'content_archives_flag' => $_v['content_archives_flag'],
				),
				'content_archives_id=:content_archives_id',
				array(':content_archives_id'=>$_v['content_archives_id'])
			);
			if($flag) {
				//记录操作日志
				$message = '{user_name}设置了文档({archive_subject})推荐属性';
				$data = array(
					'user_id' => Yii::app()->user->id,
					'user_name' => Yii::app()->user->name,
					'archive_subject' => $_v['content_archives_subject'],
					'data' => $_POST,
				);
				AdminLogs::add(Yii::app()->user->id, 'Content/Archive', $_v['content_archives_id'], 'Modify', 'success', $message, $data);
			}
		}
		
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				'href' => $_SERVER['HTTP_REFERER'],
			);
			$this->message('选中文档设置推荐成功！', self::MSG_SUCCESS, true);
		}
	}
	
	public function actionAttr()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$content_archives_id = explode(',', $_POST['Archives']['content_archives_id']);
			$dopost = $_POST['Archives']['dopost'];
			$content_archives_flag = isset($_POST['Archives']['content_archives_flag']) ? $_POST['Archives']['content_archives_flag'] : '';
			
			$_sql_addons = '';
			$_params = array();
			foreach($content_archives_id as $_k=>$_v) {
				$_sql_addons .= ":content_archives_id{$_k},";
				$_params[":content_archives_id{$_k}"] = $_v;
			}
			$_sql_addons = trim($_sql_addons, ',');
			
			$_sql = "SELECT content_archives_id, content_archives_subject, content_archives_flag FROM {{content_archives}} WHERE content_archives_id IN ({$_sql_addons})";
			$cmd = Yii::app()->db->createCommand($_sql);
			$archives = $cmd->queryAll(true, $_params);
			
			foreach($archives as $_k=>$_v) {
				$_v['content_archives_flag'] = $_v['content_archives_flag'] ? explode(',', $_v['content_archives_flag']) : array();
				//
				switch ($dopost) {
					case 'del':
						$_v['content_archives_flag'] = array_flip($_v['content_archives_flag']);
						unset($_v['content_archives_flag'][$content_archives_flag]);
						$_v['content_archives_flag'] = array_flip($_v['content_archives_flag']);
						break;
					case 'add':
						$_v['content_archives_flag'][] = $content_archives_flag;
						$_v['content_archives_flag'] = array_flip($_v['content_archives_flag']);
						$_v['content_archives_flag'] = array_flip($_v['content_archives_flag']);
						break;
				}
				sort($_v['content_archives_flag']);
				$_v['content_archives_flag'] = implode(',', $_v['content_archives_flag']);
				//
				$flag = Yii::app()->db->createCommand()->update(
					'{{content_archives}}',
					array(
						'content_archives_flag' => $_v['content_archives_flag'],
					),
					'content_archives_id=:content_archives_id',
					array(':content_archives_id'=>$_v['content_archives_id'])
				);
				if($flag) {
					//记录操作日志
					$message = '{user_name}修改了文档({archive_subject})属性';
					$data = array(
						'user_id' => Yii::app()->user->id,
						'user_name' => Yii::app()->user->name,
						'archive_subject' => $_v['content_archives_subject'],
						'data' => $_POST,
					);
					AdminLogs::add(Yii::app()->user->id, 'Content/Archive', $_v['content_archives_id'], 'Modify', 'success', $message, $data);
				}
			}
		}
		
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				'href' => $_SERVER['HTTP_REFERER'],
			);
			$this->message('修改选中文档的属性成功！', self::MSG_SUCCESS, true);
		}
	}
	
	/**
	 * 文档管理
	 */
	public function actionIndex($content_model_id = null, $class_id = null, $search_key = null, $page = null)
	{
		if($_SERVER['REQUEST_METHOD']=='POST') {
			//保存修改
			if(!is_array($_POST['Archive']['content_archives_rank'])) $_POST['Archive']['content_archives_rank'] = array();
			foreach($_POST['Archive']['content_archives_rank'] as $_k=>$_v) {
				$flag = Yii::app()->db->createCommand()->update('{{content_model}}',
					array(
						'content_archives_rank' => ($_POST['Archive']['content_archives_rank'][$_k]) ? intval($_POST['Archive']['content_archives_rank'][$_k]) : 255,
						'content_archives_lasttime' => $_SERVER['REQUEST_TIME'],
					),
					'content_archives_id=:content_archives_id',
					array(':content_archives_id'=>$_k)
				);
				if($flag) {
					//记录操作日志
					$message = '{user_name}修改了文档({archive_subject})信息';
					$data = array(
						'user_id' => Yii::app()->user->id,
						'user_name' => Yii::app()->user->name,
						'archive_subject' => $_v,
						'data' => $_POST,
					);
					AdminLogs::add(Yii::app()->user->id, 'Content/Archive', $_k, 'Modify', 'success', $message, $data);
				}
			}
			
			//$this->refresh();
			
			$this->redirect[] = array(
				'text' => '',
				'href' => $this->forward ? $this->forward : url($this->module->id. '/Content/Archives/Index'),
			);
			$this->message('修改文档完成', self::MSG_SUCCESS, true);
		}
		
		$params = array(
			'allow_cache' => false,
			'content_model_id' => $content_model_id,
			'class_id' => $class_id ? $class_id : 2,
			'search_key' => $search_key,
		);
		$_model = ContentModel::get_model_by_id($content_model_id);
		$_list_template = $_model['content_model_list_template'] ? $_model['content_model_list_template'] : 'default';
		
		$this->render("_{$_list_template}_list",
			array(
			'content_model_id' => $content_model_id,
			'class_id' => $class_id,
			'archives' => ContentArchives::Pages($params),
			'models' => ContentModel::get_models_by_cache(),
			'classes' => ContentArchivesClass::get_classes_by_cache(),
		));
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
			
			$ret = UploadFile::upload(
					array(
						'file' => $originalname,
						'upload_dir' => $_POST['upload_dir'],
					)
				);
		}
	
		// Return response
		echo json_encode($ret); exit;
	}
	
	public function actionStatic($id){
		ContentArchives::create_static_html($id);
		$flag = Yii::app()->db->createCommand()->update('{{content_archives}}',
			array(
				'content_archives_is_build' => 1,
			),
			'content_archives_id=:content_archives_id',
			array(':content_archives_id'=>$id)
		);
		if(!isset($_GET['ajax'])) {
			$this->redirect[] = array(
				'text' => '',
				'href' => $this->forward ? $this->forward : url($this->module->id. '/Content/Archives/Index'),
			);
			$this->message('生成完毕！', self::MSG_SUCCESS, true);
		}
	}
	
}
