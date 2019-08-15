
<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;

$theme = Yii::app()->theme;
$cs = Yii::app()->clientScript;
$cs->registerScriptFile( $theme->getBaseUrl() . '/js/highcharts.js' );
//$cs->registerCssFile($theme->getBaseUrl() . '/css/ProgressTracker.css');

?>

<?php 

$src_img = Yii::app()->getBaseUrl() . '../images/blue.jpg';
echo '<div class="hero-unit" style="background-image: url('.$src_img.')">'; 

?>

  <h3>ยินดีต้อนรับเข้าสู่</h3>
  <h2><?php echo Yii::app()->name;  ?></h2>
  
</div>


<div id="modal-content" class="hide">
    <div id="modal-body">
<!-- put whatever you want to show up on bootbox here -->
    	<?php 
    	
   
              
    	?>
    </div>
</div>


