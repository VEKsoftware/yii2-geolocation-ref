<?php

use yii\db\Migration;

class m180302_125402_add_location_phone_codes extends Migration
{
    /**
     * @return mixed[]
     */
    protected function data(): array
    {
        return [
            [
                'country' => 'Россия',
                'code' => [
                    'symbolic_id' => 'ru',
                    'code' => '+7',
                    'regexp' => '/^\+7/',
                ],
            ],
            [
                'country' => 'Armenia',
                'code' => [
                    'symbolic_id' => 'am',
                    'code' => '+374',
                    'regexp' => '/^\+374/',
                ],
            ],
            [
                'country' => 'Belarus',
                'code' => [
                    'symbolic_id' => 'by',
                    'code' => '+375',
                    'regexp' => '/^\+375/',
                ],
            ],
            [
                'country' => 'Kazakhstan',
                'code' => [
                    'symbolic_id' => 'kz',
                    'code' => '+7',
                    'regexp' => '/^\+7/',
                ],
            ],
            [
                'country' => 'Kyrgyzstan',
                'code' => [
                    'symbolic_id' => 'kg',
                    'code' => '+996',
                    'regexp' => '/^\+996/',
                ],
            ],
            [
                'country' => 'Moldova',
                'code' => [
                    'symbolic_id' => 'md',
                    'code' => '+373',
                    'regexp' => '/^\+373/',
                ],
            ],
            [
                'country' => 'Tadjikistan',
                'code' => [
                    'symbolic_id' => 'tj',
                    'code' => '+992',
                    'regexp' => '/^\+992/',
                ],
            ],
            [
                'country' => 'Turkmenistan',
                'code' => [
                    'symbolic_id' => 'tm',
                    'code' => '+993',
                    'regexp' => '/^\+993/',
                ],
            ],
            [
                'country' => 'Uzbekistan',
                'code' => [
                    'symbolic_id' => 'uz',
                    'code' => '+998',
                    'regexp' => '/^\+998/',
                ],
            ],
            [
                'country' => 'Ukraine',
                'code' => [
                    'symbolic_id' => 'ua',
                    'code' => '+380',
                    'regexp' => '/^\+380/',
                ],
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
    }

    public function safeDown()
    {
        echo "m180302_125402_add_location_phone_codes cannot be reverted.\n";

        return false;
    }
}
