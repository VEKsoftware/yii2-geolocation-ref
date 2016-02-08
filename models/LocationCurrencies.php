<?php

namespace geolocation\models;

use Yii;

/**
 * This is the model class for table "{{%location_currencies}}".
 */
class LocationCurrencies extends \geolocation\components\CommonRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%location_currencies}}';
    }
    
    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['location_id'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['location_id','curr_id'], 'required'],
            
            [['location_id','curr_id'], 'integer'],
            [['location_id','curr_id'], 'unique', 'targetAttribute' => ['location_id','curr_id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'location_id' => Yii::t('geolocation', 'Location Currencies Location Id'),
            'curr_id' => Yii::t('geolocation', 'Location Currencies Currency Id'),
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
    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'curr_id']);
    }
    
}
