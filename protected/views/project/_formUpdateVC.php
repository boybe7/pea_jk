<script type="text/javascript">
    $('#tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
    
    $('a[data-toggle="tab"]').on('shown', function (e) {
        e.target // activated tab
        e.relatedTarget // previous tab
    });

  function ajaxindicatorstart(text)
  {
    if(jQuery('body').find('#resultLoading').attr('id') != 'resultLoading'){
    jQuery('body').append('<div id="resultLoading" style="display:none"><div><img src="http://localhost/pea_jk/images/loading.gif"><div>'+text+'</div></div><div class="bg"></div></div>');

    }
    
    jQuery('#resultLoading').css({
      'width':'100%',
      'height':'100%',
      'position':'fixed',
      'z-index':'10000000',
      'top':'0',
      'left':'0',
      'right':'0',
      'bottom':'0',
      'margin':'auto'
    }); 
    
    jQuery('#resultLoading .bg').css({
      'background':'#000000',
      'opacity':'0.7',
      'width':'100%',
      'height':'100%',
      'position':'absolute',
      'top':'0'
    });
    
    jQuery('#resultLoading>div:first').css({
      'width': '250px',
      'height':'75px',
      'text-align': 'center',
      'position': 'fixed',
      'top':'0',
      'left':'0',
      'right':'0',
      'bottom':'0',
      'margin':'auto',
      'font-size':'16px',
      'z-index':'10',
      'color':'#ffffff'
      
    });

      jQuery('#resultLoading .bg').height('100%');
        jQuery('#resultLoading').fadeIn(300);
      jQuery('body').css('cursor', 'wait');
  }

  function ajaxindicatorstop()
  {
      jQuery('#resultLoading .bg').height('100%');
        jQuery('#resultLoading').fadeOut(300);
      jQuery('body').css('cursor', 'default');
  }
  
 
  
  jQuery(document).ajaxStart(function () {
      //show ajax indicator
    ajaxindicatorstart('Loading data.. please wait..');
  }).ajaxStop(function () {
    //hide ajax indicator
    ajaxindicatorstop();
  });



</script>


<script type="text/javascript">

  function bs_input_file2() {
    $(".input-file").before(
      function() {
        if ( ! $(this).prev().hasClass('input-ghost') ) {
          var element = $("<input type='file' name='fileupload2' id='fileupload2' class='input-ghost' style='visibility:hidden; height:0'>");
          element.attr("name",$(this).attr("name"));
          element.change(function(){
            element.next(element).find('input').val((element.val()).split('\\').pop());
          });
          $(this).find("button.btn-choose").click(function(){
            element.click();
          });

          element.change(function(e) {
              filename = e.target.files[0].name;
              
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
  $(document).ready(function() {
  bs_input_file2();

});

</script>





<?php
     $boq =  Boq::model()->findAll(array('join'=>'','condition'=>'vc_id='.$model->id));
     if(!empty($boq))    
     {
         $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'link',
            
            'type'=>'success',
            'label'=>'เพิ่มงวด',
            'icon'=>'plus-sign white',
            // 'url'=>array('exportBOQ'),
            'htmlOptions'=>array('class'=>'pull-right','style'=>'margin-left: 10px',
                  // 'onclick'=>'js:bootbox.confirm($("#modal-body").html(),"ยกเลิก","ตกลง",
                  //             function(confirmed){
                  //                 if(confirmed)
                  //                 {  console.log($("#form_export :selected").val())                                 
                  //                   //window.location.href = "../exportBOQ?vc_id='.$model->id.'&form=1";                                    
                  //                 }

                  //             })'

                  'onclick'=>'js:bootbox.prompt({
                      title: "เลือกแบบฟอร์มรายละเอียดขออนุมัติเบิกจ่าย",
                      inputType: "select",
                      inputOptions: [
                        {
                            text: "แบบฟอร์ม 1 (ค่าอุปกรณ์ค่าขนส่ง แยกกันกับค่าติดตั้งทดสอบ)",
                            value: 1,
                        },
                        {
                            text: "แบบฟอร์ม 2 (ค่าอุปกรณ์ ค่าขนส่ง และค่าติดตั้งทดสอบ เบิกรวมกัน)",
                            value: 2,
                        }],
                      callback: function (result) {
                        if(result==1 || result==2)
                        {
                            $.ajax({
                                    url: "'.$this->createUrl('PaymentDetail/create').'",
                                    type: "POST",
                                    data: {id: '.$model->id.',form_type: result},
                                    success: function (pay_no) {
                                           
                                           window.location.href = "../exportBOQ?vc_id='.$model->id.'&form="+result+"&pay_no="+pay_no; 

                                          setTimeout(function(){
                                             window.location.reload(1);
                                          }, 10000);
                                    }
                                })
                               
                        }
                      }
                  });'
            ),
          )); 
     }    
?>
<ul class="nav nav-tabs">
  
   <?php
      $payment = Yii::app()->db->createCommand()
                            ->select('MAX(pay_no) as max_pay_no')
                            ->from('payment_detail')
                            ->where("vc_id=".$model->id)
                            ->queryAll();



        echo '<li class="active"><a href="#projTab" data-toggle="tab">โครงการ</a></li>';
        echo '<li ><a href="#boqTab" data-toggle="tab">BOQ</a></li>';

        for ($i=1; $i <= $payment[0]['max_pay_no']; $i++) { 
          echo '<li ><a href="#payTab'.$i.'" data-toggle="tab">งวดที่ '.$i.'</a></li>';
        }
     
   ?>
</ul>
<div class="tab-content   well-tab">
    <!------  Project Tab ------------>
    <?php
      
         echo '<div class="tab-pane active" id="projTab">';
     
    ?>  
      <h4>รายละเอียดสัญญา</h4>
      <hr style="margin-top:5px; ">
      <?php
          $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
        'id'=>'project-form',
        'enableAjaxValidation'=>false,
        'type'=>'vertical',
          'htmlOptions'=>  array('class'=>'','style'=>''),
      )); ?>
      

        <div style="text-align:left">กรุณากรอกข้อมูลในช่องที่มีเครื่องหมาย (*) ให้ครบถ้วน</div>
            <div style="text-align:left"><?php echo $form->errorSummary(array($model));?></div>

            <br>

            <div class="row-fluid">
               <div class="span10">
                  <?php 
                  echo $form->hiddenField($model, 'proj_id'); 
                  ?>
                  <label for='project'>โครงการ</label>
                  <?php 
                     echo "<input type='text' class='span12' id='project' value='".Project::model()->findByPk($model->proj_id)->name."' readonly>"; 
                  ?>
               </div>
                <div class="span2">
                  <label for='fiscal_year'>ปี</label>
                  <?php 
                     echo "<input type='text' class='span12' id='fiscal_year' value='".Project::model()->findByPk($model->proj_id)->fiscal_year."' readonly>"; 
                  ?>
                </div>   
            </div>
            
            <div class="row-fluid">
              <?php echo $form->textFieldRow($model,'name',array('class'=>'span12','maxlength'=>500)); ?>
            </div>
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


            <div class="row-fluid">
              <?php echo $form->textAreaRow($model,'detail_approve',array('rows'=>2, 'cols'=>30, 'class'=>'span12')); ?>
            </div>

           <div class="row-fluid">
              <div class="span3">
                <?php echo $form->textFieldRow($model,'contract_no',array('class'=>'span12')); ?>
              </div>  
              <div class="span3">
                <?php echo $form->textFieldRow($model,'budget',array('class'=>'span12')); ?>
              </div>  

              <div class="span3">
                    <?php 

                    echo $form->labelEx($model,'approve_date',array('class'=>'span12','style'=>'text-align:left;padding-right:10px;')); 

                 
                        echo '<div class="input-append" style="margin-top:-10px;">'; //ใส่ icon ลงไป
                            $form->widget('zii.widgets.jui.CJuiDatePicker',

                            array(
                                'name'=>'approve_date',
                                'attribute'=>'approve_date',
                                'model'=>$model,
                                'options' => array(
                                                  'mode'=>'focus',
                                                  //'language' => 'th',
                                                  'format'=>'dd/mm/yyyy', //กำหนด date Format
                                                  'showAnim' => 'slideDown',
                                                  ),
                                'htmlOptions'=>array('class'=>'span12', 'value'=>$model->approve_date),  // ใส่ค่าเดิม ในเหตุการ Update 
                             )
                        );
                        echo '<span class="add-on"><i class="icon-calendar"></i></span></div>';

                     ?>
            </div>
            <div class="span3">
                    <?php 

                    echo $form->labelEx($model,'end_date',array('class'=>'span12','style'=>'text-align:left;padding-right:10px;')); 

                 
                        echo '<div class="input-append" style="margin-top:-10px;">'; //ใส่ icon ลงไป
                            $form->widget('zii.widgets.jui.CJuiDatePicker',

                            array(
                                'name'=>'end_date',
                                'attribute'=>'end_date',
                                'model'=>$model,
                                'options' => array(
                                                  'mode'=>'focus',
                                                  //'language' => 'th',
                                                  'format'=>'dd/mm/yyyy', //กำหนด date Format
                                                  'showAnim' => 'slideDown',
                                                  ),
                                'htmlOptions'=>array('class'=>'span12', 'value'=>$model->end_date),  // ใส่ค่าเดิม ในเหตุการ Update 
                             )
                        );
                        echo '<span class="add-on"><i class="icon-calendar"></i></span></div>';

                     ?>
            </div>
            </div>

           
            <div class="row-fluid">
              <div class="span2">
                <?php echo $form->textFieldRow($model,'percent_pay',array('class'=>'span12')); ?>
              </div>
              <div class="span2"> 
            <?php echo $form->textFieldRow($model,'percent_adv',array('class'=>'span12')); ?>
          </div>  
            </div>

            <hr style="margin-top:5px; ">

            <h4>บุคลากร</h4>
    

       

            <div class="row-fluid">
              <div class="span6">
                <label for="chairman">ประธานกรรมการ</label>
                <?php
                   $value = "";
                   if(!empty($model->id))
                   {
                      $modelMember = ContractMember::model()->findAll('vc_id =:id AND type=0', array(':id' => $model->id));
                      $value = empty($modelMember) ? "" : $modelMember[0]->name; 
                   }
                  

                   echo '<input type="text" class="span12" name="chairman" value="'.$value.'">';

                ?>
              </div>
              <div class="span4"> 
                <label for="chairman_position">ตำแหน่ง</label>
                
                <?php
                   $value = "";
                   if(!empty($model->id))
                   {
                      $value = empty($modelMember) ? "" : $modelMember[0]->position; 
                   }
                  

                   echo '<input type="text" class="span12" name="chairman_position" value="'.$value.'">';

                ?>
              </div>  
            </div>


            <?php
                   $member = array();
                   if(!empty($model->id))
                   {
                      $member = ContractMember::model()->findAll('vc_id =:id AND type=1', array(':id' => $model->id));
                     
                      
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
                   if(!empty($model->id))
                   {
                      $modelMember = ContractMember::model()->findAll('vc_id =:id AND type=2', array(':id' => $model->id));
                      
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
                   if(!empty($model->id))
                   {
                      $modelMember = ContractMember::model()->findAll('vc_id =:id AND type=3', array(':id' => $model->id));
                      
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
       
        form.append("vc_id", $('#vc_id').val());
        form.append('fileupload',files[0]);
        
         $.ajax({
               type: "POST",
               url: "../../boq/importExcel",
               //dataType:"json",
               data: form,
               contentType: false,
               processData: false,
              success:function(response){
                    
                    $('#form-import')[0].reset();
                    $("#boq-grid").yiiGridView("update",{});
                   // window.location.reload();
                    
              }

             });
      });



  });


