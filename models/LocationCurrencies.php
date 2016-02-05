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
    public function rules()
    {
        return [
            [['location_id','currency_id'], 'required'],
            
            [['location_id','currency_id'], 'integer'],
            [['location_id','currency_id'], 'unique', 'targetAttribute' => ['location_id','currency_id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'location_id' => Yii::t('partneruser', 'Location Currencies Location Id'),
            'currency_id' => Yii::t('partneruser', 'Location Currencies Currency Id'),
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
        return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
    }
    
}
