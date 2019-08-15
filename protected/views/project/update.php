<?php
$this->breadcrumbs=array(
	'Project'=>array('index'),
	'แก้ไขข้อมูลโครงการ',
);
?>
<?php 
	
	  echo $this->renderPartial('_form', array('model'=>$model)); 

?>