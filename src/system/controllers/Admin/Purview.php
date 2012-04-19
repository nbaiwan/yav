<?php

class Admin_PurviewController extends SysController {
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function deleteAction($id) {
		$purview = PurviewModel::inst()->getPurviewById($id);
		$this->db->update(
			'{{purview}}',
			array(
				'status'=>PurviewModel::STAT_STATUS_DELETED
			),
			'purview_id=:purview_id',
			array(':purview_id'=>$id)
		);
		PurviewModel::updateCache();
			
		//记录操作日志
		$message = '{user_name}删除了权限{purview_name}';
		$data = array(
			'purview_name' => $purview['purview_name'],
			'data' => array('purview_id'=>$id),
		);
		UserLogsModel::inst()->add('Admin/Purview', $id, 'Delete', 'success', $message, $data);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect('/admin_purview/index');
        }
	}
	
	/**
	 * Manages all models.
	 */
	public function indexAction()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			//保存修改
			if(!is_array($_POST['Purview']['purview_name'])) $_POST['Purview']['purview_name'] = array();
			foreach($_POST['Purview']['purview_name'] as $_k=>$_v) {
				$flag = $this->db->update(
					'{{purview}}',
					array(
						'purview_name' => $_v,
						'identify' => $_POST['Purview']['identify'][$_k],
						'purview_rank' => $_POST['Purview']['purview_rank'][$_k],
						//'lasttime' => $_SERVER['REQUEST_TIME'],
					),
					'purview_id=:purview_id',
					array('purview_id' => $_k)
				);
				if($flag) {
					$this->db->update(
						'{{purview}}',
						array(
							'lasttime' => $_SERVER['REQUEST_TIME'],
						),
						'purview_id=:purview_id',
						array('purview_id' => $_k)
					);
					//记录操作日志
					$message = '{user_name}修改了权限{purview_name}';
					$data = array(
						'purview_name' => $_v,
						'data' => array(
							'old' => PurviewModel::getPurviewById($_k),
							'new' => $_POST,
						),
					);
					UserLogsModel::inst()->add('Admin/Purview', $_k, 'Modify', 'success', $message, $data);
				}
			}
			//添加新记录
			if(!is_array($_POST['Purview']['new_purview_name'])) $_POST['Purview']['new_purview_name'] = array();
			foreach($_POST['Purview']['new_purview_name'] as $_k=>$_v) {
				if(is_array($_v)) {
					foreach($_v as $__k=>$__v) {
						$flag = $this->db->insert(
							'{{purview}}',
							array(
								'purview_id' => '',
								'parent_id' => $_k,
								'purview_name' => $__v,
								'identify' => $_POST['Purview']['new_identify'][$_k][$__k],
								'purview_rank' => $_POST['Purview']['new_purview_rank'][$_k][$__k],
								'status' => PurviewModel::STAT_STATUS_NORMAL,
								'lasttime' => $_SERVER['REQUEST_TIME'],
								'dateline' => $_SERVER['REQUEST_TIME'],
							)
						);
						if($flag) {
							//记录操作日志
							$message = '{user_name}添加了权限{purview_name}';
							$data = array(
								'purview_name' => $__v,
								'data' => $_POST['Purview'],
							);
							UserLogsModel::inst()->add('Admin/Purview', $this->db->getLastInsertID(), 'Insert', 'success', $message, $data);
						}
					}
				} else {
					$flag = $this->db->insert(
						'{{purview}}',
						array(
							'purview_id' => '',
							'parent_id' => 0,
							'purview_name' => $_v,
							'identify' => $_POST['Purview']['new_identify'][$_k],
							'purview_rank' => $_POST['Purview']['new_purview_rank'][$_k],
							'status' => PurviewModel::STAT_STATUS_NORMAL,
							'lasttime' => $_SERVER['REQUEST_TIME'],
							'dateline' => $_SERVER['REQUEST_TIME'],
						)
					);
					if($flag) {
						//记录操作日志
						$message = '{user_name}添加了权限{purview_name}';
						$data = array(
							'purview_name' => $_v,
							'data' => $_POST,
						);
						UserLogsModel::inst()->add('Admin/Purview', $this->db->getLastInsertID(), 'Insert', 'success', $message, $data);
					}
				}
			}
			PurviewModel::inst()->updateCache();
			
			$this->redirect('/admin_purview/index');
		}
		
		$this->getView()->assign(
            array(
                //'model'=>$model,
                'purviews' => PurviewModel::inst()->getPurviewList(),
            )
        );
	}
}
