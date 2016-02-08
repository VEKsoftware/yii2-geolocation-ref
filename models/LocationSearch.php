<?php

namespace geolocation\models;

use Yii;

use yii\base\Model;
use yii\data\ActiveDataProvider;

use geolocation\models\Location;

/**
 * RefRightsSearch represents the model behind the search form about `partneruser\models\RefRights`.
 */
class LocationSearch extends Location
{
    protected $formName;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type_id'], 'integer'],
            [['name'], 'string'],
        ];
    }

    /**
     * custom formName() method
     */
    public function formName( $name = null )
    {
        if( !is_null($name) ) $this->formName = $name;
        return ( is_null($this->formName) ) ? 'LocationSearch' : $this->formName;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $locationIds = null)
    {
        $query = Location::find();
        
        if( !is_null($locationIds) && is_array($locationIds) ) $query->where(['id' => $locationIds]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->setSort([
                'attributes' => [
                    'id',
                    'name',
                    'type_id',
                ]
            ]
        );

        $query->andFilterWhere([
            'id' => $this->id,
            'type_id' => $this->type_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
