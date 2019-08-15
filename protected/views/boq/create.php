<?php
$this->breadcrumbs=array(
	'Boqs'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Boq','url'=>array('index')),
	array('label'=>'Manage Boq','url'=>array('admin')),
);
?>

<h1>Create Boq</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>