<?php
$this->breadcrumbs=array(
	'System Manager',
	'采集管理',
	'模型管理',
	'字段管理',
);

$this->menu=array(
	array('label'=>'字段列表', 'cur'=>true, 'url'=>url($this->module->id . "/Collect/Model/{$collect_model_id}/Fields/Index")),
	array('label'=>'添加字段', 'url'=>url($this->module->id . "/Collect/Model/{$collect_model_id}/Fields/Create")),
);
?>
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
				'action'=>url($this->module->id . '/Collect/Template/Rank'),
				'enableAjaxValidation'=>false,
			));
		?>
        <div class="tab-content default-tab" id="tab1">
          <table>
            <thead>
              <tr>
				<th width="50"><?php echo Yii::t('admincp', '编号'); ?></th>
				<th width="80"><?php echo Yii::t('admincp', '显示顺序'); ?></th>
				<th><?php echo Yii::t('admincp', '字段名称'); ?></th>
				<th><?php echo Yii::t('admincp', '字段标识'); ?></th>
				<th><?php echo Yii::t('admincp', '字段类型'); ?></th>
				<th><?php echo Yii::t('admincp', '最后修改'); ?></th>
				<th><?php echo Yii::t('admincp', '操作'); ?></th>
			  </tr>
			</thead>
            <tfoot>
              <tr>
                <td colspan="6">
                  <div class="bulk-actions align-left">
					
                  
				  </div>
                  <div class="pagination">
				  <?php
						$this->widget('CPager',array(
								'pages'=>$fields['pages'],
							)
						);
					?>
				  </div>
                </td>
              </tr>
            </tfoot>
            <tbody>
			<?php
				foreach($fields['rows'] as $field) {
			?>
              <tr>
                <td><?php echo $field['collect_fields_id']; ?></td>
                <td><input type="text" size="5" value="<?php echo $field['collect_fields_rank']; ?>" name="rank[<?php echo $field['collect_fields_id']; ?>]" /></td>
                <td><?php echo $field['collect_fields_name']; ?></td>
                <td><?php echo $field['collect_fields_identify']; ?></td>
                <td><?php echo $field['collect_fields_type']; ?></td>
                <td><?php echo date('Y-m-d H:i:s', $field['collect_fields_lasttime']); ?></td>
                <td>
				  <a href="<?php echo url($this->module->id . "/Collect/Model/{$collect_model_id}/Fields/{$field['collect_fields_id']}/Update");?>" title="修改">
				    <img src="<?php echo $this->module->assetsUrl; ?>/images/icons/pencil.png" alt="修改" />
				  </a>
				  <a href="<?php echo url($this->module->id . "/Collect/Model/{$collect_model_id}/Fields/{$field['collect_fields_id']}/Delete");?>" onclick="return confirm('确定要删除采集模板<<?php echo $field['collect_fields_name']; ?>>吗');" title="删除">
				    <img src="<?php echo $this->module->assetsUrl; ?>/images/icons/cross.png" alt="删除" />
				  </a>
				</td>
              </tr>
			<?php
				}
			?>
            </tbody>
          </table>
        </div>
		<?php
			$this->endWidget();
		?>
      </div>
    </div>
	
	<script type="text/javascript">
	<!--
	$('input[name=checkall]').click(function(){
		//
		if($(this).attr('checked')) {
			$('input[name="fields_id[]"]').attr('checked', 'checked');
		} else {
			$('input[name="fields_id[]"]').removeAttr('checked', '');
		}
	});
	//-->
	</script>