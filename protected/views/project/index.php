<script type="text/javascript">
	$(function(){
		 $(document).on('click','.confirmation', function(event){
		      event.preventDefault();
		       link = $(this).attr("href");
		        bootbox.confirm("<font color=red><h4>ต้องการจะลบข้อมูลโครงการ?</h4></font>", function(result) {
		            if (result) {
		                 //include the href duplication link here?;
		                 //console.log($(this));
		                 window.location.href = link;

		            } else {
		               
		            }
		        });
		    });
});
</script>

<style type="text/css">

tr.filters td{
  background-color: #f5f5f5;
}
</style>

<div class="navbar">
	<div class="navbar-inner navbar-header">
		<div class="container">
			<a class="brand pull-left" href="#" >ข้อมูลโครงการ</a>
			
			<form class="navbar-form pull-right">

			  <!-- <input type="text" class="span2"> -->
			  <!-- <button type="submit" class="btn">Submit</button> -->
			  <?php
				if(Yii::app()->user->getAccess(Yii::app()->request->url))
				{
				   $this->widget('bootstrap.widgets.TbButton', array(
				    'buttonType'=>'link',
				    'type'=>'success',
				    'label'=>'เพิ่มโครงการ',
				    'icon'=>'plus-sign',
				    'url'=>array('create'),
				    'htmlOptions'=>array('class'=>'pull-right','style'=>'margin-left:10px',

						),
					)); 
				}
			?>

			</form>
		</div>	
	</div>	
</div>
<?php


