<?php

class Movie_ClassController extends SysController
{
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function deleteAction($id)
	{
		$class = MovieClassModel::inst()->getClassById($id);
        if($class) {
            $this->db->update(
                '{{movie_classes}}',
                array(
                    'class_status'=>MovieClassModel::STAT_STATUS_DELETED,
                ),
                'class_id=:class_id',
                array(':class_id'=>$id)
            );
            MovieClassModel::inst()->updateCache();
                
            //记录操作日志
            $message = '{user_name}删除了电影分类{class_name}';
            $data = array(
                'class_name' => $class['class_name'],
                'data' => array('class_id'=>$id, 'data' => $class),
            );
            UserLogsModel::inst()->add('Movie/Class', $id, 'Delete', 'success', $message, $data);
        }
		
		if(!isset($_GET['ajax'])) {
			$this->redirect('/movie_class/index');
        }
	}
	
	/**
	 * Manages all models.
	 */
	public function indexAction()
	{
        $movie_class_model = new MovieClassModel();
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			//保存修改
			if(!is_array($_POST['Class']['class_name'])) $_POST['Class']['class_name'] = array();
			foreach($_POST['Class']['class_name'] as $_k=>$_v) {
				$flag = $this->db->update(
					'{{movie_classes}}',
					array(
						'class_name' => $_v,
						'class_identify' => $_POST['Class']['class_identify'][$_k],
						'class_rank' => $_POST['Class']['class_rank'][$_k],
					),
					'class_id=:class_id',
					array('class_id' => $_k)
				);
                
				if($flag) {
					$this->db->update(
						'{{movie_classes}}',
						array(
							'class_lasttime' => $_SERVER['REQUEST_TIME'],
						),
						'class_id=:class_id',
						array('class_id' => $_k)
					);
					//记录操作日志
					$message = '{user_name}修改了电影分类{class_name}';
					$data = array(
						'class_name' => $_v,
						'data' => array(
							'old' => $movie_class_model->getClassById($_k),
							'new' => $_POST,
						),
					);
					UserLogsModel::inst()->add('Movie/Class', $_k, 'Modify', 'success', $message, $data);
				}
			}
			//添加新记录
			if(!is_array($_POST['Class']['new_class_name'])) $_POST['Class']['new_class_name'] = array();
			foreach($_POST['Class']['new_class_name'] as $_k=>$_v) {
				if(is_array($_v)) {
					foreach($_v as $__k=>$__v) {
						$flag = $this->db->insert(
							'{{movie_classes}}',
							array(
								'class_id' => '',
								'parent_id' => $_k,
								'class_name' => $__v,
								'class_identify' => $_POST['Class']['new_class_identify'][$_k][$__k],
								'class_rank' => $_POST['Class']['new_class_rank'][$_k][$__k],
								'class_status' => MovieClassModel::STAT_STATUS_NORMAL,
								'class_lasttime' => $_SERVER['REQUEST_TIME'],
								'class_dateline' => $_SERVER['REQUEST_TIME'],
							)
						);
						if($flag) {
							//记录操作日志
							$message = '{user_name}添加了电影分类{class_name}';
							$data = array(
								'class_name' => $__v,
								'data' => $_POST['Class'],
							);
							UserLogsModel::inst()->add('Movie/Class', $this->db->getLastInsertID(), 'Insert', 'success', $message, $data);
						}
					}
				} else {
					$flag = $this->db->insert(
						'{{movie_classes}}',
						array(
							'class_id' => '',
							'parent_id' => 0,
							'class_name' => $_v,
							'class_identify' => $_POST['Class']['new_class_identify'][$_k],
							'class_rank' => $_POST['Class']['new_class_rank'][$_k],
							'class_status' => MovieClassModel::STAT_STATUS_NORMAL,
							'class_lasttime' => $_SERVER['REQUEST_TIME'],
							'class_dateline' => $_SERVER['REQUEST_TIME'],
						)
					);
					if($flag) {
						//记录操作日志
						$user = $this->user;
						$message = '{user_name}添加了电影分类{class_name}';
						$data = array(
							'class_name' => $_v,
							'data' => $_POST,
						);
						UserLogsModel::inst()->add('Movie/Class', $this->db->getLastInsertID(), 'Insert', 'success', $message, $data);
					}
				}
			}
			$movie_class_model->updateCache();
			
			$this->redirect('/movie_class/index');
		}
        
        $this->getView()->assign(
            array(
                'classes' => $movie_class_model->getClassesByCache(),
            )
        );
	}
}
