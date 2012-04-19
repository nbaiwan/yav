<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'role-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?php echo Yii::t('admincp', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'RName'); ?>
		<?php echo $form->textField($model,'RName',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'RName'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'RPIDs'); ?>
		<?php //echo $form->textArea($model,'RPIDs',array('rows'=>6, 'cols'=>50)); ?>
		<div class="purviewBox">
			<?php
				foreach($purviewList as $k=>$v) {
					if($v['Deepth']==1) {
						echo "<div class=\"subject\"><label for=\"RPIDs_{$v['PID']}\">{$v['PName']}" . CHtml::checkBox('Role[RPIDs][]', in_array($v['PID'], $model['RPIDs']), array('id'=>'RPIDs_'.$v['PID'], 'value'=>$v['PID'])) . "</label></div>\r\n<div class=\"subbox\">";
						if(!isset($purviewList[$k+1]) || $purviewList[$k+1]['Deepth']==1) {
							echo "</div>\r\n";
						}
					} else if($v['Deepth']==2) {
						echo "<dl class=\"row\">\r\n\t<dt><label for=\"RPIDs_{$v['PID']}\">{$v['PName']}" . CHtml::checkBox('Role[RPIDs][]', in_array($v['PID'], $model['RPIDs']), array('id'=>'RPIDs_'.$v['PID'], 'value'=>$v['PID'])) . "</label></dt>\r\n";
						if(!isset($purviewList[$k+1]) || $purviewList[$k+1]['Deepth']<$v['Deepth']) {
							echo "</dl>\r\n</div>\r\n";
						} else if($purviewList[$k+1]['Deepth']==$v['Deepth']) {
							echo "</dl>\r\n";
						}
					} else {
						echo "\t<dd><label for=\"RPIDs_{$v['PID']}\">{$v['PName']}" . CHtml::checkBox('Role[RPIDs][]', in_array($v['PID'], $model['RPIDs']), array('id'=>'RPIDs_'.$v['PID'], 'value'=>$v['PID'])) . "</label></dd>\r\n";
						if(!isset($purviewList[$k+1])) {
							echo "\t</dl>\r\n</div>\r\n";
						} else if($purviewList[$k+1]['Deepth']==2) {
							echo "</dl>\r\n";
						} else if($purviewList[$k+1]['Deepth']==1) {
							echo "\t</dl>\r\n</div>\r\n";
						}
					}
				}
			?>
		</div>
		<script type="text/javascript">
			$('.row .subject input').click(function(){
				var checked = $(this).attr('checked');
				$(this).parent().parent().next(':first').find('input[type=checkbox]:visible').attr('checked', checked);
			});
			$('.row .subbox dt input').click(function(){
				var checked = $(this).attr('checked');
				$(this).parent().parent().parent().find('input[type=checkbox]:visible').attr('checked', checked);
			});
		</script>
		<?php echo $form->error($model,'RPIDs'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'RShow'); ?>
		<?php echo $form->dropDownList($model,'RShow', array(
					1=>Yii::t('admincp', 'Show'),
					0=>Yii::t('admincp', 'Hidden'),
				)
			);
		?>
		<?php echo $form->error($model,'RShow'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'RState'); ?>
		<?php echo $form->dropDownList($model,'RState', array(
					1=>Yii::t('admincp', 'Normal'),
					0=>Yii::t('admincp', 'Deleted'),
					-1=>Yii::t('admincp', 'Lock'),
				)
			);
		?>
		<?php echo $form->error($model,'RState'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->