<?php
$this->breadcrumbs=array(
	'Project'=>array('index'),
	'',
);
?>
<h4>เพิ่มข้อมูลสัญญาผู้รับจ้าง</h4>
<?php 
	
	  echo $this->renderPartial('_formVC', array('model'=>$model)); 

?>