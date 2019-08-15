<?php
$this->breadcrumbs=array(
	'Boqs'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Boq','url'=>array('index')),
	array('label'=>'Create Boq','url'=>array('create')),
	array('label'=>'View Boq','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage Boq','url'=>array('admin')),
);
?>

<h1>Update Boq <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>