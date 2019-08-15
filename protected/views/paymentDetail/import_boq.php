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


	 <table class="table  table-bordered table-condensed" >
	     <thead>
	      <tr>
	        <th style="text-align:center;width:5%">ลำดับ</th>
	        <th style="text-align:center;width:20%">รายละเอียด</th>
	        <th style="text-align:center;width:5%">จำนวน</th>
	        <th style="text-align:center;width:5%">หน่วย</th>
	        <th style="text-align:center;width:8%">เบิกของแล้ว</th>
	        <th style="text-align:center;width:8%">เบิกของงวดนี้</th>
	        <th style="text-align:center;width:8%">เบิกติดตั้งแล้ว</th>
	        
	        <th style="text-align:center;width:8%">เบิกติดตั้งงวดนี้</th>
	        <th style="text-align:center;width:15%">ผิดเงื่อนไข</th>
	      </tr>
	    </thead>
	    <tbody>
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

		$payment = Yii::app()->db->createCommand()
			                        ->select('MAX(pay_no) as max_pay_no')
			                        ->from('payment')
			                        ->where("proj_id=".$boq->proj_id)
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
			                        ->where("pay_type=0 AND item_id='".$boq->id."' AND proj_id='".$boq->proj_id."' AND pay_no <".$pay_no)
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

			        }

			        //install
			        $payment = Yii::app()->db->createCommand()
			                        ->select('SUM(amount) as sum')
			                        ->from('payment')
			                        //->join('user','user_create=u_id')
			                        ->where("pay_type=2 AND item_id='".$boq->id."' AND proj_id='".$boq->proj_id."' AND pay_no <".$pay_no)
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
	         

			  $row++;
			}while($index!="");
		}
		else{
			echo "งวดเบิกจ่ายไม่ถูกต้อง";
		}

	

	          ?>


	    </tbody>
	  </table>
<center>

<?php


   
	// $model=new PaymentTemp('search');

	// $this->widget('bootstrap.widgets.TbGridView',array(
	// 	'id'=>'payment-detail-grid',
	// 	'type'=>'bordered condensed',
	// 	'dataProvider'=>$model->search(),
	// 	'filter'=>$model,
	// 	'selectableRows' =>2,
	// 	'htmlOptions'=>array('style'=>'padding-top:10px'),
	//     'enablePagination' => true,
	//     'summaryText'=>'แสดงผล {start} ถึง {end} จากทั้งหมด {count} ข้อมูล',
	//     'template'=>"{items}<div class='row-fluid'><div class='span6'>{pager}</div><div class='span6'>{summary}</div></div>",
	// 	'columns'=>array(
			
	//          'no'=>array(
 //                    'name' => 'no',
 //                    'filter' => false,
 //                  'headerHtmlOptions' => array('style' => 'width:5%;text-align:center;background-color: #f5f5f5'),                     
 //                  'htmlOptions'=>array('style'=>'text-align:center;font-weight:bold')
 //                ),
 //              'detail'=>array(
 //                    'name' => 'item_id',
 //                    //'class' => 'editable.EditableColumn',
 //                    'type'=>'raw',
 //                    'value'=> function($data){
 //                    	$m = Boq::model()->findByPk($data->item_id);
 //                        if($m->type==1 || $m->type==2)
 //                           return '<b>'.$m->detail.'</b>';
 //                        else if($m->type==-1)
 //                           return '-&nbsp;&nbsp;'.$m->detail;  
 //                        else 
 //                           return '&nbsp;&nbsp;&nbsp;'.$m->detail;
 //                    },  
 //                    'filter'=>CHtml::activeTextField($model, 'item_id',array("placeholder"=>"ค้นหาตาม".$model->getAttributeLabel("item_id"))),
 //                  'headerHtmlOptions' => array('style' => 'width:25%;text-align:center;background-color: #f5f5f5'),                     
 //                  'htmlOptions'=>array('style'=>'text-align:left;padding-left:10px;')
 //                ),
              
 //              'amount'=>array(
 //                  'header' => '<a class="sort-link">จำนวน</a>',
 //                  'value'=>' Boq::model()->findByPk($data->item_id)->amount',
 //                  'headerHtmlOptions' => array('style' => 'width:5%;text-align:center;background-color: #f5f5f5'),                     
 //                  'htmlOptions'=>array('style'=>'text-align:center')
 //                ),
 //                'unit'=>array(
                  
 //                  'header' => '<a class="sort-link">หน่วย</a>',
 //                  'value'=>' Boq::model()->findByPk($data->item_id)->unit',
 //                  'headerHtmlOptions' => array('style' => 'width:5%;text-align:center;background-color: #f5f5f5'),                     
 //                  'htmlOptions'=>array('style'=>'text-align:center')
 //                ),

 //                'price_item_prev'=>array(
                  
 //                  'header' => '<a class="sort-link">เบิกค่าของแล้ว</a>',
 //                  'value'=>' Boq::model()->findByPk($data->item_id)->amount',
 //                  'headerHtmlOptions' => array('style' => 'width:10%;text-align:center;background-color: #f5f5f5'),                     
 //                  'htmlOptions'=>array('style'=>'text-align:center')
 //                ),

 //                'price_item'=>array(
                  
 //                  'header' => '<a class="sort-link">เบิกค่าของครั้งนี้</a>',
 //                  'value'=>' Boq::model()->findByPk($data->item_id)->amount',
 //                  'headerHtmlOptions' => array('style' => 'width:10%;text-align:center;background-color: #f5f5f5'),                     
 //                  'htmlOptions'=>array('style'=>'text-align:center')
 //                ),
              
 //                'price_install_prev'=>array(
                  
 //                  'header' => '<a class="sort-link">เบิกค่าของค่าติดตั้งแล้ว</a>',
 //                  'value'=>' Boq::model()->findByPk($data->item_id)->amount',
 //                  'headerHtmlOptions' => array('style' => 'width:10%;text-align:center;background-color: #f5f5f5'),                     
 //                  'htmlOptions'=>array('style'=>'text-align:center')
 //                ),
 //                'price_install'=>array(
                  
 //                  'header' => '<a class="sort-link">เบิกค่าของค่าติดตั้งครั้งนี้</a>',
 //                  'value'=>' Boq::model()->findByPk($data->item_id)->amount',
 //                  'headerHtmlOptions' => array('style' => 'width:10%;text-align:center;background-color: #f5f5f5'),                     
 //                  'htmlOptions'=>array('style'=>'text-align:center')
 //                ),
 //                'error'=>array(
                  
 //                  'header' => '<a class="sort-link">ผิดเงื่อนไข</a>',
 //                  'value'=>' Boq::model()->findByPk($data->item_id)->amount',
 //                  'headerHtmlOptions' => array('style' => 'width:10%;text-align:center;background-color: #f5f5f5'),                     
 //                  'htmlOptions'=>array('style'=>'text-align:center')
 //                ),
			
	// 	),
	// 	)
	// );


?>