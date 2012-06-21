<?php

class Movie_DistrictController extends SystemController
{
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function deleteAction($id)
	{
		$class = MovieDistrictModel::inst()->getDistrictById($id);
		$this->db->update(
			'{{movie_districts}}',
			array(
				'district_status'=>MovieDistrictModel::STAT_STATUS_DELETED,
			),
			'district_id=:district_id',
			array(':district_id'=>$id)
		);
		MovieDistrictModel::inst()->updateCache();
			
		//记录操作日志
		$message = '{user_name}删除了电影地区{district_name}';
		$data = array(
			'district_name' => $class['district_name'],
			'data' => array('district_id'=>$id, 'data' => $class),
		);
		UserLogsModel::add('Movie/District', $id, 'Delete', 'success', $message, $data);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect('/movie/district/index');
        }
	}
	
	/**
	 * Manages all models.
	 */
	public function indexAction()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			//保存修改
			if(!is_array($_POST['District']['district_name'])) $_POST['District']['district_name'] = array();
			foreach($_POST['District']['district_name'] as $_k=>$_v) {
				$flag = $this->db->update(
					'{{movie_districts}}',
					array(
						'district_name' => $_v,
						'district_identify' => $_POST['District']['district_identify'][$_k],
						'district_rank' => $_POST['District']['district_rank'][$_k],
					),
					'district_id=:district_id',
					array('district_id' => $_k)
				);
				if($flag) {
					$this->db->update(
						'{{movie_districts}}',
						array(
							'district_lasttime' => $_SERVER['REQUEST_TIME'],
						),
						'district_id=:district_id',
						array('district_id' => $_k)
					);
					//记录操作日志
					$message = '{user_name}修改了地区{district_name}';
					$data = array(
						'district_name' => $_v,
						'data' => array(
							'old' => MovieDistrictModel::inst()->getDistrictById($_k),
							'new' => $_POST,
						),
					);
					UserLogsModel::inst()->add('Movie/District', $_k, 'Modify', 'success', $message, $data);
				}
			}
			//添加新记录
			if(!is_array($_POST['District']['new_district_name'])) $_POST['District']['new_district_name'] = array();
			foreach($_POST['District']['new_district_name'] as $_k=>$_v) {
				if(is_array($_v)) {
					foreach($_v as $__k=>$__v) {
						$flag = $this->db->insert(
							'{{movie_districts}}',
							array(
								'district_id' => '',
								'district_name' => $__v,
								'district_identify' => $_POST['District']['new_district_identify'][$_k][$__k],
								'district_rank' => $_POST['District']['new_district_rank'][$_k][$__k],
								'district_status' => MovieDistrict::STAT_STATUS_NORMAL,
								'district_lasttime' => $_SERVER['REQUEST_TIME'],
								'district_dateline' => $_SERVER['REQUEST_TIME'],
							)
						);
						if($flag) {
							//记录操作日志
							$message = '{user_name}添加了电影地区{district_name}';
							$data = array(
								'district_name' => $__v,
								'data' => $_POST['District'],
							);
							UserLogsModel::inst()->add('Movie/District', $this->db->getLastInsertID(), 'Insert', 'success', $message, $data);
						}
					}
				} else {
					$flag = $this->db->insert(
						'{{movie_districts}}',
						array(
							'district_id' => '',
							'district_name' => $_v,
							'district_identify' => $_POST['District']['new_district_identify'][$_k],
							'district_rank' => $_POST['District']['new_district_rank'][$_k],
							'district_status' => MovieDistrict::STAT_STATUS_NORMAL,
							'district_lasttime' => $_SERVER['REQUEST_TIME'],
							'district_dateline' => $_SERVER['REQUEST_TIME'],
						)
					);
					if($flag) {
						//记录操作日志
						$message = '{user_name}添加了电影地区{district_name}';
						$data = array(
							'district_name' => $_v,
							'data' => $_POST,
						);
						UserLogsModel::inst()->add('Movie/District', $this->db->getLastInsertID(), 'Insert', 'success', $message, $data);
					}
				}
			}
			MovieDistrict::update_cache();
			
			$this->refresh();
		}
		
		$this->getView()->assign(
			array(
				'districts' => MovieDistrictModel::inst()->getDistrictsByCache(),
			)
		);
	}
}
