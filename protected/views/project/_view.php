<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('contract_no')); ?>:</b>
	<?php echo CHtml::encode($data->contract_no); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('approve_date')); ?>:</b>
	<?php echo CHtml::encode($data->approve_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('percent_pay')); ?>:</b>
	<?php echo CHtml::encode($data->percent_pay); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('percent_adv')); ?>:</b>
	<?php echo CHtml::encode($data->percent_adv); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('budget')); ?>:</b>
	<?php echo CHtml::encode($data->budget); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('place')); ?>:</b>
	<?php echo CHtml::encode($data->place); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('vendor')); ?>:</b>
	<?php echo CHtml::encode($data->vendor); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('updated_by')); ?>:</b>
	<?php echo CHtml::encode($data->updated_by); ?>
	<br />

	*/ ?>

</div>