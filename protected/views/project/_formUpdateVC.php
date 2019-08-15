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
              <?php echo $form->textAreaRow($model,'place',array('rows'=>2, 'cols'=>30, 'class'=>'span12')); ?>
            </div>

            <div class="row-fluid">
              <div class="span4">
                <?php echo $form->textFieldRow($model,'contract_no',array('class'=>'span12')); ?>
              </div>  
              <div class="span4">
                <?php echo $form->textFieldRow($model,'budget',array('class'=>'span12')); ?>
              </div>  

              <div class="span4">
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
                    window.location.reload();
                    
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
     
      <!-- <button class="btn btn-info" type="button" id="exportButton" style="margin-top: -10px;"><i class="icon-excel icon-white"></i> Export</button> -->
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
        $.ajax({
            url: '../importBOQ',
            type: 'POST',
            cache: false,
            data: fd,
            processData: false,
            contentType: false,
            success: function (response) { 
               $(content).html(response)
            },
            error: function () {
               
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



     ?>   
     <h4>รายละเอียดค่าใช้จ่าย งวดที่ <?php echo $i;?></h4>
    <hr style="margin-top:5px; ">

    <?php 
    echo '<form method="POST" action="" id="form-import-boq-'.$i.'" enctype="multipart/form-data" class="pull-right">';  ?>
    <div class="form-group">
      <div class="input-prepend input-file">
        <button class="btn btn-default btn-choose" type="button">Browse</button>
        <input type="text" name="filetext"  class="form-control" placeholder='Choose a file...' />
       
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
            'url'=>array('submitBOQ'),
            'htmlOptions'=>array('class'=>'pull-right','style'=>'margin-left: 10px'),
          )); 
      ?>
   
    </div>  
  </form>

     <?php  echo '<div id="boq-content-'.$i.'" style=""> </div>'; ?>  

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
</div>    
