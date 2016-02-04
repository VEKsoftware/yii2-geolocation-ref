<?php

namespace geolocation\models;

use Yii;

/**
 * This is the model class for table "{{%location}}".
 */
class Location extends \geolocation\components\CommonRecord
{
    /**
     * list of location types
     */
    public static $types = [
        'COUNTRY_GROUP' => 1,   // группа стран
        'COUNTRY' => 3,         // страна
        'REGION_GROUP' => 4,    // федеральный округ
        'REGION' => 5,          // субъект федерации (область, республика, край)
        'COUNTY' => 6,          // район области
        'CITY' => 7,            // город
        'STREET' => 8,          // улица
        'HOUSE' => 9,           // дом
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