</script>

 <?php
 if($model->lock_boq!=1)
 {
 ?>
  <form method="POST" action="" id="form-import" enctype="multipart/form-data" class="pull-right">
    <div class="form-group">
      <div class="input-prepend input-file">
        <button class="btn btn-default btn-choose" type="button">Browse</button>
        <input type="text" name="filetext"  class="form-control" placeholder='Choose a file...' />
       
      </div>
      <button class="btn btn-inverse" id="importButton" type="submit" style="margin-top: -10px;"><i class="icon-excel icon-white"></i> Import</button>
     
     
    </div>  
  </form>  
  <?php
  
  } //endif
  else{
    echo "<span class='pull-right' style='color:red'>!***โครงการนี้ไม่สามารถแก้ไขข้อมูล BOQ ได้ เพราะได้สร้างแบบฟอร์มให้ทางผู้รับจ้างแล้ว</span>";
  }
  ?>

     <?php echo '<input type="hidden" id="vc_id" name="vc_id" value="'.$model->id.'">';?>

      <?php

        

       
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
                        /*if($data->type==1 || $data->type==2)
                           return '<b>'.$data->detail.'</b>';
                        else if($data->type==-1)
                           return '-&nbsp;&nbsp;'.$data->detail;  
                        else 
                           return '&nbsp;&nbsp;&nbsp;'.$data->detail;*/
                        if($data->type==1 || $data->type==2)
                          return '<b>'.$data->detail.'</b>';
                        else if($data->indent!="")
                          return $data->indent.'&nbsp;&nbsp;'.$data->detail;  
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
        
    </div> <!-- end tab-boq -->

     <!------  PaymentDetail Tab ------------>
