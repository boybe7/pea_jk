<?php
$this->breadcrumbs=array(
	'Vendor Contracts'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List VendorContract','url'=>array('index')),
	array('label'=>'Manage VendorContract','url'=>array('admin')),
);
?>

<h1>Create VendorContract</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>