<?php
$this->breadcrumbs=array(
	'PaymentDetail'=>array('index'),
	'เพิ่มข้อมูลรายละเอียดงานเพื่อขออนุมัติเบิกจ่าย',
);
?>
<?php 
	
	  echo $this->renderPartial('_form', array('model'=>$model,'modelProj'=>$modelProj,'pay_no'=>2)); 

?>