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
				'id'=>'collect-model-fields-form',
				'enableAjaxValidation'=>false,
			));
		?>
        <div class="tab-content" id="tab2">
          <form action="#" method="post">
            <fieldset>
            <p>
              <span class="label">字段名称：</span>
			  <input type="text" name="Fields[collect_fields_name]" id="Fields[collect_fields_name]" value="<?php echo $field['collect_fields_name']; ?>"  class="text-input small-input" />
			</p>
            <p>
              <span class="label">字段标识：</span>
			  <input type="text" name="Fields[collect_fields_identify]" id="Fields[collect_fields_identify]" value="<?php echo $field['collect_fields_identify']; ?>"  class="text-input small-input" />
			</p>
            <p>
              <span class="label">电影字段：</span>
			  <select name="Fields[content_model_field_id]" id="Fields[content_model_field_id]">
	            <option value="0">===============</option>
				<?php
				    foreach($content_model_fields as $_k=>$_v){
				?>
				<option value="<?php echo $v['content_model_field_id']?>"<?php echo ($field['content_model_field_id'] == $_v['content_model_field_id']) ? " selected" : ""; ?>><?php echo $_v['content_model_field_name']?></option>
				<?php	
					}
				?>
              </select>
            </p>
            <p>
              <span class="label">字段类型：</span>
			  <select name="Fields[collect_fields_type]" id="Fields[collect_fields_type]">
				<option value="0">===============</option>
				<?php
					foreach($field_types as $_k=>$_v){
				?>
				<option value="<?php echo $_k;?>"<?php echo ($_k == $field['collect_fields_type']) ? " selected" : ""; ?>><?php echo $_v;?></option>
				<?php
					}
				?>
				</select>
            </p>
            <p>
              <span class="label">排序：</span>
			  <input type="text" name="Fields[collect_fields_rank]" id="Fields[collect_fields_rank]" value="<?php echo $field['collect_fields_rank']; ?>"  class="text-input small-input" />
			</p>
            <p>
              <input class="button" type="submit" value="保存" />
            </p>
            </fieldset>
            <div class="clear"></div>
          </form>
        </div>
		<?php
			$this->endWidget();
		?>
      </div>
    </div>