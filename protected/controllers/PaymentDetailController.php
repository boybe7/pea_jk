<?php

class PaymentDetailController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/main';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','exportBOQ','importVendorBOQ'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		// $model=new PaymentDetail;
		// $modelProj = new Project;


		// if(isset($_POST['PaymentDetail']))
		// {
		// 	$model->attributes=$_POST['PaymentDetail'];
		// 	if($model->save())
		// 		$this->redirect(array('view','id'=>$model->id));
		// }

		// $this->render('create',array(
		// 	'model'=>$model,'modelProj'=>$modelProj,'pay_no'=>2
		// ));
		$model=new PaymentDetail;
		//find last pay_no
		$payment = Yii::app()->db->createCommand()
		                        ->select('MAX(pay_no) as max_pay_no')
		                        ->from('payment_detail')
		                        ->where("vc_id=".$_POST['id'])
		                        ->queryAll();
		$model->vc_id = $_POST['id'];
		$model->pay_no = $payment[0]['max_pay_no']+1;
		$model->form_type = $_POST['form_type'];
		$model->date_create = date("Y-m-d");

		$model->save();

		echo $model->pay_no;


	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['PaymentDetail']))
		{
			$model->attributes=$_POST['PaymentDetail'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new PaymentDetail('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['PaymentDetail']))
			$model->attributes=$_GET['PaymentDetail'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new PaymentDetail('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['PaymentDetail']))
			$model->attributes=$_GET['PaymentDetail'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function actionImportVendorBOQ()
	{
		/*$model=new PaymentDetail('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['PaymentDetail']))
			$model->attributes=$_GET['PaymentDetail'];

		$this->render('import_boq',array(
			'model'=>$model,
		));*/

		if(isset($_FILES['fileupload'])){
		
			
					$upload = true; // prevent manual input
					$name = $_FILES['fileupload']['name'];
					$type = $_FILES['fileupload']['type'];
					$tmp_name = $_FILES['fileupload']['tmp_name'];

					//move_uploaded_file($tmp_name,Yii::app()->basePath."/" . $name);

					$model=new PaymentDetail('search');
					$this->render('import_boq',array(
						'filename'=>$tmp_name,
					));
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=PaymentDetail::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='payment-detail-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	
}