<?php
  Yii::app()->clientScript->registerScript('importBOQ', "
    function importBOQ(elm, index)
    {
        formName = '#form-import-boq-'+index;
        content = '#boq-content-'+index;
        var fd = new FormData($(formName)[0]);
        fd.append('vc_id', ".$model->id.");
        fd.append('pay_no', index);
        $.ajax({
            url: '../importBOQ',
            type: 'POST',
            cache: false,
            data: fd,
            processData: false,
            contentType: false,
            success: function (response) { 
               $(content).html(response)
              
               if($('#error_'+index).val() =='error')
                  $('#submit_'+index).hide()
               else
               {
                 
                  $('#submit_'+index).show('fast')
               }
            },
            error: function () {
               
            }
        });
        
    }", CClientScript::POS_END);

  Yii::app()->clientScript->registerScript('exportJK', "
    function exportJK(elm, index)
    {
       
       window.location.href = '../exportExcel?vc_id=".$model->id."&pay_no='+index
        
    }", CClientScript::POS_END);

  Yii::app()->clientScript->registerScript('submitBOQ', "
    function submitBOQ(elm, index)
    {
        formName = '#form-import-boq-'+index;
        content = '#boq-content-'+index;
        var fd = new FormData($(formName)[0]);
        fd.append('vc_id', ".$model->id.");
        fd.append('pay_no', index);

        $.ajax({
            url: '../submitBOQ',
            type: 'POST',
            cache: false,
            data: fd,
            processData: false,
            contentType: false,
            success: function (response) { 
               $(content).html('')
               if(response=='error')
                  bootbox.alert('<font color=red><h4>การนำเข้าข้อมูลไม่ถูกต้อง</h4></font>');
               else  
               {
                  window.location.reload();
               }
            },
            error: function () {
               
            }
        });
        
    }", CClientScript::POS_END);


   Yii::app()->clientScript->registerScript('printJK', "
    function printJK(e, index)
    {
       
       
        formName = '#form-import-boq-'+index;
        content = '#boq-content-'+index;
        var fd = new FormData($(formName)[0]);
        fd.append('vc_id', ".$model->id.");
        fd.append('pay_no', index);
        var filename = 'form_print_".$model->id."'
        $.ajax({
            url: '../printJK',
            type: 'POST',
            cache: false,
            data: fd,
            processData: false,
            contentType: false,
            success: function (result) {
                
                 window.open('../../report/temp/'+filename+'.pdf', '_blank', 'fullscreen=yes');              
                
            }

        });
        
    }", CClientScript::POS_END);
?>


<script type="text/javascript">
 $(document).ready(function() {
   

  });
</script>

     <?php 
     
     for ($i=1; $i <= $payment[0]['max_pay_no']; $i++) { 
       
      
        echo '<div class="tab-pane" id="payTab'.$i.'">';
            // $this->renderPartial('_formPayment', array(
            //           'model' => $model,
            //           'index' => $i,
            //           'display' => 'block'
            //       ));  
        $payment_detail = Yii::app()->db->createCommand()
                            ->select('form_type')
                            ->from('payment_detail')
                            ->where("vc_id='".$model->id."' AND pay_no =".$i)
                            ->queryAll();

        $form_type = $payment_detail[0]['form_type'];                     
     ?>   


    
     <h4>รายละเอียดค่าใช้จ่าย งวดที่ <?php echo $i;?></h4>
    
    <div class="row-fluid" style="margin-top: -35px">
 
      <?php 
    echo '<form method="POST" action="" id="form-import-boq-'.$i.'" enctype="multipart/form-data" class="pull-right">';  ?>
    <div class="form-group">
      <?php
              $this->widget('bootstrap.widgets.TbButton', array(
                  'buttonType'=>'link',
                  
                  'type'=>'info',
                  'label'=>'BOQ',
                  'icon'=>'excel ',
                  
                  'htmlOptions'=>array(
                    'class'=>'',
                    'style'=>'margin-top:-10px',
                    //'onclick' => 'exportBOQ(this,'.$i.');'
                    'onclick'=>'js:bootbox.prompt({
                          title: "เลือกแบบฟอร์มรายละเอียดขออนุมัติเบิกจ่าย",
                          inputType: "select",
                          inputOptions: [
                            {
                                text: "แบบฟอร์ม 1 (ค่าอุปกรณ์ค่าขนส่ง แยกกันกับค่าติดตั้งทดสอบ)",
                                value: 1,
                            },
                            {
                                text: "แบบฟอร์ม 2 (ค่าอุปกรณ์ ค่าขนส่ง และค่าติดตั้งทดสอบ เบิกรวมกัน)",
                                value: 2,
                            }],
                          callback: function (result) {
                            if(result==1 || result==2)
                            {
                                $.ajax({
                                        url: "'.$this->createUrl('PaymentDetail/update').'",
                                        type: "POST",
                                        data: {id: '.$model->id.',form_type: result,pay_no: '.$i.'},
                                        success: function (pay_no) {
                                               
                                               window.location.href = "../exportBOQ?vc_id='.$model->id.'&form="+result+"&pay_no="+pay_no; 

                                              setTimeout(function(){
                                                 window.location.reload(1);
                                              }, 10000);
                                        }
                                    })
                                   
                            }
                          }
                      });'
                  ),
              )); 
      ?>
      <div class="input-prepend input-file">
        <button class="btn btn-default btn-choose" type="button"><i class="icon-folder-open"></i></button>
        <input type="text" name="filetext"  class="" placeholder='Choose a file...' />
       
      </div>
      
      <?php
          

           $this->widget('bootstrap.widgets.TbButton', array(
                  'buttonType'=>'link',
                  
                  'type'=>'inverse',
                  'label'=>'Import',
                  'icon'=>'arrow-down white',
                  
                  'htmlOptions'=>array(
                    'class'=>'',
                    'style'=>'margin-top:-10px',
                    'onclick' => 'importBOQ(this,'.$i.');'
                  ),
              ));

             $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'link',
            
            'type'=>'info',
            'label'=>'Submit',
            'icon'=>'ok white',
            'htmlOptions'=>array('id'=>'submit_'.$i,'style'=>'margin-left: 10px;margin-top:-10px;display:none','onclick'=>'submitBOQ(this,'.$i.');'),
          )); 


            $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'link',
            
            'type'=>'success',
            'label'=>'Export',
            'icon'=>'excel white',
            'htmlOptions'=>array('class'=>'','style'=>'margin-left: 10px;margin-top:-10px','onclick'=>'exportJK(this,'.$i.');'),
          )); 

            $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'link',
            
            'type'=>'warning',
            'label'=>'Print',
            'icon'=>'print white',
            'htmlOptions'=>array('class'=>'','style'=>'margin-left: 10px;margin-top:-10px',
              //'onclick'=>'printJK(this,'.$i.');'
              'onclick'=>'js:bootbox.prompt({
                          title: "กำหนดจำนวนรายการต่อหน้า",
                          inputType: "number",
                          value: 35,
                            
                          callback: function (result) {
                                var filename = "form_print_'.$model->id.'"
                                $.ajax({
                                        url: "../printJK",
                                        type: "POST",
                                        data: {vc_id: '.$model->id.',pay_no: '.$i.',max_item: result},
                                        success: function (result2) {
                                               
                                               window.open("../../report/temp/"+filename+".pdf", "_blank", "fullscreen=yes"); 

                                        }
                                    })
                                   
      
                          }
                      });'
            ),
          )); 

        

       
         
      ?>
   
    </div>  
  </form>

