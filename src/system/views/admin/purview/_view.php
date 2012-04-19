<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('PID')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->PID), array('view', 'id'=>$data->PID)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('PName')); ?>:</b>
	<?php echo CHtml::encode($data->PName); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('PIdentify')); ?>:</b>
	<?php echo CHtml::encode($data->PIdentify); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('PState')); ?>:</b>
	<?php echo CHtml::encode($data->PState); ?>
	<br />


</div>