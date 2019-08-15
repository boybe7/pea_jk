<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
   
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />


	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<script type="text/javascript" src="/pea_track/themes/bootstrap/js/jquery.yiigridview.js"></script>
	<?php 
       /* Yii::app()->getClientScript()->reset(); 
        Yii::app()->bootstrap->register();   */

        
        Yii::app()->bootstrap->init();
        //$cs = Yii::app()->clientScript;
        //$cs->registerScriptFile(Yii::app()->theme->getBaseUrl().'/js/jquery.yiigridview.js');
  ?>
</head>
<link rel="shortcut icon" href="/pea_track/favicon.ico">
<style>

.dropdown-menu {
   /*background-color: #33aa33;*/
   
}
.navbar .nav > li > .dropdown-menu:after {
  /*border-bottom: 6px solid #33aa33;*/
}
.dropdown-menu > li > a {
  /*color: white;*/

}
.dropdown-menu > li > a:hover {
  background-color: white;
  
}

.dropdown-menu > li > a:hover,
.dropdown-menu > li > a:focus,
.dropdown-submenu:hover > a,
.dropdown-submenu:focus > a {
  color: #ffffff;
  text-decoration: none;
  background-color: #62c462;
  background-image: -moz-linear-gradient(top, #62c462, #51a351);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#62c462), to(#51a351));
  background-image: -webkit-linear-gradient(top, #62c462, #51a351);
  background-image: -o-linear-gradient(top,  #62c462, #51a351);
  background-image: linear-gradient(to bottom ,#62c462, #51a351);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff62c462', endColorstr='#ff51a351', GradientType=0);
}

.dropdown-menu > .active > a,
.dropdown-menu > .active > a:hover,
.dropdown-menu > .active > a:focus {
  color: #ffffff;
  text-decoration: none;
  background-color: #62c462;
  background-image: -moz-linear-gradient(top, #62c462, #51a351);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#62c462), to(#51a351));
  background-image: -webkit-linear-gradient(top, #62c462, #51a351);
  background-image: -o-linear-gradient(top,  #62c462, #51a351);
  background-image: linear-gradient(to bottom ,#62c462, #51a351);
  background-repeat: repeat-x;
  outline: 0;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff62c462', endColorstr='#ff51a351', GradientType=0);
}

.navbar .brand {
display: block;
float: left;
padding: 10px 20px 10px;
/*margin-left: -20px;*/
font-size: 20px;
font-weight: 200;
color: #fff;
text-shadow: 0 0 0 #ffffff;
}        

        
.navbar .nav > li > a{
float: none;
padding: 20px 15px 10px;
color: #fff;
text-decoration: none;
text-shadow: 0 0 0 #ffffff;
height: 35px;
}       

.navbar .nav > li > a >  i{
float: none;
/*margin-top: 5px;*/
}

.navbar .btn, .navbar .btn-group {
    margin-top: 15px;
}
.navbar .nav  > li > a:hover, .nav > li > a:focus {
    float: none;
    padding: 20px 15px 10px;
    color: #fff;
    text-decoration: none;
    text-shadow: 0 0 0 #ffffff;
    background-color: #33aa33;
}
.navbar .nav  > .active > a, .navbar .nav > .active > a:hover, .navbar .nav > .active > a:focus {
    color: #ffffff;
    background-color: #499249;

}       
 .navbar-inner {
	background-color:#229922;
    color:#ffffff;
  	border-radius:0;
}
  
.navbar-inner .navbar-nav > li > a {
  	color:#fff;
  	padding-left:20px;
  	padding-right:20px;
}
.navbar-inner .navbar-nav > .active > a, .navbar-nav > .active > a:hover, .navbar-nav > .active > a:focus {
    color: #ffffff;
     background-color: #33aa33;
	background-color:transparent;
}
      
.navbar-inner .navbar-nav > li > a:hover, .nav > li > a:focus {
    text-decoration: none;
    background-color: #33aa33;
}
      
.navbar-inner .navbar-brand {
  	color:#eeeeee;
}
.navbar-inner .navbar-toggle {
  	background-color:#eeeeee;
}
.navbar-inner .icon-bar {
  	background-color:#33aa33;
}

.nav .open>a, .nav .open>a:hover, .nav .open>a:focus {
	background-color: #33aa33;
	border-color: #428bca;
}

.navbar-inner {
    min-height: 0px;
}


nav .badge {
  background: #67c1ef;
  border-color: #30aae9;
  background-image: -webkit-linear-gradient(top, #acddf6, #67c1ef);
  background-image: -moz-linear-gradient(top, #acddf6, #67c1ef);
  background-image: -o-linear-gradient(top, #acddf6, #67c1ef);
  background-image: linear-gradient(to bottom, #acddf6, #67c1ef);
}

nav .badge.green {
  background: #77cc51;
  border-color: #59ad33;
  background-image: -webkit-linear-gradient(top, #a5dd8c, #77cc51);
  background-image: -moz-linear-gradient(top, #a5dd8c, #77cc51);
  background-image: -o-linear-gradient(top, #a5dd8c, #77cc51);
  background-image: linear-gradient(to bottom, #a5dd8c, #77cc51);
}

nav .badge.yellow {
  background: #faba3e;
  border-color: #f4a306;
  background-image: -webkit-linear-gradient(top, #fcd589, #faba3e);
  background-image: -moz-linear-gradient(top, #fcd589, #faba3e);
  background-image: -o-linear-gradient(top, #fcd589, #faba3e);
  background-image: linear-gradient(to bottom, #fcd589, #faba3e);
}

nav .badge.red {
  background: #fa623f;
  border-color: #fa5a35;
  background-image: -webkit-linear-gradient(top, #fc9f8a, #fa623f);
  background-image: -moz-linear-gradient(top, #fc9f8a, #fa623f);
  background-image: -o-linear-gradient(top, #fc9f8a, #fa623f);
  background-image: linear-gradient(to bottom, #fc9f8a, #fa623f);
}


@font-face {
    font-family: 'Boon400';
    src: url('/pea_track/fonts/boon-400.eot');
    src: url('/pea_track/fonts/boon-400.eot') format('embedded-opentype'),
         url('/pea_track/fonts/boon-400.woff') format('woff'),
         url('/pea_track/fonts/boon-400.ttf') format('truetype'),
         url('/pea_track/fonts/boon-400.svg#Boon400') format('svg');
}

@font-face {
    font-family: 'Boon700';
    src: url('/pea_track/fonts/boon-700.eot');
    src: url('/pea_track/fonts/boon-700.eot') format('embedded-opentype'),
         url('/pea_track/fonts/boon-700.woff') format('woff'),
         url('/pea_track/fonts/boon-700.ttf') format('truetype'),
         url('/pea_track/fonts/boon-700.svg#Boon700') format('svg');
}

@font-face {
    font-family: 'THSarabunPSK';
    src: url('/pea_track/fonts/thsarabunnew-webfont.eot');
    src: url('/pea_track/fonts/thsarabunnew-webfont.eot') format('embedded-opentype'),
         url('/pea_track/fonts/thsarabunnew-webfont.woff') format('woff'),
         url('/pea_track/fonts/thsarabunnew-webfont.ttf') format('truetype');
       
}

body{
    
    
     width:100%;
     min-height:340px;
     position: relative;
     /*background: url(../images/intro-bg.jpg) no-repeat center center;*/
     background-size: cover;
     font: 14px/1.4em 'Boon400',sans-serif;
     font-weight: normal;
}

input, select, textarea {
font-family: 'Boon400', sans-serif;
}

h1,h2,h3,h4{
        font-family: 'Boon700',sans-serif;
        font-weight: normal;
}

table tr .tr_white {
  background-color: #ffffff;
}

select, input[type="file"] {
    height: 30px;
    line-height: 30px;
}

.well-yellow {
    min-height: 20px;
    padding: 19px;
    margin-bottom: 20px;
    background-color: #f8c10621;
    border: 1px solid #e3e3e3;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
    -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
}

.well-blue {
    min-height: 20px;
    padding: 19px;
    margin-bottom: 20px;
    background-color: #c5d7df;
    border: 1px solid #e3e3e3;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
    -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
}
/*scrollable*/
 .ui-autocomplete {
            max-height: 200px;
            max-width: 800px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
            /* add padding to account for vertical scrollbar */
            padding-right: 20px;
        } 
</style>     
     
<body class="body">

<?php 
 //echo Yii::app()->theme->getBaseUrl(); 



if(!Yii::app()->user->isGuest)
{
  
  Yii::import('application.controllers.NotifyController');
  ///$num = notify::model()->getNotify();
  
  //$num = NotifyController::gnotify(1); // preparing object
  $Criteria = new CDbCriteria();
  $Criteria->condition = 'status=1';
  $num = count(Notify::model()->findAll($Criteria));
  
 $badge= '';
 ///$num = 0;
 if($num>0) 
  $badge=$this->widget('bootstrap.widgets.TbBadge', array(
    'type'=>'warning',
    'label'=>$num,
  ), true);

   $this->widget('bootstrap.widgets.TbNavbar',array(
    'fixed'=>'top',
    'collapse'=>true,    
    'htmlOptions'=>array('class'=>'noPrint'),
    'brand' =>  CHtml::image(Yii::app()->getBaseUrl() . '../images/pea_logo.png', 'Logo', array('width' => '220', 'height' => '30')),
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'encodeLabel'=>false,
            'items'=>array(
                array('label'=>'หน้าแรก','icon'=>'home', 'url'=>array('/site/index')),
                // array('label'=>'โครงการ','icon'=>'flag', 'url'=>array('/project/index')),
                array('label'=>'โครงการ ','icon'=>'flag', 'url'=>'#','items'=>array(
                     array('label'=>'ข้อมูลโครงการ', 'url'=>array('/project/index')),
                     array('label'=>'บันทึกค่าบริหารโครงการ', 'url'=>array('/managementCost/index'),'visible'=>!Yii::app()->user->isExecutive()),
                     array('label'=>'บันทึกความก้าวหน้าสัญญาหลัก', 'url'=>array('/paymentProjectContract/index'),'visible'=>!Yii::app()->user->isExecutive()),
                     array('label'=>'บันทึกความก้าวหน้าสัญญาจ้างช่วง/ซื้อ', 'url'=>array('/paymentOutsourceContract/index'),'visible'=>!Yii::app()->user->isExecutive()),
                     array('label'=>'บันทึกค่าบริหารโครงการ (SAP)', 'url'=>array('/managementCostSap/index'),'visible'=>Yii::app()->user->isExecutive()),
                      array('label'=>'หนังสือขอคืนค้ำประกันสัญญา', 'url'=>array('/report/guarantee'),'visible'=>!Yii::app()->user->isExecutive()),
                     
                    ),
                ),
                array('label'=>'รายงาน ','icon'=>'list-alt', 'url'=>'#','items'=>array(
                     array('label'=>'project progress report', 'url'=>array('/report/progress')),
                     array('label'=>'project summary report', 'url'=>array('/report/summary')),
                     array('label'=>'BSC report', 'url'=>array('/report/bsc')),
                     array('label'=>'รายงานค่าบริหารโครงการ', 'url'=>array('/report/management')),
                    
                     array('label'=>'รายงานสรุปรายได้/ค่าใช้จ่าย', 'url'=>array('/report/cashflow'),'visible'=>Yii::app()->user->isExecutive()),
                     array('label'=>'รายงานสรุปรายได้ ค่าใช้จ่ายงานบริการวิศวกรรม', 'url'=>array('/report/service'),'visible'=>Yii::app()->user->isExecutive()),
                     array('label'=>'สรุปงานรายรับ-รายจ่ายงานโครงการ', 'url'=>array('/report/summaryCashflow'),'visible'=>Yii::app()->user->isExecutive()),
                      array('label'=>'รายงานงบกำไรขาดทุน', 'url'=>array('/report/statement'),'visible'=>Yii::app()->user->isExecutive()),
                    
                                                                 

                    ),
                ),
                array('label'=>'แจ้งเตือน '.$badge,'icon'=>'comment', 'url'=>array('/notify/index'), 'visible'=>!Yii::app()->user->isExecutive()),
                array('label'=>'คู่สัญญา','icon'=>'briefcase', 'url'=>array('/vendor/admin'), 'visible'=>Yii::app()->user->isAdmin()),
                array('label'=>'ประเภทงาน','icon'=>'briefcase', 'url'=>array('/workcategory/admin'), 'visible'=>Yii::app()->user->isAdmin()),
                array('label'=>'ผู้ใช้งาน','icon'=>'user', 'url'=>array('/user/index'), 'visible'=>Yii::app()->user->isAdmin()),
            ),
        ),    
        array(
            'class'=>'bootstrap.widgets.TbButtonGroup',           
            'htmlOptions'=>array('class'=>'pull-right'),
            'type'=>'success', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
            'buttons'=>array(
                    array('label'=>Yii::app()->user->title.Yii::app()->user->firstname." ".Yii::app()->user->lastname,'icon'=>Yii::app()->user->usertype, 'url'=>'#'),
                    //array('label'=>Yii::app()->user->username,'icon'=>Yii::app()->user->usertype, 'url'=>'#'),
                    array('items'=>array(
                        array('label'=>'เปลี่ยนรหัสผ่าน','icon'=>'cog', 'url'=>array('/user/password/'.Yii::app()->user->ID), 'visible'=>!Yii::app()->user->isGuest),
                        '---',
                        array('label'=>'ออกจากระบบ','icon'=>'off', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
                    )),
                ),
            
        ),
        ),
    ));
}
else if(Yii::app()->user->isAdmin())
{
    
   $this->widget('bootstrap.widgets.TbNavbar',array(
    'fixed'=>'top',
    'collapse'=>true,    
    'htmlOptions'=>array('class'=>'noPrint'),
    'brand' =>  CHtml::image(Yii::app()->getBaseUrl() . '../images/pea_logo.png', 'Logo', array('width' => '260', 'height' => '30')),
    'items'=>array(
       
        array(
            'class'=>'bootstrap.widgets.TbButtonGroup',           
            'htmlOptions'=>array('class'=>'pull-right'),
            'type'=>'success', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
            'buttons'=>array(
                    array('label'=>Yii::app()->user->title.Yii::app()->user->firstname."   ".Yii::app()->user->lastname,'icon'=>Yii::app()->user->usertype, 'url'=>'#'),
                    array('items'=>array(
                        array('label'=>'เปลี่ยนรหัสผ่าน','icon'=>'cog', 'url'=>array('/user/password/'.Yii::app()->user->ID), 'visible'=>!Yii::app()->user->isGuest),
                        '---',
                        array('label'=>'ออกจากระบบ','icon'=>'off', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
                    )),
                ),
            
        ),
        ),
    ));
    
}
else{
    $this->widget('bootstrap.widgets.TbNavbar',array(
    'fixed'=>'top',
    'collapse'=>true,    
    'htmlOptions'=>array('class'=>'noprint'),
    'brand' =>  CHtml::image(Yii::app()->getBaseUrl() . '../images/pea_logo.png', 'Logo', array('width' => '260', 'height' => '30')),
   
    ));
}   
 
   ?>

    <div class="container" id="page" style="padding-top: 70px">

	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>


</div><!-- page -->

</body>
</html>
