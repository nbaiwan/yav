<div class="wide form">

<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'purview-form',
		'enableAjaxValidation'=>false,
	));
?>

	<p class="note"><?php echo Yii::t('admincp', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'PName'); ?>
		<?php echo $form->textField($model,'PName',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'PName'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'PIdentify'); ?>
		<?php echo $form->textField($model,'PIdentify',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'PIdentify'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'PState'); ?>
		<?php echo $form->textField($model,'PState'); ?>
		<?php echo $form->error($model,'PState'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php
	$this->endWidget();
?>

</div><!-- form -->