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
      if(empty($model->id))
      {
        echo '<li class="active"><a href="#projTab" data-toggle="tab">สัญญา</a></li>';
      
      }
      else{
        echo '<li ><a href="#projTab" data-toggle="tab">สัญญา</a></li>';
        echo '<li class="active"><a href="#boqTab" data-toggle="tab">BOQ</a></li>';
      }

   ?>
</ul>
<div class="tab-content   well-tab">
    <!------  Project Tab ------------>
    <?php
      if(empty($model->id))
         echo '<div class="tab-pane active" id="projTab">';
      else
         echo '<div class="tab-pane" id="projTab">'; 
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
              
                  <label for='owner'>ผู้ว่าจ้าง</label>
                  <?php 
                     echo "<input type='text' class='span12' id='owner' value='".Vendor::model()->findByPk(Project::model()->findByPk($model->proj_id)->owner_id)->v_name."' readonly>"; 
                  ?>
              
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
                      $modelMember = ProjectMember::model()->findAll('proj_id =:id AND type=0', array(':id' => $model->id));
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
                      $member = ProjectMember::model()->findAll('proj_id =:id AND type=1', array(':id' => $model->id));
                      
                   }

                   for ($i=0; $i < 5; $i++) { 
                    echo '<div class="row-fluid">
                             <div class="span6">
                                <label for="commitee">กรรมการ</label>';
               
                
                                $value = isset($member[$i]) ? $member[$i]->name : "";
                                echo ' <input type="text" class="span12" name="commitee[]"  value='.$value.'>';
                                
                      echo '</div>
                            <div class="span4"> 
                              <label for="commitee_position">ตำแหน่ง</label>';
               
                             $value = isset($member[$i]) ? $member[$i]->position : "";
                             echo ' <input type="text" class="span12" name="commitee_position[]"  value='.$value.'>';
                
                      echo '</div>  
                      </div>';

                               }
            


            ?>


            <?php
                   $value = "";
                   if(!empty($model->id))
                   {
                      $modelMember = ProjectMember::model()->findAll('proj_id =:id AND type=2', array(':id' => $model->id));
                      
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
                      $modelMember = ProjectMember::model()->findAll('proj_id =:id AND type=3', array(':id' => $model->id));
                      
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

              if(empty($model->id))
              {
                $this->widget('bootstrap.widgets.TbButton', array(
                  'buttonType'=>'submit',
                  'type'=>'primary',
                  'label'=>'บันทึก',
                ));
              }
            
             ?>
          </div>
                
        </div>
            <?php $this->endWidget(); ?>
      
  
    
    </div> <!-- end tab-pan -->


    <!------  BOQ Tab ------------>
   
    <?php
      if(!empty($model->id))
         echo '<div class="tab-pane active" id="boqTab">';
      else
         echo '<div class="tab-pane" id="boqTab">'; 
    ?>   
    <h4>รายละเอียดค่าใช้จ่าย </h4>
    <hr style="margin-top:5px; ">
        
    <!-- <form name="boq-form" id="boq-form">
        <?php echo '<input type="hidden" name="Boq[proj_id]" value="'.$model->id.'">';?>
      <div class="row-fluid">
        <div class="span1">
          <label for="Boq[no]">ลำดับที่</label>
          <input type="text" class="span12" name="Boq[no]"> 
        </div>
        <div class="span7">
          <label for="Boq[detail]">รายละเอียด</label>
          <input type="text" class="span12" name="Boq[detail]"> 
        </div>
        <div class="span1">
          <label for="Boq[amount]">จำนวน</label>
          <input type="text" class="span12" name="Boq[amount]"> 
        </div>
        <div class="span2">
          <label for="Boq[unit]">หน่วย</label>
          <input type="text" class="span12" name="Boq[unit]"> 
        </div>
        <div class="span1">
          <?php
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'link',
                
                'type'=>'success',
                'label'=>'เพิ่ม',
                'icon'=>'plus-sign white',
                
                'htmlOptions'=>array(
                  'class'=>'pull-right',
                  'style'=>'margin-top:22px',
                  'id'=>'addBOQ'
                ),
            )); 

          ?> 
        </div>
      </div>
    </form> -->

      <?php

        //$modelBOQ = Boq::model()->findAll('proj_id =:id', array(':id' => $model->id));
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
                    'class' => 'editable.EditableColumn',
                    'filter'=>CHtml::activeTextField($modelBOQ, 'no',array("placeholder"=>"ค้นหาตาม".$modelBOQ->getAttributeLabel("no"))),
                  'headerHtmlOptions' => array('style' => 'width:5%;text-align:center;background-color: #f5f5f5'),                     
                  'htmlOptions'=>array('style'=>'text-align:center')
                ),
              'detail'=>array(
                    'name' => 'detail',
                    'class' => 'editable.EditableColumn',
                    'filter'=>CHtml::activeTextField($modelBOQ, 'detail',array("placeholder"=>"ค้นหาตาม".$modelBOQ->getAttributeLabel("detail"))),
                  'headerHtmlOptions' => array('style' => 'width:65%;text-align:center;background-color: #f5f5f5'),                     
                  'htmlOptions'=>array('style'=>'text-align:left;padding-left:10px;')
                ),
              
              'amount'=>array(
                  'name' => 'amount',
                  'class' => 'editable.EditableColumn',
                  'editable' => array( //editable section
                    
                    'title'=>'แก้ไขจำนวน',
                    'url' => $this->createUrl('boq/update'),
                    'success' => 'js: function(response, newValue) {
                              if(!response.success) return response.msg;

                              $("#boq-grid").yiiGridView("update",{});
                            }',
                    'options' => array(
                      'ajaxOptions' => array('dataType' => 'json'),

                    ), 
                    'placement' => 'right',
                   
                  ),
                  'headerHtmlOptions' => array('style' => 'width:10%;text-align:center;background-color: #f5f5f5'),                     
                  'htmlOptions'=>array('style'=>'text-align:center')
                ),
                'unit'=>array(
                  
                  'name'=>'unit',
                  'class' => 'editable.EditableColumn',
                  'editable' => array( //editable section
                    
                    'title'=>'แก้ไขหน่วย',
                    'url' => $this->createUrl('boq/update'),
                    'success' => 'js: function(response, newValue) {
                              if(!response.success) return response.msg;

                              $("#boq-grid").yiiGridView("update",{});
                            }',
                    'options' => array(
                      'ajaxOptions' => array('dataType' => 'json'),

                    ), 
                    'placement' => 'right',
                   
                  ),
                  'headerHtmlOptions' => array('style' => 'width:12%;text-align:center;background-color: #f5f5f5'),                     
                  'htmlOptions'=>array('style'=>'text-align:center')
                ),
              

              array(
                'class'=>'bootstrap.widgets.TbButtonColumn',
                'headerHtmlOptions' => array('style' => 'width:8%;text-align:center;background-color: #f5f5f5'),
                'template' => '{delete}',
                'buttons'=>array(
                    'delete'=>array(
                      'url'=>'Yii::app()->createUrl("boq/delete", array("id"=>$data->id))',  

                    ))
            
              ),
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
        
    </div> <!-- end tab-pan -->
  
</div>  

<script type="text/javascript">

  $("#addBOQ").click(function(e){
      
    
      $.ajax({
         type: "POST",
         url: "../../boq/createAjax",
         dataType:"json",
         data: $("#boq-form").serialize(),
        success:function(response){
              jQuery.fn.yiiGridView.update("boq-grid");

              $('#boq-form')[0].reset();
              
        }

      });
  });
</script>