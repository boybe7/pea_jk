<?php
$this->breadcrumbs=array(
	'Vendor Contracts'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List VendorContract','url'=>array('index')),
	array('label'=>'Create VendorContract','url'=>array('create')),
	array('label'=>'Update VendorContract','url'=>array('update','id'=>$model->id)),
	array('label'=>'Delete VendorContract','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage VendorContract','url'=>array('admin')),
);
?>

<h1>View VendorContract #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'contract_no',
		'approve_date',
		'percent_pay',
		'percent_adv',
		'budget',
		'place',
		'vendor_id',
		'proj_id',
		'updated_by',
		'lock_boq',
	),
)); ?>