if(Yii::app()->user->getAccess(Yii::app()->request->url))
{


	$this->widget('ext.groupgridview.BootGroupGridView',array(
		'id'=>'project-grid',
		//'type'=>'bordered condensed',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		//'selectableRows' =>2,
		//'extraRowColumns' => array('proj_name'),
        //'extraRowPos' => 'above',
		 'mergeColumns' => array('fiscal_year','proj_name','owner_name','actions'),  
		'htmlOptions'=>array('style'=>'padding-top:10px'),
	    'enablePagination' => true,
	    'summaryText'=>'แสดงผล {start} ถึง {end} จากทั้งหมด {count} ข้อมูล',
	    'template'=>"{items}<div class='row-fluid'><div class='span6'>{pager}</div><div class='span6'>{summary}</div></div>",
		'columns'=>array(
		
	        'fiscal_year'=>array(
				    'name' => 'fiscal_year',
				    //'value'=>'$data->proj_id',
				    'value'=>'Project::model()->findByPk($data->proj_id)->fiscal_year',
				    'filter'=>CHtml::activeTextField($model, 'fiscal_year',array("placeholder"=>"ค้นหาตาม".$model->getAttributeLabel("fiscal_year"))),
					'headerHtmlOptions' => array('style' => 'width:7%;text-align:center;background-color: #f5f5f5'),  	            	  	
					'htmlOptions'=>array('style'=>'text-align:center;vertical-align:top')
		  	),
		  
		  	'owner_name'=>array(
				    'name' => 'owner_name',
				    //'value'=>'$data->proj_id',
				    'value'=>'Project::model()->findByPk($data->proj_id)->owner_name',
				    'filter'=>CHtml::activeTextField($model, 'owner_name',array("placeholder"=>"ค้นหาตาม".$model->getAttributeLabel("owner_name"))),
	
					'headerHtmlOptions' => array('style' => 'width:15%;text-align:center;background-color: #f5f5f5'),  	            	  	
					'htmlOptions'=>array('style'=>'text-align:left')
		  	),
		  		'proj_name'=>array(
				    'name' => 'proj_name',
				    //'value'=>'$data->proj_id',
				    'value'=>'Project::model()->findByPk($data->proj_id)->name',
				    'filter'=>CHtml::activeTextField($model, 'proj_name',array("placeholder"=>"ค้นหาตาม".$model->getAttributeLabel("proj_name"))),
					'headerHtmlOptions' => array('style' => 'width:20%;text-align:center;background-color: #f5f5f5'),  	            	  	
					'htmlOptions'=>array('style'=>'text-align:left')
		  	),
		  	
		  	'vendor_name'=>array(
				    'name' => 'vendor_name',
				    //'value'=>'$data->vendor_id',
				    'value'=>'Vendor::model()->findByPk($data->vendor_id)->v_name',
				    'filter'=>CHtml::activeTextField($model, 'vendor_name',array("placeholder"=>"ค้นหาตาม".$model->getAttributeLabel("vendor_name"))),
					'headerHtmlOptions' => array('style' => 'width:20%;text-align:center;background-color: #f5f5f5'),  	            	  	
					'htmlOptions'=>array('style'=>'text-align:left')
		  	),
		  	'contract_no'=>array(
				    'name' => 'contract_no',
				    
				    'filter'=>CHtml::activeTextField($model, 'contract_no',array("placeholder"=>"ค้นหาตาม".$model->getAttributeLabel("contract_no"))),
					'headerHtmlOptions' => array('style' => 'width:10%;text-align:center;background-color: #f5f5f5'),  	            	  	
					'htmlOptions'=>array('style'=>'text-align:center')
		  	),
		  	'name'=>array(
				    'name' => 'name',
				    
				    'filter'=>CHtml::activeTextField($model, 'name',array("placeholder"=>"ค้นหาตาม".$model->getAttributeLabel("name"))),
					'headerHtmlOptions' => array('style' => 'width:25%;text-align:center;background-color: #f5f5f5'),  	            	  	
					'htmlOptions'=>array('style'=>'text-align:left')
		  	),
		  	array(
				'class'=>'bootstrap.widgets.TbButtonColumn',
				'headerHtmlOptions' => array('style' => 'width:8%;text-align:center;background-color: #f5f5f5'),
				'template' => '{update} {delete}',
				//'deleteConfirmation'=>'',
				'buttons'=>array(
					    'update' => array
                                    (
                                      
                                        'url'=>function($data){

								            return Yii::app()->createUrl('/project/updateVendorContract/',

								                    array('id'=>$data->id) /* <- customise that */

								            );

								        },                                    
                                    ),

                   
                        'delete' => array
                                    (
                                                        
                                        //'url'=>'Yii::app()->createUrl("deleteVendorContract/".$data["id"])',
                                        'url'=>function($data){

								            return Yii::app()->createUrl('/project/deleteVendorContract/',

								                    array('id'=>$data->id) /* <- customise that */

								            );

								        },
                                       
                                    ),

                                )
		
			),
		
			'actions'=>array(
				'name' => 'actions', 
				'filter' => false,
				'type'=>'raw',
				'value'=>'$data->getAction($data->proj_id)',
				'headerHtmlOptions' => array('style' => 'width:8%;text-align:center;background-color: #f5f5f5'),
				'htmlOptions'=>array('style'=>'background-color: #f5f5f5')
		
			),
		),
		)
	);
}
else
{
		$this->widget('ext.groupgridview.BootGroupGridView',array(
		'id'=>'project-grid',
		//'type'=>'bordered condensed',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		//'selectableRows' =>2,
		//'extraRowColumns' => array('proj_name'),
        //'extraRowPos' => 'above',
		 'mergeColumns' => array('fiscal_year','proj_name','owner_name'),  
		'htmlOptions'=>array('style'=>'padding-top:10px'),
	    'enablePagination' => true,
	    'summaryText'=>'แสดงผล {start} ถึง {end} จากทั้งหมด {count} ข้อมูล',
	    'template'=>"{items}<div class='row-fluid'><div class='span6'>{pager}</div><div class='span6'>{summary}</div></div>",
		'columns'=>array(
		
	        'fiscal_year'=>array(
				    'name' => 'fiscal_year',
				    //'value'=>'$data->proj_id',
				    'value'=>'Project::model()->findByPk($data->proj_id)->fiscal_year',
				    'filter'=>CHtml::activeTextField($model, 'fiscal_year',array("placeholder"=>"ค้นหาตาม".$model->getAttributeLabel("fiscal_year"))),
					'headerHtmlOptions' => array('style' => 'width:7%;text-align:center;background-color: #f5f5f5'),  	            	  	
					'htmlOptions'=>array('style'=>'text-align:center;vertical-align:top')
		  	),
		  
		  	'owner_name'=>array(
				    'name' => 'owner_name',
				    //'value'=>'$data->proj_id',
				    'value'=>'Project::model()->findByPk($data->proj_id)->owner_name',
				    'filter'=>CHtml::activeTextField($model, 'owner_name',array("placeholder"=>"ค้นหาตาม".$model->getAttributeLabel("owner_name"))),
	
					'headerHtmlOptions' => array('style' => 'width:15%;text-align:center;background-color: #f5f5f5'),  	            	  	
					'htmlOptions'=>array('style'=>'text-align:left')
		  	),
		  		'proj_name'=>array(
				    'name' => 'proj_name',
				    //'value'=>'$data->proj_id',
				    'value'=>'Project::model()->findByPk($data->proj_id)->name',
				    'filter'=>CHtml::activeTextField($model, 'proj_name',array("placeholder"=>"ค้นหาตาม".$model->getAttributeLabel("proj_name"))),
					'headerHtmlOptions' => array('style' => 'width:20%;text-align:center;background-color: #f5f5f5'),  	            	  	
					'htmlOptions'=>array('style'=>'text-align:left')
		  	),
		  	
		  	'vendor_name'=>array(
				    'name' => 'vendor_name',
				    //'value'=>'$data->vendor_id',
				    'value'=>'Vendor::model()->findByPk($data->vendor_id)->v_name',
				    'filter'=>CHtml::activeTextField($model, 'vendor_name',array("placeholder"=>"ค้นหาตาม".$model->getAttributeLabel("vendor_name"))),
					'headerHtmlOptions' => array('style' => 'width:20%;text-align:center;background-color: #f5f5f5'),  	            	  	
					'htmlOptions'=>array('style'=>'text-align:left')
		  	),
		  	'contract_no'=>array(
				    'name' => 'contract_no',
				    
				    'filter'=>CHtml::activeTextField($model, 'contract_no',array("placeholder"=>"ค้นหาตาม".$model->getAttributeLabel("contract_no"))),
					'headerHtmlOptions' => array('style' => 'width:10%;text-align:center;background-color: #f5f5f5'),  	            	  	
					'htmlOptions'=>array('style'=>'text-align:center')
		  	),
		  	'name'=>array(
				    'name' => 'name',
				    
				    'filter'=>CHtml::activeTextField($model, 'name',array("placeholder"=>"ค้นหาตาม".$model->getAttributeLabel("name"))),
					'headerHtmlOptions' => array('style' => 'width:25%;text-align:center;background-color: #f5f5f5'),  	            	  	
					'htmlOptions'=>array('style'=>'text-align:left')
		  	),
		  	array(
				'class'=>'bootstrap.widgets.TbButtonColumn',
				'headerHtmlOptions' => array('style' => 'width:8%;text-align:center;background-color: #f5f5f5'),
				'template' => '{update}',
				//'deleteConfirmation'=>'',
				'buttons'=>array(
					    'update' => array
                                    (
                                      
                                        'url'=>function($data){

								            return Yii::app()->createUrl('/project/updateVendorContract/',

								                    array('id'=>$data->id) /* <- customise that */

								            );

								        },                                    
                                    ),

                   
                        'delete' => array
                                    (
                                                        
                                        //'url'=>'Yii::app()->createUrl("deleteVendorContract/".$data["id"])',
                                        'url'=>function($data){

								            return Yii::app()->createUrl('/project/deleteVendorContract/',

								                    array('id'=>$data->id) /* <- customise that */

								            );

								        },
                                       
                                    ),

                                )
		
			),
		
			
		),
		)
	);
	
}

