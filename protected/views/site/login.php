<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Login';

?>
<style type="text/css">
  body{
    
     /*background: #111111 ;   */
     width:100%;
     min-height:340px;
     position: relative;
     background: url(../images/bg.jpg) no-repeat center center;
     background-size: cover;
     font: 16px/1.6em 'Boon400',sans-serif;
     font-weight: normal;
   }  

   #login-box{
      /*opacity: 0.6;
      filter: alpha(opacity=60);*/
      background-color: rgba(231, 231, 231, 0.2);
   }

   #login-box div{
     font-weight: bold;
     color: #000000;
   }
</style>

<center>
<div class="container-fluid well" id="login-box" style="width:350px;margin-top:160px;">

 <div class="row-fluid">



          <!-- <div class="span4" ><img src="../dist/img/logo.png" ></div> -->
          
          <div class="span12" >
              <div class="row-fluid">
              <?php /** @var BootActiveForm $form */
                    echo  CHtml::image(Yii::app()->getBaseUrl() . '../images/engineer.png', 'Logo', array('width' => '120', 'height' => '120','border-radius'=> '100%'));

                    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                        'id'=>'verticalForm',
                        'htmlOptions'=>array('align'=>'left'),
                    )); ?>

                    <?php 
                    echo "<span style='display: block;margin-bottom: 5px;text-align:left'><i class='icon-user'></i>  ชื่อผู้ใช้งาน</span>";
                    echo $form->textFieldRow($model, 'username', array('class'=>'span12','labelOptions' => array('label' => false))); ?>
                    <?php 
                    echo "<span style='display: block;margin-bottom: 5px;text-align:left'><i class='icon-lock'></i>  รหัสผ่าน</span>";
                    echo $form->passwordFieldRow($model, 'password', array('class'=>'span12','labelOptions' => array('label' => false))); ?>

                    <?php $this->widget('bootstrap.widgets.TbButton', array('htmlOptions'=>array('class'=>'pull-right'),'buttonType'=>'submit','type'=>'primary', 'label'=>'Login')); ?>

                    <?php $this->endWidget(); ?>
              </div>    
          </div>
        </div>
</div>
</center>