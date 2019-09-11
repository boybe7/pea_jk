<?php

/**
 * This is the model class for table "vendor_contract".
 *
 * The followings are the available columns in table 'vendor_contract':
 * @property integer $id
 * @property string $name
 * @property string $contract_no
 * @property string $approve_date
 * @property integer $percent_pay
 * @property integer $percent_adv
 * @property integer $budget
 * @property string $detail_approve
 * @property integer $vendor_id
 * @property integer $proj_id
 * @property integer $updated_by
 * @property integer $lock_boq
 */
class VendorContract extends CActiveRecord
{
	
	public $proj_name,$fiscal_year,$vendor_name,$actions,$owner_name;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'vendor_contract';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, percent_pay,contract_no, percent_adv, vendor_id, proj_id, updated_by', 'required'),
			array('percent_pay, percent_adv, budget, vendor_id, proj_id, updated_by, lock_boq,flag_del', 'numerical', 'integerOnly'=>true),
			array('name, detail_approve', 'length', 'max'=>500),
			array('contract_no', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, contract_no, approve_date,end_date, percent_pay, percent_adv, budget, detail_approve, vendor_id, proj_id, updated_by, lock_boq,proj_name,fiscal_year,vendor_name,owner_name,actions,flag_del', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'project' => array(self::BELONGS_TO, 'Project', 'proj_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'ชื่องาน',
			'contract_no' => 'เลขที่สัญญา/ใบสั่งจ้าง',
			'approve_date' => 'วันที่ลงนาม',
			'end_date' => 'วันที่กำหนดแล้วเสร็จ',
			'percent_pay' => '% เบิก',
			'percent_adv' => '% หัก advance',
			'budget' => 'วงเงินสัญญา',
			'detail_approve' => 'รายละเอียดเพิ่ม-ลด',
			'vendor_id' => 'ผู้รับจ้าง',
			'proj_id' => 'โครงการ',
			'updated_by' => 'user update',
			'lock_boq' => 'lock ห้ามแก้ไข boq เมื่อมีการส่งแบบฟอร์มให้บริษัท',
			'flag_del' => 'flag_del',

			'proj_name'=>'โครงการ',
			'fiscal_year'=>'ปีงบประมาณ',
			'vendor_name'=>'ผู้รับจ้าง',
			'owner_name'=>'ผู้ว่าจ้าง',
			'actions'=>'',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('VendorContract.name',$this->name,true);
		$criteria->compare('contract_no',$this->contract_no,true);
		$criteria->compare('approve_date',$this->approve_date,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('percent_pay',$this->percent_pay);
		$criteria->compare('percent_adv',$this->percent_adv);
		$criteria->compare('budget',$this->budget);
		$criteria->compare('detail_approve',$this->detail_approve,true);
		$criteria->compare('vendor_id',$this->vendor_id);
		$criteria->compare('proj_id',$this->proj_id);
		$criteria->compare('updated_by',$this->updated_by);
		$criteria->compare('lock_boq',$this->lock_boq);

		if(Yii::app()->user->isAdmin())
			$criteria->compare('flag_del',$this->flag_del);
		else
		{
            $criteria->compare('VendorContract.flag_del',0);
            $criteria->compare('project.flag_del',0);
		}
		//relative search
		//$criteria->select = array('VendorContract.id','VendorContract.name','VendorContract.contract_no','VendorContract.budget');
		$criteria->alias = 'VendorContract';
		$criteria->join='LEFT JOIN project ON project.id=VendorContract.proj_id LEFT JOIN vendor ON vendor.v_id=VendorContract.vendor_id';

		$criteria->compare('project.name',$this->proj_name,true);
		$criteria->compare('project.owner_name',$this->owner_name,true);	
		$criteria->compare('project.fiscal_year',$this->fiscal_year,true);	
		$criteria->compare('vendor.v_name',$this->vendor_name,true);	
		$criteria->order = 'project.fiscal_year DESC,id DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return VendorContract the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function beforeSave()
    {
         if($this->budget!="")
		 {
		     $this->budget = str_replace(",", "", $this->budget); 
		 }
		  

        $str_date = explode("/", $this->approve_date);
        if(count($str_date)>1)
        	$this->approve_date= ($str_date[2]-543)."-".$str_date[1]."-".$str_date[0];

         $str_date = explode("/", $this->end_date);
        if(count($str_date)>1)
        	$this->end_date= ($str_date[2]-543)."-".$str_date[1]."-".$str_date[0];
        
        return parent::beforeSave();
   }
     protected function afterSave(){
            parent::afterSave();
            $str_date = explode("-", $this->approve_date);
            if(count($str_date)>1)
            	$this->approve_date = $str_date[2]."/".$str_date[1]."/".($str_date[0]);

             $str_date = explode("-", $this->end_date);
            if(count($str_date)>1)
            	$this->end_date = $str_date[2]."/".$str_date[1]."/".($str_date[0]);
               
    }
    protected function afterFind(){
            parent::afterFind();
            $str_date = explode("-", $this->approve_date);
            if($this->approve_date == "0000-00-00")
                $this->approve_date = '';
            else if(count($str_date)>1)
            	$this->approve_date = $str_date[2]."/".$str_date[1]."/".($str_date[0]+543);


            $str_date = explode("-", $this->end_date);
            if($this->end_date == "0000-00-00")
                $this->end_date = '';
            else if(count($str_date)>1)
            	$this->end_date = $str_date[2]."/".$str_date[1]."/".($str_date[0]+543);
            
                            
    }

   public function getAction($proj_id)
   {
   	    return "<a href='createVendorContract/".$proj_id."' title='เพิ่มสัญญา'><i class=' icon-plus-sign icon-green'></i></a><br><a href='update/".$proj_id."' title='แก้ไขโครงการ'><i class=' icon-ok '></i></a><br><a class='confirmation' href='delete/".$proj_id."' title='ลบโครงการ'><i class=' icon-remove icon-red'></i></a><br>";
   }
}
