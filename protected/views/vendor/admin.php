<?php
$this->breadcrumbs=array(
	'Vendors'=>array('index'),
	'Manage',
);

?>

<h4>ข้อมูลผู้ว่าจ้าง/ผู้รับจ้าง</h4>

<?php 

$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'vendor-grid',
	'type'=>'bordered condensed',
	'enablePagination' => true,
	'summaryText'=>'',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		
		'v_name'=>array(
			    //'header'=>'v_name', 
				'class' => 'editable.EditableColumn',
				'name' => 'v_name',
				'headerHtmlOptions' => array('style' => 'width:80%;text-align:center;background-color: #f5f5f5'),  	
				'htmlOptions'=>array(
	  	            	  			'style'=>'text-align:left'

	  	        ),

				'editable' => array( //editable section
					'title'=>'แก้ไข ชื่อบริษัท',
					'url' => $this->createUrl('vendor/updateVendor'),
					'success' => 'js: function(response, newValue) {
										if(!response.success) return response.msg;

										$("#vendor-grid").yiiGridView("update",{});
									}',
					'options' => array(
						'ajaxOptions' => array('dataType' => 'json'),

					), 
					'placement' => 'right',
					'display' => 'js: function() {
					
					    
					}'
				)
		),
		'type'=>array(
			'name' => 'type',
			'headerHtmlOptions' => array('style' => 'width:80%;text-align:center;background-color: #f5f5f5'),  
			'htmlOptions'=>array(
	  	            	  			'style'=>'text-align:center'

	  	        ),
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'headerHtmlOptions' => array('style' => 'width:10%;text-align:center;background-color: #f5f5f5'), 
			'template' => '{delete}'
		),
	),
)); 


?>