</div>
  

     <?php  echo '<div id="boq-content-'.$i.'" > </div>'; ?>  

     <!--Gridview -->
     <ul class="nav nav-tabs" style="margin-bottom: 0px;margin-top: 20px;">
  
       <?php
       if($form_type==1)
       {
            echo '<li class="active"><a href="#item-tab-'.$i.'" data-toggle="tab">ค่าอุปกรณ์ และขนส่ง</a></li>';
            echo '<li ><a href="#install-tab-'.$i.'" data-toggle="tab">ค่าติดตั้ง และทดสอบ</a></li>';
       }
       else
       {
            echo '<li class="active"><a href="#item-tab-'.$i.'" data-toggle="tab">ค่าอุปกรณ์ ขนส่ง และค่าติดตั้งทดสอบ</a></li>';
       }
       echo '<li ><a href="#fine-tab-'.$i.'" data-toggle="tab">หัก</a></li>';
       ?>
    </ul>
    <div class="tab-content">
        
     <?php
       
        
      $Criteria = new CDbCriteria();
      $Criteria->condition = "vc_id='$model->id'";
      $boq = Boq::model()->findAll($Criteria);   

       if($form_type==1)
       {                  
    echo '<div class="tab-pane active" id="item-tab-'.$i.'">';
      echo '<div style="overflow-x:auto;width: 100%;"> <table class="table  table-bordered table-condensed" style="width: 150%;max-width: 150%;">
       <thead>
        <tr>
          <th style="text-align:center;width:3%" rowspan=2>ลำดับ</th>
          <th style="text-align:center;width:27%" rowspan=2>รายละเอียด</th>
          <th style="text-align:center;width:40%" colspan=5>งานตามสัญญา</th>
          <th style="text-align:center;width:15%" colspan=2>ส่งมอบงานครั้งนี้</th>
          <th style="text-align:center;width:15%" colspan=2>รวมงานที่ส่งแล้ว (รวมครั้งนี้)</th>

        </tr>
        <tr>
          <th style="text-align:center;width:5%">จำนวน</th>
          <th style="text-align:center;width:5%">หน่วย</th>
          <th style="text-align:center;width:10%">ค่าอุปกรณ์/หน่วย</th>
          <th style="text-align:center;width:10%">ค่าขนส่ง/หน่วย</th>
          <th style="text-align:center;width:10%">เป็นเงิน</th>

          <th style="text-align:center;width:5%">จำนวน</th>
          <th style="text-align:center;width:10%">เป็นเงิน</th>

          <th style="text-align:center;width:5%">จำนวน</th>
          <th style="text-align:center;width:10%">เป็นเงิน</th>
         
        </tr>
      </thead>
      <tbody>';                       

        foreach ($boq as $key => $value) {
          echo '<tr>';
            echo '<td style="text-align:center">'.$value->no.'</td>';
            if($value->type==1 || $value->type==2)
                $detail = '<b>'.$value->detail.'</b>';
            else if($value->indent!="")
                $detail = $value->indent.'&nbsp;&nbsp;'.$value->detail;  
            else 
                $detail = '&nbsp;&nbsp;&nbsp;'.$value->detail;
 
            echo '<td style="text-align:left">'.$detail.'</td>';
           
            echo '<td style="text-align:center;width:5%">'.$value->amount.'</td>';
            echo '<td style="text-align:center;width:5%">'.$value->unit.'</td>';
            $price_item = is_numeric($value->price_item) ? number_format($value->price_item,0) : $value->price_item;
            $price_trans = is_numeric($value->price_trans) ? number_format($value->price_trans,0) : $value->price_trans;
            $price_install = is_numeric($value->price_install) ? number_format($value->price_install,0) : $value->price_install;
            echo '<td style="text-align:center;width:10%">'.$price_item.'</td>';
            echo '<td style="text-align:center;width:10%">'.$price_trans.'</td>';
            $price_item_all = '';
            if(!empty($value->amount) )
            { 
                $price_item = is_numeric($value->price_item) ? $value->price_item : 0;
                $price_trans = is_numeric($value->price_trans) ? $value->price_trans : 0;
                
                $price_item_all = ($price_item + $price_trans) * $value->amount;
                if(!is_numeric($value->price_item) && !is_numeric($value->price_trans))
                  echo '<td style="text-align:center">-</td>';
                else  
                   echo '<td style="text-align:center">'.number_format($price_item_all,0).'</td>';
            }
            else
                echo '<td style="text-align:center">'.$price_item_all.'</td>';

            //amount current payment
            $curr_payment = Yii::app()->db->createCommand()
                            ->select('*')
                            ->from('payment')
                            ->where("pay_type=0 AND item_id='".$value->id."' AND vc_id='".$model->id."' AND pay_no =".$i)
                            ->queryAll();
            if(!empty($curr_payment))
            {
              echo '<td style="text-align:center">'.$curr_payment[0]['amount'].'</td>';
              $price_item_all = ($price_item + $price_trans) * $curr_payment[0]['amount'];
             if(!is_numeric($value->price_item) && !is_numeric($value->price_trans))
                  echo '<td style="text-align:center">-</td>';
                else  
                   echo '<td style="text-align:center">'.number_format($price_item_all,0).'</td>';

            } 
            else{
              echo '<td style="text-align:center"></td>';
              echo '<td style="text-align:center"></td>';  
            }               

            //amount previous with current payment  
            $prev_payment = Yii::app()->db->createCommand()
                            ->select('SUM(amount) as amount')
                            ->from('payment')
                            ->where("pay_type=0 AND item_id='".$value->id."' AND vc_id='".$model->id."' AND pay_no <=".$i)
                            ->queryAll();     

            if(!empty($prev_payment) and $prev_payment[0]['amount']>0)
            {
              echo '<td style="text-align:center">'.$prev_payment[0]['amount'].'</td>';
              $price_item_all = ($price_item + $price_trans) * $prev_payment[0]['amount'];
              if(!is_numeric($value->price_item) && !is_numeric($value->price_trans))
                  echo '<td style="text-align:center">-</td>';
                else  
                   echo '<td style="text-align:center">'.number_format($price_item_all,0).'</td>';

            } 
            else{
              echo '<td style="text-align:center"></td>';
              echo '<td style="text-align:center"></td>';  
            }                                
          echo '</tr>';
                            
        }                    
        echo '</tbody></table></div>';

      echo '</div>'; //end item-tab  

      echo '<div class="tab-pane" id="install-tab-'.$i.'">';
        echo '<div style="overflow-x:auto;width: 100%;"> <table class="table  table-bordered table-condensed" style="width: 150%;max-width: 150%;">
       <thead>
        <tr>
          <th style="text-align:center;width:3%" rowspan=2>ลำดับ</th>
          <th style="text-align:center;width:27%" rowspan=2>รายละเอียด</th>
          <th style="text-align:center;width:40%" colspan=4>งานตามสัญญา</th>
          <th style="text-align:center;width:15%" colspan=2>ส่งมอบงานครั้งนี้</th>
          <th style="text-align:center;width:15%" colspan=2>รวมงานที่ส่งแล้ว (รวมครั้งนี้)</th>

        </tr>
        <tr>
          <th style="text-align:center;width:5%">จำนวน</th>
          <th style="text-align:center;width:5%">หน่วย</th>
          <th style="text-align:center;width:10%">ค่าติดตั้งทดสอบ/หน่วย</th>
          <th style="text-align:center;width:10%">เป็นเงิน</th>

          <th style="text-align:center;width:5%">จำนวน</th>
          <th style="text-align:center;width:10%">เป็นเงิน</th>

          <th style="text-align:center;width:5%">จำนวน</th>
          <th style="text-align:center;width:10%">เป็นเงิน</th>
         
        </tr>
      </thead>
      <tbody>';                       

        foreach ($boq as $key => $value) {
          echo '<tr>';
            echo '<td style="text-align:center">'.$value->no.'</td>';
           

            if($value->type==1 || $value->type==2)
                $detail = '<b>'.$value->detail.'</b>';
            else if($value->indent!="")
                $detail = $value->indent.'&nbsp;&nbsp;'.$value->detail;  
            else 
                $detail = '&nbsp;&nbsp;&nbsp;'.$value->detail;  

            echo '<td style="text-align:left">'.$detail.'</td>';
            echo '<td style="text-align:center;width:5%">'.$value->amount.'</td>';
            echo '<td style="text-align:center;width:5%">'.$value->unit.'</td>';
            
            $price_install = is_numeric($value->price_install) ? number_format($value->price_install,0) : $value->price_install;
            echo '<td style="text-align:center;width:10%">'.$price_install.'</td>';
            $price_item_all = '';
            if(!empty($value->amount) )
            { 
                $price_install = is_numeric($value->price_install) ? $value->price_install : 0;
               
                $price_item_all = $price_install * $value->amount;
                if(!is_numeric($value->price_install))
                  echo '<td style="text-align:center">-</td>';
                else  
                   echo '<td style="text-align:center">'.number_format($price_item_all,0).'</td>';
            }
            else
                echo '<td style="text-align:center">'.$price_item_all.'</td>';

            //amount current payment
            $curr_payment = Yii::app()->db->createCommand()
                            ->select('*')
                            ->from('payment')
                            ->where("pay_type=2 AND item_id='".$value->id."' AND vc_id='".$model->id."' AND pay_no =".$i)
                            ->queryAll();
            if(!empty($curr_payment))
            {
              echo '<td style="text-align:center">'.$curr_payment[0]['amount'].'</td>';
              $price_item_all = $price_install* $curr_payment[0]['amount'];
              if(!is_numeric($value->price_install))
                  echo '<td style="text-align:center">-</td>';
                else  
                   echo '<td style="text-align:center">'.number_format($price_item_all,0).'</td>';

            } 
            else{
              echo '<td style="text-align:center"></td>';
              echo '<td style="text-align:center"></td>';  
            }               

            //amount previous with current payment  
            $prev_payment = Yii::app()->db->createCommand()
                            ->select('SUM(amount) as amount')
                            ->from('payment')
                            ->where("pay_type=2 AND item_id='".$value->id."' AND vc_id='".$model->id."' AND pay_no <=".$i)
                            ->queryAll();     

            if(!empty($prev_payment) and $prev_payment[0]['amount']>0)
            {
              echo '<td style="text-align:center">'.$prev_payment[0]['amount'].'</td>';
              $price_item_all = $price_install * $prev_payment[0]['amount'];
              if(!is_numeric($value->price_install))
                  echo '<td style="text-align:center">-</td>';
                else  
                   echo '<td style="text-align:center">'.number_format($price_item_all,0).'</td>';

            } 
            else{
              echo '<td style="text-align:center"></td>';
              echo '<td style="text-align:center"></td>';  
            }                                
          echo '</tr>';
                            
        }                    
        echo '</tbody></table></div>';

      echo '</div>'; //end install-tab  
   
      }
      else if($form_type==2){
        echo '<div class="tab-pane active" id="item-tab-'.$i.'">';
      echo '<div style="overflow-x:auto;width: 100%;"> <table class="table  table-bordered table-condensed" style="width: 150%;max-width: 150%;">
       <thead>
        <tr>
          <th style="text-align:center;width:3%" rowspan=2>ลำดับ</th>
          <th style="text-align:center;width:27%" rowspan=2>รายละเอียด</th>
          <th style="text-align:center;width:35%" colspan=6>งานตามสัญญา</th>
          <th style="text-align:center;width:15%" colspan=2>ส่งมอบงานครั้งนี้</th>
          <th style="text-align:center;width:15%" colspan=2>รวมงานที่ส่งแล้ว (รวมครั้งนี้)</th>

        </tr>
        <tr>
          <th style="text-align:center;width:1%">จำนวน</th>
          <th style="text-align:center;width:1%">หน่วย</th>
          <th style="text-align:center;width:6%">ค่าอุปกรณ์/หน่วย</th>
          <th style="text-align:center;width:6%">ค่าขนส่ง/หน่วย</th>
          <th style="text-align:center;width:6%">ค่าติดตั้งทดสอบ/หน่วย</th>
          <th style="text-align:center;width:6%">เป็นเงิน</th>

          <th style="text-align:center;width:3%">จำนวน</th>
          <th style="text-align:center;width:7%">เป็นเงิน</th>

          <th style="text-align:center;width:3%">จำนวน</th>
          <th style="text-align:center;width:7%">เป็นเงิน</th>
         
        </tr>
      </thead>
      <tbody>';                       

        foreach ($boq as $key => $value) {
          echo '<tr>';
            echo '<td style="text-align:center">'.$value->no.'</td>';
             if($value->type==1 || $value->type==2)
                $detail = '<b>'.$value->detail.'</b>';
            else if($value->indent!="")
                $detail = $value->indent.'&nbsp;&nbsp;'.$value->detail;  
            else 
                $detail = '&nbsp;&nbsp;&nbsp;'.$value->detail;  
            echo '<td>'.$detail.'</td>';
            echo '<td style="text-align:center;width:5%">'.$value->amount.'</td>';
            echo '<td style="text-align:center;width:5%">'.$value->unit.'</td>';
            $price_item = is_numeric($value->price_item) ? number_format($value->price_item,0) : $value->price_item;
            $price_trans = is_numeric($value->price_trans) ? number_format($value->price_trans,0) : $value->price_trans;
            $price_install = is_numeric($value->price_install) ? number_format($value->price_install,0) : $value->price_install;
            echo '<td style="text-align:center;width:10%">'.$price_item.'</td>';
            echo '<td style="text-align:center;width:10%">'.$price_trans.'</td>';
            echo '<td style="text-align:center;width:10%">'.$price_install.'</td>';
            $price_item_all = '';
            if(!empty($value->amount) )
            { 
                $price_item = is_numeric($value->price_item) ? $value->price_item : 0;
                $price_trans = is_numeric($value->price_trans) ? $value->price_trans : 0;
                $price_install = is_numeric($value->price_install) ? $value->price_install : 0;
                
                $price_item_all = ($price_item + $price_trans + $price_install) * $value->amount;
                if(!is_numeric($value->price_item) && !is_numeric($value->price_trans) && !is_numeric($value->price_install))
                  echo '<td style="text-align:center">-</td>';
                else  
                   echo '<td style="text-align:center">'.number_format($price_item_all,0).'</td>';
            }
            else
                echo '<td style="text-align:center">'.$price_item_all.'</td>';

            //amount current payment
            $curr_payment = Yii::app()->db->createCommand()
                            ->select('*')
                            ->from('payment')
                            ->where("pay_type=3 AND item_id='".$value->id."' AND vc_id='".$model->id."' AND pay_no =".$i)
                            ->queryAll();

            if(!empty($curr_payment))
            {

              echo '<td style="text-align:center">'.$curr_payment[0]['amount'].'</td>';
              $price_item_all = ($price_item + $price_trans+$price_install) * $curr_payment[0]['amount'];
               if(!is_numeric($value->price_item) && !is_numeric($value->price_trans) && !is_numeric($value->price_install))
                  echo '<td style="text-align:center">-</td>';
                else 
                  echo '<td style="text-align:center">'.number_format($price_item_all,0).'</td>';

            } 
            else{
              echo '<td style="text-align:center"></td>';
              echo '<td style="text-align:center"></td>';  
            }               

            //amount previous with current payment  
            $prev_payment = Yii::app()->db->createCommand()
                            ->select('SUM(amount) as amount')
                            ->from('payment')
                            ->where("pay_type=3 AND item_id='".$value->id."' AND vc_id='".$model->id."' AND pay_no <=".$i)
                            ->queryAll();     

            if(!empty($prev_payment) and $prev_payment[0]['amount']>0)
            {
              echo '<td style="text-align:center">'.$prev_payment[0]['amount'].'</td>';
              $price_item_all = ($price_item + $price_trans+$price_install) * $prev_payment[0]['amount'];
              if(!is_numeric($value->price_item) && !is_numeric($value->price_trans) && !is_numeric($value->price_install))
                  echo '<td style="text-align:center">-</td>';
              else 
                 echo '<td style="text-align:center">'.number_format($price_item_all,0).'</td>';

            } 
            else{
              echo '<td style="text-align:center"></td>';
              echo '<td style="text-align:center"></td>';  
            }                                
          echo '</tr>';
                            
        }                    
        echo '</tbody></table></div>';

      echo '</div>'; //end item-tab  
      }

      echo '<div class="tab-pane" id="fine-tab-'.$i.'">';
      echo "<h5>รายละเอียดการหัก</h5>";

      $models = FineDetail::model()->findAll();
      $listData = array();
      foreach ($models as $key => $value) {
        $listData[] = array('text'=>$value->detail,'id'=>$value->detail);
      }
      //print_r($listData);

      $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'link',
            
            'type'=>'success',
            'label'=>'เพิ่มรายการ',
            'icon'=>'plus-sign white',
            // 'url'=>array('exportBOQ'),
            'htmlOptions'=>array('class'=>'pull-right','style'=>'margin-left: 10px;margin-bottom:10px',
                  

                  'onclick'=>'js:bootbox.confirm({
                      title: "เพิ่มรายการหัก",
                      message:  $("#modal-fine").html().replace("js-exampleForm", "js-bootboxForm"),
                      callback: function (result) {
                        if(result)
                        {
                            var fine_detail = $("#fine-detail :selected", ".js-bootboxForm").val();
                            var fine_cost = $("#fine-cost", ".js-bootboxForm").val();
                            $.ajax({
                                    url: "'.$this->createUrl('fine/create').'",
                                    type: "POST",
                                    data: {id: '.$model->id.',pay_no: '.$i.', detail: fine_detail, cost: fine_cost},
                                    success: function (re) {
                                           
                                          $("#fine-grid-'.$i.'").yiiGridView("update",{});
                                    }
                                })
                               
                        }
                      }
                  });'
            ),
          )); 

      $fine_model = new Fine('search');     

          
      $this->widget('bootstrap.widgets.TbGridView',array(
        'id'=>'fine-grid-'.$i,
        'type'=>'bordered condensed',
        'dataProvider'=>$fine_model->searchByPayment($model->id,$i),
       
        'htmlOptions'=>array('style'=>'padding-top:10px;width:100%'),
          'enablePagination' => true,
          'enableSorting'=>true,
          'summaryText'=>'แสดงผล {start} ถึง {end} จากทั้งหมด {count} ข้อมูล',
          'template'=>"{items}<div class='row-fluid'><div class='span6'>{pager}</div><div class='span6'>{summary}</div></div>",
        'columns'=>array(
        
          'detail'=>array(
              'name' => 'detail',
              'class' => 'editable.EditableColumn',
              'editable' => array( //editable section
                'url' => $this->createUrl('fine/update'),
                'success' => 'js: function(response, newValue) {
                          if(!response.success) return response.msg;

                          $("#fine-grid-'.$i.'").yiiGridView("update",{});
                        }',
                'options' => array(
                  'ajaxOptions' => array('dataType' => 'json'),

                ), 
                'placement' => 'right',
              ),
              'headerHtmlOptions' => array('style' => 'width:60%;text-align:center;background-color: #f5f5f5'),                     
              'htmlOptions'=>array('style'=>'text-align:left')
            ),
          'amount'=>array(
              'name' => 'amount',
              'class' => 'editable.EditableColumn',
              'editable' => array( //editable section
                'url' => $this->createUrl('fine/update'),
                'success' => 'js: function(response, newValue) {
                          if(!response.success) return response.msg;

                          $("#fine-grid-'.$i.'").yiiGridView("update",{});
                        }',
                'options' => array(
                  'ajaxOptions' => array('dataType' => 'json'),

                ), 
                'placement' => 'right',
              ),
              'headerHtmlOptions' => array('style' => 'width:30%;text-align:center;background-color: #f5f5f5'),                     
              'htmlOptions'=>array('style'=>'text-align:right')
            ),
            array(
                'class'=>'bootstrap.widgets.TbButtonColumn',
                'headerHtmlOptions' => array('style' => 'width:10%;text-align:center;background-color: #f5f5f5'),
                'template' => '{delete}',
                'buttons'=>array(
                    'delete'=>array(
                      'url'=>'Yii::app()->createUrl("fine/delete", array("id"=>$data->id))',  

                    ))
            
              ),
        ),
      ));
      echo '</div>'; //end fine-tab
      ?>
      </div> <!-- tab-end content-payment-->
	 </div> <!-- end tab-payment --> 
     
    <?php

      }//end for loop payment

    ?>       
