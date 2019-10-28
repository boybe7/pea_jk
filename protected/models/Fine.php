<?php

/**
 * This is the model class for table "fine".
 *
 * The followings are the available columns in table 'fine':
 * @property integer $id
 * @property string $detail
 * @property integer $amount
 * @property integer $vc_id
 * @property integer $pay_no
 */
class Fine extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'fine';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('detail, amount, vc_id, pay_no', 'required'),
			array('amount, vc_id, pay_no', 'numerical', 'integerOnly'=>true),
			array('detail', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, detail, amount, vc_id, pay_no', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'detail' => 'รายละเอียด',
			'amount' => 'ค่าปรับ',
			'vc_id' => 'โครงการ',
			'pay_no' => 'Pay No',
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
		$criteria->compare('detail',$this->detail,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('vc_id',$this->vc_id);
		$criteria->compare('pay_no',$this->pay_no);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function searchByPayment($vc_id,$pay_no)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('detail',$this->detail,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('vc_id',$vc_id);
		$criteria->compare('pay_no',$pay_no);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	protected function beforeValidate()
	{
		 if($this->amount!="")
		 {
		     $this->amount = str_replace(",", "", $this->amount); 
		 }
		  
		 return parent::beforeValidate();
	}


	public function beforeSave()
    {
         if($this->amount!="")
		 {
		     $this->amount = str_replace(",", "", $this->amount); 
		 }
	}

	 protected function afterFind(){
            parent::afterFind();                        
            //$this->amount = number_format($this->amount,0);
                            
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Fine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