?>

<div id="modal-content" class="hide">
    <div id="modal-body">
      <form id='project-form'>
        <h4>เพิ่มข้อมูลโครงการ</h4>
        <label for='proj_name'>ชื่อโครงการ</label>
        <input type="text" name="proj_name" class="span5">
        <div class="row-fluid">
                
              <?php 

              //echo $form->textFieldRow($model,'vendor_id',array('class'=>'span12','maxlength'=>500));
                    echo CHtml::activeHiddenField($model, 'vendor_id'); 
                    echo CHtml::activeLabelEx($model, 'vendor_id'); 

                    $vendor = Yii::app()->db->createCommand()
                        ->select('v_name')
                        ->from('vendor')
                        ->where('v_id=:id', array(':id'=>$model->vendor_id))
                        ->queryAll();
                    
                    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                            'name'=>'vendor_id',
                            'id'=>'vendor_id',
                            'value'=> empty($vendor[0])? '' : $vendor[0]['v_name'],
                           'source'=>'js: function(request, response) {
                                $.ajax({
                                    url: "'.$this->createUrl('Vendor/GetSupplier').'",
                                    dataType: "json",
                                    data: {
                                        term: request.term,
                                       
                                    },
                                    success: function (data) {
                                            response(data);

                                    }
                                })
                             }',
                            'options'=>array(
                                     'showAnim'=>'fold',
                                     'minLength'=>0,
                                     'select'=>'js: function(event, ui) {
                                        
                                           $("#VendorContract_vendor_id").val(ui.item.id);
                                           $("#vendor_name").val(ui.item.label);
                                     }'
                                    
                                     
                            ),
                           'htmlOptions'=>array(

                                'class'=>$model->hasErrors('oc_vendor_id')?'span12 error ':'span12 '
                            ),
                                  
                        ));

               ?>
            </div>
        <label for='fiscal_year'>ปีงบประมาณ</label>
        <input type="text" name="fiscal_year" class="span2">
        <br>	
        <input type="checkbox" name="special" value="1">ประเภทเพิ่ม/ลด
      </form>
      
    </div>
</div>    
