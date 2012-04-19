<?php
$this->breadcrumbs=array(
	'Lottery Manager',
	'User Manager',
	'User Invite Index',
);

$this->menu=array(
	array('label'=>'User Manager'),
	array('label'=>'User Index', 'url'=>url($this->module->id . '/Lottery/User/'.($special_id ? $special_id .'/':'').'Index')),
	array('label'=>'User Invite Index', 'cur'=>true, 'url'=>url($this->module->id . '/Lottery/User/'.($special_id ? $special_id .'/':'').$user_id.'/Invite')),
	array('label'=>'User Prize Index', 'url'=>url($this->module->id . '/Lottery/User/'.($special_id ? $special_id .'/':'').$user_id.'/Prize')),
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
	<table class="tb tb2 ">
		<tr class="header">
			<th><?php echo Yii::t('admincp', 'Invite ID'); ?></th>
			<th><?php echo Yii::t('admincp', 'Invite User ID'); ?></th>
			<th><?php echo Yii::t('admincp', 'Invite User Name'); ?></th>
			<th><?php echo Yii::t('admincp', 'Invite User Nick'); ?></th>
			<th><?php echo Yii::t('admincp', 'Invite User Ip'); ?></th>
			<th><?php echo Yii::t('admincp', 'Invite User Time'); ?></th>
		</tr>
		<?php
			foreach($data['rows'] as $row) {
		?>
		<tr class="hover">
			<td><?php echo $row['invite_id']; ?></td>
			<td><?php echo $row['invite_user_id']; ?></td>
			<td><?php echo $row['invite_user_name']; ?></td>
			<td><?php echo $row['invite_user_nick']; ?></td>
			<td><?php echo long2ip($row['invite_user_ip']); ?></td>
			<td><?php echo date('Y-m-d H:i:s', $row['invite_dateline']); ?></td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td colspan="20">
			<div class="cuspages right">
				<?php
					$this->widget('CPager',array(
							'pages'=>$data['pages'],
						)
					);
				?>
			</div>
			<div class="fixsel"></div>
			</td>
		</tr>
	</table>
