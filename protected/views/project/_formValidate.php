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
    	
    ?>	