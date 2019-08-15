<?php
$this->breadcrumbs=array(
	'Payment Details'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List PaymentDetail','url'=>array('index')),
	array('label'=>'Create PaymentDetail','url'=>array('create')),
	array('label'=>'Update PaymentDetail','url'=>array('update','id'=>$model->id)),
	array('label'=>'Delete PaymentDetail','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage PaymentDetail','url'=>array('admin')),
);
?>

<h1>View PaymentDetail #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'proj_id',
		'form_type',
		'pay_no',
		'date_create',
	),
)); ?>
