<?php
$this->breadcrumbs=array(
	'UserLogs Manager',
	'UserLogs Index',
);

$this->menu=array(
	array('label'=>'UserLogs Manager'),
	array('label'=>'UserLogs Index', 'cur'=>true, 'url'=>url($this->module->id . '/Tools/UserLogs/Index')),
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
	<form name="cpform" method="post" autocomplete="off" action="admin.php?action=announce" id="cpform">
	<table class="tb tb2 ">
		<tr class="header">
			<th><?php echo Yii::t('admincp', 'UserLogs Id'); ?></th>
			<th><?php echo Yii::t('admincp', 'UserLogs Type'); ?></th>
			<th><?php echo Yii::t('admincp', 'UserLogs ItemID'); ?></th>
			<th><?php echo Yii::t('admincp', 'UserLogs Action'); ?></th>
			<th><?php echo Yii::t('admincp', 'UserLogs Result'); ?></th>
			<th><?php echo Yii::t('admincp', 'UserLogs Message'); ?></th>
			<th><?php echo Yii::t('admincp', 'UserLogs UserIP'); ?></th>
			<th><?php echo Yii::t('admincp', 'UserLogs AddTime'); ?></th>
			<!-- <th><?php echo Yii::t('admincp', 'Operate'); ?></th> -->
		</tr>
		<?php
			foreach($data['rows'] as $row) {
		?>
		<tr class="hover">
			<td><?php echo $row['LID']; ?></td>
			<td><?php echo $row['LType']; ?></td>
			<td><?php echo $row['LItemID']; ?></td>
			<td><?php echo $row['LAction']; ?></td>
			<td><?php echo $row['LResult']; ?></td>
			<td><?php echo $row['LMessage']; ?></td>
			<td><?php echo long2ip($row['LUserIP']); ?></td>
			<td><?php echo date('Y-m-d H:i:s', $row['LAddTime']); ?></td>
			<!-- <td>
				&nbsp;
				<a href="<?php echo url($this->module->id . '/Tools/UserLogs/View/' . $row['LID']);?>">查看</a>
				<a href="<?php echo url($this->module->id . '/Tools/UserLogs/Delete/' . $row['LID']);?>" onclick="return confirm('确定要删除该记录吗');">删除</a>
			</td> -->
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
	</form>