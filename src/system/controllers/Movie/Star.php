<?php

class Movie_StarController extends SystemController {
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function deleteAction($id) {
		$class = MovieStarModel::inst()->getStarById($id);
		$this->db->update(
			'{{movie_stars}}',
			array(
				'star_status'=>MovieStarModel::STAT_STATUS_DELETED,
			),
			'star_id=:star_id',
			array(':star_id'=>$id)
		);
		MovieStarModel::updateCache();
			
		//记录操作日志
		$message = '{user_name}删除了电影明星{star_name}';
		$data = array(
			'star_name' => $class['star_name'],
			'data' => array('star_id'=>$id, 'data' => $class),
		);
		UserLogsModel::inst()->add('Movie/Star', $id, 'Delete', 'success', $message, $data);
		
		if(!isset($_GET['ajax'])) {
			$this->redirect('/movie/star/index');
        }
	}
	
	/**
	 * Manages all models.
	 */
	public function indexAction()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			//保存修改
            $old_star = MovieStarModel::inst()->getStarById($_k);
			if(!is_array($_POST['Star']['star_name'])) $_POST['Star']['star_name'] = array();
			foreach($_POST['Star']['star_name'] as $_k=>$_v) {
				$flag = $this->db->update(
					'{{movie_stars}}',
					array(
						'star_name' => $_v,
						'star_english_name' => $_POST['Star']['star_english_name'][$_k],
						'star_rank' => $_POST['Star']['star_rank'][$_k],
					),
					'star_id=:star_id',
					array('star_id' => $_k)
				);
				if($flag) {
					$this->db->update(
						'{{movie_stars}}',
						array(
							'star_lasttime' => $_SERVER['REQUEST_TIME'],
						),
						'star_id=:star_id',
						array('star_id' => $_k)
					);
					//记录操作日志
					$message = '{user_name}修改了地区{star_name}';
					$data = array(
						'star_name' => $_v,
						'data' => array(
							'old' => $old_star,
							'new' => $_POST,
						),
					);
					UserLogsModel::inst()->add('Movie/Star', $_k, 'Modify', 'success', $message, $data);
				}
			}
			//添加新记录
			if(!is_array($_POST['Star']['new_star_name'])) $_POST['Star']['new_star_name'] = array();
			foreach($_POST['Star']['new_star_name'] as $_k=>$_v) {
				if(is_array($_v)) {
					foreach($_v as $__k=>$__v) {
						$flag = $this->db->insert(
							'{{movie_stars}}',
							array(
								'star_id' => '',
								'star_name' => $__v,
								'star_english_name' => $_POST['Star']['new_star_english_name'][$_k][$__k],
								'star_rank' => $_POST['Star']['new_star_rank'][$_k][$__k],
								'star_status' => MovieStarModel::STAT_STATUS_NORMAL,
								'star_lasttime' => $_SERVER['REQUEST_TIME'],
								'star_dateline' => $_SERVER['REQUEST_TIME'],
							)
						);
						if($flag) {
							//记录操作日志
							$message = '{user_name}添加了电影明星{star_name}';
							$data = array(
								'star_name' => $__v,
								'data' => $_POST['Star'],
							);
							UserLogsModel::inst()->add('Movie/Star', $this->db->getLastInsertID(), 'Insert', 'success', $message, $data);
						}
					}
				} else {
					$flag = $this->db->insert(
						'{{movie_stars}}',
						array(
							'star_id' => '',
							'star_name' => $_v,
							'star_english_name' => $_POST['Star']['new_star_english_name'][$_k],
							'star_rank' => $_POST['Star']['new_star_rank'][$_k],
							'star_status' => MovieStarModel::STAT_STATUS_NORMAL,
							'star_lasttime' => $_SERVER['REQUEST_TIME'],
							'star_dateline' => $_SERVER['REQUEST_TIME'],
						)
					);
					if($flag) {
						//记录操作日志
						$message = '{user_name}添加了电影明星{star_name}';
						$data = array(
							'star_name' => $_v,
							'data' => $_POST,
						);
						UserLogsModel::inst()->add('Movie/Star', $this->db->getLastInsertID(), 'Insert', 'success', $message, $data);
					}
				}
			}
			MovieStarModel::inst()->updateCache();
			
			$this->redirect('/movie/star/index');
		}
		
		$this->getView()->assign(
			array(
				'stars' => MovieStarModel::inst()->getStarsByCache(),
			)
		);
	}
}
