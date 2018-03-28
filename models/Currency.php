<?php

namespace geolocation\models;

use Yii;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ref_curr".
 *
 * @property integer $id
 * @property string $name
 * @property string $full_name
 * @property string $fmt
 * @property string $countries
 * @property string $symbol
 *
 * @property Payments[] $payments
 * @property PaymentsLog[] $paymentsLogs
 * @property Wallets[] $wallets
 * @property WalletsLog[] $walletsLogs
 */
class Currency extends \geolocation\components\CommonRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ref_curr';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','full_name','fmt'], 'required'],
            [['id'], 'integer'],
            [['fmt'], 'string'],
            [['name'], 'string', 'max' => 5],
            [['full_name'], 'string', 'max' => 50],
            [['iso_code'], 'integer', 'min' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('geolocation', 'ID'),
            'name' => Yii::t('geolocation', 'Currency Name'),
            'full_name' => Yii::t('geolocation', 'Currency Full Name'),
            'fmt' => Yii::t('geolocation', 'Currency Fmt'),
            'iso_code' => Yii::t('geolocation', 'Currency Iso Code'),
            'symbol' => Yii::t('geolocation', 'Currency Symbol'),
            //'countries' => Yii::t('geolocation', 'Countries'),
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
     * Formats a money amount with currency
     *
     * @return string Formatted amount
     */
    public function format($amount)
    {
        return sprintf($this->fmt, $amount);
    }

    public function getArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'full_name' => $this->full_name,
            'format' => $this->fmt,
            'symbol' => $this->symbol,
        ];
    }

    /**
     * Yield a list of all available currencies
     */
    public function listCurrencies()
    {
        return Currency::find()->indexBy('id')->all();
        if( !empty( $currency ) ) return ArrayHelper::map( $currency, 'id', 'name' );
        return [];
    }
    
    /**
     * (rus.) Символ валюты. Предполагается, что это символ в кодировке "UTF-8".
     * Значение свойства формируется на основе свойства $fmt, которое записано в БД.
     * @return string
     */
    public function getSymbol(): string
    {
        $symbol = preg_replace('/(^(%s|\ )+)|((%s|\ )+$)/', '', $this->fmt);
        return $symbol;
    }
}
