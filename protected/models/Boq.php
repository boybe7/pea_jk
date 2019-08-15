<?php

/**
 * This is the model class for table "boq".
 *
 * The followings are the available columns in table 'boq':
 * @property integer $id
 * @property integer $vc_id
 * @property string $detail
 * @property string $no
 * @property integer $amount
 * @property string $unit
 * @property integer $order_no
 * @property string $price_trans
 * @property string $price_item
 * @property string $price_install
 * @property string $last_update
 */
class Boq extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'boq';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('vc_id, detail', 'required'),
			array('vc_id, type', 'numerical', 'integerOnly'=>true),
			array('detail', 'length', 'max'=>500),
			array('no,amount', 'length', 'max'=>50),
			array('unit', 'length', 'max'=>100),
			array('price_trans, price_item, price_install', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, vc_id, detail, no, amount, unit, type, price_trans, price_item, price_install, last_update', 'safe', 'on'=>'search'),
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
			'vc_id' => 'สัญญา',
			'detail' => 'รายละเอียด',
			'no' => 'ลำดับที่',
			'amount' => 'จำนวน',
			'unit' => 'หน่วย',
			'type' => 'type',
			'price_trans' => 'ค่าขนส่ง',
			'price_item' => 'ค่าอุปกรณ์',
			'price_install' => 'ค่าติดตั้งทดสอบ',
			'last_update' => 'Last Update',
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
		$criteria->compare('vc_id',$this->vc_id);
		$criteria->compare('detail',$this->detail,true);
		$criteria->compare('no',$this->no,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('price_trans',$this->price_trans,true);
		$criteria->compare('price_item',$this->price_item,true);
		$criteria->compare('price_install',$this->price_install,true);
		$criteria->compare('last_update',$this->last_update,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function searchByProject($vc_id)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('vc_id',$vc_id);
		$criteria->compare('detail',$this->detail,true);
		$criteria->compare('no',$this->no,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('unit',$this->unit,true);
		//$criteria->compare('type',$this->type);
		$criteria->compare('price_trans',$this->price_trans,true);
		$criteria->compare('price_item',$this->price_item,true);
		$criteria->compare('price_install',$this->price_install,true);
		$criteria->compare('last_update',$this->last_update,true);
		$criteria->order = 'id ASC'; 
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(

                        'pageSize'=>2000,

                ),
		));
	}



	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Boq the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
