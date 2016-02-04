<?php

namespace geolocation\models;

use Yii;

/**
 * This is the model class for table "{{%location}}".
 */
class Location extends \yii\db\ActiveRecord
{
    /**
     * list of location types
     */
    public static $types = [
        'GROUP' => 0,               // группа
        
        'CONTINENT' => 1,           // континент
        'REGION' => 2,              // регион
        'COUNTRY' => 3,             // страна
        'COUNTRY_DISTRICT' => 4,    // часть (район, округ, область) страны
        'SUBJECT_FEDERATION' => 5,  // субъект федерации
        'CITY' => 6,                // город
        'VILLAGE' => 7,             // деревня
        'CITY_DISTRICT' => 8,       // район города
        'METRO_STATION' => 9,       // станция метро
        'OTHER' => 10,              // другое
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%location}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','type_id'], 'required'],
            
            [['id','type_id'], 'integer'],
            [['name','code'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('partneruser', 'ID'),
            'name' => Yii::t('partneruser', 'Location Name'),
            'code' => Yii::t('partneruser', 'Location Code'),
            'type_id' => Yii::t('partneruser', 'Location Type'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTimeZone()
    {
        return $this->hasOne(LocationTimeZone::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationLinksLower()
    {
        return $this->hasMany(LocationLinks::className(), ['upper_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLower()
    {
        return $this->hasMany(Location::className(), ['id' => 'lower_id'])->via('locationLinksLower');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationLinksUpper()
    {
        return $this->hasMany(LocationLinks::className(), ['lower_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpper()
    {
        return $this->hasMany(Location::className(), ['id' => 'upper_id'])->via('locationLinksUpper');
    }
    
}
