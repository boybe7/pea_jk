<script type="text/javascript">
    $('#tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
    
    $('a[data-toggle="tab"]').on('shown', function (e) {
        e.target // activated tab
        e.relatedTarget // previous tab
    });
</script>

<ul class="nav nav-tabs">
  
   <?php
     
        echo '<li class="active"><a href="#projTab" data-toggle="tab">โครงการ</a></li>';
        echo '<li ><a href="#boqTab" data-toggle="tab">BOQ</a></li>';
     
   ?>
</ul>
<div class="tab-content   well-tab">
    <!------  Project Tab ------------>
    <?php
      
         echo '<div class="tab-pane active" id="projTab">';
     
    ?>  
    	<p class="pull-right" style="font-size: 20px;font-weight: bold;">งวดที่ <?php echo $pay_no;?></p>
    	<h4>รายละเอียดโครงการ</h4>

    	<hr style="margin-top:5px; ">
      <?php
          $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
        'id'=>'project-form',
        'enableAjaxValidation'=>false,
        'type'=>'vertical',
          'htmlOptions'=>  array('class'=>'','style'=>''),
      )); ?>
      

        <div style="text-align:left">กรุณากรอกข้อมูลในช่องที่มีเครื่องหมาย (*) ให้ครบถ้วน</div>
            <div style="text-align:left"><?php echo $form->errorSummary(array($modelProj));?></div>

            <br>
            
            <div class="row-fluid">
              <?php echo $form->textFieldRow($modelProj,'name',array('class'=>'span12','maxlength'=>500)); ?>
            </div>

            <div class="row-fluid">
              <?php echo $form->textAreaRow($modelProj,'place',array('rows'=>2, 'cols'=>30, 'class'=>'span12')); ?>
            </div>

            <div class="row-fluid">
              <div class="span4">
                <?php echo $form->textFieldRow($modelProj,'contract_no',array('class'=>'span12')); ?>
              </div>  
              <div class="span4">
                <?php echo $form->textFieldRow($modelProj,'budget',array('class'=>'span12')); ?>
              </div>  

              <div class="span4">
                    <?php 

                    echo $form->labelEx($modelProj,'approve_date',array('class'=>'span12','style'=>'text-align:left;padding-right:10px;')); 

                 
                        echo '<div class="input-append" style="margin-top:-10px;">'; //ใส่ icon ลงไป
                            $form->widget('zii.widgets.jui.CJuiDatePicker',

                            array(
                                'name'=>'approve_date',
                                'attribute'=>'approve_date',
                                'model'=>$modelProj,
                                'options' => array(
                                                  'mode'=>'focus',
                                                  //'language' => 'th',
                                                  'format'=>'dd/mm/yyyy', //กำหนด date Format
                                                  'showAnim' => 'slideDown',
                                                  ),
                                'htmlOptions'=>array('class'=>'span12', 'value'=>$modelProj->approve_date),  // ใส่ค่าเดิม ในเหตุการ Update 
                             )
                        );
                        echo '<span class="add-on"><i class="icon-calendar"></i></span></div>';

                     ?>
            </div>
            </div>

            <div class="row-fluid">
              <?php echo $form->textFieldRow($modelProj,'vendor',array('class'=>'span12','maxlength'=>500)); ?>
            </div>

            <div class="row-fluid">
              <div class="span2">
                <?php echo $form->textFieldRow($modelProj,'percent_pay',array('class'=>'span12')); ?>
              </div>
              <div class="span2"> 
            <?php echo $form->textFieldRow($modelProj,'percent_adv',array('class'=>'span12')); ?>
          </div>  
            </div>

            <hr style="margin-top:5px; ">

            <h4>บุคลากรโครงการ</h4>

       

            <div class="row-fluid">
              <div class="span6">
                <label for="chairman">ประธานกรรมการ</label>
                <?php
                   $value = "";
                   if(!empty($modelProj->id))
                   {
                      $modelMember = ProjectMember::model()->findAll('proj_id =:id AND type=0', array(':id' => $modelProj->id));
                      $value = empty($modelMember) ? "" : $modelMember[0]->name; 
                   }
                  

                   echo '<input type="text" class="span12" name="chairman" value="'.$value.'">';

                ?>
              </div>
              <div class="span4"> 
                <label for="chairman_position">ตำแหน่ง</label>
                
                <?php
                   $value = "";
                   if(!empty($modelProj->id))
                   {
                      $value = empty($modelMember) ? "" : $modelMember[0]->position; 
                   }
                  

                   echo '<input type="text" class="span12" name="chairman_position" value="'.$value.'">';

                ?>
              </div>  
            </div>


            <?php
                   $member = array();
                   if(!empty($modelProj->id))
                   {
                      $member = ProjectMember::model()->findAll('proj_id =:id AND type=1', array(':id' => $modelProj->id));
                     
                      
                   }

                   for ($i=0; $i < 5; $i++) { 
                    echo '<div class="row-fluid">
                             <div class="span6">
                                <label for="commitee">กรรมการ</label>';
               
                
                                $value = isset($member[$i]) ? $member[$i]->name : "";
                              
                                echo ' <input type="text" class="span12" name="commitee[]"   value="'.$value.'">';
                                
                      echo '</div>
                            <div class="span4"> 
                              <label for="commitee_position">ตำแหน่ง</label>';
               
                             $value = isset($member[$i]) ? $member[$i]->position : "";
                             echo ' <input type="text" class="span12" name="commitee_position[]"  value="'.$value.'">';
                
                      echo '</div>  
                      </div>';

                               }
            


            ?>


            <?php
                   $value = "";
                   if(!empty($modelProj->id))
                   {
                      $modelMember = ProjectMember::model()->findAll('proj_id =:id AND type=2', array(':id' => $modelProj->id));
                      
                   }
            ?>       
           
            <div class="row-fluid">
              <div class="span6">
                <label for="taskmaster">ผู้ควบคุมงาน</label>
                
                <?php
                      $value = empty($modelMember) ? "" : $modelMember[0]->name; 
                      echo '<input type="text" class="span12" name="taskmaster" value='.$value.'>';
                ?>
              </div>
              <div class="span4"> 
                <label for="taskmaster_position">ตำแหน่ง</label>
             <?php
                      $value = empty($modelMember) ? "" : $modelMember[0]->position; 
                      echo '<input type="text" class="span12" name="taskmaster_position" value='.$value.'>';
                ?>
          </div>  
            </div>


            <?php
                   $value = "";
                   if(!empty($modelProj->id))
                   {
                      $modelMember = ProjectMember::model()->findAll('proj_id =:id AND type=3', array(':id' => $modelProj->id));
                      
                   }
            ?>   
            <div class="row-fluid">
              <div class="span6">
                <label for="vendor">เจ้าหน้าที่ผู้รับมอบอำนาจจากผู้รับจ้าง</label>
                 <?php
                      $value = empty($modelMember) ? "" : $modelMember[0]->name; 
                      echo '<input type="text" class="span12" name="vendor" value='.$value.'>';
                ?>
              </div>
              <div class="span4"> 
                <label for="vendor_position">ตำแหน่ง</label>
           <?php
                      $value = empty($modelMember) ? "" : $modelMember[0]->position; 
                      echo '<input type="text" class="span12" name="vendor_position" value='.$value.'>';
                ?>
          </div>  
            </div>

        
        
           
        <div class="row-fluid">
            <div class="form-actions">
            <?php 

              if(Yii::app()->user->getAccess(Yii::app()->request->url))
                $this->widget('bootstrap.widgets.TbButton', array(
                  'buttonType'=>'submit',
                  'type'=>'primary',
                  'label'=>'บันทึก',
                ));
             
            
             ?>
          </div>
                
        </div>
            <?php $this->endWidget(); ?>
      
	
		
    </div> <!-- end tab-pan -->


    <!------  BOQ Tab ------------>
   
    <?php
      
         echo '<div class="tab-pane" id="boqTab">'; 
    ?>   
    <h4>รายละเอียดค่าใช้จ่าย </h4>
    <hr style="margin-top:5px; ">
    
    
