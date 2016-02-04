<?php

namespace geolocation\models;

use Yii;

/**
 * This is the model class for table "{{%location_links}}".
 */
class LocationLinks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%location_links}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['upper_id','lower_id','level'], 'required'],
            
            [['upper_id','lower_id','level'], 'integer'],
            [['upper_id','lower_id'], 'unique', 'targetAttribute' => ['upper_id','lower_id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'upper_id' => Yii::t('partneruser', 'Location Links Upper'),
            'lower_id' => Yii::t('partneruser', 'Location Links Lower'),
            'level' => Yii::t('partneruser', 'Location Links Level'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpper()
    {
        return $this->hasOne(Location::className(), ['id' => 'upper_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLower()
    {
        return $this->hasOne(Location::className(), ['id' => 'lower_id']);
    }
    
}
