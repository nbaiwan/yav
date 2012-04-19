<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'RID'); ?>
		<?php echo $form->textField($model,'RID',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'RName'); ?>
		<?php echo $form->textField($model,'RName',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'RPIDs'); ?>
		<?php echo $form->textArea($model,'RPIDs',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'RShow'); ?>
		<?php echo $form->textField($model,'RShow'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'RState'); ?>
		<?php echo $form->textField($model,'RState'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->