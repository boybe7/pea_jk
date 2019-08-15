<?php
$this->breadcrumbs=array(
	'Vendor Contracts'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List VendorContract','url'=>array('index')),
	array('label'=>'Create VendorContract','url'=>array('create')),
	array('label'=>'View VendorContract','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage VendorContract','url'=>array('admin')),
);
?>

<h1>Update VendorContract <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>