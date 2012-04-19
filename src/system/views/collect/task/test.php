<?php
$this->breadcrumbs = array(
	'System Manager',
	'采集管理',
	'任务管理',
);

$this->menu=array(
	array('label'=>'任务列表', 'url'=>url($this->module->id . '/Collect/Task/Index')),
	array('label'=>'添加任务', 'url'=>url($this->module->id . '/Collect/Task/Create')),
	array('label'=>'任务测试', 'cur'=>true, 'url'=>url($this->module->id . '/Collect/Task/Test', array('id' => $task['collect_task_id']))),
);
?>
	<style>
		.rowform { width:600px; }
		.tb2 td dl dt, .tb2 td dl dd { float:left; line-height:28px;margin-left:5px; }
		.tb2 td dl dt { width:125px; font-weight: 700;}
		form textarea.rules { width: 40% !important; }
	</style>
	
	<div class="content-box">
      <div class="content-box-header">
        <h3><?php
			foreach($this->breadcrumbs as $_k=>$_v) {
				if(in_array($_v, array_slice($this->breadcrumbs, -1))) {
					echo Yii::t('admincp', $_v);
				} else {
					echo Yii::t('admincp', $_v) . " >> ";
				}
			}
		?></h3>
        <ul class="content-box-tabs">
		  <?php
		      foreach($this->menu as $_k=>$_v) {
	      ?>
          <li><a href="<?php echo $_v['url']; ?>"<?php echo $_v['cur'] ? ' class="current"' : ''; ?>><?php echo Yii::t('admincp', $_v['label']); ?></a></li>
		  <?
			  }
		  ?>
        </ul>
        <div class="clear"></div>
      </div>
      <div class="content-box-content">
		<?php
			$form=$this->beginWidget('CActiveForm', array(
				'id'=>'base-form',
				'enableAjaxValidation'=>false,
			));
		?>
        <div class="tab-content default-tab" id="tab1">
          <form action="#" method="post">
            <fieldset>
            <p>
              <span class="label">任务名称：</span>
			  <?php echo $task['collect_task_name']; ?>
              <span class="input-notification png_bg"></span>
			</p>
            <p>
              <span class="label">测试地址：</span>
			  <?php echo $task['collect_task_urls']; ?>
              <span class="input-notification png_bg"></span>
			</p>
            <p>
              <span class="label">采集列表：</span>
			  <textarea name="Task[collect_task_urls]" id="Task[collect_task_urls]" class="text-input textarea wysiwyg" rows="10" readonly="true">
<?php
			  	foreach ($task['collect_list_urls'] as $_k=>$_v) {
			  		echo "{$_v}\r\n";
			  	}
			  ?>
			  </textarea>
            </p>
            <p>
              <span class="label">内容地址：</span>
			  <textarea name="Task[collect_task_urls]" id="Task[collect_task_urls]" class="text-input textarea wysiwyg" rows="10" readonly="true">
<?php
			  	foreach ($task['collect_content_urls'] as $_k=>$_v) {
			  		echo "{$_v}\r\n";
			  	}
			  ?>
              </textarea>
            </p>
            <?php
            	foreach ($task['collect_content_data'] as $_k=>$_v) {
            ?>
            <p>
              <span class="label"><?php echo $_v['subject']; ?>：</span>
			  <?php echo $_v['content']; ?>
			</p>
            <?php
            	}
            ?>
            </fieldset>
            <div class="clear"></div>
          </form>
        </div>
		<?php
			$this->endWidget();
		?>
      </div>
    </div>