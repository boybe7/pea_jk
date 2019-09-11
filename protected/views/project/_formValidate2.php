<style type="text/css">
	.table td {
		text-align: center;
	}
	.range-blue {
		background-color: #97e597;
	}
	.range-yellow {
		background-color: #F5D800;
	}
	.range-red {
		background-color: #ff1a1a;
	}
	
</style>
<br><br><br><h5><u>ตรวจสอบการนำเข้าข้อมูล</u></h5>
<?php
Yii::import('ext.phpexcel.XPHPExcel');    
		$objPHPExcel= XPHPExcel::createPHPExcel();

		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$objPHPExcel = $objReader->load($filename);
    			
		//---------------------ค่าของ------------------------//
		$worksheet  = $objPHPExcel->setActiveSheetIndex(0);

		$row = 6;
		//current pay_no
		$first_index = 	$worksheet->getCell("A".$row)->getCalculatedValue();
		$boq = Boq::model()->findByPk($first_index);

		$vc_id = empty($boq) ? -1 : $boq->vc_id;

		$payment = Yii::app()->db->createCommand()
		                        ->select('form_type')
		                        ->from('payment_detail')
		                        ->where("vc_id='".$model->id."' AND pay_no =".$pay_no_current)
		                        ->queryAll();

		                        
		//echo "งวดปัจจุบันคือ ".$model->id."<br>"; 
       
       //delete payment_temp

		Yii::app()->db->createCommand('DELETE FROM payment_temp WHERE vc_id='.$model->id.' AND pay_no='.$pay_no_current)->execute();

	//---------------------FORM 1 ------------------------------------//
	if($payment[0]['form_type']==1)
	{
		
       echo ' <table class="table  table-bordered table-condensed" >
	     <thead>
	      <tr>
	        <th style="text-align:center;width:5%" rowspan=2>ลำดับ</th>
	        <th style="text-align:center;width:20%" rowspan=2>รายละเอียด</th>
	        <th style="text-align:center;width:5%" rowspan=2>จำนวน</th>
	        <th style="text-align:center;width:5%" rowspan=2>หน่วย</th>

	        <th style="text-align:center;width:8%" colspan=2>ค่าอุปกรณ์และค่าขนส่ง</th>
	        <th style="text-align:center;width:8%" colspan=2>ค่าติดตั้ง</th>
	        <th style="text-align:center;width:15%"  rowspan=2>ตรวจสอบเงื่อนไข</th>
	      </tr>
	      <tr>
	      	<th style="text-align:center;width:8%">รวมส่งมอบแล้ว</th>
	        <th style="text-align:center;width:8%">ส่งมอบงวดนี้</th>
	        <th style="text-align:center;width:8%">รวมส่งมอบแล้ว</th>
	        <th style="text-align:center;width:8%">ส่งมอบงวดนี้</th>
	      </tr>
	    </thead>
	    <tbody>';   
		


		//1.check pay_no
		$pay_no = 	$worksheet->getCell("M3")->getCalculatedValue();
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
	          	
	          	

	          	if(!empty($boq->amount) )
	            {
	            	//previous pay
	            	$payment = Yii::app()->db->createCommand()
			                        ->select('SUM(amount) as sum')
			                        ->from('payment')
			                        //->join('user','user_create=u_id')
			                        ->where("pay_type=0 AND item_id='".$boq->id."' AND vc_id='".$vc_id."' AND pay_no <".$pay_no)
			                        ->queryAll();
			        $item_amount_prev = $payment[0]["sum"];
			        $item_amount_pay = $worksheet->getCell("L".$row)->getCalculatedValue();

			        $amount_max  =  $boq->amount - $item_amount_prev;

			       
			        $error = "";
			       
			        if($item_amount_pay > $amount_max)
			        {
			        	//echo "-------------ข้อผิดพลาด-------------- <br>";
			        	//echo $boq->detail."<br>";
			        	//$invalid_file = true;
			        	//echo "จำนวนตามสัญญา : ".$boq->amount." | จำนวนค่าของที่เบิกแล้ว : ".$amount_prev." | จำนวนค่าของเบิกครั้งนี้ : ".$amount_pay;
			        	//echo "!***เบิกค่าของเกินสัญญา*** <br>";
			        	$error .= '***เบิกค่าของเกินสัญญา***';

			        }else{
			        	 //insert to payment temp for submit data
			        	if($item_amount_pay > 0)
			        	{
					        $payment_model = new PaymentTemp;
					        $payment_model->item_id = $boq->id;
					        $payment_model->vc_id = $vc_id;
					        $payment_model->pay_no = $pay_no;
					        $payment_model->pay_type = 0;
					        $payment_model->amount = $item_amount_pay;
					        $payment_model->user_id = Yii::app()->user->ID;;
					        $payment_model->save();
					    }    
			        }

			        //install
			        $payment = Yii::app()->db->createCommand()
			                        ->select('SUM(amount) as sum')
			                        ->from('payment')
			                        //->join('user','user_create=u_id')
			                        ->where("pay_type=2 AND item_id='".$boq->id."' AND vc_id='".$vc_id."' AND pay_no <".$pay_no)
			                        ->queryAll();
			        $amount_prev = $payment[0]["sum"];

			        $amount_pay = $objPHPExcel->getSheet(1)->getCell('K'.$row)->getValue();
			       	$amount_max  =  ($item_amount_prev+$item_amount_pay) - $amount_prev;


			        if($amount_prev+$amount_pay > $boq->amount)
			        {
			        	//echo "-------------ข้อผิดพลาด-------------- <br>";
			        	// echo $boq->detail."<br>";
			        	// $invalid_file = true;
			        	// echo "จำนวนตามสัญญา : ".$boq->amount." | จำนวนค่าติดตั้งที่เบิกแล้ว : ".$amount_prev." | จำนวนค่าติดตั้งเบิกครั้งนี้ : ".$amount_pay;
			        	$error .= "<br>***เบิกค่าติดตั้งเกินค่าของตามสัญญา*** <br>";
			        }
			        else if($amount_pay > $amount_max)
			        {
			        	// echo "-------------ข้อผิดพลาด-------------- <br>";
			        	// echo $boq->detail."<br>";
			        	// $invalid_file = true;
			        	// echo "จำนวนค่าของเบิกรวมครั้งนี้ : ".($item_amount_prev+$item_amount_pay)." | จำนวนค่าติดตั้งที่เบิกแล้ว : ".$amount_prev." | จำนวนค่าติดตั้งเบิกครั้งนี้ : ".$amount_pay;
			        	$error .= "!***ค่าติดตั้งเบิกเกินค่าของรวม*** <br>";
			        }else{
			        	if( $amount_pay > 0)
			        	{
				        	$payment_model = new PaymentTemp;
					        $payment_model->item_id = $boq->id;
					        $payment_model->vc_id = $vc_id;
					        $payment_model->pay_no = $pay_no;
					        $payment_model->pay_type = 2;
					        $payment_model->amount = $amount_pay;
					        $payment_model->user_id = Yii::app()->user->ID;;
					        $payment_model->save();
					    }    
			        }
			        
			        $class = $error=="" ? "range-blue" : 'range-red';
			        echo "<tr >";
	          	    echo "<td class='$class'>".$boq->no."</td>";
	          	    echo "<td class='$class' style='text-align:left'>".$boq->detail."</td>";
	          	    echo "<td class='$class'>".$boq->amount."</td>";
	          	    echo "<td class='$class'>".$boq->unit."</td>";
	          	    echo "<td class='$class'>".$item_amount_prev."</td>";
			        echo "<td class='$class'>".$item_amount_pay."</td>";
			        echo "<td class='$class'>".$amount_prev."</td>";
			        echo "<td class='$class'>".$amount_pay."</td>";
			        echo "<td class='$class'>".$error."</td>";

	            }	
	             else{
		          	echo "<tr>";
	          	    echo "<td>".$boq->no."</td>";
	          	    echo "<td style='text-align:left'>".$boq->detail."</td>";
	          	    echo "<td>".$boq->amount."</td>";
	          	    echo "<td>".$boq->unit."</td>";
	          	    
		          	echo "<td>-</td>";
		          	echo "<td>-</td>";
		          	echo "<td>-</td>";
		          	echo "<td>-</td>";
		          	echo "<td>-</td>";
		          }
		         echo "</tr>";
	          }
	          else{


	          	
	          }
	         

			  $row++;
			}while($index!="");
		}
		else{
			echo "<h5>ข้อผิดพลาด:</h5><div class='alert alert-danger'>งวดเบิกจ่ายไม่ถูกต้อง</div>";
		}

	}
	else if($payment[0]['form_type']==2)
	{
		
		 echo ' <table class="table  table-bordered table-condensed" >
	     <thead>
	      <tr>
	        <th style="text-align:center;width:5%" rowspan=2>ลำดับ</th>
	        <th style="text-align:center;width:20%" rowspan=2>รายละเอียด</th>
	        <th style="text-align:center;width:5%" rowspan=2>จำนวน</th>
	        <th style="text-align:center;width:5%" rowspan=2>หน่วย</th>

	        <th style="text-align:center;width:15%" colspan=2 >ค่าอุปกรณ์ ค่าขนส่ง และค่าติดตั้ง</th>

	        <th style="text-align:center;width:15%" rowspan=2>ตรวจสอบเงื่อนไข</th>
	      </tr>
	      <tr>
	       

	        <th style="text-align:center;width:15%" >รวมส่งมอบแล้ว</th>
	        <th style="text-align:center;width:15%">ส่งมอบงวดนี้</th>

	      </tr>
	    </thead>
	    <tbody>';   
		

		//1.check pay_no
		$pay_no = 	$worksheet->getCell("N3")->getCalculatedValue();
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
	          	
	          	

	          	if(!empty($boq->amount))
	            {
	            	//previous pay
	            	$payment = Yii::app()->db->createCommand()
			                        ->select('SUM(amount) as sum')
			                        ->from('payment')
			                        //->join('user','user_create=u_id')
			                        ->where("pay_type=3 AND item_id='".$boq->id."' AND vc_id='".$vc_id."' AND pay_no <".$pay_no)
			                        ->queryAll();
			        $item_amount_prev = $payment[0]["sum"];
			        $item_amount_prev = empty($item_amount_prev) ? 0 : $item_amount_prev;
			        $item_amount_pay = $worksheet->getCell("M".$row)->getCalculatedValue();

			        $amount_max  =  $boq->amount - $item_amount_prev;

			       

			        $error = "";
			       
			        if($item_amount_pay > $amount_max)
			        {
			        	//echo "-------------ข้อผิดพลาด-------------- <br>";
			        	//echo $boq->detail."<br>";
			        	//$invalid_file = true;
			        	//echo "จำนวนตามสัญญา : ".$boq->amount." | จำนวนค่าของที่เบิกแล้ว : ".$amount_prev." | จำนวนค่าของเบิกครั้งนี้ : ".$amount_pay;
			        	//echo "!***เบิกค่าของเกินสัญญา*** <br>";
			        	$error .= '***เบิกเกินสัญญา***';

			        }
			        else{
			        	 //insert to payment temp for submit data
			        	if($item_amount_pay>0)
			        	{
					        $payment_model = new PaymentTemp;
					        $payment_model->item_id = $boq->id;
					        $payment_model->vc_id = $vc_id;
					        $payment_model->pay_no = $pay_no;
					        $payment_model->pay_type = 3;
					        $payment_model->amount = $item_amount_pay;
					        $payment_model->user_id = Yii::app()->user->ID;;
					        $payment_model->save();
					    }    

			        }

			       
			        
			        $class = $error=="" ? "range-blue" : 'range-red';
			        echo "<tr >";
	          	    echo "<td class='$class'>".$boq->no."</td>";
	          	    echo "<td class='$class' style='text-align:left'>".$boq->detail."</td>";
	          	    echo "<td class='$class'>".$boq->amount."</td>";
	          	    echo "<td class='$class'>".$boq->unit."</td>";
	          	    echo "<td class='$class'>".$item_amount_prev."</td>";
			        echo "<td class='$class'>".$item_amount_pay."</td>";
			       
			        echo "<td class='$class'>".$error."</td>";

	            }	
	             else{
		          	echo "<tr>";
	          	    echo "<td>".$boq->no."</td>";
	          	    echo "<td style='text-align:left'>".$boq->detail."</td>";
	          	    echo "<td>".$boq->amount."</td>";
	          	    echo "<td>".$boq->unit."</td>";
	          	    
		          	echo "<td>-</td>";
		          	echo "<td>-</td>";
		          	
		          	echo "<td>-</td>";
		          }
		         echo "</tr>";
	          }
	         

			  $row++;
			}while($index!="");
		}
		else{
			echo "<h5>ข้อผิดพลาด:</h5><div class='alert alert-danger'>งวดเบิกจ่ายไม่ถูกต้อง</div>";
		}
	}

	          ?>


	    </tbody>
	  </table>
<center>