<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'project-form',
  'htmlOptions'=>array(
      'class'=>'well',
    ),
	'enableAjaxValidation'=>false,
)); ?>

  <h4>ข้อมูลโครงการ</h4>
	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
  <div class="row-fluid">
	 <?php echo $form->textFieldRow($model,'name',array('class'=>'span12','maxlength'=>500)); ?>
  </div> 
	<div class="row-fluid">
             
              <?php 

              //echo $form->textFieldRow($model,'owner_id',array('class'=>'span12','maxlength'=>500));
                    echo CHtml::activeHiddenField($model, 'owner_id'); 
                    echo CHtml::activeLabelEx($model, 'owner_id'); 

                    $vendor = Yii::app()->db->createCommand()
                        ->select('v_name')
                        ->from('vendor')
                        ->where('v_id=:id', array(':id'=>$model->owner_id))
                        ->queryAll();
                    
                    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                            'name'=>'owner_name',
                            'id'=>'owner_name',
                            'value'=> empty($vendor[0])? '' : $vendor[0]['v_name'],
                           'source'=>'js: function(request, response) {
                                $.ajax({
                                    url: "'.$this->createUrl('Vendor/GetVendor').'",
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
                                        
                                           $("#Project_owner_id").val(ui.item.id);
                                           
                                     }'
                                    
                                     
                            ),
                           'htmlOptions'=>array(

                                'class'=>'span12 '
                            ),
                                  
                        ));

               ?>
            </div>
  <div class="row-fluid">
    <div class="span4">
      <?php echo $form->textFieldRow($model,'fiscal_year',array('class'=>'span12')); ?>
    </div>
      
  </div>
 
 <div class="row-fluid" >

      <?php
   
      echo $form->checkBoxRow($model,'is_special',  array('value'=>1, 'uncheckValue'=>0));
       //echo $form->checkboxRow($model,'is_special',array('class'=>'span12')); ?>
    </div>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>'บันทึก',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
