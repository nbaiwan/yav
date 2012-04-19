<?php
$this->breadcrumbs=array(
	'Lottery Manager',
	'User Manager',
	'User Prize Index',
);

$this->menu=array(
	array('label'=>'User Manager'),
	array('label'=>'User Index', 'url'=>url($this->module->id . '/Lottery/User/'.($special_id ? $special_id .'/':'').'Index')),
	array('label'=>'User Invite Index', 'url'=>url($this->module->id . '/Lottery/User/'.($special_id ? $special_id .'/':'').$user_id.'/Invite')),
	array('label'=>'User Prize Index', 'cur'=>true, 'url'=>url($this->module->id . '/Lottery/User/'.($special_id ? $special_id .'/':'').$user_id.'/Prize')),
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
			<th><?php echo Yii::t('admincp', 'User ID'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Name'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Nick'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Order Id'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Order Serial Number'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Prize Name'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Order Ip'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Order Time'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Order Status'); ?></th>
		</tr>
		<?php
			foreach($data['rows'] as $row) {
		?>
		<tr class="hover">
			<td><?php echo $row['user_id']; ?></td>
			<td><?php echo $row['user_name']; ?></td>
			<td><?php echo $row['user_nick']; ?></td>
			<td><?php echo $row['order_id']; ?></td>
			<td><?php echo $row['order_serial_number']; ?></td>
			<td><?php echo $row['prize_name']; ?></td>
			<td><?php echo long2ip($row['order_user_ip']); ?></td>
			<td><?php echo date('Y-m-d H:i:s', $row['order_dateline']); ?></td>
			<td><?php echo Order::get_order_status($row['order_status']); ?></td>
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
