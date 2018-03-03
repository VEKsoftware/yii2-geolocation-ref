<?php

use yii\db\Migration;
use yii\db\Query;

use geolocation\models\Location;

class m180302_125402_add_location_phone_codes extends Migration
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->db = Location::getDb();
    }
    
    /**
     * @return mixed[]
     */
    protected function data(): array
    {
        return [
            [
                // 'country' => 'Россия',
                'symbolic_id' => 'ru',
                'code' => '+7',
                // 'regexp' => '/^\+7/',
            ],
        ];
    }
    
    public function safeUp()
    {
        $this->createTable(
            'public.phone_codes',
            [
                'id' => $this->primaryKey(),
                'symbolic_id' => $this->string(2)->unique()->notNull(),
                'code' => $this->string()->notNull(),
            ]
        );
        
        $this->createTable(
            'public.location_phone_codes',
            [
                'location_id' => $this->integer()->notNull()->unique(),
                'phone_code_id' => $this->integer()->notNull(),
            ]
        );
        
        $this->addForeignKey(
            'fk-location_phone_codes-location_id-location-id',
            'public.location_phone_codes',
            'location_id',
            'public.location',
            'id',
            'CASCADE',
            'CASCADE'
        );
        
        $this->addForeignKey(
            'fk-location_phone_codes-phone_code_id-phone_codes-id',
            'public.location_phone_codes',
            'phone_code_id',
            'public.phone_codes',
            'id',
            'CASCADE',
            'CASCADE'
        );
        
        $this->batchInsert(
            'public.phone_codes',
            array_keys($this->data()[0]),
            $this->data()
        );
        
        $locationPhoneCodes = (new Query())
            ->select([
                'phone_code_id' => 'pc.id',
                'location_id' => 'loc.id',
            ])
            ->from(['pc' => 'public.phone_codes'])
            ->innerJoin(
                ['loc' => 'public.location'],
                'loc.type_id = 2 AND loc.code = pc.symbolic_id'
            )
            ->createCommand($this->db)
            ->queryAll();
            
        $this->batchInsert(
            'public.location_phone_codes',
            ['location_id', 'phone_code_id'],
            $locationPhoneCodes
        );
    }

    public function safeDown()
    {
        $this->truncateTable('public.location_phone_codes');
        $this->truncateTable('public.phone_codes');
        
        $this->dropTable('public.location_phone_codes');
        $this->dropTable('public.phone_codes');
    }
}
