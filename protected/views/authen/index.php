<?php
$this->breadcrumbs=array(
	'สิทธิการเข้าถึงข้อมูล',
	
);
?>

<h3>สิทธิการเข้าถึงข้อมูล</h3>

<?php 

$this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'link',
    
    'type'=>'success',
    'label'=>'เพิ่ม/แก้ไขสิทธิ',
    'icon'=>'plus-sign',
    'url'=>array('update'),
    'htmlOptions'=>array('class'=>'pull-right','style'=>'margin:0px 10px 0px 10px;'),
)); 



$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'user-group-grid',
	'dataProvider'=>$model->search(),
	'htmlOptions'=>array('style'=>'padding-top:40px'),
	'type'=>'bordered condensed',
	'summaryText'=>'แสดงผล {start} ถึง {end} จากทั้งหมด {count} ข้อมูล',
    'template'=>"{items}<div class='row-fluid'><div class='span6'>{pager}</div><div class='span6'>{summary}</div></div>",
	
	'columns'=>array(
		'group_name',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'headerHtmlOptions' => array('style' => 'width:5%;text-align:center;background-color: #f5f5f5'),
			'template' => ' {delete}',
	
		),
	),
)); ?>