<script type="text/javascript">
  function bs_input_file() {
    $(".input-file").before(
      function() {
        if ( ! $(this).prev().hasClass('input-ghost') ) {
          var element = $("<input type='file' name='fileupload' id='fileupload' class='input-ghost' style='visibility:hidden; height:0'>");
          element.attr("name",$(this).attr("name"));
          element.change(function(){
            element.next(element).find('input').val((element.val()).split('\\').pop());
          });
          $(this).find("button.btn-choose").click(function(){
            element.click();
          });

          element.change(function(e) {
              filename = e.target.files[0].name;
              console.log(element)
          });
          
          $(this).find('input').css("cursor","pointer");
          $(this).find('input').mousedown(function() {
            $(this).parents('.input-file').prev().click();
            return false;
          });
          return element;
        }
      }
    );
  }

  

  $(function() {
    bs_input_file();

      var files;
      $('input[type=file]').on('change',prepareUpload);

      function prepareUpload(event) {
        files = event.target.files;
      }

      $("#form-import").on('submit',function(e){

        e.preventDefault();
        form = new FormData();
       
        form.append("proj_id", $('#proj_id').val());
        form.append('fileupload',files[0]);
        
         $.ajax({
               type: "POST",
               url: "importVendorBOQ",
               //dataType:"json",
               data: form,
               contentType: false,
               processData: false,
              success:function(response){
                    $('#boq-tab-content').html(response)
                    //$('#form-import')[0].reset();
                    //$("#boq-grid").yiiGridView("update",{});
                    
              }

             });
      });



  });


