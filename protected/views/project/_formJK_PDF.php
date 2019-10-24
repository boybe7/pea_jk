<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes

function renderDate($value)
{
          $th_month = array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
          $dates = explode("/", $value);
          $renderDate = 0;
          if(count($dates)==3)
             $renderDate = $dates[0]." ".$th_month[intval($dates[1])]." ".$dates[2];
          if($renderDate==0)
              $renderDate = "";   

          return $renderDate;             
}

function formatMoney($number, $cents = 1) { // cents: 0=never, 1=if needed, 2=always
  if (is_numeric($number)) { // a number
    if (!$number) { // zero
      $money = ($cents == 2 ? '0.00' : '0'); // output zero
    } else { // value
      if (floor($number) == $number) { // whole number
        $money = number_format($number, ($cents == 2 ? 2 : 0)); // format
      } else { // cents
        $money = number_format(round($number, 2), ($cents == 0 ? 0 : 2)); // format
      } // integer or decimal
    } // value
    return $money;
  } // numeric
} // formatMoney


function renderDate2($value)
{
    $th_month = array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
    $dates = explode("-", $value);
    $d=0;
    $mi = 0;
    $yi = 0;
    foreach ($dates as $key => $value) {
         $d++;
         if($d==2)
            $mi = $value;
         if($d==1)
            $yi = $value;
    }
    if(substr($mi, 0,1)==0)
        $mi = substr($mi, 1);
    if(substr($dates[2], 0,1)==0)
        $d = substr($dates[2], 1);
    else
        $d = $dates[2];

    $renderDate = $d." ".$th_month[$mi]." ".$yi;
    if($renderDate==0)
        $renderDate = "";   

    return $renderDate;             
}

function bahtText($amount)
{
    $integer = explode('.', number_format(abs($amount), 2, '.', ''));

    $baht = convert($integer[0]);
    $satang = convert($integer[1]);

    $output = $amount < 0 ? 'ลบ' : '';
    $output .= $baht ? $baht.'บาท' : '';
    $output .= $satang ? $satang.'สตางค์' : 'ถ้วน';

    return $baht.$satang === '' ? 'ศูนย์บาทถ้วน' : $output;
}

function convert($number)
{
    $values = ['', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า'];
    $places = ['', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'];
    $exceptions = ['หนึ่งสิบ' => 'สิบ', 'สองสิบ' => 'ยี่สิบ', 'สิบหนึ่ง' => 'สิบเอ็ด'];

    $output = '';

    foreach (str_split(strrev($number)) as $place => $value) {
        if ($place % 6 === 0 && $place > 0) {
            $output = $places[6].$output;
        }

        if ($value !== '0') {
            $output = $values[$value].$places[$place % 6].$output;
        }
    }

    foreach ($exceptions as $search => $replace) {
        $output = str_replace($search, $replace, $output);
    }

    return $output;
}

// Include the main TCPDF library (search for installation path).
require_once('/../tcpdf/tcpdf.php');

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        
        // Set font
        //$this->SetFont('helvetica', 'B', 20);
        // Title
        //$this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-10);
        // Set font
        $this->SetFont('thsarabun', '', 11);
        // Page number
        //$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        // Logo
        //$image_file = 'bank/image/mwa2.jpg';
        //$this->Image($image_file, 170, 270, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        //$this->Cell(0, 5, date("d/m/Y"), 0, false, 'R', 0, '', 0, false, 'T', 'M');

        //$this->writeHTMLCell(145, 550, 70, 200, '-'.$this->getAliasNumPage().'/'.$this->getAliasNbPages().'-', 0, 1, false, true, 'C', false);
        //writeHTMLCell ($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=false, $reseth=true, $align='', $autopadding=true)
    }
}

// create new PDF document
//$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new MYPDF('L', PDF_UNIT, 'A3', true, 'UTF-8', false);
$pdf->SetFont('thsarabun', '', 11, '', true);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Boybe');
$pdf->SetTitle('ฟอร์ม จค.');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setPrintHeader(false);
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(7, 5, 3);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetAutoPageBreak(TRUE, 5);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font


// Add a page
// This method has several options, check the source code documentation for more information.
//$pdf->AddPage();

// set text shadow effect
//$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
$html = "";
//$vc_id = 24;
//$pay_no = 1;
//$max_row = 29;

    $model_vc = VendorContract::model()->findByPk($vc_id);

    //----------------------style-------------------------------//
    $border_left_top = 'border-left: 1px solid black;border-top: 1px solid black;';
    $border_left_bottom = 'border-left: 1px solid black;border-bottom: 1px solid black;';
    $border_right_top = 'border-right: 1px solid black;border-top: 1px solid black;';
    $border_right_bottom = 'border-right: 1px solid black;border-bottom: 1px solid black;';
    $border_left_right = 'border-right: 1px solid black;border-left: 1px solid black;border-bottom: 1px solid grey;';
    $border_left_right2 = 'border-right: 1px solid black;border-left: 1px solid black;border-bottom: 1px solid black;';

     //committee
        $modelMember = ContractMember::model()->findAll('vc_id =:id AND type=0', array(':id' => $vc_id));
        $committee_header = empty($modelMember) ? new ContractMember : $modelMember[0]; 
        $committee_member = ContractMember::model()->findAll('vc_id =:id AND type=1', array(':id' => $vc_id));
        $modelMember = ContractMember::model()->findAll('vc_id =:id AND type=2', array(':id' => $vc_id));
        $committee_control = empty($modelMember) ? new ContractMember : $modelMember[0]; 
        $modelMember = ContractMember::model()->findAll('vc_id =:id AND type=3', array(':id' => $vc_id));
        $committee_vendor = empty($modelMember) ? new ContractMember : $modelMember[0]; 


     //find form type   
     $modelPD = PaymentDetail::model()->findAll('vc_id =:id AND pay_no=:pay_no', array(':id' => $vc_id,':pay_no'=>$pay_no));      
     $form_type = !empty($modelPD) ? $modelPD[0]->form_type : 1;
           