</div>	

<script type="text/javascript">
  $("#exportButton").on('click',function(e){
       alert("click") 
  });
</script>



<div id="modal-content" class="hide">
    <div id="modal-body">
      <form>
        <h4>สร้างแบบฟอร์มรายละเอียดขออนุมัติเบิกจ่าย</h4>
        <p>**เมื่อสร้างแบบฟอร์มแล้ว จะไม่สามารถแก้ไขรายละเอียดค่าใช้จ่ายได้**</p>
        <select id="form_export" class="span5">
        <option value="1">แบบฟอร์ม 1 (ค่าอุปกรณ์ค่าขนส่ง แยกกันกับค่าติดตั้งทดสอบ)</option>
        <option value="2">แบบฟอร์ม 2 (ค่าอุปกรณ์ ค่าขนส่ง และค่าติดตั้งทดสอบ เบิกรวมกัน)</option>
      </select>
      </form>
      
    </div>

    <div id="modal-fine">
       <form class="js-exampleForm">
        <label for='fine-detail'>รายละเอียด</label>
        <?php
        $models = FineDetail::model()->findAll();

         // format models resulting using listData     
         $listData =  CHtml::listData($models,'detail', 'detail'); 
         echo CHtml::dropDownList('fine-detail', '', $listData);
        ?>
        <!-- <input type="text" name="fine-detail" id="fine-detail" class="span4"> -->
        <label for='fine-cost'>ค่าปรับ</label>
        <input type="text" name="fine-cost" id="fine-cost">
       </form> 
    </div>

</div>    
