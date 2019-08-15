<style type="text/css">
	.icon-x3{
	    -webkit-transform:scale(1.2);
	    -moz-transform:scale(1.2);
	    -o-transform:scale(1.2);
	}
	
</style>

<div class="navbar">
	<div class="navbar-inner navbar-header">
		<div class="container">
			<a class="brand pull-left" href="#" >ข้อมูลรายละเอียดงานเพื่อขออนุมัติเบิกจ่าย</a>
			
			<form class="navbar-form pull-right">

			  <!-- <input type="text" class="span2"> -->
			  <!-- <button type="submit" class="btn">Submit</button> -->
			  <?php
				//if(Yii::app()->user->getAccess(Yii::app()->request->url))
				//{
				   $this->widget('bootstrap.widgets.TbButton', array(
				    'buttonType'=>'link',
				    
				    'type'=>'success',
				    'label'=>'เพิ่มข้อมูล',
				    'icon'=>'plus-sign',
				    'url'=>array('create'),
				    'htmlOptions'=>array('class'=>'pull-right','style'=>'margin-left:10px'),
					)); 
				//}
			?>

			</form>
		</div>	
	</div>	
</div>
<?php



if(Yii::app()->user->getAccess(Yii::app()->request->url))
{

	
}
else
{

	$this->widget('bootstrap.widgets.TbGridView',array(
		'id'=>'payment-detail-grid',
		'type'=>'bordered condensed',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'selectableRows' =>2,
		'htmlOptions'=>array('style'=>'padding-top:10px'),
	    'enablePagination' => true,
	    'summaryText'=>'แสดงผล {start} ถึง {end} จากทั้งหมด {count} ข้อมูล',
	    'template'=>"{items}<div class='row-fluid'><div class='span6'>{pager}</div><div class='span6'>{summary}</div></div>",
		'columns'=>array(
			
	        'proj_id'=>array(
				    'name' => 'proj_id',
				    'value' => 'Project::model()->findByPk($data->proj_id)->name',
				    'filter'=>CHtml::activeTextField($model, 'proj_id',array("placeholder"=>"ค้นหาตาม".$model->getAttributeLabel("proj_id"))),
					'headerHtmlOptions' => array('style' => 'width:30%;text-align:center;background-color: #f5f5f5'),  	            	  	
					'htmlOptions'=>array('style'=>'text-align:center')
		  	),
		  	'vendor'=>array(
				    'header' => '<a class="sort-link">ผู้รับจ้าง</a>',
				    'value' => 'Project::model()->findByPk($data->proj_id)->vendor',
				    
					'headerHtmlOptions' => array('style' => 'width:20%;text-align:center;background-color: #f5f5f5'),  	            	  	
					'htmlOptions'=>array('style'=>'text-align:center')
		  	),			
		  	'budget'=>array(
				    'header' => '<a class="sort-link">วงเงินสัญญา <br>(บาท)</a>',
				    'headerHtmlOptions'=>array(),
				    'value' => 'number_format(Project::model()->findByPk($data->proj_id)->budget,2)',
					'headerHtmlOptions' => array('style' => 'width:12%;text-align:center;background-color: #f5f5f5'),  	            	  
					'htmlOptions'=>array('style'=>'text-align:center')
		  	),
		  	'pay_amount'=>array(
				    'header' => '<a class="sort-link">วงเงินรวมเบิกจ่าย (บาท)</a>',
				    'headerHtmlOptions'=>array(),
				    'value'=>'$data->getPayAmount($data->proj_id,$data->pay_no)',
					'headerHtmlOptions' => array('style' => 'width:12%;text-align:center;background-color: #f5f5f5'),  	     

					'htmlOptions'=>array('style'=>'text-align:center')
		  	),
		  	'remain_amount'=>array(
				    'header' => '<a class="sort-link">วงเงินคงเหลือ <br>(บาท)</a>',
				    'headerHtmlOptions'=>array(),
				    'value'=>'$data->getRemainAmount($data->proj_id,$data->pay_no)',
					'headerHtmlOptions' => array('style' => 'width:12%;text-align:center;background-color: #f5f5f5'),  	     

					'htmlOptions'=>array('style'=>'text-align:center')
		  	),
		  	'pay_no'=>array(
				    'name' => 'pay_no',
				    'headerHtmlOptions'=>array(),
					'headerHtmlOptions' => array('style' => 'width:8%;text-align:center;background-color: #f5f5f5'),  	     

					'htmlOptions'=>array('style'=>'text-align:center')
		  	),
			array(
				'class'=>'bootstrap.widgets.TbButtonColumn',
				'headerHtmlOptions' => array('style' => 'width:8%;text-align:center;background-color: #f5f5f5'),
				'template' => '{view}&nbsp;&nbsp;&nbsp;{print}',
				'buttons'=>array(
					'view'=>array(
                      //'url'=>'Yii::app()->createUrl("printJK", array("id"=>$data->id))', 
                      'icon' => 'icon-eye-open icon-x3',

                    ),
                    'print'=>array(
                      'url'=>'Yii::app()->createUrl("printJK", array("id"=>$data->id))', 
                      'icon' => 'icon-print icon-x3',

                    )
                )
		
			),
		),
		)
	);
}

?>