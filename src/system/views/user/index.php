<?php
$this->breadcrumbs=array(
	'Lottery Manager',
	'User Manager',
	'User Index',
);

$this->menu=array(
	array('label'=>'User Manager'),
	array('label'=>'User Index', 'cur'=>true, 'url'=>url($this->module->id . '/Lottery/User/'.($special_id ? $special_id .'/':'').'Index')),
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
	<form action="" method="get">
		<?php
			$select_specials = array(
				0 => '不限制',
			);
			foreach($specials['rows'] as $special) {
				$select_specials[$special['special_id']] = $special['special_name'];
			}
		?>
		<div style="height: 30px; line-height: 30px;">活动专题：<?php echo CHtml::dropDownList('special_id', $special_id, $select_specials)?>&nbsp;用户编号：<input type="text" class="txt" id="user_id" name="user_id" value="<?php echo $user_id; ?>" />&nbsp;用户名：<input type="text" class="txt" id="user_name" name="user_name" value="<?php echo $user_name; ?>" />&nbsp;昵称：<input type="text" class="txt" id="user_nick" name="user_nick" value="<?php echo $user_nick; ?>" /> <input type="submit" onclick="return srchforum()" value="查询" class="btn"></div>
	</form>
	<table class="tb tb2 ">
		<tr class="header">
			<th><?php echo Yii::t('admincp', 'User ID'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Name'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Nick'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Play Time'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Remain Time'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Invite Number'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Last Login Time'); ?></th>
			<th><?php echo Yii::t('admincp', 'User Last Login Ip'); ?></th>
			<!-- <th><?php echo Yii::t('admincp', 'User Create Time'); ?></th> -->
			<th><?php echo Yii::t('admincp', 'Operate'); ?></th>
		</tr>
		<?php
			foreach($data['rows'] as $row) {
		?>
		<tr class="hover">
			<td><?php echo $row['user_id']; ?></td>
			<td><?php echo $row['user_name']; ?></td>
			<td><?php echo $row['user_nick']; ?></td>
			<td><?php echo $row['user_play_times']; ?></td>
			<td><?php echo $row['user_remain_times']; ?></td>
			<td><?php echo $row['user_invite_number']; ?></td>
			<td><?php echo date('Y-m-d H:i:s', $row['user_lasttime']); ?></td>
			<td><?php echo long2ip($row['user_lastip']); ?></td>
			<!-- <td><?php echo date('Y-m-d H:i:s', $row['user_dateline']); ?></td> -->
			<td>
				<a href="<?php echo url($this->module->id . '/Lottery/User/'.($special_id ? $special_id .'/':'').$row['user_id'].'/Invite'); ?>">邀请列表</a>
				<a href="<?php echo url($this->module->id . '/Lottery/User/'.($special_id ? $special_id .'/':'').$row['user_id'].'/Prize'); ?>">中奖查询</a>
			</td>
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
