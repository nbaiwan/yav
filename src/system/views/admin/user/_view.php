<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('AID')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->AID), array('view', 'id'=>$data->AID)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('AUserName')); ?>:</b>
	<?php echo CHtml::encode($data->AUserName); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('AEmail')); ?>:</b>
	<?php echo CHtml::encode($data->AEmail); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('AUserPwd')); ?>:</b>
	<?php echo CHtml::encode($data->AUserPwd); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ASalt')); ?>:</b>
	<?php echo CHtml::encode($data->ASalt); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ARID')); ?>:</b>
	<?php echo CHtml::encode($data->ARID); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('APIDs')); ?>:</b>
	<?php echo CHtml::encode($data->APIDs); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ALastTime')); ?>:</b>
	<?php echo CHtml::encode($data->ALastTime); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('ALastIp')); ?>:</b>
	<?php echo CHtml::encode($data->ALastIp); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ATimes')); ?>:</b>
	<?php echo CHtml::encode($data->ATimes); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('AState')); ?>:</b>
	<?php echo CHtml::encode($data->AState); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('AIsDel')); ?>:</b>
	<?php echo CHtml::encode($data->AIsDel); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('AAddTime')); ?>:</b>
	<?php echo CHtml::encode($data->AAddTime); ?>
	<br />

	*/ ?>

</div>