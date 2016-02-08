<?php

namespace geolocation\models;

use Yii;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%location}}".
 */
class Location extends \geolocation\components\CommonRecord
{
    /**
     * list of location types
     */
    public static $types = [
        [
            'id' => 1,
            'name' => 'Группа стран',
            'tag' => 'country_group',
        ],
        [
            'id' => 2,
            'name' => 'Страна',
            'tag' => 'country',
        ],
        [
            'id' => 3,
            'name' => 'Федеральный округ',
            'tag' => 'region_group',
        ],
        [
            'id' => 4,
            'name' => 'Субъект федерации', // область, республика, край
            'tag' => 'region',
        ],
        [
            'id' => 5,
            'name' => 'Город',
            'tag' => 'city',
        ],
/*
        [
            'id' => 5,
            'name' => 'Район',
            'tag' => 'county',
        ],
        [
            'id' => 6,
            'name' => 'Город',
            'tag' => 'city',
        ],
        [
            'id' => 7,
            'name' => 'Улица',
            'tag' => 'street',
        ],
        [
            'id' => 8,
            'name' => 'Дом',
            'tag' => 'house',
        ],
*/
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
            'id' => Yii::t('geolocation', 'ID'),
            'name' => Yii::t('geolocation', 'Location Name'),
            'code' => Yii::t('geolocation', 'Location Code'),
            'type_id' => Yii::t('geolocation', 'Location Type'),
        ];
    }

    /**
     * @inherit
     */
    public function behaviors()
    {
        $module = \geolocation\GeoLocation::getInstance();
        if(is_object($module)) {
            return [
                'access'=>[
                    'class'=> $module->accessClass,
                ],
            ];
        } else {
            return [];
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTimeZoneObj()
    {
        return $this->hasOne(LocationTimeZones::className(), ['location_id' => 'id']);
    }
    
    /**
     * timezone for current object or for his nearest upper object
     * 
     * @return timezone AS string (or return NULL)
     */
    public function getTimezone()
    {
        if ( !empty($this->timeZoneObj) ) return $this->timeZoneObj->timezone;
        
        $lowest = $this->findLowestUpper();
        return ( !is_null($lowest) ) ? $lowest->timezone : null;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrencyObj()
    {
        return $this->hasOne(LocationCurrencies::className(), ['location_id' => 'id']);
    }
    
    /**
     * currency for current object or for his nearest upper object
     * 
     * @return currency AS object (or return NULL)
     */
    public function getCurrency()
    {
        if ( !empty($this->currencyObj) ) return $this->currencyObj->currency;
        
        $lowest = $this->findLowestUpper();
        return ( !is_null($lowest) ) ? $lowest->currency : null;
    }
    
    /**
     * @return 
     */
    public function findLowestUpper()
    {
        if( empty($this->upper) ) return null;
        
        $lowestTypeId = 0;
        foreach( $this->upper as $upper ) {
            if( $upper->type_id > $lowestTypeId ) {
                $lowestTypeId = $upper->type_id;
                $lowest = $upper;
            }
        }
        
        return ( isset($lowest) ) ? $lowest : null;
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
        return $this->hasMany(Location::className(), ['id' => 'lower_id'])
            ->via('locationLinksLower')
            ->from(['lower' => static::tableName()]);
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
        return $this->hasMany(Location::className(), ['id' => 'upper_id'])
            ->via('locationLinksUpper')
            ->from(['upper' => static::tableName()]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public static function findById( $id )
    {
        return static::find()->where(['location.id' => $id]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function findByUpperId( $id )
    {
        return static::find()->joinWith('upper')->where(['upper.id' => $id]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function findFirst()
    {
        $types = ArrayHelper::map(static::$types,'tag','id');
        return static::find()->where(['type_id' => $types['country']]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function findByName( $name )
    {
        return static::find()->where(['like','name',$name]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function findLocation( $input )
    {
        if( !is_array($input) ) return null;
        
        $listTypes = ArrayHelper::map( static::$types, 'tag', 'id' );
        
        $filtered = array_filter( $input, function( $key ) use($listTypes) { return isset( $listTypes[$key] ); }, ARRAY_FILTER_USE_KEY );
        if( empty($filtered) ) return null;
        
        $counter = 0;
        $locationTable = Location::tableName();
        
        $query = static::find();
        foreach( $filtered as $type => $name ) {
            
            if( $counter === 0 ) {
                
                $query->andWhere(['and', [$locationTable.'.type_id' => $listTypes[$type]], [$locationTable.'.name' => $name]]);
                
            } else {
                
                $linksTable = 'link'.strval($counter);
                
                $query->leftJoin(LocationLinks::tableName().' AS '.$linksTable, $linksTable.'.lower_id = '.$locationTable.'.id');
                
                $locationTable = 'loc'.strval($counter);
                $query->leftJoin(Location::tableName().' AS '.$locationTable, $locationTable.'.id = '.$linksTable.'.upper_id');
                
                $query->andWhere(['and', [$locationTable.'.type_id' => $listTypes[$type]], [$locationTable.'.name' => $name]]);
            }
            
            $counter++;
            
        }
        
        //$result = $query->all();
        //if( is_array($result) && (count($result) == 1) ) return $result[0];
        //return $result;
        
        return $query->one();
    }

    public function getFullName()
    {
        $uppers = $this->upper;
        $uppers[] = $this;
        return join(', ',ArrayHelper::getColumn($uppers,'name'));
    }
}
