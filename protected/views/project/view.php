<?php
$this->breadcrumbs=array(
	'Project'=>array('index'),
	'ข้อมูลโครงการ',
);
?>
<?php 
	
	  echo $this->renderPartial('_formUpdate', array('model'=>$model)); 

?>