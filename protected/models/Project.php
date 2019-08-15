<?php

/**
 * This is the model class for table "project".
 *
 * The followings are the available columns in table 'project':
 * @property integer $id
 * @property string $name
 * @property integer $fiscal_year
 * @property integer $is_special
 * @property integer $updated_by
 */
class Project extends CActiveRecord
{
	public $vc_name,$vc_contract_no,$vc_budget,$vc_vendor_name,$owner;

	public function getOwner(){

         return $this->owner;

     }


     public function setOwner($owner){

         $this->owner = $owner;

     }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'project';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name,owner_id', 'required'),
			array('fiscal_year, is_special, updated_by', 'numerical', 'integerOnly'=>true),
			array('name,owner_id', 'length', 'max'=>500),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id,owner_id,owner_name, name, fiscal_year, is_special, updated_by,vc_name,vc_contract_no,vc_budget,vc_vendor_name', 'safe', 'on'=>'search'),
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
			'vendor_contract' => array(self::HAS_MANY, 'VendorContract', 'proj_id'),
			'owner'=> array(self::BELONGS_TO, 'Vendor','owner_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'โครงการ',
			'fiscal_year' => 'ปีงบประมาณ',
			'is_special' => 'โครงการเพิ่มลด',
			'updated_by' => 'user update',
			'owner_id'=>'ผู้ว่าจ้าง',

			'vc_name'=>'ชื่องาน',
			'vc_contract_no'=>'เลขที่สัญญา/ใบสั่งจ้าง',
			'vc_budget'=>'วงเงินสัญญา',
			'vc_vendor_name'=>'ผู้รับจ้าง'
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('fiscal_year',$this->fiscal_year);
		$criteria->compare('is_special',$this->is_special);
		$criteria->compare('updated_by',$this->updated_by);

		//relative search
		$criteria->select = array('Project.id','Project.name','Project.fiscal_year');
		$criteria->alias = 'Project';
		$criteria->join='LEFT JOIN vendor_contract ON Project.id=vendor_contract.proj_id LEFT JOIN vendor ON vendor.v_id=vendor_contract.vendor_id';

		$criteria->compare('vendor_contract.name',$this->vc_name,true);	
		$criteria->compare('vendor_contract.contract_no',$this->vc_contract_no,true);	
		$criteria->compare('vendor_contract.budget',$this->vc_budget,true);
		$criteria->compare('vendor.v_name',$this->vc_vendor_name,true);	

		//$criteria->group = 'Project.id';
	

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	// public function getVendor($data,$attribute)
	// {
		
	// 	$Criteria = new CDbCriteria();
	// 	$Criteria->alias = 'vendor';
	// 	$Criteria->join='LEFT JOIN vendor_contract ON vendor.v_id=vendor_contract.vendor_id';
 //        $Criteria->condition = "vendor_contract.proj_id=".$data->id;
 //        $model = Vendor::model()->findAll($Criteria);
 //        //return print_r($data->vendor_contract->name);
 //        if(!empty($model))
	// 	   return $model[0]->$attribute;
	// }

	public function getName($data)
	{
		
      
        if(!empty($data->vendor_contract))
		   return print_r($data->vendor_contract);
	}

	public function getVendorContract($data,$attribute)
	{
		
		$str = "";
		foreach ($data->vendor_contract as $key => $value) {
			$str .= $value->$attribute."<hr>";
		}

        return substr($str,0,-4)."<br><br>";
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Project the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	 protected function afterFind(){
         
    }
}