if($form_type==1)
{ 
    //---------------------------FORM 1 ------------------------------------------//
    //---------------------------อุปกรณ์ ขนส่ง------------------------------------------//
    //header
     $html .= '<div style="text-align:center"><u>การไฟฟ้าส่วนภูมิภาค</u><br><u>รายละเอียดของงานเพื่อขออนุมัติเบิกจ่ายเงิน</u></div>';
     $html .= "<table border=0>";
            $html .= '<tr><td colspan=2 style="width:80%"></td><td style="width:20%;text-align:right">แบบฟอร์ม จค.1</td></tr>';
            $html .= '<tr><td style="width:80%;text-align:center;'.$border_left_top.' ">'.$model_vc->name.'</td><td colspan=2 style="width:20%;text-align:center; '.$border_right_top.'"></td></tr>';

            $detail = "สัญญาเลขที่ ".$model_vc->contract_no."   ลงวันที่   ".renderDate($model_vc->approve_date) ."   จำนวนเงินตามสัญญา ".number_format($model_vc->budget,0)."  บาท (ไม่รวมภาษีมูลค่าเพิ่ม)   ผู้รับจ้าง  ".Vendor::model()->findByPk($model_vc->vendor_id)->v_name.'  กำหนดแล้วเสร็จตามสัญญา วันที่  '.renderDate($model_vc->end_date);
            if(!empty($model_vc->detail_approve))
                 $detail .= "<br>".$model_vc->detail_approve;

            $html .= '<tr><td style="width:80%;text-align:center; '.$border_left_bottom.'">'.$detail.'</td><td style="width:10%;text-align:center; border-bottom: 1px solid black;">ค่าอุปกรณ์และขนส่ง</td><td style="width:10%;text-align:center; '.$border_right_bottom.'">งวดที่ '.$pay_no.'</td></tr>';
     $html .= "</table>";
    
    //details
    $html .= "<table border=0>";
            $html .= '<tr><td colspan=9 style="background-color:#fff18a;width:56%;text-align:center;border: 1px solid black;">สำหรับผู้รับจ้าง</td><td colspan=8 style="background-color:#bcff70;width:44%;text-align:center;border: 1px solid black;">สำหรับการไฟฟ้าส่วนภูมิภาค</td></tr>';
            $row_com = 6;
            $html .=  '<tr>

                          <td style="text-align:center;width:2%;border:1px solid black;" rowspan="2">&nbsp;<br>ลำดับ</td>
                          <td  style="text-align:center;width:26%;border:1px solid black;" rowspan="2">&nbsp;<br>รายละเอียด</td>
                          <td style="text-align:center;width:20%;border:1px solid black;" colspan=5>งานตามสัญญา</td>
                          <td style="text-align:center;width:8%;border:1px solid black;" colspan=2>ส่งมอบงานครั้งนี้</td>


                          <td style="text-align:center;width:8%;border:1px solid black;" colspan=2>รวมงานที่ส่งแล้ว (รวมครั้งนี้)</td>
                          <td style="text-align:center;width:8%;border:1px solid black;" colspan=2>สรุปคณะกรรมการตรวจรับ</td>
                          <td style="text-align:center;width:3%;border:1px solid black;" rowspan="2">&nbsp;<br>หมายเหตุ</td>
                          <td rowspan="'.($row_com+2).'" colspan=3 style="width:25%;'.$border_right_top.'">  
                              <br><br>&nbsp;&nbsp; เรียน &nbsp;&nbsp;&nbsp;คณะกรรมการตรวจรับงานจ้าง <br>
                              &#9633; ผลงานที่ส่งมอบครั้งนี้ มีรายละเอียดครบถ้วนถูกต้องตามข้อกำหนดในสัญญาทุกประการ <br>
                              &#9633; ผลงานที่ส่งมอบครั้งนี้ ก่อสร้างแล้วเสร็จเมื่อวันที่ …………………………………… <br>
                              &#9633; งานแล้วเสร็จภายในกำหนดเวลาตามสัญญา  <br> 
                              &#9633; งานแล้วเสร็จช้ากว่ากำหนดตามสัญญา……………วัน <br>
                              &nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ ………………………………………………………  ผู้ควบคุมงาน <br>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$committee_control->name.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ตำแหน่ง&nbsp;'.$committee_control->position.'  <br>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;วันที่ ……………………………………  <br>
                          </td>
                                                   
                        </tr>
                        <tr>

                          <td style="text-align:center;width:2%;border:1px solid black;">จำนวน</td>
                          <td style="text-align:center;width:2%;border:1px solid black;">หน่วย</td>
                          <td style="text-align:center;width:6%;border:1px solid black;">ค่าอุปกรณ์<br>ต่อหน่วย</td>
                          <td style="text-align:center;width:5%;border:1px solid black;">ค่าขนส่ง<br>ต่อหน่วย</td>
                          <td style="text-align:center;width:5%;border:1px solid black;">เป็นเงิน<br>(บาท)</td>

                          <td style="text-align:center;width:2%;border:1px solid black;">จำนวน</td>
                          <td style="text-align:center;width:6%;border:1px solid black;">เป็นเงิน<br>(บาท)</td>

                          <td style="text-align:center;width:2%;border:1px solid black;">จำนวน</td>
                          <td style="text-align:center;width:6%;border:1px solid black;">เป็นเงิน<br>(บาท)</td>

                          <td style="text-align:center;width:2%;border:1px solid black;">จำนวน</td>
                          <td style="text-align:center;width:6%;border:1px solid black;">เป็นเงิน<br>(บาท)</td>

                          
                          
                        </tr>';
           
           
        $header_table = $html;    



            //header
      $header_table2 = '<div style="text-align:center"><u>การไฟฟ้าส่วนภูมิภาค</u><br><u>รายละเอียดของงานเพื่อขออนุมัติเบิกจ่ายเงิน</u></div>';
      $header_table2 .= "<table border=0>";
             $header_table2 .= '<tr><td colspan=2 style="width:80%"></td><td style="width:20%;text-align:right">แบบฟอร์ม จค.1</td></tr>';
             $header_table2 .= '<tr><td style="width:80%;text-align:center;'.$border_left_top.' ">'.$model_vc->name.'</td><td colspan=2 style="width:20%;text-align:center; '.$border_right_top.'"></td></tr>';

             $detail = "สัญญาเลขที่ ".$model_vc->contract_no."   ลงวันที่   ".renderDate($model_vc->approve_date) ."   จำนวนเงินตามสัญญา ".number_format($model_vc->budget,0)."  บาท (ไม่รวมภาษีมูลค่าเพิ่ม)   ผู้รับจ้าง  ".Vendor::model()->findByPk($model_vc->vendor_id)->v_name.'  กำหนดแล้วเสร็จตามสัญญา วันที่  '.renderDate($model_vc->end_date);

             if(!empty($model_vc->detail_approve))
                 $detail .= "<br>".$model_vc->detail_approve;


             $header_table2 .= '<tr><td style="width:80%;text-align:center; '.$border_left_bottom.'">'.$detail.'</td><td style="width:10%;text-align:center; border-bottom: 1px solid black;">ค่าติดตั้งและทดสอบ</td><td style="width:10%;text-align:center; '.$border_right_bottom.'">งวดที่ '.$pay_no.'</td></tr>';
      $header_table2 .= "</table>";
    
     //details
     $header_table2 .= "<table border=0>";
             $header_table2 .= '<tr><td colspan=8 style="background-color:#fff18a;width:56%;text-align:center;border: 1px solid black;">สำหรับผู้รับจ้าง</td><td colspan=8 style="background-color:#bcff70;width:44%;text-align:center;border: 1px solid black;">สำหรับการไฟฟ้าส่วนภูมิภาค</td></tr>';
             $row_com = 6;
             $header_table2 .=  '<tr>

                          <td style="text-align:center;width:2%;border:1px solid black;" rowspan="2">&nbsp;<br>ลำดับ</td>
                          <td  style="text-align:center;width:30%;border:1px solid black;" rowspan="2">&nbsp;<br>รายละเอียด</td>
                          <td style="text-align:center;width:16%;border:1px solid black;" colspan=4>งานตามสัญญา</td>
                          <td style="text-align:center;width:8%;border:1px solid black;" colspan=2>ส่งมอบงานครั้งนี้</td>


                          <td style="text-align:center;width:8%;border:1px solid black;" colspan=2>รวมงานที่ส่งแล้ว (รวมครั้งนี้)</td>
                          <td style="text-align:center;width:8%;border:1px solid black;" colspan=2>สรุปคณะกรรมการตรวจรับ</td>
                          <td style="text-align:center;width:3%;border:1px solid black;" rowspan="2">&nbsp;<br>หมายเหตุ</td>
                          <td rowspan="'.($row_com+2).'" colspan=3 style="width:25%;'.$border_right_top.'">  
                              <br><br>&nbsp;&nbsp; เรียน &nbsp;&nbsp;&nbsp;คณะกรรมการตรวจรับงานจ้าง <br>
                              &#9633; ผลงานที่ส่งมอบครั้งนี้ มีรายละเอียดครบถ้วนถูกต้องตามข้อกำหนดในสัญญาทุกประการ <br>
                              &#9633; ผลงานที่ส่งมอบครั้งนี้ ก่อสร้างแล้วเสร็จเมื่อวันที่ …………………………………… <br>
                              &#9633; งานแล้วเสร็จภายในกำหนดเวลาตามสัญญา  <br> 
                              &#9633; งานแล้วเสร็จช้ากว่ากำหนดตามสัญญา……………วัน <br>
                              &nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ ………………………………………………………  ผู้ควบคุมงาน <br>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$committee_control->name.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ตำแหน่ง&nbsp;'.$committee_control->position.'  <br>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;วันที่ ……………………………………  <br>
                          </td>
                                                   
                        </tr>
                        <tr>

                          <td style="text-align:center;width:2%;border:1px solid black;">จำนวน</td>
                          <td style="text-align:center;width:3%;border:1px solid black;">หน่วย</td>
                          <td style="text-align:center;width:6%;border:1px solid black;">ราคาติดตั้ง/ทดสอบ<br>ต่อหน่วย</td>
                          
                          <td style="text-align:center;width:5%;border:1px solid black;">เป็นเงิน<br>(บาท)</td>

                          <td style="text-align:center;width:2%;border:1px solid black;">จำนวน</td>
                          <td style="text-align:center;width:6%;border:1px solid black;">เป็นเงิน<br>(บาท)</td>

                          <td style="text-align:center;width:2%;border:1px solid black;">จำนวน</td>
                          <td style="text-align:center;width:6%;border:1px solid black;">เป็นเงิน<br>(บาท)</td>

                          <td style="text-align:center;width:2%;border:1px solid black;">จำนวน</td>
                          <td style="text-align:center;width:6%;border:1px solid black;">เป็นเงิน<br>(บาท)</td>

                          
                          
                        </tr>';






        $Criteria = new CDbCriteria();
        $Criteria->condition = "vc_id=$vc_id";
        $boq = Boq::model()->findAll($Criteria); 

        $Criteria = new CDbCriteria();
        $Criteria->condition = "vc_id=$vc_id";
        $fineModel = Fine::model()->findAll($Criteria); 

       

        //----table config----//
        //$max_row = 35;
        $row_height = 20;
        $max_page = ceil(count($boq)*1.0 / $max_row); 
        //$html .="max_page:".$max_page;

        $row = 0;
        $page = 1;
        $summary_cost_all = 0;
        $summary_curr_all = 0;
        $summary_prev_all = 0;
        $summary_cost_page = 0;
        $summary_curr_page = 0;
        $summary_prev_page = 0;

        //-----install------
        $row2 = 0;
        $page2 = 1;
        $summary_cost_all2 = 0;
        $summary_curr_all2 = 0;
        $summary_prev_all2 = 0;
        $summary_cost_page2 = 0;
        $summary_curr_page2 = 0;
        $summary_prev_page2 = 0;
        $html2 = "";


        foreach ($boq as $key => $value) {


                //------------------------------Item & Transport------------------------------//
                $html .='<tr>';
                  //$html .='<td style="height:'.$row_height.'px;text-align:center;'.$border_left_right.'">'.$row.'</td>';
                  $html .='<td style="height:'.$row_height.'px;text-align:center;'.$border_left_right.'">'.$value->no.'</td>';


                  if($value->type==1 || $value->type==2)
                      $detail = '<b>'.$value->detail.'</b>';
                  else if($value->indent!="")
                      $detail = $value->indent.'&nbsp;&nbsp;'.$value->detail;  
                  else 
                      $detail = '&nbsp;&nbsp;&nbsp;'.$value->detail;   

                  $html .= '<td style="'.$border_left_right.'"> '.$detail.'</td>';
                  
                  $html .= '<td style="text-align:center;'.$border_left_right.'">'.$value->amount.'</td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'">'.$value->unit.'</td>';
                  
                  $price_item = is_numeric($value->price_item) ? number_format($value->price_item,2) : $value->price_item;
                  $price_trans = is_numeric($value->price_trans) ? number_format($value->price_trans,2) : $value->price_trans;
                  $price_install = is_numeric($value->price_install) ? number_format($value->price_install,2) : $value->price_install;

                  if(!is_numeric($value->price_item) && !is_numeric($value->price_trans) && $value->price_item==$value->price_trans && $value->price_item!="")
                  {
                     $html .= '<td colspan="2" style="text-align:center;'.$border_left_right.'">'.$price_item.'</td>';
                  }
                  else
                  {
                      $html .= '<td style="text-align:right;'.$border_left_right.'">'.$price_item.'</td>';
                      $html .= '<td style="text-align:right;'.$border_left_right.'">'.$price_trans.'</td>';
                  }
                  
                  $price_item_all = '';



                    if(!empty($value->amount) )
                    { 
                        $price_item = is_numeric($value->price_item) ? $value->price_item : 0;
                        $price_trans = is_numeric($value->price_trans) ? $value->price_trans : 0;
                        
                        $price_item_all = ($price_item + $price_trans) * $value->amount;

                        $summary_cost_page += $price_item_all;
                        $summary_cost_all += $price_item_all;

                        if(!is_numeric($value->price_item) && !is_numeric($value->price_trans))
                          $html .= '<td style="text-align:center;'.$border_left_right.'">'.$value->price_item.'</td>';
                        else  
                          $html .= '<td style="text-align:right;'.$border_left_right.'">'.number_format($price_item_all,2).'</td>';
                    }
                    else
                        $html .= '<td style="text-align:right;'.$border_left_right.'">'.$price_item_all.'</td>';

                    //amount current payment
                    $curr_payment = Yii::app()->db->createCommand()
                                    ->select('*')
                                    ->from('payment')
                                    ->where("pay_type=0 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no =".$pay_no)
                                    ->queryAll();
                    $current_payment = "";                
                    if(!empty($curr_payment))
                    {
                      $html .= '<td style="text-align:center;'.$border_left_right.'">'.$curr_payment[0]['amount'].'</td>';
                      $current_payment = $curr_payment[0]['amount'];
                      $price_item_all = ($price_item + $price_trans) * $curr_payment[0]['amount'];

                      $summary_curr_page += $price_item_all;
                      $summary_curr_all += $price_item_all;

                     if(!is_numeric($value->price_item) && !is_numeric($value->price_trans))
                          $html .= '<td style="text-align:center;'.$border_left_right.'">'.$value->price_item.'</td>';
                        else  
                          $html .= '<td style="text-align:right;'.$border_left_right.'">'.number_format($price_item_all,2).'</td>';

                    } 
                    else{
                      $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                      if(empty($value->amount))
                        $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';  
                      else
                         $html .= '<td style="text-align:center;'.$border_left_right.'">-</td>';
                    }               

                    //amount previous with current payment  
                    $prev_payment = Yii::app()->db->createCommand()
                                    ->select('SUM(amount) as amount')
                                    ->from('payment')
                                    ->where("pay_type=0 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no <=".$pay_no)
                                    ->queryAll();     

                    if(!empty($prev_payment) and $prev_payment[0]['amount']>0)
                    {
                      $html .= '<td style="text-align:center;'.$border_left_right.'">'.$prev_payment[0]['amount'].'</td>';
                      $price_item_all = ($price_item + $price_trans) * $prev_payment[0]['amount'];

                      $summary_prev_page += $price_item_all;
                      $summary_prev_all += $price_item_all;

                      if(!is_numeric($value->price_item) && !is_numeric($value->price_trans))
                          $html .= '<td style="text-align:center;'.$border_left_right.'">'.$value->price_item.'</td>';
                        else  
                           $html .= '<td style="text-align:right;'.$border_left_right.'">'.number_format($price_item_all,2).'</td>';

                    } 
                    else{
                      $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                      if(empty($value->amount))
                        $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';  
                      else
                         $html .= '<td style="text-align:center;'.$border_left_right.'">-</td>';  
                    }   


                  //committee check  

                  if(!empty($curr_payment))
                  {
                    $html .= '<td style="text-align:center;'.$border_left_right.'">'.$curr_payment[0]['amount'].'</td>';
                    $current_payment = $curr_payment[0]['amount'];
                    $price_item_all = ($price_item + $price_trans) * $curr_payment[0]['amount'];
                   if(!is_numeric($value->price_item) && !is_numeric($value->price_trans))
                        $html .= '<td style="text-align:center;'.$border_left_right.'">'.$value->price_item.'</td>';
                      else  
                        $html .= '<td style="text-align:right;'.$border_left_right.'">'.number_format($price_item_all,2).'</td>';

                  } 
                  else{
                    $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                    if(empty($value->amount))
                        $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';  
                      else
                         $html .= '<td style="text-align:center;'.$border_left_right.'">-</td>';
                  }         

                  //note
                  $html .= '<td style="width:3%;text-align:center;'.$border_left_right.'"></td>'; 

                  if($page==2)
                     $row_com = 7;
            
                  if($row==$row_com + ($max_row*($page-1)))
                  {
                      $html .= '<td colspan=3 style="width:25%;text-align:left;'.$border_right_bottom.'"></td>'; 
                  }
                  else if($row>$row_com + ($max_row*($page-1)) )
                  {
                    if($page!=$max_page)  
                      $html .= '<td colspan=3 style="width:25%;text-align:left;border-right:1px solid black;">'.$row_com.'</td>'; 
                    else{
                      if($row== $max_row*($page-1) + $row_com+1)
                      {
                          $rowspan = $max_row - $row_com;
                          
                          $payment_all = Yii::app()->db->createCommand()
                                    ->select('SUM(payment.amount*(price_item+price_trans)) as amount')
                                    ->from('payment')
                                    ->join('boq','boq.id=payment.item_id')
                                    ->where(" pay_type=0 AND payment.vc_id='".$vc_id."' AND pay_no =".$pay_no)
                                    ->queryAll();  

                          $summary_curr = $payment_all[0]['amount'];          

                          if($model_vc->percent_adv!=0)
                          {  
                             $advance_pay = ($model_vc->percent_adv/100.0) * ($summary_curr*($model_vc->percent_pay/100.0));
                             $advance_pay_str = number_format($advance_pay,2);
                          }else{
                             $advance_pay = 0;
                             $advance_pay_str = "-"; 
                          }
                               

                          $remain_pay = ($summary_curr*($model_vc->percent_pay/100.0)) - $advance_pay;

                          $fine_all = 0;  
                          $fine_html = "";
                          $fine_count = count($fineModel);
                          $fi = 0;

                          $number_style = "border-bottom:1px dotted grey;text-align:right;";
                          foreach ($fineModel as $key => $fine) {
                            if($fi==0)
                            {
                              $fine_html .= '<tr><td width="10%">&nbsp;&nbsp;&nbsp;&nbsp;<u>หัก</u></td><td width="40%">-  '.$fine->detail.'</td><td width="40%" style="'.$number_style.'">'.number_format($fine->amount,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td width="10%">&nbsp;&nbsp;บาท</td></tr>';


                            }
                            else
                            {
                              $fine_html .= '<tr><td></td><td width="40%">-  '.$fine->detail.'</td><td width="40%" style="'.$number_style.'">'.number_format($fine->amount,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td width="10%">&nbsp;&nbsp;บาท</td></tr>';

                            }
                            
                            $fine_all += $fine->amount;

                            $fi++;
                          }


                          if(empty($fineModel))
                          {
                             $fine_html .= '<tr><td width="10%">&nbsp;&nbsp;&nbsp;&nbsp;<u>หัก</u></td><td width="40%">- </td><td width="40%" style="'.$number_style.'">-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td width="10%">&nbsp;&nbsp;บาท</td></tr>';

                          }

                           //$html .= '<td colspan=3 style="width:25%;text-align:left;border-right:1px solid black;">'.$row.'</td>'; 

                    
                       

                          $html .= '<td rowspan="'.$rowspan.'" colspan=3 style="width:25%;text-align:left;'.$border_right_bottom.'"><br><br>&nbsp; เรียน &nbsp;……………………………………………………………………………<br>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; คณะกรรมการตรวจรับงานจ้างได้ทำการตรวจรับงานดังกล่าวแล้ว <br>
                               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;เมื่อวันที่ …………………………………… ปรากฎว่า<br>
                              &#9633; ถูกต้องครบถ้วนเป็นไปตามสัญญาทุกประการ เห็นควรรับมอบงานและจ่ายเงินให้แก่ผู้รับจ้างดังนี้  <br> 
                              &#9633; ผู้รับจ้างส่งมอบงานมีรายละเอียดส่วนใหญ่ถูกต้องตามสัญญา และมีรายละเอียดส่วนย่อยที่ <br>
                              &nbsp;&nbsp;&nbsp; ไม่ใช่สาระสำคัญแตกต่างจากสัญญา  และไม่ก่อให้เกิดความเสียหายต่อการใช้งาน จึงเห็นควร <br>
                              &nbsp;&nbsp;&nbsp; รับมอบงาน  และอนุมัติจ่ายเงินให้แก่ผู้รับจ้างดังนี้ <br>
                              <table border=0 width="70%">
                                <tr><td colspan=2 width="50%">&nbsp;&nbsp;&nbsp;&nbsp;ค่าจ้าง 100%</td><td width="40%" style="border-bottom:1px dotted grey;text-align:right">'.number_format($summary_curr,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td width="10%">&nbsp;&nbsp;บาท</td></tr>

                                <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;เบิก '.$model_vc->percent_pay.' %</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($summary_curr*$model_vc->percent_pay/100.0,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                                <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;หัก Advance '.$model_vc->percent_adv.' %</td><td style="border-bottom:1px dotted grey;text-align:right">'.$advance_pay_str.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                                <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;คงเหลือ</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($remain_pay,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                                <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;บวก ค่าภาษีมูลค่าเพิ่ม 7%</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($remain_pay*0.07,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                                <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;คงเหลือจ่าย</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($remain_pay*1.07,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                                '.$fine_html.'
                                <tr><td width="10%">&nbsp;</td><td width="40%">คงจ่ายสุทธิ</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($remain_pay*1.07 - $fine_all,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>
                                <tr><td colspan="3" style="width:100%;text-align:center">('.bahtText($remain_pay*1.07-$fine_all).')</td></tr>
                              </table>
                             
                             <br><br>
                             <table border=0 width="80%">
                                <tr><td width="20%" align="right">&nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ</td><td style="width:50%;text-align:center;">………………………………………………</td><td width="30%">ประธานกรรมการ</td></tr>
                                <tr><td></td><td style="text-align:center">'.$committee_header->name.'</td><td>ตำแหน่ง &nbsp;'.$committee_header->position.'</td></tr>
                                <tr><td></td><td></td><td>&nbsp;</td></tr>';

                              foreach ($committee_member as $key => $cm) {
                                  
                               $html .= '<tr><td align="right">&nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ</td><td align="center">………………………………………………</td><td>กรรมการ</td></tr>
                                <tr><td></td><td align="center">'.$cm->name.'</td><td>ตำแหน่ง &nbsp;'.$cm->position.'</td></tr>
                                <tr><td></td><td></td><td>&nbsp;</td></tr>';
                              }  
                            $html .='  </table>
                           

                          </td>';
                      } 
                    
                    }  
                  }
                 
                  
                $html .= '</tr>';

                if($row % ($max_row*$page) == 0 && $row!=0)
                {
                     
                      //summary

                      $html .= '<tr>';
                      $html .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';
                      $html .= '<td style="text-align:center;border:1px solid black;">รวม ('.$page.')</td>';
                      $html .= '<td colspan=4 style="width:15%;text-align:right;'.$border_left_right2.'"></td>';
                      
                      $html .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_page,2).'</td>';
                      $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                      $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page,2).'</td>';
                      $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                      $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_page,2).'</td>';
                      $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                      $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page,2).'</td>';
                      $html .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';

                        $summary_cost_page = 0;
                        $summary_curr_page = 0;
                        $summary_prev_page = 0;

                      if($page!=$max_page)
                      {
                         
                          $html .= '<td rowspan="2" colspan=3 style="width:25%;text-align:left;border-top:1px solid black;'.$border_left_right2.'"></td>'; 
                      }
                      $html .= '</tr>';

                      $html .= '<tr>';
                        $html .= '<td colspan=2 style="width:28%;text-align:center;'.$border_left_right2.'">
                                    <u>เจ้าหน้าที่ผู้ได้รับมอบอำนาจจากผู้รับจ้าง</u><br><br>
                                    ลงชื่อ............................................<br>
                                    '.$committee_vendor->name.' &nbsp;&nbsp;  ผู้จัดการโครงการ <br>
                                    วันที่ .....................................

                                  </td>';

                        $html .= '<td colspan=8 style="width:30%;text-align:center;'.$border_left_right2.'">
                                    <u>ผู้ควบคุมงาน</u><br><br>
                                    ลงชื่อ............................................<br>
                                    '.$committee_control->name.' &nbsp;&nbsp;  ตำแหน่ง &nbsp;&nbsp;  '.$committee_control->position.'<br>
                                    วันที่ .....................................

                                  </td>';

                        $html .= '<td colspan=4 style="width:17%;text-align:center;'.$border_left_right2.'">
                               
                                  </td>';          
                      $html .= '</tr>';


                      if($page==$max_page-1)
                      {
                        $summary_cost_page = 0;
                        $summary_curr_page = 0;
                        $summary_prev_page = 0;
                      }
                     

                      $page++;

                      $html .= '</table>'; 
                      $html .= '<br pagebreak="true">';
                      $html .= $header_table;

                      //$row = -1;

                }          

              $row++;

              //--------------------------- Installation-------------------------------//
              $html2 .='<tr>';
                 //$html2 .='<td style="height:'.$row_height.'px;text-align:center;'.$border_left_right.'">'.$row2.'</td>';
                 $html2 .='<td style="height:'.$row_height.'px;text-align:center;'.$border_left_right.'">'.$value->no.'</td>';

                  if($value->type==1 || $value->type==2)
                      $detail = '<b>'.$value->detail.'</b>';
                  else if($value->indent!="")
                      $detail = $value->indent.'&nbsp;&nbsp;'.$value->detail;  
                  else 
                      $detail = '&nbsp;&nbsp;&nbsp;'.$value->detail;   

                 $html2 .= '<td style="'.$border_left_right.'"> '.$detail.'</td>';
                
                 $html2 .= '<td style="text-align:center;'.$border_left_right.'">'.$value->amount.'</td>';
                 $html2 .= '<td style="text-align:center;'.$border_left_right.'">'.$value->unit.'</td>';
                
                 $price_item = is_numeric($value->price_item) ? number_format($value->price_item,2) : $value->price_item;
                 $price_trans = is_numeric($value->price_trans) ? number_format($value->price_trans,2) : $value->price_trans;
                 $price_install = is_numeric($value->price_install) ? number_format($value->price_install,2) : $value->price_install;

                 if(!is_numeric($value->price_install) )
                     $html2 .= '<td style="text-align:center;'.$border_left_right.'">'.$price_install.'</td>';
                 else
                     $html2 .= '<td style="text-align:right;'.$border_left_right.'">'.$price_install.'</td>';
                
                
                 $price_item_all = '';

                 if(!empty($value->amount) )
                 { 
                     $price_install = is_numeric($value->price_install) ? $value->price_install : 0;
                  
                     $price_item_all = ($price_install + $price_trans) * $value->amount;

                     $summary_cost_page2 += $price_item_all;
                     $summary_cost_all2 += $price_item_all;

                     if(!is_numeric($value->price_install) )
                       $html2 .= '<td style="text-align:center;'.$border_left_right.'">'.$value->price_install.'</td>';
                     else  
                       $html2 .= '<td style="text-align:right;'.$border_left_right.'">'.number_format($price_item_all,2).'</td>';
                 }
                 else
                     $html2 .= '<td style="text-align:right;'.$border_left_right.'">'.$price_item_all.'</td>';

                 //amount current payment
                 $curr_payment = Yii::app()->db->createCommand()
                                 ->select('*')
                                 ->from('payment')
                                 ->where("pay_type=2 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no =".$pay_no)
                                 ->queryAll();
                 $current_payment = "";                
                 if(!empty($curr_payment))
                 {
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'">'.$curr_payment[0]['amount'].'</td>';
                   $current_payment = $curr_payment[0]['amount'];
                   $price_item_all = ($price_install) * $curr_payment[0]['amount'];

                   $summary_curr_page2 += $price_item_all;
                   $summary_curr_all2 += $price_item_all;

                  if(!is_numeric($value->price_install) )
                       $html2 .= '<td style="text-align:center;'.$border_left_right.'">'.$value->price_install.'</td>';
                     else  
                       $html2 .= '<td style="text-align:right;'.$border_left_right.'">'.number_format($price_item_all,2).'</td>';

                 } 
                 else{
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                   if(!empty($value->amount) )
                      $html2 .= '<td style="text-align:center;'.$border_left_right.'">-</td>';
                   else
                      $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';     
                 }               

                 //amount previous with current payment  
                 $prev_payment = Yii::app()->db->createCommand()
                                 ->select('SUM(amount) as amount')
                                 ->from('payment')
                                 ->where("pay_type=2 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no <=".$pay_no)
                                 ->queryAll();     

                 if(!empty($prev_payment) and $prev_payment[0]['amount']>0)
                 {
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'">'.$prev_payment[0]['amount'].'</td>';
                   $price_item_all = ($price_install) * $prev_payment[0]['amount'];

                   $summary_prev_page2 += $price_item_all;
                   $summary_prev_all2 += $price_item_all;

                   if(!is_numeric($value->price_install))
                       $html2 .= '<td style="text-align:center;'.$border_left_right.'">'.$value->price_install.'</td>';
                     else  
                        $html2 .= '<td style="text-align:right;'.$border_left_right.'">'.number_format($price_item_all,2).'</td>';

                 } 
                 else{
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                   if(!empty($value->amount) )
                      $html2 .= '<td style="text-align:center;'.$border_left_right.'">-</td>';
                   else
                      $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';     
                 }   


                 //committee check  

                 if(!empty($curr_payment))
                 {
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'">'.$curr_payment[0]['amount'].'</td>';
                   $current_payment = $curr_payment[0]['amount'];
                   $price_item_all = ($price_install) * $curr_payment[0]['amount'];
                  if(!is_numeric($value->price_install) )
                       $html2 .= '<td style="text-align:center;'.$border_left_right.'">'.$value->price_install.'</td>';
                     else  
                       $html2 .= '<td style="text-align:right;'.$border_left_right.'">'.number_format($price_item_all,2).'</td>';

                 } 
                 else{
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                   if(!empty($value->amount) )
                      $html2 .= '<td style="text-align:center;'.$border_left_right.'">-</td>';
                   else
                      $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';     
                 }         

                 //note
                 $html2 .= '<td style="width:3%;text-align:center;'.$border_left_right.'"></td>'; 

                 if($page2==2)
                    $row_com = 7;
                
                 if($row2==$row_com + ($max_row*($page2-1)))
                 {
                     $html2 .= '<td colspan=3 style="width:25%;text-align:left;'.$border_right_bottom.'"></td>'; 
                 }
                 else if($row2>$row_com + ($max_row*($page2-1)) )
                 {
                   if($page2!=$max_page)  
                     $html2 .= '<td colspan=3 style="width:25%;text-align:left;border-right:1px solid black;"></td>'; 
                   else{
                     if($row2== $max_row*($page2-1) + $row_com+1)
                     {
                         $rowspan = $max_row - $row_com;


                         $payment_all = Yii::app()->db->createCommand()
                                    ->select('SUM(payment.amount*(price_install)) as amount')
                                    ->from('payment')
                                    ->join('boq','boq.id=payment.item_id')
                                    ->where(" pay_type=2 AND payment.vc_id='".$vc_id."' AND pay_no =".$pay_no)
                                    ->queryAll();  

                          $summary_curr = $payment_all[0]['amount'];          


     

                         if($model_vc->percent_adv!=0)
                          {  
                             $advance_pay = ($model_vc->percent_adv/100.0) * ($summary_curr*($model_vc->percent_pay/100.0));
                             $advance_pay_str = number_format($advance_pay,2);
                          }else{
                             $advance_pay = 0;
                             $advance_pay_str = "-"; 
                          }
                         
                         $remain_pay = ($summary_curr*($model_vc->percent_pay/100.0)) - $advance_pay;

                         $fine_all = 0;  
                         $fine_html = "";
                         $fine_count = count($fineModel);
                         $fi = 0;

                          $number_style = "border-bottom:1px dotted grey;text-align:right;";
                          foreach ($fineModel as $key => $fine) {
                            if($fi==0)
                            {
                              $fine_html .= '<tr><td width="10%">&nbsp;&nbsp;&nbsp;&nbsp;<u>หัก</u></td><td width="40%">-  '.$fine->detail.'</td><td width="40%" style="'.$number_style.'">'.number_format($fine->amount,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td width="10%">&nbsp;&nbsp;บาท</td></tr>';


                            }
                            else
                            {
                              $fine_html .= '<tr><td></td><td width="40%">-  '.$fine->detail.'</td><td width="40%" style="'.$number_style.'">'.number_format($fine->amount,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td width="10%">&nbsp;&nbsp;บาท</td></tr>';

                            }
                            
                            $fine_all += $fine->amount;

                            $fi++;
                          }


                         if(empty($fineModel))
                          {
                             $fine_html .= '<tr><td width="10%">&nbsp;&nbsp;&nbsp;&nbsp;<u>หัก</u></td><td width="40%">- </td><td width="40%" style="'.$number_style.'">-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td width="10%">&nbsp;&nbsp;บาท</td></tr>';

                          }

                         $html2 .= '<td rowspan="'.$rowspan.'" colspan=3 style="width:25%;text-align:left;'.$border_right_bottom.'"><br><br>&nbsp; เรียน &nbsp;……………………………………………………………………………<br>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; คณะกรรมการตรวจรับงานจ้างได้ทำการตรวจรับงานดังกล่าวแล้ว <br>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; เมื่อวันที่ …………………………………… ปรากฎว่า<br>
                             &#9633; ถูกต้องครบถ้วนเป็นไปตามสัญญาทุกประการ เห็นควรรับมอบงานและจ่ายเงินให้แก่ผู้รับจ้างดังนี้  <br> 
                             &#9633; ผู้รับจ้างส่งมอบงานมีรายละเอียดส่วนใหญ่ถูกต้องตามสัญญา และมีรายละเอียดส่วนย่อยที่ <br>
                             &nbsp;&nbsp;&nbsp; ไม่ใช่สาระสำคัญแตกต่างจากสัญญา  และไม่ก่อให้เกิดความเสียหายต่อการใช้งาน จึงเห็นควร <br>
                             &nbsp;&nbsp;&nbsp; รับมอบงาน  และอนุมัติจ่ายเงินให้แก่ผู้รับจ้างดังนี้ <br>
                             <table border=0 width="70%">
                               <tr><td colspan=2 width="50%">&nbsp;&nbsp;&nbsp;&nbsp;ค่าจ้าง 100%</td><td width="40%" style="border-bottom:1px dotted grey;text-align:right">'.number_format($summary_curr,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td width="10%">&nbsp;&nbsp;บาท</td></tr>

                               <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;เบิก '.$model_vc->percent_pay.' %</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($summary_curr*$model_vc->percent_pay/100.0,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                               <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;หัก Advance '.$model_vc->percent_adv.' %</td><td style="border-bottom:1px dotted grey;text-align:right">'.$advance_pay_str.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                               <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;คงเหลือ</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($remain_pay,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                               <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;บวก ค่าภาษีมูลค่าเพิ่ม 7%</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($remain_pay*0.07,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                               <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;คงเหลือจ่าย</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($remain_pay*1.07,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                               '.$fine_html.'
                                <tr><td width="10%">&nbsp;</td><td width="40%">คงจ่ายสุทธิ</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($remain_pay*1.07 - $fine_all,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>
                                <tr><td colspan="3" style="width:100%;text-align:center">('.bahtText($remain_pay*1.07-$fine_all).')</td></tr>
                              </table>
                           
                            <br><br>
                            <table border=0 width="80%">
                               <tr><td width="20%" align="right">&nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ</td><td style="width:50%;text-align:center;">………………………………………………</td><td width="30%">ประธานกรรมการ</td></tr>
                               <tr><td></td><td style="text-align:center">'.$committee_header->name.'</td><td>ตำแหน่ง &nbsp;'.$committee_header->position.'</td></tr>
                               <tr><td></td><td></td><td>&nbsp;</td></tr>';

                             foreach ($committee_member as $key => $cm) {
                                
                              $html2 .= '<tr><td align="right">&nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ</td><td align="center">………………………………………………</td><td>กรรมการ</td></tr>
                               <tr><td></td><td align="center">'.$cm->name.'</td><td>ตำแหน่ง &nbsp;'.$cm->position.'</td></tr>
                               <tr><td></td><td></td><td>&nbsp;</td></tr>';
                             }  
                           $html2 .='  </table>
                         

                         </td>';
                     }    
                   }  
                 }
               
                
               $html2 .= '</tr>';

               if($row2 % ($max_row*$page2) == 0 && $row2!=0)
               {
                   
                     //summary

                     $html2 .= '<tr>';
                     $html2 .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';
                     $html2 .= '<td style="text-align:center;border:1px solid black;">รวม ('.$page2.')</td>';
                     $html2 .= '<td colspan=3 style="width:11%;text-align:right;'.$border_left_right2.'"></td>';
                    
                     $html2 .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_page2,2).'</td>';
                     $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                     $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page2,2).'</td>';
                     $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                     $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_page2,2).'</td>';
                     $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                     $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page2,2).'</td>';
                     $html2 .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';
                     if($page2!=$max_page)
                     {
                       
                         $html2 .= '<td rowspan="2" colspan=3 style="width:25%;text-align:left;border-top:1px solid black;'.$border_left_right2.'"></td>'; 
                     }
                     $html2 .= '</tr>';

                     $html2 .= '<tr>';
                       $html2 .= '<td colspan=2 style="width:32%;text-align:center;'.$border_left_right2.'">
                                   <u>เจ้าหน้าที่ผู้ได้รับมอบอำนาจจากผู้รับจ้าง</u><br><br>
                                   ลงชื่อ............................................<br>
                                   '.$committee_vendor->name.' &nbsp;&nbsp;  ผู้จัดการโครงการ <br>
                                   วันที่ .....................................

                                 </td>';

                       $html2 .= '<td colspan=7 style="width:26%;text-align:center;'.$border_left_right2.'">
                                   <u>ผู้ควบคุมงาน</u><br><br>
                                   ลงชื่อ............................................<br>
                                   '.$committee_control->name.' &nbsp;&nbsp;  ตำแหน่ง &nbsp;&nbsp;  '.$committee_control->position.'<br>
                                   วันที่ .....................................

                                 </td>';

                       $html2 .= '<td colspan=4 style="width:17%;text-align:center;'.$border_left_right2.'">
                             
                                 </td>';          
                     $html2 .= '</tr>';


                     if($page2==$max_page-1)
                     {
                       $summary_cost_page2 = 0;
                       $summary_curr_page2 = 0;
                       $summary_prev_page2 = 0;
                     }
                   

                     $page2++;

                     $html2 .= '</table>'; 
                     $html2 .= '<br pagebreak="true">';
                     $html2 .= $header_table2;

                     
               }          

               $row2++;
                            
        } //end foreach  



         //--------------------------- Item-------------------------------//
        if($row==$max_row*$page-1 )
        {
                $html .= '<tr>';
                $html .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';
                $html .= '<td style="text-align:center;border:1px solid black;">รวม ('.$page.')</td>';
                $html .= '<td colspan=4 style="width:15%;text-align:right;'.$border_left_right2.'"></td>';
                
                $html .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_page,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_page,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page,2).'</td>';
                $html .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';

                  $summary_cost_page = 0;
                  $summary_curr_page = 0;
                  $summary_prev_page = 0;

                if($page!=$max_page)
                {
                   
                    $html .= '<td rowspan="2" colspan=3 style="width:25%;text-align:left;border-top:1px solid black;'.$border_left_right2.'"></td>'; 
                }
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';

                $all_page = "";
                for ($p=0; $p < $page; $p++) { 
                      $all_page .= "รวม (".($p+1).")+";
                }
                $all_page = substr($all_page, 0,strlen($all_page)-1);

                $html .= '<td style="text-align:center;border:1px solid black;">'.$all_page.'</td>';
                $html .= '<td colspan=4 style="width:15%;text-align:right;'.$border_left_right2.'"></td>';
                
                $html .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_all,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_all,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_all,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_all,2).'</td>';
                $html .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';
                if($page!=$max_page)
                {
                   
                    $html .= '<td rowspan="3" colspan=3 style="width:25%;text-align:left;border-top:1px solid black;'.$border_left_right2.'"></td>'; 
                }
                $html .= '</tr>';

                $html .= '<tr>';
                  $html .= '<td colspan=2 style="width:28%;text-align:center;'.$border_left_right2.'">
                              <u>เจ้าหน้าที่ผู้ได้รับมอบอำนาจจากผู้รับจ้าง</u><br><br>
                              ลงชื่อ............................................<br>
                              '.$committee_vendor->name.' &nbsp;&nbsp;  ผู้จัดการโครงการ <br>
                              วันที่ .....................................

                            </td>';

                  $html .= '<td colspan=8 style="width:30%;text-align:center;'.$border_left_right2.'">
                              <u>ผู้ควบคุมงาน</u><br><br>
                              ลงชื่อ............................................<br>
                              '.$committee_control->name.' &nbsp;&nbsp;  ตำแหน่ง &nbsp;&nbsp;  '.$committee_control->position.'<br>
                              วันที่ .....................................

                            </td>';

                   $html .= '<td colspan=2 style="width:8%;text-align:center;'.$border_left_bottom.'">รวมเป็นจำนวนเงินที่เบิก 100%
                         
                            </td><td style="width:6%;text-align:right;border-bottom:1px solid black;">'.number_format($summary_curr_all,2).'</td><td style="width:3%;text-align:right;'.$border_right_bottom.'"></td>';  
                   $html .= '<td rowspan="3" colspan=3 style="width:25%;text-align:center;border-top:1px solid black;'.$border_left_right2.'">
                                อนุมัติ <br><br>
                                ลงชื่อ............................................<br>
                                (....................................................................) <br>
                                วันที่ .....................................
                              </td>';          

                $html .= '</tr>';
        }          
        else if($page==$max_page)
        {

          for ($i=$row; $i < $max_row*$page+1 ; $i++) { 
              //summary
              if($i!=$max_row*$page)
              {    
                $html .= '<tr>';
                  $html .= '<td style="height:'.$row_height.'px;text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  if($page!=$max_page)
                  {
                      $html .= '<td style="width:3%;text-align:center;"></td>';
                      $html .= '<td colspan=2 style="text-align:left;border-right:1px solid black;"></td>'; 
                  }
               $html .= '</tr>';
              }
              else{
                $html .= '<tr>';
                $html .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';
                $html .= '<td style="text-align:center;border:1px solid black;">รวม ('.$page.')</td>';
                $html .= '<td colspan=4 style="width:15%;text-align:right;'.$border_left_right2.'"></td>';
                
                $html .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_page,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_page,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page,2).'</td>';
                $html .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';

                  $summary_cost_page = 0;
                  $summary_curr_page = 0;
                  $summary_prev_page = 0;

                if($page!=$max_page)
                {
                   
                    $html .= '<td rowspan="2" colspan=3 style="width:25%;text-align:left;border-top:1px solid black;'.$border_left_right2.'"></td>'; 
                }
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';

                $all_page = "";
                for ($p=0; $p < $page; $p++) { 
                      $all_page .= "รวม (".($p+1).")+";
                }
                $all_page = substr($all_page, 0,strlen($all_page)-1);

                $html .= '<td style="text-align:center;border:1px solid black;">'.$all_page.'</td>';
                $html .= '<td colspan=4 style="width:15%;text-align:right;'.$border_left_right2.'"></td>';
                
                $html .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_all,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_all,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_all,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_all,2).'</td>';
                $html .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';
                if($page!=$max_page)
                {
                   
                    $html .= '<td rowspan="3" colspan=3 style="width:25%;text-align:left;border-top:1px solid black;'.$border_left_right2.'"></td>'; 
                }
                $html .= '</tr>';

                $html .= '<tr>';
                  $html .= '<td colspan=2 style="width:28%;text-align:center;'.$border_left_right2.'">
                              <u>เจ้าหน้าที่ผู้ได้รับมอบอำนาจจากผู้รับจ้าง</u><br><br>
                              ลงชื่อ............................................<br>
                              '.$committee_vendor->name.' &nbsp;&nbsp;  ผู้จัดการโครงการ <br>
                              วันที่ .....................................

                            </td>';

                  $html .= '<td colspan=8 style="width:30%;text-align:center;'.$border_left_right2.'">
                              <u>ผู้ควบคุมงาน</u><br><br>
                              ลงชื่อ............................................<br>
                              '.$committee_control->name.' &nbsp;&nbsp;  ตำแหน่ง &nbsp;&nbsp;  '.$committee_control->position.'<br>
                              วันที่ .....................................

                            </td>';

                   $html .= '<td colspan=2 style="width:8%;text-align:center;'.$border_left_bottom.'">รวมเป็นจำนวนเงินที่เบิก 100%
                         
                            </td><td style="width:6%;text-align:right;border-bottom:1px solid black;">'.number_format($summary_curr_all,2).'</td><td style="width:3%;text-align:right;'.$border_right_bottom.'"></td>';  
                   $html .= '<td rowspan="3" colspan=3 style="width:25%;text-align:center;border-top:1px solid black;'.$border_left_right2.'">
                                อนุมัติ <br><br>
                                ลงชื่อ............................................<br>
                                (....................................................................) <br>
                                วันที่ .....................................
                              </td>';          

                $html .= '</tr>';

              }
          }   

        }
         $html .= '</table>';


          //--------------------------- Install-------------------------------//
             if($row2==$max_row*$page2-1 )
         {
                 $html2 .= '<tr>';
                 $html2 .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';
                 $html2 .= '<td style="text-align:center;border:1px solid black;">รวม ('.$page2.')</td>';
                 $html2 .= '<td colspan=3 style="width:11%;text-align:right;'.$border_left_right2.'"></td>';
                
                 $html2 .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_page2,2).'</td>';
                 $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                 $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page2,2).'</td>';
                 $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                 $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_page2,2).'</td>';
                 $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                 $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page2,2).'</td>';
                 $html2 .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';
                 if($page2!=$max_page)
                 {
                   
                     $html2 .= '<td rowspan="2" colspan=3 style="width:25%;text-align:left;border-top:1px solid black;'.$border_left_right2.'"></td>'; 
                 }
                 $html2 .= '</tr>';
                 $html2 .= '<tr>';
                 $html2 .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';

                 $all_page = "";
                 for ($p=0; $p < $page2; $p++) { 
                       $all_page .= "รวม (".($p+1).")+";
                 }
                 $all_page = substr($all_page, 0,strlen($all_page)-1);

                 $html2 .= '<td style="text-align:center;border:1px solid black;">'.$all_page.'</td>';
                 $html2 .= '<td colspan=3 style="width:11%;text-align:right;'.$border_left_right2.'"></td>';
                
                 $html2 .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_all2,2).'</td>';
                 $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                 $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_all2,2).'</td>';
                 $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                 $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_all2,2).'</td>';
                 $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                 $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_all2,2).'</td>';
                 $html2 .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';
                 if($page2!=$max_page)
                 {
                   
                     $html2 .= '<td rowspan="3" colspan=3 style="width:25%;text-align:left;border-top:1px solid black;'.$border_left_right2.'"></td>'; 
                 }
                 $html2 .= '</tr>';

                 $html2 .= '<tr>';
                   $html2 .= '<td colspan=2 style="width:32%;text-align:center;'.$border_left_right2.'">
                               <u>เจ้าหน้าที่ผู้ได้รับมอบอำนาจจากผู้รับจ้าง</u><br><br>
                               ลงชื่อ............................................<br>
                               '.$committee_vendor->name.' &nbsp;&nbsp;  ผู้จัดการโครงการ <br>
                               วันที่ .....................................

                             </td>';

                   $html2 .= '<td colspan=7 style="width:26%;text-align:center;'.$border_left_right2.'">
                               <u>ผู้ควบคุมงาน</u><br><br>
                               ลงชื่อ............................................<br>
                               '.$committee_control->name.' &nbsp;&nbsp;  ตำแหน่ง &nbsp;&nbsp;  '.$committee_control->position.'<br>
                               วันที่ .....................................

                             </td>';

                    $html2 .= '<td colspan=2 style="width:8%;text-align:center;'.$border_left_bottom.'">รวมเป็นจำนวนเงินที่เบิก 100%
                         
                             </td><td style="width:6%;text-align:right;border-bottom:1px solid black;">'.number_format($summary_curr_all2,2).'</td><td style="width:3%;text-align:right;'.$border_right_bottom.'"></td>';  
                    $html2 .= '<td rowspan="3" colspan=3 style="width:25%;text-align:center;border-top:1px solid black;'.$border_left_right2.'">
                                 อนุมัติ <br><br>
                                 ลงชื่อ............................................<br>
                                 (....................................................................) <br>
                                 วันที่ .....................................
                               </td>';          

                 $html2 .= '</tr>';
         }          
         else if($page2==$max_page)
         {

           for ($i=$row2; $i < $max_row*$page2+1 ; $i++) { 
               //summary
               if($i!=$max_row*$page2)
               {    
                 $html2 .= '<tr>';
                   $html2 .= '<td style="height:'.$row_height.'px;text-align:center;'.$border_left_right.'"></td>';
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                 
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                   $html2 .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                   if($page2!=$max_page)
                   {
                       $html2 .= '<td style="width:3%;text-align:center;"></td>';
                       $html2 .= '<td colspan=2 style="text-align:left;border-right:1px solid black;"></td>'; 
                   }
                $html2 .= '</tr>';
               }
               else{
                 $html2 .= '<tr>';
                 $html2 .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';
                 $html2 .= '<td style="text-align:center;border:1px solid black;">รวม ('.$page2.')</td>';
                 $html2 .= '<td colspan=3 style="width:11%;text-align:right;'.$border_left_right2.'"></td>';
                
                 $html2 .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_page2,2).'</td>';
                 $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                 $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page2,2).'</td>';
                 $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                 $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_page2,2).'</td>';
                 $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                 $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page2,2).'</td>';
                 $html2 .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';
                 if($page2!=$max_page)
                 {
                   
                     $html2 .= '<td rowspan="2" colspan=3 style="width:25%;text-align:left;border-top:1px solid black;'.$border_left_right2.'"></td>'; 
                 }
                 $html2 .= '</tr>';
                 $html2 .= '<tr>';
                 $html2 .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';

                 $all_page = "";
                 for ($p=0; $p < $page2; $p++) { 
                       $all_page .= "รวม (".($p+1).")+";
                 }
                 $all_page = substr($all_page, 0,strlen($all_page)-1);

                 $html2 .= '<td style="text-align:center;border:1px solid black;">'.$all_page.'</td>';
                 $html2 .= '<td colspan=3 style="width:11%;text-align:right;'.$border_left_right2.'"></td>';
                
                 $html2 .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_all2,2).'</td>';
                 $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                 $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_all2,2).'</td>';
                 $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                 $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_all2,2).'</td>';
                 $html2 .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                 $html2 .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_all2,2).'</td>';
                 $html2 .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';
                 if($page2!=$max_page)
                 {
                   
                     $html2 .= '<td rowspan="3" colspan=3 style="width:25%;text-align:left;border-top:1px solid black;'.$border_left_right2.'"></td>'; 
                 }
                 $html2 .= '</tr>';

                 $html2 .= '<tr>';
                   $html2 .= '<td colspan=2 style="width:32%;text-align:center;'.$border_left_right2.'">
                               <u>เจ้าหน้าที่ผู้ได้รับมอบอำนาจจากผู้รับจ้าง</u><br><br>
                               ลงชื่อ............................................<br>
                               '.$committee_vendor->name.' &nbsp;&nbsp;  ผู้จัดการโครงการ <br>
                               วันที่ .....................................

                             </td>';

                   $html2 .= '<td colspan=7 style="width:26%;text-align:center;'.$border_left_right2.'">
                               <u>ผู้ควบคุมงาน</u><br><br>
                               ลงชื่อ............................................<br>
                               '.$committee_control->name.' &nbsp;&nbsp;  ตำแหน่ง &nbsp;&nbsp;  '.$committee_control->position.'<br>
                               วันที่ .....................................

                             </td>';

                    $html2 .= '<td colspan=2 style="width:8%;text-align:center;'.$border_left_bottom.'">รวมเป็นจำนวนเงินที่เบิก 100%
                         
                             </td><td style="width:6%;text-align:right;border-bottom:1px solid black;">'.number_format($summary_curr_all2,2).'</td><td style="width:3%;text-align:right;'.$border_right_bottom.'"></td>';  
                    $html2 .= '<td rowspan="3" colspan=3 style="width:25%;text-align:center;border-top:1px solid black;'.$border_left_right2.'">
                                 อนุมัติ <br><br>
                                 ลงชื่อ............................................<br>
                                 (....................................................................) <br>
                                 วันที่ .....................................
                               </td>';          

                 $html2 .= '</tr>';

               }
           }   

        }
        $html2 .= "</table>";      



        //---------------------------ติดตั้ง ทดสอบ ---------------------------------------//
          $html .= '<br pagebreak="true">';
          $html .= $header_table2;
          $html .= $html2;
   
}
else
{
        //---------------------------   FORM 2   ---------------------------------------//

      //header
     $html .= '<div style="text-align:center"><u>การไฟฟ้าส่วนภูมิภาค</u><br><u>รายละเอียดของงานเพื่อขออนุมัติเบิกจ่ายเงิน</u></div>';
     $html .= "<table border=0>";
            $html .= '<tr><td colspan=2 style="width:80%"></td><td style="width:20%;text-align:right">แบบฟอร์ม จค.1</td></tr>';
            $html .= '<tr><td style="width:80%;text-align:center;'.$border_left_top.' ">'.$model_vc->name.'</td><td colspan=2 style="width:20%;text-align:center; '.$border_right_top.'"></td></tr>';

            $detail = "สัญญาเลขที่ ".$model_vc->contract_no."   ลงวันที่   ".renderDate($model_vc->approve_date) ."   จำนวนเงินตามสัญญา ".number_format($model_vc->budget,0)."  บาท (ไม่รวมภาษีมูลค่าเพิ่ม)   ผู้รับจ้าง  ".Vendor::model()->findByPk($model_vc->vendor_id)->v_name.'  กำหนดแล้วเสร็จตามสัญญา วันที่  '.renderDate($model_vc->end_date);
            if(!empty($model_vc->detail_approve))
                 $detail .= "<br>".$model_vc->detail_approve;

            $html .= '<tr><td style="width:65%;text-align:center; '.$border_left_bottom.'">'.$detail.'</td><td style="width:25%;text-align:center; border-bottom: 1px solid black;">ค่าอุปกรณ์ ค่าขนส่ง ค่าติดตั้งและทดสอบ</td><td style="width:10%;text-align:center; '.$border_right_bottom.'">งวดที่ '.$pay_no.'</td></tr>';
     $html .= "</table>";

      //details
      $html .= "<table border=0>";
            $html .= '<tr><td colspan=10 style="background-color:#fff18a;width:56%;text-align:center;border: 1px solid black;">สำหรับผู้รับจ้าง</td><td colspan=8 style="background-color:#bcff70;width:44%;text-align:center;border: 1px solid black;">สำหรับการไฟฟ้าส่วนภูมิภาค</td></tr>';
            $row_com = 6;
            $html .=  '<tr>

                          <td style="text-align:center;width:2%;border:1px solid black;" rowspan="2">&nbsp;<br>ลำดับ</td>
                          <td  style="text-align:center;width:20%;border:1px solid black;" rowspan="2">&nbsp;<br>รายละเอียด</td>
                          <td style="text-align:center;width:26%;border:1px solid black;" colspan=6>งานตามสัญญา</td>
                          <td style="text-align:center;width:8%;border:1px solid black;" colspan=2>ส่งมอบงานครั้งนี้</td>


                          <td style="text-align:center;width:8%;border:1px solid black;" colspan=2>รวมงานที่ส่งแล้ว (รวมครั้งนี้)</td>
                          <td style="text-align:center;width:8%;border:1px solid black;" colspan=2>สรุปคณะกรรมการตรวจรับ</td>
                          <td style="text-align:center;width:3%;border:1px solid black;" rowspan="2">&nbsp;<br>หมายเหตุ</td>
                          <td rowspan="'.($row_com+2).'" colspan=3 style="width:25%;'.$border_right_top.'">  
                              <br><br>&nbsp;&nbsp; เรียน &nbsp;&nbsp;&nbsp;คณะกรรมการตรวจรับงานจ้าง <br>
                              &#9633; ผลงานที่ส่งมอบครั้งนี้ มีรายละเอียดครบถ้วนถูกต้องตามข้อกำหนดในสัญญาทุกประการ <br>
                              &#9633; ผลงานที่ส่งมอบครั้งนี้ ก่อสร้างแล้วเสร็จเมื่อวันที่ …………………………………… <br>
                              &#9633; งานแล้วเสร็จภายในกำหนดเวลาตามสัญญา  <br> 
                              &#9633; งานแล้วเสร็จช้ากว่ากำหนดตามสัญญา……………วัน <br>
                              &nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ ………………………………………………………  ผู้ควบคุมงาน <br>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$committee_control->name.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ตำแหน่ง&nbsp;'.$committee_control->position.'  <br>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;วันที่ ……………………………………  <br>
                          </td>
                                                   
                        </tr>
                        <tr>

                          <td style="text-align:center;width:2%;border:1px solid black;">จำนวน</td>
                          <td style="text-align:center;width:2%;border:1px solid black;">หน่วย</td>
                          <td style="text-align:center;width:6%;border:1px solid black;">ค่าอุปกรณ์<br>ต่อหน่วย</td>
                          <td style="text-align:center;width:5%;border:1px solid black;">ค่าขนส่ง<br>ต่อหน่วย</td>
                          <td style="text-align:center;width:6%;border:1px solid black;">ค่าติดตั้ง/ทดสอบ<br>ต่อหน่วย</td>
                          <td style="text-align:center;width:5%;border:1px solid black;">เป็นเงิน<br>(บาท)</td>

                          <td style="text-align:center;width:2%;border:1px solid black;">จำนวน</td>
                          <td style="text-align:center;width:6%;border:1px solid black;">เป็นเงิน<br>(บาท)</td>

                          <td style="text-align:center;width:2%;border:1px solid black;">จำนวน</td>
                          <td style="text-align:center;width:6%;border:1px solid black;">เป็นเงิน<br>(บาท)</td>

                          <td style="text-align:center;width:2%;border:1px solid black;">จำนวน</td>
                          <td style="text-align:center;width:6%;border:1px solid black;">เป็นเงิน<br>(บาท)</td>

                          
                          
                        </tr>';

                        $Criteria = new CDbCriteria();
                        $Criteria->condition = "vc_id=$vc_id";
                        $boq = Boq::model()->findAll($Criteria); 

                        $Criteria = new CDbCriteria();
                        $Criteria->condition = "vc_id=$vc_id";
                        $fineModel = Fine::model()->findAll($Criteria); 

                       

                        //----table config----//
                        //$max_row = 30;
                        $row_height = 20;
                        $max_page = ceil(count($boq)*1.0 / $max_row); 
                        //$html .="max_page:".$max_page;

                        $row = 0;
                        $page = 1;
                        $summary_cost_all = 0;
                        $summary_curr_all = 0;
                        $summary_prev_all = 0;
                        $summary_cost_page = 0;
                        $summary_curr_page = 0;
                        $summary_prev_page = 0;
           
           
        $header_table = $html;    

        foreach ($boq as $key => $value) {


                //------------------------------Item & Transport------------------------------//
                $html .='<tr>';
                  //$html .='<td style="height:'.$row_height.'px;text-align:center;'.$border_left_right.'">'.$row.'</td>';
                  $html .='<td style="height:'.$row_height.'px;text-align:center;'.$border_left_right.'">'.$value->no.'</td>';

                  if($value->type==1 || $value->type==2)
                      $detail = '<b>'.$value->detail.'</b>';
                  else if($value->indent!="")
                      $detail = $value->indent.'&nbsp;&nbsp;'.$value->detail;  
                  else 
                      $detail = '&nbsp;&nbsp;&nbsp;'.$value->detail;   
                    
                  $html .= '<td style="'.$border_left_right.'"> '.$detail.'</td>';
                  
                  $html .= '<td style="text-align:center;'.$border_left_right.'">'.$value->amount.'</td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'">'.$value->unit.'</td>';
                  
                  $price_item = is_numeric($value->price_item) ? number_format($value->price_item,2) : $value->price_item;
                  $price_trans = is_numeric($value->price_trans) ? number_format($value->price_trans,2) : $value->price_trans;
                  $price_install = is_numeric($value->price_install) ? number_format($value->price_install,2) : $value->price_install;

                  if(!is_numeric($value->price_item) && !is_numeric($value->price_trans) && !is_numeric($value->price_install)  && $value->price_item==$value->price_trans && $value->price_item!=""  && $value->price_item==$value->price_install)
                  {
                     $html .= '<td colspan="3" style="text-align:center;'.$border_left_right.'">'.$price_item.'</td>';
                  }
                  else if(!is_numeric($value->price_item) && !is_numeric($value->price_trans)   && $value->price_item==$value->price_trans && $value->price_item!=""  && $value->price_item!=$value->price_install)
                  {
                    
                     $html .= '<td colspan="2" style="text-align:center;'.$border_left_right.'">'.$price_item.'</td>';
                     if(!is_numeric($value->price_install))
                      $html .= '<td style="text-align:center;'.$border_left_right.'">'.$price_install.'</td>';
                     else 
                       $html .= '<td style="text-align:right;'.$border_left_right.'">'.$price_install.'</td>';
                  }
                  else
                  {
                      $html .= '<td style="text-align:right;'.$border_left_right.'">'.$price_item.'</td>';
                      $html .= '<td style="text-align:right;'.$border_left_right.'">'.$price_trans.'</td>';
                      $html .= '<td style="text-align:right;'.$border_left_right.'">'.$price_install.'</td>';
                  }
                  
                  $price_item_all = '';



                    if(!empty($value->amount) )
                    { 
                        $price_item = is_numeric($value->price_item) ? $value->price_item : 0;
                        $price_trans = is_numeric($value->price_trans) ? $value->price_trans : 0;
                        $price_install = is_numeric($value->price_install) ? $value->price_install : 0;
                        
                        $price_item_all = ($price_item + $price_trans + $price_install) * $value->amount;

                        $summary_cost_page += $price_item_all;
                        $summary_cost_all += $price_item_all;

                        if(!is_numeric($value->price_item) && !is_numeric($value->price_trans))
                          $html .= '<td style="text-align:center;'.$border_left_right.'">'.$value->price_item.'</td>';
                        else  
                          $html .= '<td style="text-align:right;'.$border_left_right.'">'.number_format($price_item_all,2).'</td>';
                    }
                    else
                        $html .= '<td style="text-align:right;'.$border_left_right.'">'.$price_item_all.'</td>';

                    //amount current payment
                    $curr_payment = Yii::app()->db->createCommand()
                                    ->select('*')
                                    ->from('payment')
                                    ->where("pay_type=3 AND item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no =".$pay_no)
                                    ->queryAll();
                    $current_payment = "";                
                    if(!empty($curr_payment))
                    {
                      $html .= '<td style="text-align:center;'.$border_left_right.'">'.formatMoney($curr_payment[0]['amount']).'</td>';
                      $current_payment = $curr_payment[0]['amount'];
                      $price_item_all = ($price_item + $price_trans + $price_install) * $curr_payment[0]['amount'];

                      $summary_curr_page += $price_item_all;
                      $summary_curr_all += $price_item_all;

                     if(!is_numeric($value->price_item) && !is_numeric($value->price_trans))
                          $html .= '<td style="text-align:center;'.$border_left_right.'">'.$value->price_item.'</td>';
                        else  
                          $html .= '<td style="text-align:right;'.$border_left_right.'">'.number_format($price_item_all,2).'</td>';

                    } 
                    else{
                      $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                      if(empty($value->amount))
                        $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';  
                      else
                         $html .= '<td style="text-align:center;'.$border_left_right.'">-</td>';
                    }               

                    //amount previous with current payment  
                    $prev_payment = Yii::app()->db->createCommand()
                                    ->select('SUM(amount) as amount')
                                    ->from('payment')
                                    ->where(" item_id='".$value->id."' AND vc_id='".$vc_id."' AND pay_no <=".$pay_no)
                                    ->queryAll();     

                    if(!empty($prev_payment) and $prev_payment[0]['amount']>0)
                    {
                      $html .= '<td style="text-align:center;'.$border_left_right.'">'.formatMoney($prev_payment[0]['amount']).'</td>';
                      $price_item_all = ($price_item + $price_trans + $price_install) * $prev_payment[0]['amount'];

                      $summary_prev_page += $price_item_all;
                      $summary_prev_all += $price_item_all;

                      if(!is_numeric($value->price_item) && !is_numeric($value->price_trans))
                          $html .= '<td style="text-align:center;'.$border_left_right.'">'.$value->price_item.'</td>';
                        else  
                           $html .= '<td style="text-align:right;'.$border_left_right.'">'.number_format($price_item_all,2).'</td>';

                    } 
                    else{
                      $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                      if(empty($value->amount))
                        $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';  
                      else
                         $html .= '<td style="text-align:center;'.$border_left_right.'">-</td>';  
                    }   


                  //committee check  

                  if(!empty($curr_payment))
                  {
                    $html .= '<td style="text-align:center;'.$border_left_right.'">'.formatMoney($curr_payment[0]['amount']).'</td>';
                    $current_payment = $curr_payment[0]['amount'];
                    $price_item_all = ($price_item + $price_trans + $price_install) * $curr_payment[0]['amount'];
                   if(!is_numeric($value->price_item) && !is_numeric($value->price_trans))
                        $html .= '<td style="text-align:center;'.$border_left_right.'">'.$value->price_item.'</td>';
                      else  
                        $html .= '<td style="text-align:right;'.$border_left_right.'">'.number_format($price_item_all,2).'</td>';

                  } 
                  else{
                    $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                    if(empty($value->amount))
                        $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';  
                      else
                         $html .= '<td style="text-align:center;'.$border_left_right.'">-</td>';
                  }         

                  //note
                  $html .= '<td style="width:3%;text-align:center;'.$border_left_right.'"></td>'; 

                  if($page==2)
                     $row_com = 7;
            
                  if($row==$row_com + ($max_row*($page-1)))
                  {
                      $html .= '<td colspan=3 style="width:25%;text-align:left;'.$border_right_bottom.'"></td>'; 
                  }
                  else if($row>$row_com + ($max_row*($page-1)) )
                  {
                    if($page!=$max_page)  
                      $html .= '<td colspan=3 style="width:25%;text-align:left;border-right:1px solid black;"></td>'; 
                    else{
                      if($row== $max_row*($page-1) + $row_com+1)
                      {
                          $rowspan = $max_row - $row_com + 1;

                           $payment_all = Yii::app()->db->createCommand()
                                    ->select('SUM(payment.amount*(price_install+price_item+price_trans)) as amount')
                                    ->from('payment')
                                    ->join('boq','boq.id=payment.item_id')
                                    ->where(" pay_type=3 AND payment.vc_id='".$vc_id."' AND pay_no =".$pay_no)
                                    ->queryAll();  

                          $summary_curr = $payment_all[0]['amount'];          
                          if($model_vc->percent_adv!=0)
                          {  
                             $advance_pay = ($model_vc->percent_adv/100.0) * ($summary_curr*($model_vc->percent_pay/100.0));
                             $advance_pay_str = number_format($advance_pay,2);
                          }else{
                             $advance_pay = 0;
                             $advance_pay_str = "-"; 
                          }
                               

                          $remain_pay = ($summary_curr*($model_vc->percent_pay/100.0)) - $advance_pay;

                          $fine_all = 0;  
                          $fine_html = "";
                          $fine_count = count($fineModel);
                          $fi = 0;

                          $number_style = "border-bottom:1px dotted grey;text-align:right;";
                          foreach ($fineModel as $key => $fine) {
                            if($fi==0)
                            {
                              $fine_html .= '<tr><td width="10%">&nbsp;&nbsp;&nbsp;&nbsp;<u>หัก</u></td><td width="40%">-  '.$fine->detail.'</td><td width="40%" style="'.$number_style.'">'.number_format($fine->amount,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td width="10%">&nbsp;&nbsp;บาท</td></tr>';


                            }
                            else
                            {
                              $fine_html .= '<tr><td></td><td width="40%">-  '.$fine->detail.'</td><td width="40%" style="'.$number_style.'">'.number_format($fine->amount,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td width="10%">&nbsp;&nbsp;บาท</td></tr>';

                            }
                            
                            $fine_all += $fine->amount;

                            $fi++;
                          }


                          if(empty($fineModel))
                          {
                             $fine_html .= '<tr><td width="10%">&nbsp;&nbsp;&nbsp;&nbsp;<u>หัก</u></td><td width="40%">- </td><td width="40%" style="'.$number_style.'">-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td width="10%">&nbsp;&nbsp;บาท</td></tr>';

                          }

       
                          $html .= '<td rowspan="'.$rowspan.'" colspan=3 style="width:25%;text-align:left;'.$border_right_bottom.'"><br><br>&nbsp; เรียน &nbsp;……………………………………………………………………………<br>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; คณะกรรมการตรวจรับงานจ้างได้ทำการตรวจรับงานดังกล่าวแล้ว <br>
                               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;เมื่อวันที่ …………………………………… ปรากฎว่า<br>
                              &#9633; ถูกต้องครบถ้วนเป็นไปตามสัญญาทุกประการ เห็นควรรับมอบงานและจ่ายเงินให้แก่ผู้รับจ้างดังนี้  <br> 
                              &#9633; ผู้รับจ้างส่งมอบงานมีรายละเอียดส่วนใหญ่ถูกต้องตามสัญญา และมีรายละเอียดส่วนย่อยที่ <br>
                              &nbsp;&nbsp;&nbsp; ไม่ใช่สาระสำคัญแตกต่างจากสัญญา  และไม่ก่อให้เกิดความเสียหายต่อการใช้งาน จึงเห็นควร <br>
                              &nbsp;&nbsp;&nbsp; รับมอบงาน  และอนุมัติจ่ายเงินให้แก่ผู้รับจ้างดังนี้ <br>
                              <table border=0 width="70%">
                                <tr><td colspan=2 width="50%">&nbsp;&nbsp;&nbsp;&nbsp;ค่าจ้าง 100%</td><td width="40%" style="border-bottom:1px dotted grey;text-align:right">'.number_format($summary_curr,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td width="10%">&nbsp;&nbsp;บาท</td></tr>

                                <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;เบิก '.$model_vc->percent_pay.' %</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($summary_curr*$model_vc->percent_pay/100.0,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                                <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;หัก Advance '.$model_vc->percent_adv.' %</td><td style="border-bottom:1px dotted grey;text-align:right">'.$advance_pay_str.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                                <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;คงเหลือ</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($remain_pay,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                                <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;บวก ค่าภาษีมูลค่าเพิ่ม 7%</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($remain_pay*0.07,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                                <tr><td colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;คงเหลือจ่าย</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($remain_pay*1.07,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>

                                '.$fine_html.'
                                <tr><td width="10%">&nbsp;</td><td width="40%">คงจ่ายสุทธิ</td><td style="border-bottom:1px dotted grey;text-align:right">'.number_format($remain_pay*1.07 - $fine_all,2).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;บาท</td></tr>
                                <tr><td colspan="3" style="width:100%;text-align:center">('.bahtText($remain_pay*1.07-$fine_all).')</td></tr>
                              </table>
                             
                             <br><br>
                             <table border=0 width="80%">
                                <tr><td width="20%" align="right">&nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ</td><td style="width:50%;text-align:center;">………………………………………………</td><td width="30%">ประธานกรรมการ</td></tr>
                                <tr><td></td><td style="text-align:center">'.$committee_header->name.'</td><td>ตำแหน่ง &nbsp;'.$committee_header->position.'</td></tr>
                                <tr><td></td><td></td><td>&nbsp;</td></tr>';

                              foreach ($committee_member as $key => $cm) {
                                  
                               $html .= '<tr><td align="right">&nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ</td><td align="center">………………………………………………</td><td>กรรมการ</td></tr>
                                <tr><td></td><td align="center">'.$cm->name.'</td><td>ตำแหน่ง &nbsp;'.$cm->position.'</td></tr>
                                <tr><td></td><td></td><td>&nbsp;</td></tr>';
                              }  
                            $html .='  </table>
                           

                          </td>';
                      }    
                    }  
                  }
                 
                  
                $html .= '</tr>';

                if($row % ($max_row*$page) == 0 && $row!=0)
                {
                     
                      //summary

                      $html .= '<tr>';
                      $html .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';
                      $html .= '<td style="text-align:center;border:1px solid black;">รวม ('.$page.')</td>';
                      $html .= '<td colspan=5 style="width:21%;text-align:right;'.$border_left_right2.'"></td>';
                      
                      $html .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_page,2).'</td>';
                      $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                      $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page,2).'</td>';
                      $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                      $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_page,2).'</td>';
                      $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                      $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page,2).'</td>';
                      $html .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';

                        $summary_cost_page = 0;
                        $summary_curr_page = 0;
                        $summary_prev_page = 0;

                      if($page!=$max_page)
                      {
                         
                          $html .= '<td rowspan="2" colspan=3 style="width:25%;text-align:left;border-top:1px solid black;'.$border_left_right2.'"></td>'; 
                      }
                      $html .= '</tr>';

                      $html .= '<tr>';
                        $html .= '<td colspan=2 style="width:22%;text-align:center;'.$border_left_right2.'">
                                    <u>เจ้าหน้าที่ผู้ได้รับมอบอำนาจจากผู้รับจ้าง</u><br><br>
                                    ลงชื่อ............................................<br>
                                    '.$committee_vendor->name.' &nbsp;&nbsp;  ผู้จัดการโครงการ <br>
                                    วันที่ .....................................

                                  </td>';

                        $html .= '<td colspan=8 style="width:36%;text-align:center;'.$border_left_right2.'">
                                    <u>ผู้ควบคุมงาน</u><br><br>
                                    ลงชื่อ............................................<br>
                                    '.$committee_control->name.' &nbsp;&nbsp;  ตำแหน่ง &nbsp;&nbsp;  '.$committee_control->position.'<br>
                                    วันที่ .....................................

                                  </td>';

                        $html .= '<td colspan=4 style="width:17%;text-align:center;'.$border_left_right2.'">
                               
                                  </td>';          
                      $html .= '</tr>';


                      if($page==$max_page-1)
                      {
                        $summary_cost_page = 0;
                        $summary_curr_page = 0;
                        $summary_prev_page = 0;
                      }
                     

                      $page++;

                      $html .= '</table>'; 
                      $html .= '<br pagebreak="true">';
                      $html .= $header_table;

                      //$row = -1;

                }          

              $row++;
            } //end foreach  


            if($row==$max_row*$page-1 )
            {
                $html .= '<tr>';
                $html .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';
                $html .= '<td style="text-align:center;border:1px solid black;">รวม ('.$page.')</td>';
                $html .= '<td colspan=5 style="width:21%;text-align:right;'.$border_left_right2.'"></td>';
                
                $html .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_page,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_page,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page,2).'</td>';
                $html .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';

                  $summary_cost_page = 0;
                  $summary_curr_page = 0;
                  $summary_prev_page = 0;

                if($page!=$max_page)
                {
                   
                    $html .= '<td rowspan="2" colspan=3 style="width:25%;text-align:left;border-top:1px solid black;'.$border_left_right2.'"></td>'; 
                }
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';

                $all_page = "";
                for ($p=0; $p < $page; $p++) { 
                      $all_page .= "รวม (".($p+1).")+";
                }
                $all_page = substr($all_page, 0,strlen($all_page)-1);

                $html .= '<td style="text-align:center;border:1px solid black;">'.$all_page.'</td>';
                $html .= '<td colspan=5 style="width:21%;text-align:right;'.$border_left_right2.'"></td>';
                
                $html .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_all,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_all,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_all,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_all,2).'</td>';
                $html .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';
                if($page!=$max_page)
                {
                   
                    $html .= '<td rowspan="3" colspan=3 style="width:25%;text-align:left;border-top:1px solid black;'.$border_left_right2.'"></td>'; 
                }
                $html .= '</tr>';

                $html .= '<tr>';
                  $html .= '<td colspan=2 style="width:22%;text-align:center;'.$border_left_right2.'">
                              <u>เจ้าหน้าที่ผู้ได้รับมอบอำนาจจากผู้รับจ้าง</u><br><br>
                              ลงชื่อ............................................<br>
                              '.$committee_vendor->name.' &nbsp;&nbsp;  ผู้จัดการโครงการ <br>
                              วันที่ .....................................

                            </td>';

                  $html .= '<td colspan=8 style="width:36%;text-align:center;'.$border_left_right2.'">
                              <u>ผู้ควบคุมงาน</u><br><br>
                              ลงชื่อ............................................<br>
                              '.$committee_control->name.' &nbsp;&nbsp;  ตำแหน่ง &nbsp;&nbsp;  '.$committee_control->position.'<br>
                              วันที่ .....................................

                            </td>';

                   $html .= '<td colspan=2 style="width:8%;text-align:center;'.$border_left_bottom.'">รวมเป็นจำนวนเงินที่เบิก 100%
                         
                            </td><td style="width:6%;text-align:right;border-bottom:1px solid black;">'.number_format($summary_curr_all,2).'</td><td style="width:3%;text-align:right;'.$border_right_bottom.'"></td>';  
                   $html .= '<td rowspan="3" colspan=3 style="width:25%;text-align:center;border-top:1px solid black;'.$border_left_right2.'">
                                อนุมัติ <br><br>
                                ลงชื่อ............................................<br>
                                (....................................................................) <br>
                                วันที่ .....................................
                              </td>';          

                $html .= '</tr>';
        }          
        else if($page==$max_page)
        {

          for ($i=$row; $i < $max_row*$page+1 ; $i++) { 
              //summary
              if($i!=$max_row*$page)
              {    
                $html .= '<tr>';
                  $html .= '<td style="height:'.$row_height.'px;text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  $html .= '<td style="text-align:center;'.$border_left_right.'"></td>';
                  if($page!=$max_page)
                  {
                      $html .= '<td style="width:3%;text-align:center;"></td>';
                      $html .= '<td colspan=2 style="text-align:left;border-right:1px solid black;"></td>'; 
                  }


               $html .= '</tr>';
              }
              else{
                $html .= '<tr>';
                $html .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';
                $html .= '<td style="text-align:center;border:1px solid black;">รวม ('.$page.')</td>';
                $html .= '<td colspan=5 style="width:21%;text-align:right;'.$border_left_right2.'"></td>';
                
                $html .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_page,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_page,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_page,2).'</td>';
                $html .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';

                  $summary_cost_page = 0;
                  $summary_curr_page = 0;
                  $summary_prev_page = 0;

                if($page!=$max_page)
                {
                   
                    $html .= '<td rowspan="3" colspan=3 style="width:25%;text-align:left;border-top:1px solid black;'.$border_left_right2.'"></td>'; 
                }




                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td style="height:'.$row_height.'px;text-align:center;border:1px solid black;"></td>';
                $all_page = "";
                for ($p=0; $p < $page; $p++) { 
                  
                      $all_page .= "รวม (".($p+1).")+";
                      if($p%6==0 && $p!=0)
                        $all_page .= '<br>';
                }
                $all_page = $page==1 ? "" : substr($all_page, 0,strlen($all_page)-1);


                $html .= '<td style="text-align:center;border:1px solid black;">'.$all_page.'</td>';
                $html .= '<td colspan=5 style="width:21%;text-align:right;'.$border_left_right2.'"></td>';
                
                $html .= '<td style="width:5%;text-align:right;'.$border_left_right2.'">'.number_format($summary_cost_all,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_all,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_prev_all,2).'</td>';
                $html .= '<td style="width:2%;text-align:center;'.$border_left_right2.'"></td>';
                $html .= '<td style="width:6%;text-align:right;'.$border_left_right2.'">'.number_format($summary_curr_all,2).'</td>';
                $html .= '<td style="width:3%;text-align:center;'.$border_left_right2.'"></td>';

            
                
                $html .= '</tr>';




                $html .= '<tr>';
                  $html .= '<td colspan=2 style="width:22%;text-align:center;'.$border_left_right2.'">
                              <u>เจ้าหน้าที่ผู้ได้รับมอบอำนาจจากผู้รับจ้าง</u><br><br>
                              ลงชื่อ............................................<br>
                              '.$committee_vendor->name.' &nbsp;&nbsp;  ผู้จัดการโครงการ <br>
                              วันที่ .....................................

                            </td>';

                  $html .= '<td colspan=8 style="width:36%;text-align:center;'.$border_left_right2.'">
                              <u>ผู้ควบคุมงาน</u><br><br>
                              ลงชื่อ............................................<br>
                              '.$committee_control->name.' &nbsp;&nbsp;  ตำแหน่ง &nbsp;&nbsp;  '.$committee_control->position.'<br>
                              วันที่ .....................................

                            </td>';

                  $html .= '<td colspan=2 style="width:8%;text-align:center;'.$border_left_bottom.'">รวมเป็นจำนวนเงินที่เบิก 100%
                         
                            </td><td style="width:6%;text-align:right;border-bottom:1px solid black;">'.number_format($summary_curr_all,2).'</td><td style="width:3%;text-align:right;'.$border_right_bottom.'"></td>';  
                   $html .= '<td rowspan="2" colspan=3 style="width:25%;text-align:center;border-top:1px solid black;'.$border_left_right2.'">
                                อนุมัติ <br><br>
                                ลงชื่อ............................................<br>
                                (....................................................................) <br>
                                วันที่ .....................................
                              </td>';          
          
                $html .= '</tr>';

              }
          } //forloop
       }     

        $html .= "</table>"; 
}



echo $html;
  
$pdf->AddPage();
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
//$filename = "ฟอร์ม จค. ".VendorContract::model()->findByPk($vc_id)->name." งวด ".$pay_no.".pdf";
//$filename = iconv('UTF-8','TIS-620', $filename);
$filename = 'form_print_'.$vc_id.'.pdf';
//$pdf->Output($_SERVER['DOCUMENT_ROOT'].'/pea_jk/report/temp/'.$filename,'F');
// This method has several options, check the source code documentation for more information.
/*if(file_exists($_SERVER['DOCUMENT_ROOT'].'/pea_jk/report/temp/'.$filename))
{    
    unlink($_SERVER['DOCUMENT_ROOT'].'/pea_jk/report/temp/'.$filename);
       // echo "xx";
    $pdf->Output($_SERVER['DOCUMENT_ROOT'].'/pea_jk/report/temp/'.$filename,'F');
}else{
   $pdf->Output($_SERVER['DOCUMENT_ROOT'].'/pea_jk/report/temp/'.$filename,'F');
}
ob_end_clean() ;*/



//exit;
?>