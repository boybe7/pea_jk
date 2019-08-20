<?php

/**
 * This is the model class for table "payment".
 *
 * The followings are the available columns in table 'payment':
 * @property integer $id
 * @property integer $item_id
 * @property integer $vc_id
 * @property integer $pay_no
 * @property integer $pay_type
 * @property integer $amount
 * @property string $pay_date
 */
class Payment extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'payment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('item_id, vc_id, pay_no, pay_type, amount, pay_date', 'required'),
			array('item_id, vc_id, pay_no, pay_type, amount', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, item_id, vc_id, pay_no, pay_type, amount, pay_date', 'safe', 'on'=>'search'),
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
			'item_id' => 'รายละเอียด',
			'vc_id' => 'Vc',
			'pay_no' => 'Pay No',
			'pay_type' => '0=ค่าของ 1=ค่าขนส่ง 2 = ค่าติดตั้งทดสอบ 3 =รวม',
			'amount' => 'Amount',
			'pay_date' => 'Pay Date',
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
		$criteria->compare('item_id',$this->item_id);
		$criteria->compare('vc_id',$this->vc_id);
		$criteria->compare('pay_no',$this->pay_no);
		$criteria->compare('pay_type',$this->pay_type);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('pay_date',$this->pay_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function searchPayment($vc_id,$pay_no)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('item_id',$this->item_id);
		$criteria->compare('vc_id',$vc_id);
		$criteria->compare('pay_no',$pay_no);
		$criteria->compare('pay_type',$this->pay_type);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('pay_date',$this->pay_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Payment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
