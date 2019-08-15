<?php

/**
 * This is the model class for table "payment_detail".
 *
 * The followings are the available columns in table 'payment_detail':
 * @property integer $id
 * @property integer $proj_id
 * @property integer $form_type
 * @property integer $pay_no
 * @property string $date_create
 */
class PaymentDetail extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'payment_detail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('vc_id, form_type, pay_no, date_create', 'required'),
			array('vc_id, form_type, pay_no', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, vc_id, form_type, pay_no, date_create', 'safe', 'on'=>'search'),
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
			'vc_id' => 'โครงการ',
			'form_type' => '0 =ค่าขนส่ง แยกจากค่าติดตั้ง 1= รวมหมด',
			'pay_no' => 'งวดที่',
			'date_create' => 'Date Create',
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
		$criteria->compare('form_type',$this->form_type);
		$criteria->compare('pay_no',$this->pay_no);
		$criteria->compare('date_create',$this->date_create,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PaymentDetail the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getPayAmount($proj_id='',$pay_no='')
	{
		$payment = Yii::app()->db->createCommand()
			                        ->select('SUM(amount) as sum')
			                        ->from('payment')
			                        //->join('user','user_create=u_id')
			                        ->where("pay_type=0 AND vc_id='".$proj_id."' AND pay_no <".$pay_no)
			                        ->queryAll();
	    $amount_prev = $payment[0]["sum"];
		return 0;
	}

	public function getRemainAmount($proj_id='',$pay_no='')
	{
		return 0;
	}
}
