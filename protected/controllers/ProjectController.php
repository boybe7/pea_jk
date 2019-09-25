<?php

class ProjectController extends Controller
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
				'actions'=>array('view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','index','update','createBOQ','exportBOQ','createByAjax','createVendorContract','updateVendorContract','importBOQ','submitBOQ','printJK','printTestJK','exportExcel'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','deleteVendorContract'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionPrintJK()
    {
  
  		
        $this->renderPartial('_formJK_PDF', array(
            'vc_id' => $_POST['vc_id'],
            'pay_no' => $_POST['pay_no'],
            'filename' => '',
            
        ));
        
        //if (Yii::app()->request->isAjaxRequest)
        //echo $filename;
        
    }

    public function actionExportExcel()
    {
    	   $model_vc  = VendorContract::model()->findByPk($_GET["vc_id"]);
    	   //find form type   
	     	$modelPD = PaymentDetail::model()->findAll('vc_id =:id AND pay_no=:pay_no', array(':id' =>$_GET["vc_id"],':pay_no'=>$_GET["pay_no"]));      
	     	$form_type = !empty($modelPD) ? $modelPD[0]->form_type : 1;

	     	$pay_no = $_GET["pay_no"];
	     	$vc_id = $_GET["vc_id"];

	     	$Criteria = new CDbCriteria();
	        $Criteria->condition = "vc_id=".$_GET["vc_id"];
	        $boq = Boq::model()->findAll($Criteria); 

	        $Criteria = new CDbCriteria();
	        $Criteria->condition = "vc_id=".$_GET["vc_id"];
	        $fineModel = Fine::model()->findAll($Criteria); 


		   Yii::import('ext.phpexcel.XPHPExcel');    
		   $objPHPExcel= XPHPExcel::createPHPExcel();
		   $objReader = PHPExcel_IOFactory::createReader('Excel2007');
           $objPHPExcel = $objReader->load("templates/template_form.xlsx");

           //using for page more than 3 
           function copyRows(PHPExcel_Worksheet $sheet,$srcRow,$dstRow,$height,$width) {
			    for ($row = 0; $row < $height; $row++) {
			        for ($col = 0; $col < $width; $col++) {
			            $cell = $sheet->getCellByColumnAndRow($col, $srcRow + $row);
			            $style = $sheet->getStyleByColumnAndRow($col, $srcRow + $row);
			            $dstCell = PHPExcel_Cell::stringFromColumnIndex($col) . (string)($dstRow + $row);
			            $sheet->setCellValue($dstCell, $cell->getValue());
			            $sheet->duplicateStyle($style, $dstCell);
			        }

			        $h = $sheet->getRowDimension($srcRow + $row)->getRowHeight();
			        $sheet->getRowDimension($dstRow + $row)->setRowHeight($h);
			    }

			    foreach ($sheet->getMergeCells() as $mergeCell) {
			        $mc = explode(":", $mergeCell);
			        $col_s = preg_replace("/[0-9]*/", "", $mc[0]);
			        $col_e = preg_replace("/[0-9]*/", "", $mc[1]);
			        $row_s = ((int)preg_replace("/[A-Z]*/", "", $mc[0])) - $srcRow;
			        $row_e = ((int)preg_replace("/[A-Z]*/", "", $mc[1])) - $srcRow;

			        if (0 <= $row_s && $row_s < $height) {
			            $merge = $col_s . (string)($dstRow + $row_s) . ":" . $col_e . (string)($dstRow + $row_e);
			            $sheet->mergeCells($merge);
			        } 
			    }
			}

			function renderDate($value)
			{
			    $th_month = array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
			    $dates = explode("/", $value);
			    $d=0;
			    $mi = 0;
			    $yi = 0;
			    foreach ($dates as $key => $value) {
			         $d++;
			         if($d==2)
			            $mi = $value;
			         if($d==3)
			            $yi = $value;
			    }
			    if(substr($mi, 0,1)==0)
			        $mi = substr($mi, 1);
			    if(substr($dates[0], 0,1)==0)
			        $d = substr($dates[0], 1);


			    $renderDate = $d." ".$th_month[$mi]." ".$yi;
			    if($renderDate==0)
			        $renderDate = "";   

			    return $renderDate;             
			}


			$max_row = 35;
			$max_page = ceil(count($boq)*1.0 / $max_row); 

			//-----------header------------//
			$detail = "สัญญาเลขที่ ".$model_vc->contract_no."   ลงวันที่   ".renderDate($model_vc->approve_date) ."   จำนวนเงินตามสัญญา ".number_format($model_vc->budget,0)."  บาท (ไม่รวมภาษีมูลค่าเพิ่ม)   ผู้รับจ้าง  ".Vendor::model()->findByPk($model_vc->vendor_id)->v_name.'  กำหนดแล้วเสร็จตามสัญญา วันที่  '.renderDate($model_vc->end_date);
            if(!empty($model_vc->detail_approve))
                 $detail .= "\n".$model_vc->detail_approve;

            //committee
	        $modelMember = ContractMember::model()->findAll('vc_id =:id AND type=0', array(':id' => $vc_id));
	        $committee_header = empty($modelMember) ? new ContractMember : $modelMember[0]; 
	        $committee_member = ContractMember::model()->findAll('vc_id =:id AND type=1', array(':id' => $vc_id));
	        $modelMember = ContractMember::model()->findAll('vc_id =:id AND type=2', array(':id' => $vc_id));
	        $committee_control = empty($modelMember) ? new ContractMember : $modelMember[0]; 
	        $modelMember = ContractMember::model()->findAll('vc_id =:id AND type=3', array(':id' => $vc_id));
	        $committee_vendor = empty($modelMember) ? new ContractMember : $modelMember[0]; 

	        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
			
	        $filename = "";

			if($form_type==1)
			{


				$objPHPExcel->setActiveSheetIndex(0);

				$row = 0;
		        $page = 1;
		        $summary_cost_all = 0;
		        $summary_curr_all = 0;
		        $summary_prev_all = 0;
		        $summary_cost_page = 0;
		        $summary_curr_page = 0;
		        $summary_prev_page = 0;

				if($max_page==1)
				{
					$filename = "form 1 max_page1.xlsx";
				 	//---------------   Item & Install Form1 using for 1 Page-----------------//
		           $objPHPExcel->setActiveSheetIndex(0);
		           $objPHPExcel->getActiveSheet()->unmergeCells('D7:H7');
		           $objPHPExcel->getActiveSheet()->unmergeCells('I7:J7');
		           $objPHPExcel->getActiveSheet()->unmergeCells('K7:L7');
		           $objPHPExcel->getActiveSheet()->unmergeCells('M7:N7');
		           $objPHPExcel->getActiveSheet()->removeRow(1,50);

		            $objPHPExcel->getActiveSheet()->setCellValue('A4', $model_vc->name);  
            		$objPHPExcel->getActiveSheet()->setCellValue('A5', $detail);
            		$objPHPExcel->getActiveSheet()->getStyle('A5')->getAlignment()->setWrapText(true);
            		$objPHPExcel->getActiveSheet()->setCellValue('V5', 'งวดที่ : '.$pay_no); 

            		$objPHPExcel->getActiveSheet()->getStyle('A1:S5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            		$objPHPExcel->getActiveSheet()->setCellValue('B45', "รวม (1)");
            		$objPHPExcel->getActiveSheet()->setCellValue('C46', "รวม (1)");
            		$objPHPExcel->getActiveSheet()->setCellValue('J46', "=+J45");
            		$objPHPExcel->getActiveSheet()->setCellValue('L46', "=+L45");
            		$objPHPExcel->getActiveSheet()->setCellValue('N46', "=+N45");

            		$objPHPExcel->getActiveSheet()->setCellValue('R14', "(".$committee_control->name.")");
            		$objPHPExcel->getActiveSheet()->setCellValue('V14', $committee_control->position);
            		$objPHPExcel->getActiveSheet()->setCellValue('C50', "(".$committee_vendor->name.")   ผู้จัดการโครงการ");
            		// $objPHPExcel->getActiveSheet()->mergeCells('F49:I49');
            		$objPHPExcel->getActiveSheet()->setCellValue('F50', "(".$committee_control->name.")  ตำแหน่ง ".$committee_control->position);

            		//-------------------Install sheet----------------------//
					$objPHPExcel->setActiveSheetIndex(1);
					 $objPHPExcel->getActiveSheet()->unmergeCells('D7:H7');
		           $objPHPExcel->getActiveSheet()->unmergeCells('I7:J7');
		           $objPHPExcel->getActiveSheet()->unmergeCells('K7:L7');
		           $objPHPExcel->getActiveSheet()->unmergeCells('M7:N7');
		           $objPHPExcel->getActiveSheet()->removeRow(1,50);

				    $objPHPExcel->getActiveSheet()->mergeCells('A1:V1');
		            $objPHPExcel->getActiveSheet()->mergeCells('A2:V2');
		            $objPHPExcel->getActiveSheet()->mergeCells('A4:V4');
		            $objPHPExcel->getActiveSheet()->mergeCells('A5:S5');
		            $objPHPExcel->getActiveSheet()->setCellValue('A4', $model_vc->name);  
            		$objPHPExcel->getActiveSheet()->setCellValue('A5', $detail);
            		$objPHPExcel->getActiveSheet()->getStyle('A5')->getAlignment()->setWrapText(true);
            		$objPHPExcel->getActiveSheet()->setCellValue('V5', 'งวดที่ : '.$pay_no); 

            		$objPHPExcel->getActiveSheet()->getStyle('A1:S5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            		//$objPHPExcel->getActiveSheet()->mergeCells('R14:T14');
            		$objPHPExcel->getActiveSheet()->setCellValue('B45', "รวม (1)");
            		$objPHPExcel->getActiveSheet()->setCellValue('C46', "รวม (1)");
            		$objPHPExcel->getActiveSheet()->setCellValue('J46', "=+J45");
            		$objPHPExcel->getActiveSheet()->setCellValue('L46', "=+L45");
            		$objPHPExcel->getActiveSheet()->setCellValue('N46', "=+N45");

            		$objPHPExcel->getActiveSheet()->setCellValue('R14', "(".$committee_control->name.")");
            		$objPHPExcel->getActiveSheet()->setCellValue('V14', $committee_control->position);
            		$objPHPExcel->getActiveSheet()->setCellValue('A50', "(".$committee_vendor->name.")   ผู้จัดการโครงการ");
            		//$objPHPExcel->getActiveSheet()->mergeCells('F49:I49');
            		$objPHPExcel->getActiveSheet()->setCellValue('E50', "(".$committee_control->name.")  ตำแหน่ง ".$committee_control->position);

            		$row = 10;
            		$row_start = 10;
            		foreach ($boq as $key => $value) {
            				if($value->type==2) // PART
			            	{
			            		$row = $row == $max_row+$row_start-1 ? $row + 16 : $row ;
			            		$objPHPExcel->setActiveSheetIndex(0);		   
			            		$objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
			            		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $value->detail);	

			            		$objPHPExcel->setActiveSheetIndex(1);		            	
			            		$objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
			            		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $value->detail);	
			            		//$header_row[] = $row;
			            	}
			            	else if($value->type==1) //item
			             	{
			             		$row = $row == $max_row+$row_start-1 ? $row + 16 : $row ;
			             		$objPHPExcel->setActiveSheetIndex(0);
			            		$objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
			            		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $value->detail);	

			            		$objPHPExcel->setActiveSheetIndex(1);
			            		$objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
			            		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $value->detail);	
			            		//$header_row[] = $row;
			             	}
			             	else if($value->type==-1) //indent
			             	{
			             		$objPHPExcel->setActiveSheetIndex(0);
			            		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, "-");	
			            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	

			            		$objPHPExcel->setActiveSheetIndex(1);
			            		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, "-");	
			            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	
			             	}
			             	else{
			             		$objPHPExcel->setActiveSheetIndex(0);
			             		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	

			             		$objPHPExcel->setActiveSheetIndex(1);
			             		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	
			             	}

			             	$objPHPExcel->setActiveSheetIndex(0);
			             	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $value->no);
			             	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $value->amount);
			             	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $value->unit);
			             	if(!is_numeric($value->price_item) && !is_numeric($value->price_trans) && $value->price_item==$value->price_trans && $value->price_item!="")
                  			{
                  				
                  				$objPHPExcel->getActiveSheet()->mergeCells('F'.$row.':G'.$row);
                  				$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $value->price_item);
                  			}
                  			else
                  			{
                  				
                  				$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $value->price_item);
			             		$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $value->price_trans);
                  			}	
			             	

			             	$price_item_all = ($value->price_item+$value->price_trans)*$value->amount;
			         		//if(is_numeric($value->price_item) && is_numeric($value->price_trans)) 
			             		$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $price_item_all);
			             	    	

			             	//amount current payment
		                    $curr_payment = Yii::app()->db->createCommand()
		                                    ->select('*')
		                                    ->from('payment')
		                                    ->where("pay_type=0 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no =".$pay_no)
		                                    ->queryAll();
		                    $current_payment = "";                
		                    if(!empty($curr_payment))
		                    {
		                    	$current_payment = $curr_payment[0]['amount'];
		                    	$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $current_payment);
		                    	$objPHPExcel->getActiveSheet()->setCellValue('M'.$row, $current_payment);
			                    $price_item_all = ($value->price_item + $value->price_trans) * $curr_payment[0]['amount'];
			                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $price_item_all);
			                    $summary_curr_page += $price_item_all;


		                    }
		                    //amount previous with current payment  
		                    $prev_payment = Yii::app()->db->createCommand()
		                                    ->select('SUM(amount) as amount')
		                                    ->from('payment')
		                                    ->where("pay_type=0 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no <=".$pay_no)
		                                    ->queryAll();     

		                    if(!empty($prev_payment) and $prev_payment[0]['amount']>0)
		                    {
		                    	$prev_payment = $prev_payment[0]['amount'];
		                    	$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $prev_payment);
			                    $price_item_all = ($value->price_item + $value->price_trans) * $prev_payment;
			                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$row, $price_item_all);
		                    }




		                    $objPHPExcel->setActiveSheetIndex(1);
			             	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $value->no);
			             	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $value->amount);
			             	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $value->unit);
			             	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $value->price_install);
			             	
			             	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, 0);
			             	$price_item_all = ($value->price_install)*$value->amount;
			         
			             	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $price_item_all);

			             	//amount current payment
		                    $curr_payment = Yii::app()->db->createCommand()
		                                    ->select('*')
		                                    ->from('payment')
		                                    ->where("pay_type=2 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no =".$pay_no)
		                                    ->queryAll();
		                    $current_payment = "";                
		                    if(!empty($curr_payment))
		                    {
		                    	$current_payment = $curr_payment[0]['amount'];
		                    	$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $current_payment);
		                    	$objPHPExcel->getActiveSheet()->setCellValue('M'.$row, $current_payment);
			                    $price_item_all = ($value->price_install) * $curr_payment[0]['amount'];
			                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $price_item_all);
			                    $summary_curr_page += $price_item_all;


		                    }

		                    //amount previous with current payment  
		                    $prev_payment = Yii::app()->db->createCommand()
		                                    ->select('SUM(amount) as amount')
		                                    ->from('payment')
		                                    ->where("pay_type=2 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no <=".$pay_no)
		                                    ->queryAll();     

		                    if(!empty($prev_payment) and $prev_payment[0]['amount']>0)
		                    {
		                    	$prev_payment = $prev_payment[0]['amount'];
		                    	$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $prev_payment);
			                    $price_item_all = ($value->price_install) * $prev_payment;
			                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$row, $price_item_all);
		                    }




            			$row++;
            		}

            		//-------- Summary-----------------//
            		$objPHPExcel->setActiveSheetIndex(0);
            		$row_summary = ($max_page-1)*50 + 24;
            		$objPHPExcel->getActiveSheet()->setCellValue('Q'.($row_summary+1), "เบิก ".$model_vc->percent_pay."%");
            		$objPHPExcel->getActiveSheet()->setCellValue('T'.($row_summary+1), "=+T".$row_summary."*".$model_vc->percent_pay."%" );

            		$objPHPExcel->getActiveSheet()->setCellValue('Q'.($row_summary+2), "หัก Advance ".$model_vc->percent_adv."%");
            		$objPHPExcel->getActiveSheet()->setCellValue('T'.($row_summary+2), "=+T".$row_summary."*".$model_vc->percent_adv."%" );

            		//fine detail
            		$row_fine = 80;
            		foreach ($fineModel as $key => $fine) {
            			$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_fine, "- ".$fine->detail);
            			$objPHPExcel->getActiveSheet()->setCellValue('T'.$row_fine, $fine->amount);

            			$row_fine++;

            		}

            		$objPHPExcel->getActiveSheet()->getStyle("T".$row_summary.":T".($row_summary+11))->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');

            		$objPHPExcel->getActiveSheet()->getStyle("H10:H45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("J10:J45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("L10:L45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("N10:N47")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');

            		$objPHPExcel->getActiveSheet()->getStyle("I10:I47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("K10:K47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("M10:M47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		

            		$objPHPExcel->setActiveSheetIndex(1);
            		$row_summary = ($max_page-1)*50 + 24;
            		$objPHPExcel->getActiveSheet()->setCellValue('Q'.($row_summary+1), "เบิก ".$model_vc->percent_pay."%");
            		$objPHPExcel->getActiveSheet()->setCellValue('T'.($row_summary+1), "=+T".$row_summary."*".$model_vc->percent_pay."%" );

            		$objPHPExcel->getActiveSheet()->setCellValue('Q'.($row_summary+2), "หัก Advance ".$model_vc->percent_adv."%");
            		$objPHPExcel->getActiveSheet()->setCellValue('T'.($row_summary+2), "=+T".$row_summary."*".$model_vc->percent_adv."%" );
            		//fine detail
            		$row_fine = 80;
            		foreach ($fineModel as $key => $fine) {
            			$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_fine, "- ".$fine->detail);
            			$objPHPExcel->getActiveSheet()->setCellValue('T'.$row_fine, $fine->amount);

            			$row_fine++;

            		}

            		//cell accouting format
            		$objPHPExcel->getActiveSheet()->getStyle("T".$row_summary.":T".($row_summary+11))->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');

            		$objPHPExcel->getActiveSheet()->getStyle("H10:H45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("J10:J45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("L10:L45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("N10:N47")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');

            		$objPHPExcel->getActiveSheet()->getStyle("I10:I47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("K10:K47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("M10:M47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);


            		//---------committee----------//
            		$row_committee = ($max_page-1)*50 + 36;
            		$objPHPExcel->setActiveSheetIndex(0);
            		if(count($committee_member)<=2)
            		{
            			$row_comm = $row_committee+1;
            			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");           			
					    $objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ประธานกรรมการ ");
            			$row_comm++;
            			$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            			$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$committee_header->name.')');
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$committee_header->position);
            			$row_comm = $row_comm+2;
            			foreach ($committee_member as $key => $member) {
            				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");
            				$objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "กรรมการ ");
            				$row_comm++;
            				$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            				$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$member->name.')');
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$member->position);
            				$row_comm = $row_comm+2;

            			}

            		}
            		else
            		{
            			$row_comm = $row_committee;
            			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");
            			$objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ประธานกรรมการ ");
            			$row_comm++;
            			$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            			$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$committee_header->name.')');
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$committee_header->position);
            			$row_comm ++;
            			foreach ($committee_member as $key => $member) {
            				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");
            				$objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "กรรมการ ");
            				$row_comm++;
            				$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            				$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$member->name.')');
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$member->position);
            				$row_comm++;

            			}
            		}
            		//----------sheet install------------------//
            		$objPHPExcel->setActiveSheetIndex(1);
            		if(count($committee_member)<=2)
            		{
            			$row_comm = $row_committee+1;
            			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");           			
					    $objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ประธานกรรมการ ");
            			$row_comm++;
            			$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            			$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$committee_header->name.')');
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$committee_header->position);
            			$row_comm = $row_comm+2;
            			foreach ($committee_member as $key => $member) {
            				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");
            				$objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "กรรมการ ");
            				$row_comm++;
            				$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            				$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$member->name.')');
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$member->position);
            				$row_comm = $row_comm+2;

            			}

            		}
            		else
            		{
            			$row_comm = $row_committee;
            			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");
            			$objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ประธานกรรมการ ");
            			$row_comm++;
            			$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            			$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$committee_header->name.')');
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$committee_header->position);
            			$row_comm ++;
            			foreach ($committee_member as $key => $member) {
            				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");
            				$objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "กรรมการ ");
            				$row_comm++;
            				$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            				$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$member->name.')');
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$member->position);
            				$row_comm++;

            			}
            		}


				}
				else if($max_page==2)
				{
					$filename = "form 1 max_page2.xlsx";
					//-------------------Item sheet----------------------//
					$objPHPExcel->setActiveSheetIndex(0);
				    $objPHPExcel->getActiveSheet()->mergeCells('A1:V1');
		            $objPHPExcel->getActiveSheet()->mergeCells('A2:V2');
		            $objPHPExcel->getActiveSheet()->mergeCells('A4:V4');
		            $objPHPExcel->getActiveSheet()->mergeCells('A5:S5');
		            $objPHPExcel->getActiveSheet()->setCellValue('A4', $model_vc->name);  
            		$objPHPExcel->getActiveSheet()->setCellValue('A5', $detail);
            		$objPHPExcel->getActiveSheet()->getStyle('A5')->getAlignment()->setWrapText(true);
            		$objPHPExcel->getActiveSheet()->setCellValue('V5', 'งวดที่ : '.$pay_no); 

            		$objPHPExcel->getActiveSheet()->getStyle('A1:S5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            		$objPHPExcel->getActiveSheet()->mergeCells('R14:T14');
            		$objPHPExcel->getActiveSheet()->setCellValue('R14', "(".$committee_control->name.")");
            		$objPHPExcel->getActiveSheet()->setCellValue('V14', $committee_control->position);
            		$objPHPExcel->getActiveSheet()->setCellValue('C49', "(".$committee_vendor->name.")   ผู้จัดการโครงการ");
            		$objPHPExcel->getActiveSheet()->mergeCells('F49:I49');
            		$objPHPExcel->getActiveSheet()->setCellValue('F49', "(".$committee_control->name.")  ตำแหน่ง ".$committee_control->position);


            		$objPHPExcel->getActiveSheet()->setCellValue('R64', "(".$committee_control->name.")");
            		$objPHPExcel->getActiveSheet()->setCellValue('V64', $committee_control->position);
            		$objPHPExcel->getActiveSheet()->setCellValue('C100', "(".$committee_vendor->name.")   ผู้จัดการโครงการ");
            		$objPHPExcel->getActiveSheet()->setCellValue('F100', "(".$committee_control->name.")  ตำแหน่ง ".$committee_control->position);




            	    //-------------------Install sheet----------------------//
					$objPHPExcel->setActiveSheetIndex(1);
				    $objPHPExcel->getActiveSheet()->mergeCells('A1:V1');
		            $objPHPExcel->getActiveSheet()->mergeCells('A2:V2');
		            $objPHPExcel->getActiveSheet()->mergeCells('A4:V4');
		            $objPHPExcel->getActiveSheet()->mergeCells('A5:S5');
		            $objPHPExcel->getActiveSheet()->setCellValue('A4', $model_vc->name);  
            		$objPHPExcel->getActiveSheet()->setCellValue('A5', $detail);
            		$objPHPExcel->getActiveSheet()->getStyle('A5')->getAlignment()->setWrapText(true);
            		$objPHPExcel->getActiveSheet()->setCellValue('V5', 'งวดที่ : '.$pay_no); 

            		$objPHPExcel->getActiveSheet()->getStyle('A1:S5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            		$objPHPExcel->getActiveSheet()->mergeCells('R14:T14');
            		$objPHPExcel->getActiveSheet()->setCellValue('R14', "(".$committee_control->name.")");
            		$objPHPExcel->getActiveSheet()->setCellValue('V14', $committee_control->position);
            		$objPHPExcel->getActiveSheet()->setCellValue('C49', "(".$committee_vendor->name.")   ผู้จัดการโครงการ");
            		$objPHPExcel->getActiveSheet()->mergeCells('F49:I49');
            		$objPHPExcel->getActiveSheet()->setCellValue('F49', "(".$committee_control->name.")  ตำแหน่ง ".$committee_control->position);


            		$objPHPExcel->getActiveSheet()->setCellValue('R64', "(".$committee_control->name.")");
            		$objPHPExcel->getActiveSheet()->setCellValue('V64', $committee_control->position);
            		$objPHPExcel->getActiveSheet()->setCellValue('A100', "(".$committee_vendor->name.")   ผู้จัดการโครงการ");
            		$objPHPExcel->getActiveSheet()->setCellValue('F100', "(".$committee_control->name.")  ตำแหน่ง ".$committee_control->position);

            		$row = 10;
            		$row_start = 10;
            		foreach ($boq as $key => $value) {
            			if($row==$max_row+$row_start)
            			{
            				$row = $row + 14;//skip summary and header next page
            				$objPHPExcel->setActiveSheetIndex(0);
            				$objPHPExcel->getActiveSheet()->setCellValue('A54', $model_vc->name);  
		            		$objPHPExcel->getActiveSheet()->setCellValue('A55', $detail);
		            		$objPHPExcel->getActiveSheet()->setCellValue('V55', 'งวดที่ : '.$pay_no);
		            		$objPHPExcel->setActiveSheetIndex(1);
            				$objPHPExcel->getActiveSheet()->setCellValue('A54', $model_vc->name);  
		            		$objPHPExcel->getActiveSheet()->setCellValue('A55', $detail);
		            		$objPHPExcel->getActiveSheet()->setCellValue('V55', 'งวดที่ : '.$pay_no); 
            			}
            			else
            			{
            				
            				

            				if($value->type==2) // PART
			            	{
			            		$row = $row == $max_row+$row_start-1 ? $row + 16 : $row ;
			            		$objPHPExcel->setActiveSheetIndex(0);
			            		$objPHPExcel->getActiveSheet()->setCellValue('A54', $model_vc->name);  
			            		$objPHPExcel->getActiveSheet()->setCellValue('A55', $detail);
			            		$objPHPExcel->getActiveSheet()->setCellValue('V55', 'งวดที่ : '.$pay_no); 

			            		$objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
			            		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $value->detail);	

			            		$objPHPExcel->setActiveSheetIndex(1);
			            		$objPHPExcel->getActiveSheet()->setCellValue('A54', $model_vc->name);  
			            		$objPHPExcel->getActiveSheet()->setCellValue('A55', $detail);
			            		$objPHPExcel->getActiveSheet()->setCellValue('V55', 'งวดที่ : '.$pay_no); 

			            		$objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
			            		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $value->detail);	
			            		//$header_row[] = $row;
			             	}
			             	else if($value->type==1) //item
			             	{
			             		$row = $row == $max_row+$row_start-1 ? $row + 16 : $row ;
			             		$objPHPExcel->setActiveSheetIndex(0);
			             		$objPHPExcel->getActiveSheet()->setCellValue('A54', $model_vc->name);  
			            		$objPHPExcel->getActiveSheet()->setCellValue('A55', $detail);
			            		$objPHPExcel->getActiveSheet()->setCellValue('V55', 'งวดที่ : '.$pay_no); 
			            		$objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
			            		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $value->detail);	

			            		$objPHPExcel->setActiveSheetIndex(1);
			             		$objPHPExcel->getActiveSheet()->setCellValue('A54', $model_vc->name);  
			            		$objPHPExcel->getActiveSheet()->setCellValue('A55', $detail);
			            		$objPHPExcel->getActiveSheet()->setCellValue('V55', 'งวดที่ : '.$pay_no); 
			            		$objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
			            		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $value->detail);	
			            		//$header_row[] = $row;
			             	}
			             	else if($value->type==-1) //indent
			             	{
			             		$objPHPExcel->setActiveSheetIndex(0);
			            		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, "-");	
			            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	

			            		$objPHPExcel->setActiveSheetIndex(1);
			            		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, "-");	
			            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	
			             	}
			             	else{
			             		$objPHPExcel->setActiveSheetIndex(0);
			             		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	

			             		$objPHPExcel->setActiveSheetIndex(1);
			             		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	
			             	}

			             	$objPHPExcel->setActiveSheetIndex(0);
			             	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $value->no);
			             	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $value->amount);
			             	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $value->unit);
			             	if(!is_numeric($value->price_item) && !is_numeric($value->price_trans) && $value->price_item==$value->price_trans && $value->price_item!="")
                  			{
                  				
                  				$objPHPExcel->getActiveSheet()->mergeCells('F'.$row.':G'.$row);
                  				$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $value->price_item);
                  			}
                  			else
                  			{
                  				
                  				$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $value->price_item);
			             		$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $value->price_trans);
                  			}	
			             	

			             	$price_item_all = ($value->price_item+$value->price_trans)*$value->amount;
			         		//if(is_numeric($value->price_item) && is_numeric($value->price_trans)) 
			             		$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $price_item_all);
			             	    	

			             	//amount current payment
		                    $curr_payment = Yii::app()->db->createCommand()
		                                    ->select('*')
		                                    ->from('payment')
		                                    ->where("pay_type=0 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no =".$pay_no)
		                                    ->queryAll();
		                    $current_payment = "";                
		                    if(!empty($curr_payment))
		                    {
		                    	$current_payment = $curr_payment[0]['amount'];
		                    	$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $current_payment);
		                    	$objPHPExcel->getActiveSheet()->setCellValue('M'.$row, $current_payment);
			                    $price_item_all = ($value->price_item + $value->price_trans) * $curr_payment[0]['amount'];
			                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $price_item_all);
			                    $summary_curr_page += $price_item_all;


		                    }
		                    //amount previous with current payment  
		                    $prev_payment = Yii::app()->db->createCommand()
		                                    ->select('SUM(amount) as amount')
		                                    ->from('payment')
		                                    ->where("pay_type=0 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no <=".$pay_no)
		                                    ->queryAll();     

		                    if(!empty($prev_payment) and $prev_payment[0]['amount']>0)
		                    {
		                    	$prev_payment = $prev_payment[0]['amount'];
		                    	$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $prev_payment);
			                    $price_item_all = ($value->price_item + $value->price_trans) * $prev_payment;
			                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$row, $price_item_all);
		                    }




		                    $objPHPExcel->setActiveSheetIndex(1);
			             	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $value->no);
			             	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $value->amount);
			             	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $value->unit);
			             	$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $value->price_install);
			             	
			             	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, 0);
			             	$price_item_all = ($value->price_install)*$value->amount;
			         
			             	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $price_item_all);

			             	//amount current payment
		                    $curr_payment = Yii::app()->db->createCommand()
		                                    ->select('*')
		                                    ->from('payment')
		                                    ->where("pay_type=2 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no =".$pay_no)
		                                    ->queryAll();
		                    $current_payment = "";                
		                    if(!empty($curr_payment))
		                    {
		                    	$current_payment = $curr_payment[0]['amount'];
		                    	$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $current_payment);
		                    	$objPHPExcel->getActiveSheet()->setCellValue('M'.$row, $current_payment);
			                    $price_item_all = ($value->price_install) * $curr_payment[0]['amount'];
			                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $price_item_all);
			                    $summary_curr_page += $price_item_all;


		                    }

		                    //amount previous with current payment  
		                    $prev_payment = Yii::app()->db->createCommand()
		                                    ->select('SUM(amount) as amount')
		                                    ->from('payment')
		                                    ->where("pay_type=2 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no <=".$pay_no)
		                                    ->queryAll();     

		                    if(!empty($prev_payment) and $prev_payment[0]['amount']>0)
		                    {
		                    	$prev_payment = $prev_payment[0]['amount'];
		                    	$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $prev_payment);
			                    $price_item_all = ($value->price_install) * $prev_payment;
			                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$row, $price_item_all);
		                    }





            			}
            			$row++;
            		}

            		//-------- Summary-----------------//
            		$objPHPExcel->setActiveSheetIndex(0);
            		$row_summary = ($max_page-1)*50 + 24;
            		$objPHPExcel->getActiveSheet()->setCellValue('Q'.($row_summary+1), "เบิก ".$model_vc->percent_pay."%");
            		$objPHPExcel->getActiveSheet()->setCellValue('T'.($row_summary+1), "=+T".$row_summary."*".$model_vc->percent_pay."%" );

            		$objPHPExcel->getActiveSheet()->setCellValue('Q'.($row_summary+2), "หัก Advance ".$model_vc->percent_adv."%");
            		$objPHPExcel->getActiveSheet()->setCellValue('T'.($row_summary+2), "=+T".$row_summary."*".$model_vc->percent_adv."%" );

            		//fine detail
            		$row_fine = 80;
            		foreach ($fineModel as $key => $fine) {
            			$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_fine, "- ".$fine->detail);
            			$objPHPExcel->getActiveSheet()->setCellValue('T'.$row_fine, $fine->amount);

            			$row_fine++;

            		}

            		$objPHPExcel->getActiveSheet()->getStyle("T".$row_summary.":T".($row_summary+11))->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');

            		$objPHPExcel->getActiveSheet()->getStyle("H10:H45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("J10:J45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("L10:L45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("N10:N45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');

            		$objPHPExcel->getActiveSheet()->getStyle("H60:H96")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("J60:J96")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("L60:L96")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("N60:N97")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');

            		$objPHPExcel->getActiveSheet()->getStyle("I10:I47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("K10:K47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("M10:M47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

            		$objPHPExcel->getActiveSheet()->getStyle("I60:I96")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("K60:K96")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("M60:M96")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

            		$objPHPExcel->setActiveSheetIndex(1);
            		$row_summary = ($max_page-1)*50 + 24;
            		$objPHPExcel->getActiveSheet()->setCellValue('Q'.($row_summary+1), "เบิก ".$model_vc->percent_pay."%");
            		$objPHPExcel->getActiveSheet()->setCellValue('T'.($row_summary+1), "=+T".$row_summary."*".$model_vc->percent_pay."%" );

            		$objPHPExcel->getActiveSheet()->setCellValue('Q'.($row_summary+2), "หัก Advance ".$model_vc->percent_adv."%");
            		$objPHPExcel->getActiveSheet()->setCellValue('T'.($row_summary+2), "=+T".$row_summary."*".$model_vc->percent_adv."%" );
            		//fine detail
            		$row_fine = 80;
            		foreach ($fineModel as $key => $fine) {
            			$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_fine, "- ".$fine->detail);
            			$objPHPExcel->getActiveSheet()->setCellValue('T'.$row_fine, $fine->amount);

            			$row_fine++;

            		}

            		//cell accouting format
            		$objPHPExcel->getActiveSheet()->getStyle("T".$row_summary.":T".($row_summary+11))->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');

            		$objPHPExcel->getActiveSheet()->getStyle("H10:H45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("J10:J45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("L10:L45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("N10:N45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');

            		$objPHPExcel->getActiveSheet()->getStyle("H60:H96")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("J60:J96")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("L60:L96")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("N60:N97")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');

            		$objPHPExcel->getActiveSheet()->getStyle("I10:I47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("K10:K47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("M10:M47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

            		$objPHPExcel->getActiveSheet()->getStyle("I60:I96")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("K60:K96")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("M60:M96")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);


            		//---------committee----------//
            		$row_committee = ($max_page-1)*50 + 36;
            		$objPHPExcel->setActiveSheetIndex(0);
            		if(count($committee_member)<=2)
            		{
            			$row_comm = $row_committee+1;
            			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");           			
					    $objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ประธานกรรมการ ");
            			$row_comm++;
            			$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            			$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$committee_header->name.')');
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$committee_header->position);
            			$row_comm = $row_comm+2;
            			foreach ($committee_member as $key => $member) {
            				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");
            				$objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "กรรมการ ");
            				$row_comm++;
            				$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            				$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$member->name.')');
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$member->position);
            				$row_comm = $row_comm+2;

            			}

            		}
            		else
            		{
            			$row_comm = $row_committee;
            			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");
            			$objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ประธานกรรมการ ");
            			$row_comm++;
            			$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            			$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$committee_header->name.')');
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$committee_header->position);
            			$row_comm ++;
            			foreach ($committee_member as $key => $member) {
            				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");
            				$objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "กรรมการ ");
            				$row_comm++;
            				$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            				$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$member->name.')');
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$member->position);
            				$row_comm++;

            			}
            		}


            		//----------sheet install------------------//
            		$objPHPExcel->setActiveSheetIndex(1);
            		if(count($committee_member)<=2)
            		{
            			$row_comm = $row_committee+1;
            			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");           			
					    $objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ประธานกรรมการ ");
            			$row_comm++;
            			$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            			$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$committee_header->name.')');
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$committee_header->position);
            			$row_comm = $row_comm+2;
            			foreach ($committee_member as $key => $member) {
            				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");
            				$objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "กรรมการ ");
            				$row_comm++;
            				$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            				$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$member->name.')');
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$member->position);
            				$row_comm = $row_comm+2;

            			}

            		}
            		else
            		{
            			$row_comm = $row_committee;
            			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");
            			$objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ประธานกรรมการ ");
            			$row_comm++;
            			$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            			$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$committee_header->name.')');
            			$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$committee_header->position);
            			$row_comm ++;
            			foreach ($committee_member as $key => $member) {
            				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row_comm, "ลงชื่อ ");
            				$objPHPExcel->getActiveSheet()->getStyle('R'.$row_comm.':T'.$row_comm)->getBorders()->getBottom()
					                ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED );
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "กรรมการ ");
            				$row_comm++;
            				$objPHPExcel->getActiveSheet()->mergeCells('R'.$row_comm.':T'.$row_comm);
            				$objPHPExcel->getActiveSheet()->setCellValue('R'.$row_comm, '('.$member->name.')');
            				$objPHPExcel->getActiveSheet()->setCellValue('U'.$row_comm, "ตำแหน่ง ".$member->position);
            				$row_comm++;

            			}
            		}



				}	
				else // page > 2
				{
					$filename = "form 1 max_page 3.xlsx";
					$objPHPExcel->setActiveSheetIndex(0);
					$objPHPExcel->getActiveSheet()->insertNewRowBefore(51, 50*($max_page-2));
					for ($i=1; $i <= $max_page-2 ; $i++) { 

						copyRows($objPHPExcel->getActiveSheet(), 1, 50*$i + 1, 50, 22);
					}

					$objPHPExcel->setActiveSheetIndex(1);
					$objPHPExcel->getActiveSheet()->insertNewRowBefore(51, 50*($max_page-2));
					for ($i=1; $i <= $max_page-2 ; $i++) { 
					
						copyRows($objPHPExcel->getActiveSheet(), 1, 50*$i + 1, 50, 22);
					}
					

					//-------------------Item sheet----------------------//
					$objPHPExcel->setActiveSheetIndex(0);
					$objPHPExcel->getActiveSheet()->getStyle("H10:H45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("J10:J45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("L10:L45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		//$objPHPExcel->getActiveSheet()->getStyle("M10:M45")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("N10:N45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');

            		$objPHPExcel->getActiveSheet()->getStyle("I10:I47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("K10:K47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("M10:M47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);


				    $objPHPExcel->getActiveSheet()->mergeCells('A1:V1');
		            $objPHPExcel->getActiveSheet()->mergeCells('A2:V2');
		            $objPHPExcel->getActiveSheet()->mergeCells('A4:V4');
		            $objPHPExcel->getActiveSheet()->mergeCells('A5:S5');
		            $objPHPExcel->getActiveSheet()->setCellValue('A4', $model_vc->name);  
            		$objPHPExcel->getActiveSheet()->setCellValue('A5', $detail);
            		$objPHPExcel->getActiveSheet()->getStyle('A5')->getAlignment()->setWrapText(true);
            		$objPHPExcel->getActiveSheet()->setCellValue('V5', 'งวดที่ : '.$pay_no); 

            		$objPHPExcel->getActiveSheet()->getStyle('A1:S5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            		$objPHPExcel->getActiveSheet()->mergeCells('R14:T14');
            		$objPHPExcel->getActiveSheet()->setCellValue('R14', "(".$committee_control->name.")");
            		$objPHPExcel->getActiveSheet()->setCellValue('V14', $committee_control->position);
            		$objPHPExcel->getActiveSheet()->setCellValue('C49', "(".$committee_vendor->name.")   ผู้จัดการโครงการ");
            		$objPHPExcel->getActiveSheet()->mergeCells('F49:I49');
            		$objPHPExcel->getActiveSheet()->setCellValue('F49', "(".$committee_control->name.")  ตำแหน่ง ".$committee_control->position);


            	    //-------------------Install sheet----------------------//
					$objPHPExcel->setActiveSheetIndex(1);
					$objPHPExcel->getActiveSheet()->getStyle("H10:H45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("J10:J45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("L10:L45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		//$objPHPExcel->getActiveSheet()->getStyle("M10:M45")->getNumberFormat()->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("N10:N45")->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
            		$objPHPExcel->getActiveSheet()->getStyle("I10:I47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("K10:K47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            		$objPHPExcel->getActiveSheet()->getStyle("M10:M47")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);


				    $objPHPExcel->getActiveSheet()->mergeCells('A1:V1');
		            $objPHPExcel->getActiveSheet()->mergeCells('A2:V2');
		            $objPHPExcel->getActiveSheet()->mergeCells('A4:V4');
		            $objPHPExcel->getActiveSheet()->mergeCells('A5:S5');
		            $objPHPExcel->getActiveSheet()->setCellValue('A4', $model_vc->name);  
            		$objPHPExcel->getActiveSheet()->setCellValue('A5', $detail);
            		$objPHPExcel->getActiveSheet()->getStyle('A5')->getAlignment()->setWrapText(true);
            		$objPHPExcel->getActiveSheet()->setCellValue('V5', 'งวดที่ : '.$pay_no); 

            		$objPHPExcel->getActiveSheet()->getStyle('A1:S5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            		$objPHPExcel->getActiveSheet()->mergeCells('R14:T14');
            		$objPHPExcel->getActiveSheet()->setCellValue('R14', "(".$committee_control->name.")");
            		$objPHPExcel->getActiveSheet()->setCellValue('V14', $committee_control->position);
            		$objPHPExcel->getActiveSheet()->setCellValue('C49', "(".$committee_vendor->name.")   ผู้จัดการโครงการ");
            		$objPHPExcel->getActiveSheet()->mergeCells('F49:I49');
            		$objPHPExcel->getActiveSheet()->setCellValue('F49', "(".$committee_control->name.")  ตำแหน่ง ".$committee_control->position);


            	
					$row = 10;
            		$row_start = 10;
            		$page = 1;

            		$objPHPExcel->setActiveSheetIndex(0);
            		$item_page = 0;
            		foreach ($boq as $key => $value) {
            			if($row%50==0 )
            			{
            				$page++;
            				$item_page = 0;
            				//$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $page);  

            				//--------------header----------------//
            				//-------------------Item sheet----------------------//
							$objPHPExcel->setActiveSheetIndex(0);
						    $objPHPExcel->getActiveSheet()->mergeCells('A'.($row+1).':V'.($row+1));
				            $objPHPExcel->getActiveSheet()->mergeCells('A'.($row+2).':V'.($row+2));
				            $objPHPExcel->getActiveSheet()->mergeCells('A'.($row+4).':V'.($row+4));
				            $objPHPExcel->getActiveSheet()->mergeCells('A'.($row+5).':S'.($row+5));
				            $objPHPExcel->getActiveSheet()->setCellValue('A'.($row+4), $model_vc->name);  
		            		$objPHPExcel->getActiveSheet()->setCellValue('A'.($row+5), $detail);
		            		$objPHPExcel->getActiveSheet()->getStyle('A'.($row+5))->getAlignment()->setWrapText(true);
		            		$objPHPExcel->getActiveSheet()->setCellValue('V'.($row+5), 'งวดที่ : '.$pay_no); 

		            		$objPHPExcel->getActiveSheet()->getStyle('A'.($row+1).':S'.($row+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		            		
		            		if($page!=$max_page)
		            		{
		            			$objPHPExcel->getActiveSheet()->mergeCells('R'.($row+14).':T'.($row+14));
		            			$objPHPExcel->getActiveSheet()->setCellValue('R'.($row+14), "(".$committee_control->name.")");
		            			$objPHPExcel->getActiveSheet()->setCellValue('V'.($row+14), $committee_control->position);
		            			$objPHPExcel->getActiveSheet()->setCellValue('C'.($row+49), "(".$committee_vendor->name.")   ผู้จัดการโครงการ");
		            			$objPHPExcel->getActiveSheet()->mergeCells('F'.($row+49).':I'.($row+49));
		            			$objPHPExcel->getActiveSheet()->setCellValue('F'.($row+49), "(".$committee_control->name.")  ตำแหน่ง ".$committee_control->position);
		            		}
		            		else{
		            			//$objPHPExcel->getActiveSheet()->mergeCells('R'.($row+14).':T'.($row+14));
		            			$objPHPExcel->getActiveSheet()->setCellValue('R'.($row+14), "(".$committee_control->name.")");
		            			$objPHPExcel->getActiveSheet()->setCellValue('V'.($row+14), $committee_control->position);
		            			$objPHPExcel->getActiveSheet()->setCellValue('C'.($row+50), "(".$committee_vendor->name.")   ผู้จัดการโครงการ");
		            			//$objPHPExcel->getActiveSheet()->mergeCells('F'.($row+50).':I'.($row+50));
		            			$objPHPExcel->getActiveSheet()->setCellValue('F'.($row+50), "(".$committee_control->name.")  ตำแหน่ง ".$committee_control->position);	
		            		}
		            		//summary
		            		$objPHPExcel->getActiveSheet()->setCellValue('C'.($row+45), "รวม(".($page).")");
		            		$objPHPExcel->getActiveSheet()->setCellValue('H'.($row+45), "=SUM(H".(($page-1)*50 + 10).":H".(($page-1)*50 + 10 + 34).")");
		            		$objPHPExcel->getActiveSheet()->setCellValue('J'.($row+45), "=SUM(J".(($page-1)*50 + 10).":J".(($page-1)*50 + 10 + 34).")");
		            		$objPHPExcel->getActiveSheet()->setCellValue('L'.($row+45), "=SUM(L".(($page-1)*50 + 10).":L".(($page-1)*50 + 10 + 34).")");
		            		$objPHPExcel->getActiveSheet()->setCellValue('N'.($row+45), "=SUM(N".(($page-1)*50 + 10).":N".(($page-1)*50 + 10 + 34).")");


		            		$objPHPExcel->getActiveSheet()->getStyle("H".(($page-1)*50 + 10).":H".(($page-1)*50 + 10 + 34))->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
		            		$objPHPExcel->getActiveSheet()->getStyle("J".(($page-1)*50 + 10).":J".(($page-1)*50 + 10 + 34))->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
		            		$objPHPExcel->getActiveSheet()->getStyle("L".(($page-1)*50 + 10).":L".(($page-1)*50 + 10 + 34))->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
		            		
		            		$objPHPExcel->getActiveSheet()->getStyle("N".(($page-1)*50 + 10).":N".(($page-1)*50 + 10 + 34))->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');

		            		
            				$objPHPExcel->getActiveSheet()->getStyle("I".(($page-1)*50 + 10).":I".(($page-1)*50 + 10 + 34))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            				$objPHPExcel->getActiveSheet()->getStyle("K".(($page-1)*50 + 10).":K".(($page-1)*50 + 10 + 34))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            				$objPHPExcel->getActiveSheet()->getStyle("M".(($page-1)*50 + 10).":M".(($page-1)*50 + 10 + 34))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);



		            		//-------------------Install sheet----------------------//
							$objPHPExcel->setActiveSheetIndex(1);
						    $objPHPExcel->getActiveSheet()->mergeCells('A'.($row+1).':V'.($row+1));
				            $objPHPExcel->getActiveSheet()->mergeCells('A'.($row+2).':V'.($row+2));
				            $objPHPExcel->getActiveSheet()->mergeCells('A'.($row+4).':V'.($row+4));
				            $objPHPExcel->getActiveSheet()->mergeCells('A'.($row+5).':S'.($row+5));
				            $objPHPExcel->getActiveSheet()->setCellValue('A'.($row+4), $model_vc->name);  
		            		$objPHPExcel->getActiveSheet()->setCellValue('A'.($row+5), $detail);
		            		$objPHPExcel->getActiveSheet()->getStyle('A'.($row+5))->getAlignment()->setWrapText(true);
		            		$objPHPExcel->getActiveSheet()->setCellValue('V'.($row+5), 'งวดที่ : '.$pay_no); 

		            		$objPHPExcel->getActiveSheet()->getStyle('A'.($row+1).':S'.($row+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		            		

		            		if($page!=$max_page)
		            		{
		            			$objPHPExcel->getActiveSheet()->mergeCells('R'.($row+14).':T'.($row+14));
		            			$objPHPExcel->getActiveSheet()->setCellValue('R'.($row+14), "(".$committee_control->name.")");
		            			$objPHPExcel->getActiveSheet()->setCellValue('V'.($row+14), $committee_control->position);
		            			$objPHPExcel->getActiveSheet()->setCellValue('C'.($row+49), "(".$committee_vendor->name.")   ผู้จัดการโครงการ");
		            			$objPHPExcel->getActiveSheet()->mergeCells('F'.($row+49).':I'.($row+49));
		            			$objPHPExcel->getActiveSheet()->setCellValue('F'.($row+49), "(".$committee_control->name.")  ตำแหน่ง ".$committee_control->position);
		            		}
		            		else{
		            			//$objPHPExcel->getActiveSheet()->mergeCells('R'.($row+14).':T'.($row+14));
		            			$objPHPExcel->getActiveSheet()->setCellValue('R'.($row+14), "(".$committee_control->name.")");
		            			$objPHPExcel->getActiveSheet()->setCellValue('V'.($row+14), $committee_control->position);
		            			$objPHPExcel->getActiveSheet()->setCellValue('A'.($row+50), "(".$committee_vendor->name.")   ผู้จัดการโครงการ");
		            			//$objPHPExcel->getActiveSheet()->mergeCells('F'.($row+50).':I'.($row+50));
		            			$objPHPExcel->getActiveSheet()->setCellValue('F'.($row+50), "(".$committee_control->name.")  ตำแหน่ง ".$committee_control->position);	
		            		}

            				
            				$row = ($page-1)*50 + 9;//skip footer and header
            				//$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $page); 
            				//break;
            			}
            			else
            			{
            				$item_page++;

            				if($item_page <= 35)
            				{
            					
            					
            					if($value->type==2 || $value->type==1) //part
            					{
            						
            						

            						$objPHPExcel->setActiveSheetIndex(0);
            						$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $value->no);
            						$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $value->detail);

            						$objPHPExcel->setActiveSheetIndex(1);
            						$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $value->no);
            						$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $value->detail);
            					
            					}
            					else if($value->type==-1) //indent
				             	{
				             		$objPHPExcel->setActiveSheetIndex(0);
				             		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $value->no);
				            		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, "-");	
				            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	

				            		$objPHPExcel->setActiveSheetIndex(1);
				            		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $value->no);
				            		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, "-");	
				            		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	
				             	}
				             	else{
				             		$objPHPExcel->setActiveSheetIndex(0);
				             		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $value->no);
				             		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	

				             		$objPHPExcel->setActiveSheetIndex(1);
				             		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $value->no);
				             		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value->detail);	
				             	}

				             	$objPHPExcel->setActiveSheetIndex(0);

				             	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $value->amount);
				             	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $value->unit);
				             	if(!is_numeric($value->price_item) && !is_numeric($value->price_trans) && $value->price_item==$value->price_trans && $value->price_item!="")
	                  			{
	                  				
	                  				$objPHPExcel->getActiveSheet()->mergeCells('F'.$row.':G'.$row);
	                  				$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $value->price_item);
	                  			}
	                  			else
	                  			{
	                  				
	                  				$objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $value->price_item);
				             		$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $value->price_trans);
	                  			}	
				             	

				             	$price_item_all = ($value->price_item+$value->price_trans)*$value->amount;
				         		//if(is_numeric($value->price_item) && is_numeric($value->price_trans)) 
				             		$objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $price_item_all);
				             	    	

				             	//amount current payment
			                    $curr_payment = Yii::app()->db->createCommand()
			                                    ->select('*')
			                                    ->from('payment')
			                                    ->where("pay_type=0 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no =".$pay_no)
			                                    ->queryAll();
			                    $current_payment = "";                
			                    if(!empty($curr_payment))
			                    {
			                    	$current_payment = $curr_payment[0]['amount'];
			                    	$objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $current_payment);
			                    	$objPHPExcel->getActiveSheet()->setCellValue('M'.$row, $current_payment);
			                    	
				                    $price_item_all = ($value->price_item + $value->price_trans) * $curr_payment[0]['amount'];
				                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $price_item_all);
				                    
				                    $summary_curr_page += $price_item_all;


			                    }

			                  

			                    //amount previous with current payment  
			                    $prev_payment = Yii::app()->db->createCommand()
			                                    ->select('SUM(amount) as amount')
			                                    ->from('payment')
			                                    ->where("pay_type=0 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no <=".$pay_no)
			                                    ->queryAll();     

			                    if(!empty($prev_payment) and $prev_payment[0]['amount']>0)
			                    {
			                    	$prev_payment = $prev_payment[0]['amount'];
			                    	$objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $prev_payment);
				                    $price_item_all = ($value->price_item + $value->price_trans) * $prev_payment;
				                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$row, $price_item_all);
			                    }

            					$objPHPExcel->setActiveSheetIndex(0);		            			
					            $objPHPExcel->getActiveSheet()->setCellValue('N'.$row, "=J".$row);

					            
            				
            				}
            			
			             	


            			}

            			

            			$row++;
            		}
		   			
				}

			}
			else
			{
				$objPHPExcel->setActiveSheetIndex(2);

				if($max_page==1)
				{
				 	//---------------   Form2 using for 1 Page-----------------//
		             $objPHPExcel->getActiveSheet()->unmergeCells('A1:V1');
		             $objPHPExcel->getActiveSheet()->unmergeCells('A2:V2');
		             $objPHPExcel->getActiveSheet()->unmergeCells('A4:V4');
		           // $objPHPExcel->getActiveSheet()->unmergeCells('A5:S5');
		           // $objPHPExcel->getActiveSheet()->unmergeCells('D7:J7');
		           // $objPHPExcel->getActiveSheet()->unmergeCells('K7:L7');
		           // $objPHPExcel->getActiveSheet()->unmergeCells('M7:N7');
				   // $objPHPExcel->getActiveSheet()->unmergeCells('O7:P7');
		           // $objPHPExcel->getActiveSheet()->removeRow(1,50);

		            $objPHPExcel->getActiveSheet()->setCellValue('A4', $model_vc->name);  
            		$objPHPExcel->getActiveSheet()->setCellValue('A5', $detail); 

				}
				else
				{


				}	


			}

		  

			



           //$objPHPExcel->getActiveSheet()->insertNewRowBefore(51,50); 

           //$sheet = $objPHPExcel->getActiveSheet();
		   //copyRows($sheet, 1, 51, 50, 22);

           //$row = 1;
           //$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$row,"ใบสั่งจ้างเลขที่ : ");

           ob_end_clean();
			ob_start();


			//$filename = "ใบ จค. xxxx.xlsx";

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

    }

    public function actionPrintTestJK()
    {
  
  		
        $this->renderPartial('_formJK_PDF', array(
            'vc_id' => 3,
            'pay_no' => 1,
            'filename' => '',
            
        ));
        
        //if (Yii::app()->request->isAjaxRequest)
        //echo $filename;
        
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

	public function actionCreateByAjax()
	{
		$model=new Project;

		if(isset($_POST['proj_name']) && !empty($_POST['proj_name']))
		{
			$model->name = $_POST['proj_name'];
			$model->is_special = isset($_POST['special']) ? $_POST['special'] : 0;
			$model->fiscal_year = $_POST['fiscal_year'];
			$model->updated_by = Yii::app()->user->ID;

			if($model->save())
	           echo CJSON::encode(array(
	                'status'=>'success','proj_id'=>$model->id
	                ));
	        else
	            echo CJSON::encode(array(
	                'status'=>'failure'));

	        //header('Content-type: text/plain');
	        //print_r($model);
	                
		}
		else{
			 echo CJSON::encode(array(
	                'status'=>'failure'));
		}


	}

	public function actionSubmitBOQ()
	{
		
		try{

			$payment = Yii::app()->db->createCommand()
                                    ->select('*')
                                    ->from('payment_temp')
                                    ->where("vc_id='".$_POST['vc_id']."' AND pay_no =".$_POST['pay_no'])
                                    ->queryAll();  

			if(!empty($payment))
			{
				//delete old submit on payment
				Yii::app()->db->createCommand('DELETE FROM payment WHERE vc_id='.$_POST['vc_id'].' AND pay_no='.$_POST['pay_no'])->execute();

				//insert new data from temp table
				$current_date = date('Y-m-d');
				Yii::app()->db->createCommand('INSERT INTO payment(item_id,vc_id,pay_no,pay_type,amount,pay_date) SELECT item_id,vc_id,pay_no,pay_type,amount,"'.$current_date.'" FROM payment_temp WHERE vc_id='.$_POST['vc_id'].' AND pay_no='.$_POST['pay_no'])->execute();
			}
			else	
				echo "error";

			//delete temp
			//Yii::app()->db->createCommand('DELETE FROM payment_temp WHERE vc_id='.$_POST['vc_id'].' AND pay_no='.$_POST['pay_no'])->execute();

		}catch (Exception $e) {

		    var_dump($e->getMessage());
		    die();

		}

	}

	public function actionCreateVendorContract($id)
	{	
		$modelMember = array();
		$model=new VendorContract;
		$model->proj_id = $id;

		if(isset($_POST['VendorContract']))
		{
			$model->attributes=$_POST['VendorContract'];
			$model->updated_by = Yii::app()->user->ID;
			$model->approve_date = $_POST['VendorContract']['approve_date'];

			$vendor =  Vendor::model()->findAll(array('join'=>'','condition'=>'v_name="'.$_POST['vendor_id'].'" AND type="Supplier"'));
			//save new vendor
			if(empty($vendor))
			{
				$vendor = new Vendor;
				$vendor->v_name = $_POST['vendor_id'];
				$vendor->type = 'Supplier';
				if($vendor->save())
					$model->vendor_id = $vendor->v_id;

			}
			else
			{
				 $model->vendor_id = $vendor[0]->v_id;
			}

			//header('Content-type: text/plain');
			if($model->save())
			{
				$vc_id =  $model->getPrimaryKey();

				                           	
				if(isset($_POST['chairman']) && $_POST['chairman']!="")
				{     
					$member = new ContractMember;
					$member->vc_id = $vc_id;
					$member->name = $_POST['chairman'];
					$member->position = empty($_POST['chairman_position']) ? '':$_POST['chairman_position'] ;
					$member->type = 0;
					$member->save();

					//print_r($member);                    
                    $modelMember[] = $member;  

				}

				if(isset($_POST['commitee'])) 
				{     
					$index= 0;
					foreach ($_POST['commitee'] as $key => $value) {
						if($value !="")
						{
							$member = new ContractMember;
							$member->vc_id = $vc_id;
							$member->name = $value;
							$member->position = empty($_POST['commitee_position'][$index]) ? '':$_POST['commitee_position'][$index] ;
							$member->type = 1;
							$member->save();
							$modelMember[] = $member;

							//print_r($member);  
						}

						$index++;
					}
					

				}

				if(isset($_POST['taskmaster']) && $_POST['taskmaster']!="")
				{     
					$member = new ContractMember;
					$member->vc_id = $vc_id;
					$member->name = $_POST['taskmaster'];
					$member->position = empty($_POST['taskmaster_position']) ? '':$_POST['taskmaster_position'] ;
					$member->type = 2;
					$member->save();
					$modelMember[] = $member;

					//print_r($member);  

				}

				if(isset($_POST['vendor']) && $_POST['vendor']!="")
				{     
					$member = new ContractMember;
					$member->vc_id = $vc_id;
					$member->name = $_POST['vendor'];
					$member->position = empty($_POST['vendor_position']) ? '':$_POST['vendor_position'] ;
					$member->type = 3;
					$member->save();
					$modelMember[] = $member;
					//print_r($member);  

				}

				
				$this->redirect(array('updateVendorContract', 'id' => $model->id));
			}
			//print_r($model);
			//exit;		
				//$this->redirect(array('index'));
		}

		$this->render('createVendorContract',array(
			'model'=>$model,
		));

	}

	public function actionCreate()
	{
		$model=new Project;
		
		//header('Content-type: text/plain');
		if(isset($_POST['Project']))
		{
			$model->attributes = $_POST['Project'];
			$model->updated_by = Yii::app()->user->ID;
			$model->owner_name = $_POST['owner_name'];
			 
			$owner =  Vendor::model()->findAll(array('join'=>'','condition'=>'v_name="'.$_POST['owner_name'].'" AND type="Owner"'));
			//save new owner
			if(empty($owner))
			{
				$owner = new Vendor;
				$owner->type = 'Owner';
				$owner->v_name = $_POST['owner_name'];
				if($owner->save())
					$model->owner_id = $owner->v_id;

			}
			else{
				$model->owner_id = $owner[0]->v_id;				
			}

			//print_r($owner);
			//exit;
			if($model->save())
			{
				$this->redirect(array('createVendorContract', 'id' => $model->id));
			}
			
			
			
		}

		$this->render('create',array(
			'model'=>$model
		));
	}


	public function actionCreateBOQ($id)
	{	
		$model=$this->loadModel($id);
		
		
		$this->render('create',array(
			'model'=>$model
		));

	}

	
	public function actionUpdate($id)
	{	
		$model=$this->loadModel($id);
		
		if(isset($_POST['Project']))
		{
			$model->attributes = $_POST['Project'];
			$model->updated_by = Yii::app()->user->ID;
			$model->owner_name = $_POST['owner_name'];
			 
			$owner =  Vendor::model()->findAll(array('join'=>'','condition'=>'v_name="'.$_POST['owner_name'].'" AND type="Owner"'));
			//save new owner
			if(empty($owner))
			{
				$owner = new Vendor;
				$owner->type = 'Owner';
				$owner->v_name = $_POST['owner_name'];
				if($owner->save())
					$model->owner_id = $owner->v_id;

			}
			else{
				$model->owner_id = $owner[0]->v_id;				
			}

			//print_r($owner);
			//exit;
			if($model->save())
			{
				$this->redirect(array('index'));
			}
		}
		
		$this->render('update',array(
			'model'=>$model
		));

	}


	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdateVendorContract($id)
	{
		$model= VendorContract::model()->findByPk($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['VendorContract']))
		{
			$model->attributes=$_POST['VendorContract'];
			$model->updated_by = Yii::app()->user->ID;
			$model->approve_date = $_POST['VendorContract']['approve_date'];
			$model->end_date = $_POST['VendorContract']['end_date'];

			$vendor =  Vendor::model()->findAll(array('join'=>'','condition'=>'v_name="'.$_POST['vendor_id'].'" AND type="Supplier"'));
			//save new vendor
			if(empty($vendor))
			{
				$vendor = new Vendor;
				$vendor->v_name = $_POST['vendor_id'];
				$vendor->type = 'Supplier';
				if($vendor->save())
					$model->vendor_id = $vendor->v_id;

			}
			else
			{
				 $model->vendor_id = $vendor[0]->v_id;
			}


			if($model->save())
			{	

				$vc_id = $id;
				Yii::app()->db->createCommand('DELETE FROM contract_member WHERE vc_id='.$id)->execute();
				//header('Content-type: text/plain');
                           	 

				if(isset($_POST['chairman']) && $_POST['chairman']!="")
				{     
					$member = new ContractMember;
					$member->vc_id = $vc_id;
					$member->name = $_POST['chairman'];
					$member->position = empty($_POST['chairman_position']) ? '':$_POST['chairman_position'] ;
					$member->type = 0;
					$member->save();

					//print_r($member);                    
                    $modelMember[] = $member;  

				}

				if(isset($_POST['commitee'])) 
				{     
					$index= 0;
					foreach ($_POST['commitee'] as $key => $value) {
						if($value !="")
						{
							$member = new ContractMember;
							$member->vc_id = $vc_id;
							$member->name = $value;
							$member->position = empty($_POST['commitee_position'][$index]) ? '':$_POST['commitee_position'][$index] ;
							$member->type = 1;
							$member->save();
							$modelMember[] = $member;

							//print_r($member);  
						}

						$index++;
					}
					

				}

				if(isset($_POST['taskmaster']) && $_POST['taskmaster']!="")
				{     
					$member = new ContractMember;
					$member->vc_id = $vc_id;
					$member->name = $_POST['taskmaster'];
					$member->position = empty($_POST['taskmaster_position']) ? '':$_POST['taskmaster_position'] ;
					$member->type = 2;
					$member->save();
					$modelMember[] = $member;

					//print_r($member);  

				}

				if(isset($_POST['vendor']) && $_POST['vendor']!="")
				{     
					$member = new ContractMember;
					$member->vc_id = $vc_id;
					$member->name = $_POST['vendor'];
					$member->position = empty($_POST['vendor_position']) ? '':$_POST['vendor_position'] ;
					$member->type = 3;
					$member->save();
					$modelMember[] = $member;
					//print_r($member);  

				}

				$this->redirect(Yii::app()->request->urlReferrer);
			}
		}


		$this->render('updateVC',array(
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
			if(Yii::app()->user->isAdmin())
	       	{		
				if($this->loadModel($id)->delete()){
					$m_vc =  VendorContract::model()->findAll(array('join'=>'','condition'=>'proj_id='.$id));
					foreach ($m_vc as $key => $value) {

						if($value->delete())
						{//delete relation table
							Yii::app()->db->createCommand('DELETE FROM contract_member WHERE vc_id='.$value->id)->execute();
							Yii::app()->db->createCommand('DELETE FROM boq WHERE vc_id='.$value->id)->execute();
						}
					}
				}
			}
			else{

				$model = $this->loadModel($id);
				$model->flag_del = 1;
				$model->save();

				Yii::app()->db->createCommand('UPDATE vendor_contract SET flag_del=1 WHERE proj_id='.$id)->execute();
			}

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionDeleteVendorContract($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			

			// we only allow deletion via POST request
	       	if(Yii::app()->user->isAdmin())
	       	{		


					if(VendorContract::model()->findByPk($id)->delete()){
						Yii::app()->db->createCommand('DELETE FROM contract_member WHERE vc_id='.$id)->execute();
						Yii::app()->db->createCommand('DELETE FROM boq WHERE vc_id='.$id)->execute();


					}
			}
			else{

				$model = VendorContract::model()->findByPk($id);
				$model->flag_del = 1;
				$model->save();

				


			}				
			
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		// $model=new Project('search');
		// $model->unsetAttributes();  // clear any default values
		// if(isset($_GET['Project']))
		// 	$model->attributes=$_GET['Project'];

		$model = new VendorContract('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['VendorContract']))
			$model->attributes=$_GET['VendorContract'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Project('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Project']))
			$model->attributes=$_GET['Project'];

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
		$model=Project::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='project-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionImportBOQ()
	{
		
		$error = "";
		if(isset($_FILES['fileupload2']) && file_exists($_FILES['fileupload2']['tmp_name'])){
		
			
					$upload = true; // prevent manual input
					$name = $_FILES['fileupload2']['name'];
					$type = $_FILES['fileupload2']['type'];
					$tmp_name = $_FILES['fileupload2']['tmp_name'];

					$model = VendorContract::model()->findByPk($_POST['vc_id']);

					//move_uploaded_file($tmp_name,Yii::app()->basePath."/" . $name);
					$this->renderPartial('_formValidate',array(
						'filename'=>$tmp_name,'pay_no_current'=>$_POST['pay_no'],'model'=>$model
					));
					
		}
		else{
			$error = "file not found";
		}



	}

	public function actionExportBOQ()
    {

    	    $pay_no = $_GET['pay_no'];
		    $vc_id = $_GET['vc_id'];
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
		if($_GET['form']==1)
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
						if($value->unit=="Lot" || $value->unit=="lot")
							$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_DECIMAL );
						else
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
						$objValidation->setFormula1(0.0);
						$objValidation->setFormula2(floatval($amount_max-$amount_prev));

						//add formula
						$objPHPExcel->getActiveSheet()->setCellValue('L'.$row,0);
						if(is_numeric($value->price_item))
							$objPHPExcel->getActiveSheet()->setCellValue('M'.$row,'=PRODUCT(SUM(G'.$row.':H'.$row.'),L'.$row.')');
						else
							$objPHPExcel->getActiveSheet()->setCellValue('M'.$row,'-');

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
						if($value->unit=="Lot" || $value->unit=="lot")
							$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_DECIMAL );
						else
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
						$objValidation->setFormula1(0);
						$objValidation->setFormula2('=SUM(ค่าอุปกรณ์!J'.$row.',ค่าอุปกรณ์!L'.$row.')');

						//add formula
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$row,0);
						if(is_numeric($value->price_install))
						    $objPHPExcel->getActiveSheet()->setCellValue('L'.$row,'=PRODUCT(G'.$row.',K'.$row.')');
						else
                             $objPHPExcel->getActiveSheet()->setCellValue('L'.$row,'-');
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
		else if($_GET['form']==2)
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
		                        ->where("pay_type=3 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no <".$pay_no)
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
						if($value->unit=="Lot" || $value->unit=="lot")
							$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_DECIMAL );
						else
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
						$objValidation->setFormula1(0);
						$objValidation->setFormula2(floatval($amount_max-$amount_prev));

						//add formula
						$objPHPExcel->getActiveSheet()->setCellValue('M'.$row,0);
						if(is_numeric($value->price_item))
							$objPHPExcel->getActiveSheet()->setCellValue('N'.$row,'=PRODUCT(SUM(G'.$row.':I'.$row.'),M'.$row.')');
						else
							$objPHPExcel->getActiveSheet()->setCellValue('N'.$row,'-');

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
}
