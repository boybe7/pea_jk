<?php
$this->breadcrumbs=array(
	'User'=>array('index'),
	'Create',
);


?>

<center><h3>เพิ่มผู้ใช้งานระบบ</h3></center>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>