</script>

  <form method="POST" action="" id="form-import" enctype="multipart/form-data" class="pull-right">
    <div class="form-group">
      <div class="input-prepend input-file">
        <button class="btn btn-default btn-choose" type="button">Browse</button>
        <input type="text" name="filetext"  class="form-control" placeholder='Choose a file...' />
       
      </div>
      <button class="btn btn-success" id="importButton" type="submit" style="margin-top: -10px;"><i class="icon-excel icon-white"></i> Import</button>
      <?php
         $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'link',
            
            'type'=>'info',
            'label'=>'Submit',
            'icon'=>'plus white',
            'url'=>array('submitBOQ'),
            'htmlOptions'=>array('class'=>'pull-right','style'=>'margin-left: 10px'),
          )); 
      ?>
   
    </div>  
  </form>  


     <?php echo '<input type="hidden" id="proj_id" name="proj_id" value="'.$model->id.'">';?>


    <div id='boq-tab-content' style="margin-top: -40px"> </div>

 

      <?php

        
/*
       
        $modelBOQ=new Boq('search'); 
                   
        $this->widget('bootstrap.widgets.TbGridView',array(
            'id'=>'boq-grid',
            'type'=>'bordered condensed',
            'dataProvider'=>$modelBOQ->searchByProject($model->id),
            
            'selectableRows' =>2,
            'htmlOptions'=>array('style'=>'padding-top:10px'),
              'enablePagination' => true,
              'summaryText'=>'แสดงผล {start} ถึง {end} จากทั้งหมด {count} ข้อมูล',
              'template'=>"{items}<div class='row-fluid'><div class='span6'>{pager}</div><div class='span6'>{summary}</div></div>",
            'columns'=>array(
              'checkbox'=> array(
                        'id'=>'selectedID',
                        'class'=>'CCheckBoxColumn',
                        //'selectableRows' => 2, 
                       'headerHtmlOptions' => array('style' => 'width:3%;text-align:center;background-color: #f5f5f5'),
                         'htmlOptions'=>array(
                                      'style'=>'text-align:center'

                                    )           
                  ),
              'no'=>array(
                    'name' => 'no',
                    //'class' => 'editable.EditableColumn',
                    'filter'=>CHtml::activeTextField($modelBOQ, 'no',array("placeholder"=>"ค้นหาตาม".$modelBOQ->getAttributeLabel("no"))),
                  'headerHtmlOptions' => array('style' => 'width:5%;text-align:center;background-color: #f5f5f5'),                     
                  'htmlOptions'=>array('style'=>'text-align:center;font-weight:bold')
                ),
              'detail'=>array(
                    'name' => 'detail',
                    //'class' => 'editable.EditableColumn',
                    'type'=>'raw',
                    'value'=> function($data){
                        if($data->type==1 || $data->type==2)
                           return '<b>'.$data->detail.'</b>';
                        else if($data->type==-1)
                           return '-&nbsp;&nbsp;'.$data->detail;  
                        else 
                           return '&nbsp;&nbsp;&nbsp;'.$data->detail;
                    },  
                    'filter'=>CHtml::activeTextField($modelBOQ, 'detail',array("placeholder"=>"ค้นหาตาม".$modelBOQ->getAttributeLabel("detail"))),
                  'headerHtmlOptions' => array('style' => 'width:50%;text-align:center;background-color: #f5f5f5'),                     
                  'htmlOptions'=>array('style'=>'text-align:left;padding-left:10px;')
                ),
              
              'amount'=>array(
                  'name' => 'amount',
                  // 'class' => 'editable.EditableColumn',
                  // 'editable' => array( //editable section
                    
                  //   'title'=>'แก้ไขจำนวน',
                  //   'url' => $this->createUrl('boq/update'),
                  //   'success' => 'js: function(response, newValue) {
                  //             if(!response.success) return response.msg;

                  //             $("#boq-grid").yiiGridView("update",{});
                  //           }',
                  //   'options' => array(
                  //     'ajaxOptions' => array('dataType' => 'json'),

                  //   ), 
                  //   'placement' => 'right',
                   
                  // ),
                  'headerHtmlOptions' => array('style' => 'width:5%;text-align:center;background-color: #f5f5f5'),                     
                  'htmlOptions'=>array('style'=>'text-align:center')
                ),
                'unit'=>array(
                  
                  'name'=>'unit',
                  // 'class' => 'editable.EditableColumn',
                  // 'editable' => array( //editable section
                    
                  //   'title'=>'แก้ไขหน่วย',
                  //   'url' => $this->createUrl('boq/update'),
                  //   'success' => 'js: function(response, newValue) {
                  //             if(!response.success) return response.msg;

                  //             $("#boq-grid").yiiGridView("update",{});
                  //           }',
                  //   'options' => array(
                  //     'ajaxOptions' => array('dataType' => 'json'),

                  //   ), 
                  //   'placement' => 'right',
                   
                  // ),
                  'headerHtmlOptions' => array('style' => 'width:5%;text-align:center;background-color: #f5f5f5'),                     
                  'htmlOptions'=>array('style'=>'text-align:center')
                ),

                'price_item'=>array(
                  
                  'name'=>'price_item',
                  
                  'headerHtmlOptions' => array('style' => 'width:10%;text-align:center;background-color: #f5f5f5'),                     
                  'htmlOptions'=>array('style'=>'text-align:center')
                ),
                'price_trans'=>array(
                  
                  'name'=>'price_trans',
                  
                  'headerHtmlOptions' => array('style' => 'width:10%;text-align:center;background-color: #f5f5f5'),                     
                  'htmlOptions'=>array('style'=>'text-align:center')
                ),
                'price_install'=>array(
                  
                  'name'=>'price_install',
                  
                  'headerHtmlOptions' => array('style' => 'width:10%;text-align:center;background-color: #f5f5f5'),                     
                  'htmlOptions'=>array('style'=>'text-align:center')
                ),
              

              // array(
              //   'class'=>'bootstrap.widgets.TbButtonColumn',
              //   'headerHtmlOptions' => array('style' => 'width:8%;text-align:center;background-color: #f5f5f5'),
              //   'template' => '{delete}',
              //   'buttons'=>array(
              //       'delete'=>array(
              //         'url'=>'Yii::app()->createUrl("boq/delete", array("id"=>$data->id))',  

              //       ))
            
              // ),
            ),
            )
          );

*/
      ?>

      <div class="row-fluid">
            <div class="form-actions">
            <?php 

              if(Yii::app()->user->getAccess(Yii::app()->request->url))
                $this->widget('bootstrap.widgets.TbButton', array(
                  'buttonType'=>'link',
                  'type'=>'primary',
                  'label'=>'บันทึก',
                   'url'=>array('project/index'),
                ));
             
            
             ?>
          </div>
                
        </div>
        
    </div> <!-- end tab-pan -->
	
</div>	
