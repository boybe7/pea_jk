<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
  'type'=>'horizontal',
  'htmlOptions'=>  array('class'=>'well span5 offset3 text-center','style'=>''),
)); ?>

	<?php echo $form->errorSummary($model); ?>

  
  <div class="row-fluid">
   
    <div class="span12">
      <?php echo $form->textFieldRow($model,'username',array('class'=>'span12','maxlength'=>100)); ?>
    </div>
    
  </div>
  <div class="row-fluid">
    <div class="span12">
      <?php echo $form->passwordFieldRow($model,'password',array('class'=>'span12','maxlength'=>100)); ?>
      
    </div>
  </div>
  <div class="row-fluid">
   
    <div class="span8">
      <?php echo $form->textFieldRow($model,'title',array('class'=>'span12','maxlength'=>10)); ?>
    </div>
    
  </div>
  <div class="row-fluid">
   
    <div class="span12">
      <?php echo $form->textFieldRow($model,'firstname',array('class'=>'span12','maxlength'=>100)); ?>
    </div>
    
  </div>
  <div class="row-fluid">
   
    <div class="span12">
      <?php echo $form->textFieldRow($model,'lastname',array('class'=>'span12','maxlength'=>100)); ?>
    </div>
    
  </div>
  <div class="row-fluid">
    
    <div class="span12">
      <?php //echo $form->textFieldRow($model,'type_id',array('class'=>'span12','maxlength'=>1)); ?>
       <?php 

        $data = array(array("value"=>"1","text"=>"Admin"),array("value"=>"2","text"=>"SuperUser"),array("value"=>"3","text"=>"User"),array("value"=>"4","text"=>"Executive")); 
        $typelist = CHtml::listData($data,'value','text');
        echo $form->dropDownListRow($model, 'u_group', $typelist,array('class'=>'span12','prompt'=>'--กรุณาเลือก--')); 
       ?>   
    </div>
   
  </div>	
  
  <div class="row-fluid">
	<div class="span12 form-actions ">
		<?php 
                
      
      $this->widget('bootstrap.widgets.TbButton', array(
			   'buttonType'=>'link',
			   'type'=>'danger',
			   'label'=>'ยกเลิก',
         'htmlOptions'=>array('class'=>'pull-right'),               
          'url'=>array("admin"), 
		  )); 
     $this->widget('bootstrap.widgets.TbButton', array(
         'buttonType'=>'submit',
         'htmlOptions'=>array('class'=>'pull-right','style'=>'margin:0px 10px 0px 10px;'),
         'type'=>'primary',
         'label'=>'บันทึก',
                    
      )); 
         
    ?>
	</div>
  </div>
<?php $this->endWidget(); ?>
