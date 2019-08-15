<?php
$this->breadcrumbs=array(
	'Vendor Contracts',
);

$this->menu=array(
	array('label'=>'Create VendorContract','url'=>array('create')),
	array('label'=>'Manage VendorContract','url'=>array('admin')),
);
?>

<h1>Vendor Contracts</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
