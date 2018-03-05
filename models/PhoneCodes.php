<?php

namespace geolocation\models;

use Yii;

/**
 * This is the model class for table "{{%phone_codes}}".
 * 
 * @property integer    $id
 * @property string     $symbolic_id
 * @property string     $code
 */
class PhoneCodes extends \geolocation\components\CommonRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%phone_codes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'symbolic_id', 'code'], 'required'],
            ['id', 'integer'],
            [['symbolic_id', 'code'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('geolocation', 'ID'),
            'symbolic_id' => Yii::t('geolocation', 'Symbolic ID'),
            'code' => Yii::t('geolocation', 'Code'),
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
}