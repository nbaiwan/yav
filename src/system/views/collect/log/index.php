<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/datepicker/WdatePicker.js"></script>
<?php
$this->breadcrumbs=array(
	'采集管理',
	'采集日志',
);

?>
	<table class="tb tb2 " id="tips">
		<tr>
			<th class="partition">技巧提示</th>
		</tr>
		<tr>
			<td class="tipsblock">
			<ul id="tipslis">
				<li><!--版主用户名为粗体，则表示该版主权限可继承到下级版块--></li>
			</ul>
			</td>
		</tr>
	</table>
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'collect-log-form',
			'enableAjaxValidation'=>false,
			'method' =>"get",
			'action'=>'/SysManager/Collect/Log/Index/'
		));
	?>
        关键字：
	  <input type="text" id="msg" name="msg" class="txt" value="<?php echo $_GET['msg']; ?>" />
	  
	  日期：
	  <input type="text" id="date" name="date" class="txt" value="<?php echo $_GET['date']; ?>" onFocus="WdatePicker()" />
          <input type="submit" value=" 搜 索 " />
	<table class="tb tb2 ">
		<tr class="header">
			<th width="30"><?php echo Yii::t('admincp', '编号'); ?></th>
                        <th><?php echo Yii::t('admincp', '信息'); ?></th>
		</tr>
		<?php
			foreach($log_arr['rows'] as $v) {
		?>
		<tr class="hover">
                	<td><?php echo $v['collect_log_id'];?></td>
                        <td><?php echo $v['collect_log_msg']; ?></td>
		</tr>
		<?php
			}
		?>
                <tr>
			<td colspan="20">
			<div class="cuspages right">
				<?php
					$this->widget('CPager',array(
							'pages'=>$log_arr['pages'],
						)
					);
				?>
			</div>
			<div class="fixsel"></div>
			</td>
		</tr>
	</table>
	<?php
		$this->endWidget();
	?>
	