<?php
$this->breadcrumbs=array(
	'Staff'=>array('index'),
	$model->title=>array('view','id'=>$model->staff_id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Staff','url'=>array('index')),
	array('label'=>'Create Staff','url'=>array('create')),
	array('label'=>'View Staff','url'=>array('view','id'=>$model->staff_id)),
	array('label'=>'Manage Staff','url'=>array('admin')),
);
?>

<center><h3>แก้ไขผู้ใช้งานระบบ</h3></center>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>