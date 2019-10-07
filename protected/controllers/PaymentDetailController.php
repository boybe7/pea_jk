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

		//$this->exportBOQ($model->pay_no,$model->vc_id,$model->form_type);


	}

	public function actionUpdate()
	{
		$model = PaymentDetail::model()->findAll('vc_id =:id AND pay_no=:pay_no', array(':id' =>$_POST["id"],':pay_no'=>$_POST["pay_no"]));
		$model[0]->form_type = $_POST['form_type'];
		$model[0]->date_create = date("Y-m-d");
		$model[0]->save();

		echo $model[0]->pay_no;
	}

	public function exportBOQ($pay_no,$vc_id,$form)
    {

    	    //$pay_no = $_GET['pay_no'];
		    //$vc_id = $_GET['vc_id'];
		    $model=VendorContract::model()->findByPk($vc_id);
		    if($model->lock_boq!=1)
		    {
		    	$model->lock_boq = 1;
		    	$model->save();

		    }
		    	
		    Yii::import('ext.phpexcel.XPHPExcel');    
		    $objPHPExcel= XPHPExcel::createPHPExcel();
		    $objReader = PHPExcel_IOFactory::createReader('Excel2007');

		$date = new DateTime(null, new DateTimeZone('America/Los_Angeles'));
        $current_date = $date->getTimestamp();    
		$password_lock = $current_date;   


		//-----------STYLE-----------------------//
		$borderAll = new PHPExcel_Style();
	    	$borderAll->applyFromArray(
			        array(
			       'font'  => array(
			            'name'  => 'TH SarabunPSK', 
			            'size'  => 16,			                          
			            'color' => array(
			            	'rgb'   => '000000'
			            )
			        ),
			        'fill' => array(
			        	'type' => PHPExcel_Style_Fill::FILL_SOLID,
            			'color' => array('rgb' => 'e0e0d1')
			        ),
			        'borders' => array(
				            'left'    => array(
				            	'style'   => PHPExcel_Style_Border::BORDER_THIN ,
				            	'color'   => array(
				            		'rgb'     => '000000'
				              	)
				           	),
				           	'right'    => array(
				            	'style'   => PHPExcel_Style_Border::BORDER_THIN ,
				            	'color'   => array(
				            		'rgb'     => '000000'
				              	)
				           	),
				           	'bottom'    => array(
				            	'style'   => PHPExcel_Style_Border::BORDER_THIN ,
				            	'color'   => array(
				            		'rgb'     => '000000'
				              	)
				           	),
			        	)
			));

			$borderIndent = new PHPExcel_Style();
	    	$borderIndent->applyFromArray(
			        array(
			       'font'  => array(
			            'name'  => 'TH SarabunPSK', 
			            'size'  => 16,			                          
			            'color' => array(
			            	'rgb'   => '000000'
			            )
			        ),
			        'fill' => array(
			        	'type' => PHPExcel_Style_Fill::FILL_SOLID,
            			'color' => array('rgb' => 'e0e0d1')
			        ),
			        'borders' => array(
				           
				           	'right'    => array(
				            	'style'   => PHPExcel_Style_Border::BORDER_THIN ,
				            	'color'   => array(
				            		'rgb'     => '000000'
				              	)
				           	),
				           	'bottom'    => array(
				            	'style'   => PHPExcel_Style_Border::BORDER_THIN ,
				            	'color'   => array(
				            		'rgb'     => '000000'
				              	)
				           	),
			        	)
			));

			$borderPart = new PHPExcel_Style();
	    	$borderPart->applyFromArray(
			        array(
			       'font'  => array(
			            'name'  => 'TH SarabunPSK', 
			            'size'  => 16,			                          
			            'color' => array(
			            	'rgb'   => '000000'
			            )
			        ),
			        'fill' => array(
			        	'type' => PHPExcel_Style_Fill::FILL_SOLID,
            			'color' => array('rgb' => 'e0e0d1')
			        ),
			        'borders' => array(
				           
				           	'top'    => array(
				            	'style'   => PHPExcel_Style_Border::BORDER_THIN ,
				            	'color'   => array(
				            		'rgb'     => '000000'
				              	)
				           	),
				           	'bottom'    => array(
				            	'style'   => PHPExcel_Style_Border::BORDER_THIN ,
				            	'color'   => array(
				            		'rgb'     => '000000'
				              	)
				           	),
			        	)
			));

			$headerStyle = array(
			    'font'  => array(
			            'name'  => 'TH SarabunPSK', 
			            'size'  => 16,	
			            'bold'  => true,		                          
			            'color' => array(
			            	'rgb'   => '000000'
			            )
			        ));
 

		    //----------------------FORM 1----------------------------//
		if($form==1)
		{	
            //$objPHPExcel = new PHPExcel();
            $objPHPExcel = $objReader->load("templates/template_boq_1.xlsx");

            //--------------- ค่าของ ------------------------//
            $vendor = Vendor::model()->findByPk($model->vendor_id)->v_name ;
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('B2', $model->name);
            $detail = "สัญญาเลขที่ ".$model->contract_no." ลงวันที่   ".$model->approve_date." ผู้รับจ้าง  ".$vendor;
            $objPHPExcel->getActiveSheet()->setCellValue('B3', $detail);
            $objPHPExcel->getActiveSheet()->setCellValue('M3', $pay_no);

            $row_start = 6;
            $row = 6;
            $header_row = array();
            $input_row = array();

            //load BOQ
            $Criteria = new CDbCriteria();
            $Criteria->condition = "vc_id='$vc_id'";
            $boq = Boq::model()->findAll($Criteria);

            foreach ($boq as $key => $value) {
            	
            	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $value->id);
            	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $value->no);

            	if($value->type==2) // PART
            	{
            		//merge C & D
            		$objPHPExcel->getActiveSheet()->mergeCells('C'.$row.':D'.$row);
            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	
            		$header_row[] = $row;
             	}
             	else if($value->type==1) //item
             	{
             		//merge C & D
            		$objPHPExcel->getActiveSheet()->mergeCells('C'.$row.':D'.$row);
            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	
            		$header_row[] = $row;
             	}
             	else if($value->type==-1) //indent
             	{
             		
            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, "-");	
            		$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $value->detail);	
             	}
             	else{
             		$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $value->detail);	
             	}

             	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $value->amount);
            	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $value->unit);
            	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $value->price_item);
            	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $value->price_trans);

            	
            	if(!(is_numeric($value->price_item) && is_numeric($value->price_trans)))
            		$objPHPExcel->getActiveSheet()->mergeCells('G'.$row.':H'.$row);

            	
            	
            	if(!empty($value->amount) )
            	{	
            		
            	  	if(is_numeric($value->price_item) &&  is_numeric($value->price_trans))
            	  	{	
            			$price_item_all = ($value->price_item + $value->price_trans) * $value->amount;
            			$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $price_item_all);
                    }
            		//previous pay
            		$payment = Yii::app()->db->createCommand()
		                        ->select('SUM(amount) as sum')
		                        ->from('payment')
		                        //->join('user','user_create=u_id')
		                        ->where("pay_type=0 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no <".$pay_no)
		                        ->queryAll();
		        	$amount_prev = $payment[0]["sum"];
		        	if($amount_prev > 0)
		        	{
		        		$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $amount_prev);
		        		if(is_numeric($value->price_item) &&  is_numeric($value->price_trans))
            	  		{
		        			$prev_price_item_all = ($value->price_item + $value->price_trans) * $amount_prev;
            				$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $prev_price_item_all);
            			}
            		}	

            		
            		// Set input validation
					$amount_max = $value->amount;
					if($amount_max-$amount_prev >0)
					{	
						$objValidation = $objPHPExcel->getActiveSheet()->getCell('L'.$row)->getDataValidation();
						$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_WHOLE );
						$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_STOP );
						$objValidation->setAllowBlank(true);
						$objValidation->setShowInputMessage(true);
						$objValidation->setShowErrorMessage(true);
						$objValidation->setErrorTitle('Input error');
						
						$recommen_val = 'กรอกจำนวนค่าอุปกรณ์เกินจำนวนตามสัญญา ไม่เกิน '.($amount_max-$amount_prev);
						$objValidation->setError($recommen_val);
						$objValidation->setPromptTitle('คำแนะนำการเบิกค่าอุปกรณ์ 	:');
						$objValidation->setPrompt($recommen_val);
						$objValidation->setFormula1(1);
						$objValidation->setFormula2($amount_max-$amount_prev);

						//add formula
						$objPHPExcel->getActiveSheet()->setCellValue('L'.$row,0);
						$objPHPExcel->getActiveSheet()->setCellValue('M'.$row,'=PRODUCT(SUM(G'.$row.':H'.$row.'),L'.$row.')');

						$input_row[] = $row;
					}

            	}
            		
            	
            	
            	$row++;
            }


            
			$objPHPExcel->getActiveSheet()->setSharedStyle($borderAll, 'B'.$row_start.":B".($row-1));
			$objPHPExcel->getActiveSheet()->setSharedStyle($borderAll, 'E'.$row_start.":M".($row-1));
			$objPHPExcel->getActiveSheet()->setSharedStyle($borderIndent, 'D'.$row_start.":D".($row-1));
			$objPHPExcel->getActiveSheet()->setSharedStyle($borderPart, 'C'.$row_start.":C".($row-1));

			foreach ($header_row as $key => $r) {
				$objPHPExcel->getActiveSheet()->getStyle('B'.$r.":C".$r)->applyFromArray($headerStyle);
			}

			$objPHPExcel->getActiveSheet()->getStyle('G'.$row_start.":H".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//set number format
			$objPHPExcel->getActiveSheet()->getStyle('G'.$row_start.":I".($row-1))->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('K'.$row_start.":K".($row-1))->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('M'.$row_start.":M".($row-1))->getNumberFormat()->setFormatCode('#,##0.00');
            	
            //set center
			$objPHPExcel->getActiveSheet()->getStyle('B'.$row_start.":B".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$row_start.":F".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('J'.$row_start.":J".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('L'.$row_start.":L".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            	
            //Hide id column
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setVisible(false);

            //lock cell
            
			$objPHPExcel->getActiveSheet()->protectCells('A1:M'.($row-1), $password_lock);
			$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);

			//unlock input cell
			foreach ($input_row as $key => $r) {
				$objPHPExcel->getActiveSheet()->getStyle('L'.$r)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
				$objPHPExcel->getActiveSheet()->getStyle('L'.$r)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						->getStartColor()->setRGB('ffffff');
			}
			//--------------------------end------------------------------//

			//---------------------ค่าติดตั้งทดสอบ---------------------------//
			$objPHPExcel->setActiveSheetIndex(1);
            $objPHPExcel->getActiveSheet()->setCellValue('B2', $model->name);
            $detail = "สัญญาเลขที่ ".$model->contract_no." ลงวันที่   ".$model->approve_date." ผู้รับจ้าง  ".$vendor ;
            $objPHPExcel->getActiveSheet()->setCellValue('B3', $detail);
            $objPHPExcel->getActiveSheet()->setCellValue('L3', $pay_no);

            $row_start = 6;
            $row = 6;
            $header_row = array();
            $input_row = array();

            
            foreach ($boq as $key => $value) {
            	
            	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $value->id);
            	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $value->no);

            	if($value->type==2) // PART
            	{
            		//merge C & D
            		$objPHPExcel->getActiveSheet()->mergeCells('C'.$row.':D'.$row);
            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	
            		$header_row[] = $row;
             	}
             	else if($value->type==1) //item
             	{
             		//merge C & D
            		$objPHPExcel->getActiveSheet()->mergeCells('C'.$row.':D'.$row);
            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	
            		$header_row[] = $row;
             	}
             	else if($value->type==-1) //indent
             	{
             		
            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, "-");	
            		$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $value->detail);	
             	}
             	else{
             		$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $value->detail);	
             	}

             	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $value->amount);
            	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $value->unit);
            	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $value->price_install);

            	
         	
            	//ค่าติดตั้งห้ามเบิกเกินค่าของ  (ค่าติดตั้งเก่า+ค่าติดตั้งปัจจุบัน <= ของเก่า+ของปัจจุบัน)
            	if(!empty($value->amount))
            	{	
            		
            		if(is_numeric($value->price_install))
            		{
            			$price_item_all = ($value->price_install) * $value->amount;
            			$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $price_item_all);
	
            		}
            		
					//ดึงจำนวนของที่ส่งมอบงวดนี้
					$payment = Yii::app()->db->createCommand()
		                        ->select('SUM(amount) as sum')
		                        ->from('payment')
		                        //->join('user','user_create=u_id')
		                        ->where("pay_type=0 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no <".$pay_no)
		                        ->queryAll();
		        	
		        	//ค่าของรวมก่อนหน้า
		        	$previous_item_amount = $payment[0]["sum"];
            		//$previous_item_amount = $objPHPExcel->getSheet(0)->getCell('L'.$row)->getValue();

            		//previous pay
            		$payment = Yii::app()->db->createCommand()
		                        ->select('SUM(amount) as sum')
		                        ->from('payment')
		                        //->join('user','user_create=u_id')
		                        ->where("pay_type=2 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no <".$pay_no)
		                        ->queryAll();
		        	//ค่าติดตั้งรวมก่อนหน้า
		        	$amount_prev = $payment[0]["sum"];
		        	


		        	if($amount_prev > 0)
		        	{
		        		$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $amount_prev);
		        		if(is_numeric($value->price_install))
            			{
		        			$prev_price_item_all = ($value->price_install) * $amount_prev;
            				$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $prev_price_item_all);
            			}
            		}	

            		
            		// Set input validation
            		//ค่าติดตั้งปัจจุบัน <= (ค่าของรวมก่อนหน้า + ค่าของปัจจุบัน) - ค่าติดตั้งรวมก่อนหน้า
					$amount_max = $value->amount;
					if($amount_max-$amount_prev >0)
					{	
						$objValidation = $objPHPExcel->getActiveSheet()->getCell('K'.$row)->getDataValidation();
						$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_WHOLE );
						$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_STOP );
						$objValidation->setAllowBlank(true);
						$objValidation->setShowInputMessage(true);
						$objValidation->setShowErrorMessage(true);
						$objValidation->setErrorTitle('Input error');
						
						$recommen_val = 'กรอกเกินจำนวนค่าอุปกรณ์รวมครั้งนี้';
						$objValidation->setError($recommen_val);
						$objValidation->setPromptTitle('คำแนะนำการเบิกค่าติดตั้งทดสอบ 	:');
						$objValidation->setPrompt($recommen_val);
						$objValidation->setFormula1(1);
						$objValidation->setFormula2('=SUM(ค่าอุปกรณ์!J'.$row.',ค่าอุปกรณ์!L'.$row.')');

						//add formula
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$row,0);
						$objPHPExcel->getActiveSheet()->setCellValue('L'.$row,'=PRODUCT(G'.$row.',K'.$row.')');

						$input_row[] = $row;
					}

            	}
            		
            	
            	
            	$row++;
            }



            $objPHPExcel->getActiveSheet()->setSharedStyle($borderAll, 'B'.$row_start.":B".($row-1));
			$objPHPExcel->getActiveSheet()->setSharedStyle($borderAll, 'E'.$row_start.":L".($row-1));
			$objPHPExcel->getActiveSheet()->setSharedStyle($borderIndent, 'D'.$row_start.":D".($row-1));
			$objPHPExcel->getActiveSheet()->setSharedStyle($borderPart, 'C'.$row_start.":C".($row-1));

			foreach ($header_row as $key => $r) {
				$objPHPExcel->getActiveSheet()->getStyle('B'.$r.":C".$r)->applyFromArray($headerStyle);
			}

			//set number format
			$objPHPExcel->getActiveSheet()->getStyle('G'.$row_start.":G".($row-1))->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('J'.$row_start.":H".($row-1))->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('L'.$row_start.":L".($row-1))->getNumberFormat()->setFormatCode('#,##0.00');
            	
            //set center
            $objPHPExcel->getActiveSheet()->getStyle('G'.$row_start.":G".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objPHPExcel->getActiveSheet()->getStyle('B'.$row_start.":B".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$row_start.":F".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('I'.$row_start.":I".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('K'.$row_start.":K".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            	
            //Hide id column
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setVisible(false);

            //lock cell
            $password_lock = "12345";
			$objPHPExcel->getActiveSheet()->protectCells('A1:L'.($row-1), $password_lock);
			$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);

			//unlock input cell
			foreach ($input_row as $key => $r) {
				$objPHPExcel->getActiveSheet()->getStyle('K'.$r)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
				$objPHPExcel->getActiveSheet()->getStyle('K'.$r)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						->getStartColor()->setRGB('ffffff');
			}

			//------------------end ค่าติดตั้งทดสอบ--------------------------//
			

		}
		else if($form==2)
		{
		   //----------------------------FORM 2-----------------------//
			$objPHPExcel = $objReader->load("templates/template_boq_2.xlsx");

			//--------------- ค่าของ ------------------------//
            $vendor = Vendor::model()->findByPk($model->vendor_id)->v_name ;
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('B2', $model->name);
            $detail = "สัญญาเลขที่ ".$model->contract_no." ลงวันที่   ".$model->approve_date." ผู้รับจ้าง  ".$vendor;
            $objPHPExcel->getActiveSheet()->setCellValue('B3', $detail);
            $objPHPExcel->getActiveSheet()->setCellValue('N3', $pay_no);

            $row_start = 6;
            $row = 6;
            $header_row = array();
            $input_row = array();

            //load BOQ
            $Criteria = new CDbCriteria();
            $Criteria->condition = "vc_id='$vc_id'";
            $boq = Boq::model()->findAll($Criteria);

            foreach ($boq as $key => $value) {
            	
            	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $value->id);
            	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $value->no);

            	if($value->type==2) // PART
            	{
            		//merge C & D
            		$objPHPExcel->getActiveSheet()->mergeCells('C'.$row.':D'.$row);
            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	
            		$header_row[] = $row;
             	}
             	else if($value->type==1) //item
             	{
             		//merge C & D
            		$objPHPExcel->getActiveSheet()->mergeCells('C'.$row.':D'.$row);
            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	
            		$header_row[] = $row;
             	}
             	else if($value->type==-1) //indent
             	{
             		
            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, "-");	
            		$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $value->detail);	
             	}
             	else{
             		$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $value->detail);	
             	}

             	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $value->amount);
            	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $value->unit);
            	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $value->price_item);
            	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $value->price_trans);
            	$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $value->price_install);

            	if(!(is_numeric($value->price_item) && is_numeric($value->price_trans) && is_numeric($value->price_install)))
            		$objPHPExcel->getActiveSheet()->mergeCells('G'.$row.':I'.$row);
            	else if(!(is_numeric($value->price_item) && is_numeric($value->price_trans)))
            		$objPHPExcel->getActiveSheet()->mergeCells('G'.$row.':H'.$row);

            	
            
            	//--------check เงื่อนไข/////// ------------//
            	if(!empty($value->amount) )
            	{	
            		$price_item = is_numeric($value->price_item) ? $value->price_item : 0;
	            	$price_trans = is_numeric($value->price_trans) ? $value->price_trans : 0;
	            	$price_install = is_numeric($value->price_install) ? $value->price_install : 0;

	            	
	            	$price_item_all = ($price_item + $price_trans + $price_install) * $value->amount;

	            	if(is_numeric($value->price_item) || is_numeric($value->price_trans) || is_numeric($value->price_install))
	            		$objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $price_item_all);
                
            	  	
            		//previous pay
            		$payment = Yii::app()->db->createCommand()
		                        ->select('SUM(amount) as sum')
		                        ->from('payment')
		                        //->join('user','user_create=u_id')
		                        ->where("pay_type=0 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no <".$pay_no)
		                        ->queryAll();
		        	$amount_prev = $payment[0]["sum"];
		        	if($amount_prev > 0)
		        	{
		        		$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $amount_prev);
		        		if(is_numeric($value->price_item) &&  is_numeric($value->price_trans))
            	  		{
		        			$prev_price_item_all = ($price_item + $price_trans + $price_install) * $amount_prev;
            				$objPHPExcel->getActiveSheet()->setCellValue('L'.$row, $prev_price_item_all);
            			}
            		}	

            		
            		// Set input validation
					$amount_max = $value->amount;
					if($amount_max-$amount_prev >0)
					{	
						$objValidation = $objPHPExcel->getActiveSheet()->getCell('M'.$row)->getDataValidation();
						$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_WHOLE );
						$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_STOP );
						$objValidation->setAllowBlank(true);
						$objValidation->setShowInputMessage(true);
						$objValidation->setShowErrorMessage(true);
						$objValidation->setErrorTitle('Input error');
						
						$recommen_val = 'กรอกจำนวนค่าอุปกรณ์เกินจำนวนตามสัญญา ไม่เกิน '.($amount_max-$amount_prev);
						$objValidation->setError($recommen_val);
						$objValidation->setPromptTitle('คำแนะนำการเบิกค่าอุปกรณ์ 	:');
						$objValidation->setPrompt($recommen_val);
						$objValidation->setFormula1(1);
						$objValidation->setFormula2($amount_max-$amount_prev);

						//add formula
						$objPHPExcel->getActiveSheet()->setCellValue('M'.$row,0);
						$objPHPExcel->getActiveSheet()->setCellValue('N'.$row,'=PRODUCT(SUM(G'.$row.':I'.$row.'),M'.$row.')');

						$input_row[] = $row;
					}

            	}
            		
            	
            	
            	
            	$row++;
            }

            $objPHPExcel->getActiveSheet()->setSharedStyle($borderAll, 'B'.$row_start.":B".($row-1));
			$objPHPExcel->getActiveSheet()->setSharedStyle($borderAll, 'E'.$row_start.":N".($row-1));
			$objPHPExcel->getActiveSheet()->setSharedStyle($borderIndent, 'D'.$row_start.":D".($row-1));
			$objPHPExcel->getActiveSheet()->setSharedStyle($borderPart, 'C'.$row_start.":C".($row-1));

			foreach ($header_row as $key => $r) {
				$objPHPExcel->getActiveSheet()->getStyle('B'.$r.":C".$r)->applyFromArray($headerStyle);
			}

			$objPHPExcel->getActiveSheet()->getStyle('G'.$row_start.":I".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//set number format
			$objPHPExcel->getActiveSheet()->getStyle('G'.$row_start.":J".($row-1))->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('L'.$row_start.":L".($row-1))->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('N'.$row_start.":N".($row-1))->getNumberFormat()->setFormatCode('#,##0.00');
            	
            //set center
			$objPHPExcel->getActiveSheet()->getStyle('B'.$row_start.":B".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$row_start.":F".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('K'.$row_start.":K".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('M'.$row_start.":M".($row-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            	
            //Hide id column
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setVisible(false);

            //lock cell
            
			$objPHPExcel->getActiveSheet()->protectCells('A1:N'.($row-1), $password_lock);
			$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);

			//unlock input cell
			foreach ($input_row as $key => $r) {
				$objPHPExcel->getActiveSheet()->getStyle('M'.$r)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
				$objPHPExcel->getActiveSheet()->getStyle('M'.$r)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						->getStartColor()->setRGB('ffffff');
			}


		}	
			$objPHPExcel->getProperties()
						    ->setCreator("boybe")
						    ->setLastModifiedBy("boybe")
						    ->setTitle("PHPExcel Test Document")
						    ->setSubject("PHPExcel Test Document")
						    ->setDescription("Test document for PHPExcel, generated using PHP classes.")
						    ->setKeywords("office PHPExcel php")
						    ->setCategory("Test result file");

			ob_end_clean();
			ob_start();


			$filename = "ใบ จค. ".Vendor::model()->findByPk($model->vendor_id)->v_name." งวด ".$pay_no.".xlsx";

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header ('Pragma: public'); // HTTP/1.0

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,  'Excel2007');
			$objWriter->save('php://output');  //
			// Yii::app()->end(); 

			$xlsData = ob_get_contents();
			//ob_end_clean();
			$response =  array(
		        'op' => 'ok',
		        'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
		    );

			
    }

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	// public function actionUpdate($id)
	// {
	// 	$model=$this->loadModel($id);

	// 	// Uncomment the following line if AJAX validation is needed
	// 	// $this->performAjaxValidation($model);

	// 	if(isset($_POST['PaymentDetail']))
	// 	{
	// 		$model->attributes=$_POST['PaymentDetail'];
	// 		if($model->save())
	// 			$this->redirect(array('view','id'=>$model->id));
	// 	}

	// 	$this->render('update',array(
	// 		'model'=>$model,
	// 	));
	// }




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
