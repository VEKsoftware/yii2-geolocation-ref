<?php

namespace geolocation\models;

use Yii;

/**
 * This is the model class for table "{{%location_phone_codes}}".
 */
class LocationPhoneCodes extends \geolocation\components\CommonRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%location_phone_codes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['location_id', 'phone_code_id'], 'required'],
            [['location_id', 'phone_code_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'location_id' => Yii::t('geolocation', 'Location ID'),
            'phone_code_id' => Yii::t('geolocation', 'Phone Code ID'),
        ];
    }

    /**
     * @inherit
     */
    public function behaviors()
    {
        return [
            'access'=>[
                'class'=>\geolocation\GeoLocation::getInstance()->accessClass,
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhoneCode()
    {
        return $this->hasOne(PhoneCodes::className(), ['id' => 'phone_code_id']);
    }
}