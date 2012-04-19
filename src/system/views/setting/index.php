	<style>
		p { padding:0px; }
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
        <div class="tab-content" id="tab2">
            <?php
		      $form=$this->beginWidget('CActiveForm', array(
			      "id" => "setting-form",
				  "enableAjaxValidation" => false,
			  ));
		    ?>
            <fieldset>
			<?php
				foreach($settings as $setting) {
			?>
            <p>
            	<span class="label"><?php echo $setting['setting_name']; ?>：</span>
				<?php
					if($setting['setting_type'] == 'text') {
				?>
            	<input class="text-input small-input" type="text" name="Setting[<?php echo $setting['setting_identify']; ?>]" id="Setting[<?php echo $setting['setting_identify']; ?>]" value="<?php echo $setting['setting_value']; ?>" />
            	<!-- <span class="input-notification success png_bg">Successful message</span> -->
				<?php
					} else if($setting['setting_type'] == 'textarea') {
				?>
            	<textarea class="text-input textarea wysiwyg" id="textarea" name="textfield" cols="79" rows="15"><?php echo $setting['setting_value']; ?></textarea>
				<?php
					} else if($setting['setting_type'] == 'radio') {
						$options = json_decode($setting['setting_options'], true);
						foreach($options as $option) {
				?>
				<input type="radio" name="Setting[<?php echo $setting['setting_identify']; ?>]" id="Setting[<?php echo $setting['setting_identify']; ?>]" value="<?php echo $option; ?>"<?php echo $setting['setting_value']==$option ? 'checked' : ''; ?> />
				<?php echo $option; ?>
				<?php
						}
					} else if($setting['setting_type'] == 'checkbox') {
						$options = json_decode($setting['setting_options'], true);
						foreach($options as $option) {
				?>
				<input type="radio" name="Setting[<?php echo $setting['setting_identify']; ?>]" id="Setting[<?php echo $setting['setting_identify']; ?>]" value="<?php echo $option; ?>"<?php echo $setting['setting_value']==$option ? 'checked' : ''; ?> />
				<?php echo $option; ?>
				<?php
						}
					} else if($setting['setting_type'] == 'select') {
						$options = json_decode($setting['setting_options'], true);
				?>
				<select name="Setting[<?php echo $setting['setting_identify']; ?>]" id="Setting[<?php echo $setting['setting_identify']; ?>]" class="small-input">
				<?php
						foreach($options as $_k=>$_v) {
				?>
					<option value="<?php echo $_k; ?>"<?php echo $setting['setting_value']==$_k? ' selected' : ''; ?>><?php echo $_v; ?></option>
				<?php
						}
			    	}
				?>
				</select>
				<br />
            	<small><?php echo $setting['setting_message']; ?></small>
			</p>
            <div class="clear"></div>
			<?
			    }
			?>
            <p>
              <input class="button" type="submit" value="保存设置" />
            </p>
            </fieldset>
          	<?php $this->endWidget(); ?>
        </div>
      </div>
    </div>