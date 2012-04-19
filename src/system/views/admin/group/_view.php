<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('RID')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->RID), array('view', 'id'=>$data->RID)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('RName')); ?>:</b>
	<?php echo CHtml::encode($data->RName); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('RPIDs')); ?>:</b>
	<?php echo CHtml::encode($data->RPIDs); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('RShow')); ?>:</b>
	<?php echo CHtml::encode($data->RShow); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('RState')); ?>:</b>
	<?php echo CHtml::encode($data->RState); ?>
	<br />


</div>