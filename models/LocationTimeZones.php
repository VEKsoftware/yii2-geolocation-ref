<?php

namespace geolocation\models;

use Yii;

/**
 * This is the model class for table "{{%location_timezones}}".
 */
class LocationTimeZones extends \geolocation\components\CommonRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%location_timezones}}';
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
            [['location_id','timezone'], 'required'],
            
            [['location_id'], 'integer'],
            [['location_id'], 'unique'],
            
            [['timezone'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'location_id' => Yii::t('geolocation', 'Location ID'),
            'timezone' => Yii::t('geolocation', 'TimeZone'),
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
    
}
