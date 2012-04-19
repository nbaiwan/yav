	<style>
		.rowform { width:600px; }
		.tb2 td dl dt, .tb2 td dl dd { float:left; line-height:28px; }
		.tb2 td dl dt { width:90px; font-weight: 700; margin-left:10px;}
		.purviews .subject { clear:both; font-weight: 700; color:#f00; }
		.purviews dl.row { clear:both; }
		.purviews dl.row dt { font-weight:normal; margin-left:0px; color:#00f; width:110px; float: left; }
		.purviews dl.row dd { font-weight:normal; float:left; }
		.radio_text{
			width:160px;
			float:left;
		}
	</style>
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'admin-form',
			'enableAjaxValidation'=>false,
		));
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
        <div class="tab-content" id="tab2">
          <form action="#" method="post">
            <fieldset>
            <p>
				<span class="label">用户名：</span>
				<?php
					if(empty($user['user_id'])) {
				?>
				<input type="text" name="Admin[user_name]" id="Admin[user_name]" value="<?php echo $user['user_name']; ?>" class="text-input small-input" />
				<?php
					} else {
						echo $user['user_name'], "<input type=\"hidden\" name=\"Admin[user_name]\" id=\"Admin[user_name]\" value=\"{$user['user_name']}\" />";
					}
				?>
				<span class="input-notification png_bg"></span></p>
            <p>
				<span class="label">密码：</span>
				<input type="password" name="Admin[password]" id="Admin[password]" value="" class="text-input small-input" />
				<span class="input-notification png_bg"></span></p>
            </p>
            <p>
				<span class="label">真实姓名：</span>
				<input type="text" name="Admin[realname]" id="Admin[realname]" value="<?php echo $user['realname']; ?>" class="text-input small-input" />
				<span class="input-notification png_bg"></span></p>
            <p>
				<span class="label">电子邮箱：</span>
				<input type="text" name="Admin[email]" id="Admin[email]" value="<?php echo $user['email']; ?>" class="text-input small-input" />
				<span class="input-notification png_bg"></span></p>
            <p>
				<span class="label">管理组：</span>
				<select name="Admin[role_id]" id="Admin[role_id]"<?php echo $user['is_system'] || $user['user_id'] == Yii::app()->user->id ? ' disabled="true"' : ''; ?> class="small-input">
					<?php
						$prefix_tree = array(
							1 => '',
							2 => '├─',
							3 => '├─├─',
							4 => '├─├─├─',
							5 => '├─├─├─├─',
						);
						foreach($roles as $_k=>$_v) {
							if($roles[Yii::app()->user->id]['purviews'] != 'all' && $_v['user_id'] == Yii::app()->user->id && $_v['role_id'] == Yii::app()->user->role_id) {
								echo "<optgroup label=\"{$_v['role_name']}\"></optgroup>\r\n";
							} else {
								$selected = $_v['role_id'] == $user['role_id'] ? ' selected' : '';
								echo "<option value=\"{$_v['role_id']}\"{$selected}>{$prefix_tree[$_v['deepth']]}{$_v['role_name']}</option>\r\n";
							}
						}
					?>
				</select>
            </p>
            <div>
				<span class="label">自定义权限：</span>
				<div class="purviews">
					<?php
						foreach($purviews as $_k=>$_v) {
							if($_v['deepth'] == 1) {
					?>
					<div class="subject">
					<?php
							} else if($_v['deepth'] == 2) {
					?>
					<dl class="row">
							<dt>
					<?php
							} else {
					?>
							<dd>
					<?php
							}
					?>
						<input type="checkbox" id="Admin_purviews_<?php echo $_v['purview_id']; ?>" name="Admin['purviews']" value="<?php echo $_v['purview_id']; ?>"<?php echo $user['is_system'] || $user['user_id'] == Yii::app()->user->id ? ' disabled="true"' : ''; ?> />
						<label for="Admin_purviews_<?php echo $_v['purview_id']; ?>"><?php echo $_v['purview_name']; ?></label>
					<?php
							if($_v['deepth'] == 1) {
								echo "\t</div>\r\n<div class=\"subbox\">";
							} else if($_v['deepth'] == 2) {
								if(!isset($purviews[$_k+1]) || $purviews[$_k+1]['deepth']<$_v['deepth']) {
									echo "\t\t</dt>\r\n\t</dl>\r\n</div>\r\n";
								} else if($purviews[$_k+1]['deepth']==$_v['deepth']) {
									echo "\t\t</dt>\r\n\t</dl>\r\n";
								}
							} else {
								if(!isset($purviews[$_k+1])) {
									echo "\t\t</dd>\r\n\t</dl>\r\n</div>\r\n";
								} else if($purviews[$_k+1]['deepth']==2) {
									echo "\t\t</dd>\r\n\t</dl>\r\n";
								} else if($purviews[$_k+1]['deepth']==1) {
									echo "\t\t</dd>\r\n\t</dl>\r\n</div>\r\n";
								}
							}
						}
					?>
				</div>
            </div>
			<div class="clear"></div>
            <p>
				<span class="label">权重：</span>
				<input type="text" name="Admin[user_rank]" id="Admin[user_rank]" value="<?php echo $user['user_rank']; ?>" class="text-input small-input" />
				<span class="input-notification png_bg"></span>
				<br />
				<small>权重越小，显示越靠前</small></p>
            <p>
				<span class="label">状态：</span>
				<input type="radio" id="Admin_status_4" name="Admin[status]" value="4"<?php echo $user['status'] == Admin::STAT_STATUS_NORMAL ? ' checked="true"' : ''; ?><?php echo $user['is_system'] || $user['user_id'] == Yii::app()->user->id ? ' disabled="true"' : ''; ?> />
				<label for="Admin_status_4">启用</label>
				<input type="radio" id="Admin_status_1" name="Admin[status]" value="1"<?php echo $user['status'] <> Admin::STAT_STATUS_NORMAL ? ' checked="true"' : ''; ?><?php echo $user['is_system'] || $user['user_id'] == Yii::app()->user->id ? ' disabled="true"' : ''; ?> />
				<label for="Admin_status_1">禁用</label></p>
            <p>
              <input class="button" type="submit" value="保存" />
            </p>
            </fieldset>
            <div class="clear"></div>
          </form>
        </div>
      </div>
    </div>
	<?php
		$this->endWidget();
	?>
	<script type="text/javascript">
	<!--
		$('.purviews .subject input').click(function(){
			var sEl = $(this).parent().next(':first').find('input[type=checkbox]:visible'); 
			if($(this).attr('checked')) {
				$(sEl).attr('checked', true);
			} else {
				$(sEl).removeAttr('checked');
			}
		});
		$('.purviews .subbox dt input').click(function(){
			var pEl = $(this).parent().parent().parent().prev().find('input[type=checkbox]:visible');
			var sEl = $(this).parent().parent().find('input[type=checkbox]:visible'); 
			if($(this).attr('checked')) {
				pEl.attr('checked', true);
				sEl.attr('checked', true);
			} else {
				//pEl.removeAttr('checked');
				sEl.removeAttr('checked');
			}
		});
		$('.purviews .subbox dd input').click(function(){
			if($(this).attr('checked')) {
				$(this).parents('.subbox').prev().find('input[type=checkbox]:visible').attr('checked', true);
				$(this).parents('.row').find('dt input[type=checkbox]:visible').attr('checked', true);
			}
		});
	//-->
	</script>