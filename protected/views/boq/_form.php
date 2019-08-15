<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'boq-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'proj_id',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'detail',array('class'=>'span5','maxlength'=>500)); ?>

	<?php echo $form->textFieldRow($model,'no',array('class'=>'span5','maxlength'=>5)); ?>

	<?php echo $form->textFieldRow($model,'amount',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'unit',array('class'=>'span5','maxlength'=>100)); ?>

	<?php echo $form->textFieldRow($model,'order_no',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'price_trans',array('class'=>'span5','maxlength'=>20)); ?>

	<?php echo $form->textFieldRow($model,'price_item',array('class'=>'span5','maxlength'=>20)); ?>

	<?php echo $form->textFieldRow($model,'price_install',array('class'=>'span5','maxlength'=>20)); ?>

	<?php echo $form->textFieldRow($model,'last_update',array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
