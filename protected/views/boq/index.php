<?php
$this->breadcrumbs=array(
	'Boqs',
);

$this->menu=array(
	array('label'=>'Create Boq','url'=>array('create')),
	array('label'=>'Manage Boq','url'=>array('admin')),
);
?>

<h1>Boqs</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
