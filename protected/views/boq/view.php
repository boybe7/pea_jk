<?php
$this->breadcrumbs=array(
	'Boqs'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Boq','url'=>array('index')),
	array('label'=>'Create Boq','url'=>array('create')),
	array('label'=>'Update Boq','url'=>array('update','id'=>$model->id)),
	array('label'=>'Delete Boq','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Boq','url'=>array('admin')),
);
?>

<h1>View Boq #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'proj_id',
		'detail',
		'no',
		'amount',
		'unit',
		'order_no',
		'price_trans',
		'price_item',
		'price_install',
		'last_update',
	),
)); ?>
