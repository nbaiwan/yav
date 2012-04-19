<?php

class Movie_RundateController extends SysController {
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function deleteAction($id)
	{
		$rundate = MovieRunDateModel::inst()->getRundateById($id);
		$this->db->update(
			'{{movie_rundates}}',
			array(
				'rundate_status'=>MovieRunDateModel::STAT_STATUS_DELETED,
			),
			'rundate_id=:rundate_id',
			array(':rundate_id'=>$id)
		);
		MovieRunDateModel::inst()->updateCache();
			
		//记录操作日志
		$message = '{user_name}删除了上映时间{rundate_date}';
		$data = array(
			'rundate_date' => $rundate['rundate_date'],
			'data' => array('rundate_id'=>$id, 'data' => $rundate),
		);
		UserLogsModel::inst()->add('Movie/RunDate', $id, 'Delete', 'success', $message, $data);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect('/movie_rundate/index');
        }
	}
	
	/**
	 * Manages all models.
	 */
	public function indexAction()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			//保存修改
			if(!is_array($_POST['RunDate']['rundate_date'])) $_POST['RunDate']['rundate_date'] = array();
			foreach($_POST['RunDate']['rundate_date'] as $_k=>$_v) {
				$flag = $this->db->update(
					'{{movie_rundates}}',
					array(
						'rundate_date' => $_v,
						'rundate_rank' => $_POST['RunDate']['rundate_rank'][$_k],
					),
					'rundate_id=:rundate_id',
					array('rundate_id' => $_k)
				);
				if($flag) {
					$this->db->update(
						'{{movie_rundates}}',
						array(
							'rundate_lasttime' => $_SERVER['REQUEST_TIME'],
						),
						'rundate_id=:rundate_id',
						array('rundate_id' => $_k)
					);
					//记录操作日志
					$message = '{user_name}修改了地区{rundate_date}';
					$data = array(
						'rundate_date' => $_v,
						'data' => array(
							'old' => MovieRunDateModel::get_rundate_by_id($_k),
							'new' => $_POST,
						),
					);
					UserLogsModel::inst()->add('Movie/RunDate', $_k, 'Modify', 'success', $message, $data);
				}
			}
			//添加新记录
			if(!is_array($_POST['RunDate']['new_rundate_date'])) $_POST['RunDate']['new_rundate_date'] = array();
			foreach($_POST['RunDate']['new_rundate_date'] as $_k=>$_v) {
				if(is_array($_v)) {
					foreach($_v as $__k=>$__v) {
						$flag = $this->db->insert(
							'{{movie_rundates}}',
							array(
								'rundate_id' => '',
								'rundate_date' => $__v,
								'rundate_rank' => $_POST['RunDate']['new_rundate_rank'][$_k][$__k],
								'rundate_status' => MovieRunDateModel::STAT_STATUS_NORMAL,
								'rundate_lasttime' => $_SERVER['REQUEST_TIME'],
								'rundate_dateline' => $_SERVER['REQUEST_TIME'],
							)
						);
						if($flag) {
							//记录操作日志
							$message = '{user_name}添加了上映时间{rundate_date}';
							$data = array(
								'rundate_date' => $__v,
								'data' => $_POST['RunDate'],
							);
							UserLogsModel::inst()->add('Movie/RunDate', $this->db->getLastInsertID(), 'Insert', 'success', $message, $data);
						}
					}
				} else {
					$flag = $this->db->insert(
						'{{movie_rundates}}',
						array(
							'rundate_id' => '',
							'rundate_date' => $_v,
							'rundate_rank' => $_POST['RunDate']['new_rundate_rank'][$_k],
							'rundate_status' => MovieRunDateModel::STAT_STATUS_NORMAL,
							'rundate_lasttime' => $_SERVER['REQUEST_TIME'],
							'rundate_dateline' => $_SERVER['REQUEST_TIME'],
						)
					);
					if($flag) {
						//记录操作日志
						$message = '{user_name}添加了上映时间{rundate_date}';
						$data = array(
							'rundate_date' => $_v,
							'data' => $_POST,
						);
						UserLogsModel::inst()->add('Movie/RunDate', $this->db->getLastInsertID(), 'Insert', 'success', $message, $data);
					}
				}
			}
			MovieRunDateModel::inst()->updateCache();
			
			$this->redirect('/movie_rundate/index');
		}
		
		$this->getView()->assign(
			array(
				'rundates' => MovieRunDateModel::inst()->getRundatesByCache(),
			)
		);
	}
}
