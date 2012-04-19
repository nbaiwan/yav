<?php
$this->breadcrumbs=array(
	'System Manager',
	'Admin Manager',
);

$this->menu=array(
	array('label'=>'Admin Index', 'cur'=>true, 'url'=>url($this->module->id . '/Admin/Admin/Index')),
	array('label'=>'Create Role', 'url'=>url($this->module->id . '/Admin/Role/Create')),
	array('label'=>'Create Admin', 'url'=>url($this->module->id . '/Admin/Admin/Create')),
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
        <div class="tab-content default-tab" id="tab1">
          <table>
            <thead>
              <tr>
                <th>
                  <input class="check-all" type="checkbox" />
                </th>
				<th><?php echo Yii::t('admincp', '用户名'); ?></th>
				<th><?php echo Yii::t('admincp', '姓名'); ?></th>
				<th><?php echo Yii::t('admincp', '用户组'); ?></th>
				<th><?php echo Yii::t('admincp', '邮箱'); ?></th>
				<th><?php echo Yii::t('admincp', '最后登录时间'); ?></th>
				<th><?php echo Yii::t('admincp', '最后登录IP'); ?></th>
				<th><?php echo Yii::t('admincp', '创建时间'); ?></th>
				<th><?php echo Yii::t('admincp', '状态'); ?></th>
				<th><?php echo Yii::t('admincp', '操作'); ?></th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <td colspan="20">
				  <div class="pagination">
				  <?php
					$this->widget('CPager',array(
							'pages'=>$data['pages'],
						)
					);
				  ?>
				  </div>
                  <!-- End .pagination -->
                  <div class="clear"></div>
                </td>
              </tr>
            </tfoot>
            <tbody>
				<?php
					foreach($data['rows'] as $row) {
				?>
				<tr class="hover">
					<td><?php echo $row['user_id']; ?></td>
					<td><?php echo $row['user_name']; ?></td>
					<td><?php echo $row['realname']; ?></td>
					<td><?php echo $row['role_name']; ?></td>
					<td><?php echo $row['email']; ?></td>
					<td><?php echo date('Y-m-d H:i', $row['lastvisit']); ?></td>
					<td><?php echo long2ip($row['lastip']); ?></td>
					<td><?php echo date('Y-m-d H:i', $row['lasttime']); ?></td>
					<!-- <td><?php echo date('Y-m-d H:i', $row['dateline']); ?></td> -->
					<td><?php echo $row['status'] == Admin::STAT_STATUS_NORMAL ? '启用' : '<span style="color:#f00;">禁用</span>'; ?></td>
					<td>
						&nbsp;
						<?php
							if($this->checkAccess('Admin/Admin/Modify')) {
						?>
						<a href="<?php echo url($this->module->id . "/Admin/Admin/{$row['user_id']}/Update");?>" title="修改"><img src="<?php echo $this->module->assetsUrl; ?>/images/icons/pencil.png" alt="修改" /></a>
						<?php
							} else {
								echo "<img src=\"{$this->module->assetsUrl}/images/icons/pencil.png\" alt=\"修改\" />";
							}
							
							if($this->checkAccess('Admin/Admin/Delete')) {
						?>
						<a href="<?php echo url($this->module->id . "/Admin/Admin/{$row['user_id']}/Delete");?>" onclick="return confirm('确定要删除该记录吗');"><img src="<?php echo $this->module->assetsUrl; ?>/images/icons/cross.png" alt="删除" /></a>
						<?php
							} else {
								echo "&nbsp;<img src=\"{$this->module->assetsUrl}/images/icons/cross.png\" alt=\"删除\" />";
							}
						?>
					</td>
				</tr>
				<?php
					}
				?>
            </tbody>
          </table>
        </div>
      </div>
    </div>