<?php
$this->breadcrumbs=array(
	'Payment Details'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List PaymentDetail','url'=>array('index')),
	array('label'=>'Create PaymentDetail','url'=>array('create')),
	array('label'=>'View PaymentDetail','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage PaymentDetail','url'=>array('admin')),
);
?>

<h1>Update PaymentDetail <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>