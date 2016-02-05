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
        
        return $query->one();
    }
}
