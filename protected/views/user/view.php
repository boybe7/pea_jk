<?php
$this->breadcrumbs=array(
	'Staff'=>array('index'),
	$model->title.$model->firstname."  ".$model->lastname,
);

$this->menu=array(
	array('label'=>'List Staff','url'=>array('index')),
	array('label'=>'Create Staff','url'=>array('create')),
	array('label'=>'Update Staff','url'=>array('update','id'=>$model->staff_id)),
	array('label'=>'Delete Staff','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->staff_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Staff','url'=>array('admin')),
);
?>

<h3>ข้อมูลผู้ใช้งานระบบ</h3>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'staff_id',
		'title',
		'firstname',
		'lastname',
		'phone',
		'type_id',
		'username',
		'password',
	),
)); ?>
