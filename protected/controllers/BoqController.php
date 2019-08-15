<?php

class BoqController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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
				'actions'=>array('create','createAjax','update','importExcel','validateFileImport'),
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
		$model=new Boq;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Boq']))
		{
			$model->attributes=$_POST['Boq'];

			print_r($model);
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function actionCreateAjax()
	{
		$model=new Boq;

		$message = "";
		if(isset($_POST['Boq']))
		{
			$model->attributes=$_POST['Boq'];
		    $model->amount = $_POST['Boq']['amount'];
			$model->last_update = (date("Y")).date("-m-d H:i:s");
			
			if($model->save())
			    $message = "success";
			else
				$message = "fail"; 
		}

	

		echo json_encode($message);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate()
	{
		$es = new EditableSaver('Boq');
	    try {

	    	
	    	$es->update();
	    } catch(CException $e) {
	    	echo CJSON::encode(array('success' => false, 'msg' => $e->getMessage()));
	    	return;
	    }
	    echo CJSON::encode(array('success' => true));
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
		$dataProvider=new CActiveDataProvider('Boq');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Boq('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Boq']))
			$model->attributes=$_GET['Boq'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Boq::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='boq-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionImportExcel()
	{
		Yii::import('ext.phpexcel.XPHPExcel');    
		$objPHPExcel= XPHPExcel::createPHPExcel();
		
		header('Content-type: text/plain');

		$sheet_no = 1;
		
	
		if(isset($_FILES['fileupload'])){
					$name = $_FILES['fileupload']['name'];
					$type = $_FILES['fileupload']['type'];
					$tmp_name = $_FILES['fileupload']['tmp_name'];
					$extension = pathinfo($name,PATHINFO_EXTENSION);


					$vc_id = $_POST['vc_id'];

					if(in_array($extension, array('xls','xlsx')))
					{
						$objReader = $extension==='xls' ? PHPExcel_IOFactory::createReader('Excel5') : PHPExcel_IOFactory::createReader('Excel2007');

					
						move_uploaded_file($tmp_name,Yii::app()->basePath."/" . $name);

						$objPHPExcel = $objReader->load(Yii::app()->basePath."/" . $name);

					    $transaction=Yii::app()->db->beginTransaction();

					    //delete old data
					    Yii::app()->db->createCommand('DELETE FROM boq WHERE vc_id='.$vc_id)->execute();

				    try {	
							for ($i=1; $i < $objPHPExcel->getSheetCount(); $i++) { 
								
								$sheet_no = $i;
								$worksheet  = $objPHPExcel->setActiveSheetIndex($sheet_no);

								$model_list = array();


								//part header
								$part_header = $worksheet->getCell("A1")->getCalculatedValue();
								if($part_header!="")
								{	
									$model=new Boq;
									$model->vc_id = $vc_id;
									$model->detail = $part_header;
									$model->type = 2;
									$model->save();
									$model_list[] = $model;
								}

								//start read details
								$row = 7;

								do{
									$no = $worksheet->getCell("A".$row)->getCalculatedValue();
									if(stristr($no, 'รวมเป็น') !== false){
										break;
									}
									$indent = $worksheet->getCell("B".$row)->getCalculatedValue();
									$have_indent = false;

									echo $row."==========<br>";
									
									if(trim($indent)=="-")
									{
										$have_indent = true;
										$detail = $worksheet->getCell("C".$row)->getCalculatedValue();
										$amount = $worksheet->getCell("E".$row)->getCalculatedValue();
										$unit = $worksheet->getCell("F".$row)->getCalculatedValue();
										$price_item = $worksheet->getCell("G".$row)->getCalculatedValue();
										$price_trans = $worksheet->getCell("I".$row)->getCalculatedValue();
										$price_trans = empty($price_trans) ? $price_item : $price_trans;
										$price_install = $worksheet->getCell("K".$row)->getCalculatedValue();
										$price_install = empty($price_install) ? $price_trans : $price_install;
										$type = -1;

										echo "indent:".$detail.":".$price_item.":".$price_trans.":".$price_install;
				
									}
									else if($indent!=""){
										$detail = $indent;
										$amount = $worksheet->getCell("E".$row)->getCalculatedValue();
										$unit = $worksheet->getCell("F".$row)->getCalculatedValue();
										$price_item = $worksheet->getCell("G".$row)->getCalculatedValue();
										$price_trans = $worksheet->getCell("I".$row)->getCalculatedValue();
										$price_trans = empty($price_trans) ? $price_item : $price_trans;
										$price_install = $worksheet->getCell("K".$row)->getCalculatedValue();
										$price_install = empty($price_install) ? $price_trans : $price_install;
										$type = 1;
										echo "type1:".$detail.":".$price_item.":".$price_trans.":".$price_install;
				
									}
									else{
										$detail = $worksheet->getCell("C".$row)->getCalculatedValue();
										$amount = $worksheet->getCell("E".$row)->getCalculatedValue();
										$unit = $worksheet->getCell("F".$row)->getCalculatedValue();
										$price_item = $worksheet->getCell("G".$row)->getCalculatedValue();
										$price_trans = $worksheet->getCell("I".$row)->getCalculatedValue();
										$price_trans = empty($price_trans) ? $price_item : $price_trans;
										$price_install = $worksheet->getCell("K".$row)->getCalculatedValue();
										$price_install = empty($price_install) ? $price_trans : $price_install;
										$type = 0;

										echo "type0:".$detail.":".$price_item.":".$price_trans.":".$price_install;
									}	

									$model=new Boq;
									$model->no = $no;
									$model->vc_id = $vc_id;
									$model->detail = $detail;
									$model->type = $type;
									$model->amount = $amount;
									$model->unit = $unit;
									$model->price_item = $price_item;
									$model->price_trans = $price_trans;
									$model->price_install = $price_install;
									$model->save();
									$model_list[] = $model;

									$row++;
								
								}while ( stristr($no, 'รวมเป็น') === false && $row <1000);


							}	


							$transaction->commit();

						}
						catch(Exception $e)
				 		{
				 			$transaction->rollBack();
				 			Yii::trace(CVarDumper::dumpAsString($e->getMessage()));
				 	        	//you should do sth with this exception (at least log it or show on page)
				 	        	Yii::log( 'Exception when saving data: ' . $e->getMessage(), CLogger::LEVEL_ERROR );
				 
						}	 		

						unlink(Yii::app()->basePath."/" . $name);

					}	
		}

		exit;
	}

	public function actionValidateFileImport()
	{
		Yii::import('ext.phpexcel.XPHPExcel');    
		$objPHPExcel= XPHPExcel::createPHPExcel();

		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$objPHPExcel = $objReader->load(Yii::app()->basePath."/../invalid_file.xlsx");

		
		//---------------------ค่าของ------------------------//
		$worksheet  = $objPHPExcel->setActiveSheetIndex(0);

		$row = 6;
		//current pay_no
		$first_index = 	$worksheet->getCell("A".$row)->getCalculatedValue();
		$boq = Boq::model()->findByPk($first_index);

		$payment = Yii::app()->db->createCommand()
			                        ->select('MAX(pay_no) as max_pay_no')
			                        ->from('payment')
			                        ->where("vc_id=".$boq->vc_id)
			                        ->queryAll();

		$pay_no_current = $payment[0]["max_pay_no"]+1;

		echo "งวดปัจจุบันคือ ".$pay_no_current."<br>"; 

		//1.check pay_no
		$pay_no = 	$worksheet->getCell("M3")->getCalculatedValue();
		echo "งวดที่ส่ง ".$pay_no."<br>";

		$invalid_file = false;
		$error_msg = array();

		if($pay_no == $pay_no_current)
		{
			
			do {
			  $index = 	$worksheet->getCell("A".$row)->getCalculatedValue();
			  //echo $index."<br>";
			  
			  //load BOQ
	          $boq = Boq::model()->findByPk($index);
	          if(!empty($boq))
	          {
	          	if(!empty($boq->amount) &&  is_numeric($boq->price_item) &&  is_numeric($boq->price_trans))
	            {
	            	//previous pay
	            	$payment = Yii::app()->db->createCommand()
			                        ->select('SUM(amount) as sum')
			                        ->from('payment')
			                        //->join('user','user_create=u_id')
			                        ->where("pay_type=0 AND item_id='".$boq->id."' AND vc_id='".$boq->vc_id."' AND pay_no <".$pay_no)
			                        ->queryAll();
			        $amount_prev = $payment[0]["sum"];
			        $amount_pay = $worksheet->getCell("L".$row)->getCalculatedValue();

			        $amount_max  =  $boq->amount - $amount_prev;
			       
			        if($amount_pay > $amount_max)
			        {
			        	echo "-------------ข้อผิดพลาด-------------- <br>";
			        	echo $boq->detail."<br>";
			        	$invalid_file = true;
			        	echo "จำนวนตามสัญญา : ".$boq->amount." | จำนวนค่าของที่เบิกแล้ว : ".$amount_prev." | จำนวนค่าของเบิกครั้งนี้ : ".$amount_pay;
			        	echo "!***เบิกค่าของเกินสัญญา*** <br>";
			        }
	            }	
	          }
	         

			  $row++;
			}while($index!="");
		}
		else{
			echo "งวดเบิกจ่ายไม่ถูกต้อง";
		}

		//---------------------ค่าติดตั้ง------------------------//
		$worksheet  = $objPHPExcel->setActiveSheetIndex(1);

		$row = 6;
		//current pay_no
		$first_index = 	$worksheet->getCell("A".$row)->getCalculatedValue();
		$boq = Boq::model()->findByPk($first_index);

		$payment = Yii::app()->db->createCommand()
			                        ->select('MAX(pay_no) as max_pay_no')
			                        ->from('payment')
			                        ->where("vc_id=".$boq->vc_id)
			                        ->queryAll();

		$pay_no_current = $payment[0]["max_pay_no"]+1;

		//echo "งวดปัจจุบันคือ ".$pay_no_current."<br>"; 

		//1.check pay_no
		$pay_no = 	$worksheet->getCell("L3")->getCalculatedValue();
		//echo "งวดที่ส่ง ".$pay_no."<br>";

		$invalid_file = false;
		$error_msg = array();

		if($pay_no == $pay_no_current)
		{
			
			do {
			  $index = 	$worksheet->getCell("A".$row)->getCalculatedValue();
			  //echo $index."<br>";
			  
			  //load BOQ
	          $boq = Boq::model()->findByPk($index);
	          if(!empty($boq))
	          {
	          	if(!empty($boq->amount) &&  is_numeric($boq->price_install))
	            {
	            	//previous pay
	            	$payment = Yii::app()->db->createCommand()
			                        ->select('SUM(amount) as sum')
			                        ->from('payment')
			                        //->join('user','user_create=u_id')
			                        ->where("pay_type=0 AND item_id='".$boq->id."' AND vc_id='".$boq->vc_id."' AND pay_no <".$pay_no)
			                        ->queryAll();
			        $item_amount_prev = $payment[0]["sum"];

	            	$payment = Yii::app()->db->createCommand()
			                        ->select('SUM(amount) as sum')
			                        ->from('payment')
			                        //->join('user','user_create=u_id')
			                        ->where("pay_type=2 AND item_id='".$boq->id."' AND vc_id='".$boq->vc_id."' AND pay_no <".$pay_no)
			                        ->queryAll();
			        $amount_prev = $payment[0]["sum"];

			        $amount_pay = $worksheet->getCell("K".$row)->getCalculatedValue();

			        $item_amount_pay = $objPHPExcel->getSheet(0)->getCell('L'.$row)->getValue();
			        $amount_max  =  ($item_amount_prev+$item_amount_pay) - $amount_prev;
			        //echo $amount_prev+$amount_pay."<br>";
			        //echo  ($item_amount_prev+$item_amount_pay) - $amount_prev."<br>";
			        if($amount_prev+$amount_pay > $boq->amount)
			        {
			        	echo "-------------ข้อผิดพลาด-------------- <br>";
			        	echo $boq->detail."<br>";
			        	$invalid_file = true;
			        	echo "จำนวนตามสัญญา : ".$boq->amount." | จำนวนค่าติดตั้งที่เบิกแล้ว : ".$amount_prev." | จำนวนค่าติดตั้งเบิกครั้งนี้ : ".$amount_pay;
			        	echo "!***ค่าติดตั้งเบิกเกินค่าของตามสัญญา*** <br>";
			        }
			        else if($amount_pay > $amount_max)
			        {
			        	echo "-------------ข้อผิดพลาด-------------- <br>";
			        	echo $boq->detail."<br>";
			        	$invalid_file = true;
			        	echo "จำนวนค่าของเบิกรวมครั้งนี้ : ".($item_amount_prev+$item_amount_pay)." | จำนวนค่าติดตั้งที่เบิกแล้ว : ".$amount_prev." | จำนวนค่าติดตั้งเบิกครั้งนี้ : ".$amount_pay;
			        	echo "!***ค่าติดตั้งเบิกเกินค่าของรวม*** <br>";
			        }

	            }	
	          }
	         

			  $row++;
			}while($index!="");
		}
		else{
			echo "งวดเบิกจ่ายไม่ถูกต้อง";
		}

	}
}
