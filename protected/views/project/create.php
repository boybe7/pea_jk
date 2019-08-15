<?php
$this->breadcrumbs=array(
	'Project'=>array('index'),
	'เพิ่มข้อมูลโครงการ',
);
?>
<?php 
	
	  echo $this->renderPartial('_form', array('model'=>$model)); 

?>