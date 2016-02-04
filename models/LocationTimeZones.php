<?php

namespace geolocation\models;

use Yii;

/**
 * This is the model class for table "{{%location_timezones}}".
 */
class LocationTimeZones extends \yii\db\ActiveRecord
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
            'location_id' => Yii::t('partneruser', 'Location ID'),
            'timezone' => Yii::t('partneruser', 'TimeZone'),
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
