<?php

class UserLogsController extends SystemController
{
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$ids = is_array($id) ? $id : array($id);
		
		Goods::model()->updateAll(array(
				'GState'=>0,
			),
			'GID in (:GID)',
			array(
				'GID' => implode(',', $ids),
			)
		);
		
		$this->redirect(array('Goods/Index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		/*
		$model=new Goods('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Goods']))
			$model->attributes=$_GET['Goods'];

		$this->render('index',array(
			'model'=>$model,
		));*/
		
		$params = array(
			'allow_cache'=>false
		);
		
		$this->render('/userlogs/index', array(
				'data' => UserLogs::Pages($params),
			)
		);
	}
	
	/**
	 * 
	 * Enter description here ...
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
		die(json_encode($ret));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Goods::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='goods-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
