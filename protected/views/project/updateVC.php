<?php
$this->breadcrumbs=array(
	'Project'=>array('index'),
	'ข้อมูลสัญญา',
);
?>
<?php 
	
	  echo $this->renderPartial('_formUpdateVC', array('model'=>$model)); 

?>