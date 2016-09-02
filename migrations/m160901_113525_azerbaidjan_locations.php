<?php

use yii\db\Migration;

class m160901_113525_azerbaidjan_locations extends Migration
{
    public function safeUp()
    {
        $this->batchInsert(
            '{{location}}',
            [
                'name',
                'code',
                'type_id'
            ],
            [
                ['Azərbaycan', 'az', 2],
                ['Azərbaycan', 'az', 3],
                ['Azərbaycan', 'az', 4],
            ]
        );

        /* @var array $az_c */
        $az_c = (new \yii\db\Query())
            ->select('id')
            ->from('{{location}}')
            ->where(['and', ['name' => 'Azərbaycan'], ['code' => 'az'], ['type_id' => 2]])
            ->one();

        /* @var array $az_gr */
        $az_gr = (new \yii\db\Query())
            ->select('id')
            ->from('{{location}}')
            ->where(['and', ['name' => 'Azərbaycan'], ['code' => 'az'], ['type_id' => 3]])
            ->one();

        /* @var array $az_r */
        $az_r = (new \yii\db\Query())
            ->select('id')
            ->from('{{location}}')
            ->where(['and', ['name' => 'Azərbaycan'], ['code' => 'az'], ['type_id' => 4]])
            ->one();

        /* Добавляем связи между: страна - группа регионов - регион */
        $this->batchInsert(
            '{{location_links}}',
            [
                'upper_id',
                'lower_id'
            ],
            [
                [$az_c['id'], $az_gr['id']],
                [$az_c['id'], $az_r['id']],
                [$az_gr['id'], $az_r['id']],
            ]
        );


        // Autonomy cities
        $count = $this->db
            ->createCommand()
            ->batchInsert(
                '{{location}}',
                ['name', 'type_id'],
                [
                    ['Culfa', 5],
                    ['Naxçıvan', 5],
                    ['Ordubad', 5],
                    ['Şahbuz', 5],
                    ['Şərur', 5],
                    ['Babək', 5],
                    ['Qıvraq', 5],
                    ['Sədərək', 5],
                ]
            )
            ->execute();
        $az_autonomy_cities_ids = $this->db->getLastInsertID('location_id_seq');

        $ins = [];
        for ($i = 0; $i < $count; $i++) {
            $id = $az_autonomy_cities_ids - $i;
            array_push($ins, [$az_c['id'], $id]);
            array_push($ins, [$az_gr['id'], $id]);
            array_push($ins, [$az_r['id'], $id]);
        }
        $this->batchInsert('{{location_links}}', ['upper_id', 'lower_id'], $ins);

        // Central cities
        $count = $this->db
            ->createCommand()
            ->batchInsert(
                '{{location}}',
                ['name', 'type_id'],
                [
                    ['Ağcabədi', 5],
                    ['Ağdaş', 5],
                    ['Ağstafa', 5],
                    ['Ağsu', 5],
                    ['Astara', 5],
                    ['Bakı', 5],
                    ['Balakən', 5],
                    ['Beyləqan', 5],
                    ['Bərdə', 5],
                    ['Biləsuvar', 5],
                    ['Cəlilabad', 5],
                    ['Daşkəsən', 5],
                    ['Dəliməmmədli', 5],
                    ['Gədəbəy', 5],
                    ['Gəncə', 5],
                    ['Goranboy', 5],
                    ['Göyçay', 5],
                    ['Göygöl', 5],
                    ['Göytəpə', 5],
                    ['Hacıqabul', 5],
                    ['Horadiz', 5],
                    ['İmişli', 5],
                    ['İsmayıllı', 5],
                    ['Kürdəmir', 5],
                    ['Lerik', 5],
                    ['Lənkəran', 5],
                    ['Liman', 5],
                    ['Masallı', 5],
                    ['Mingəçevir', 5],
                    ['Naftalan', 5],
                    ['Neftçala', 5],
                    ['Oğuz', 5],
                    ['Qax', 5],
                    ['Qazax', 5],
                    ['Qəbələ', 5],
                    ['Qobustan', 5],
                    ['Qovlar', 5],
                    ['Quba', 5],
                    ['Qusar', 5],
                    ['Saatlı', 5],
                    ['Sabirabad', 5],
                    ['Şabran', 5],
                    ['Salyan', 5],
                    ['Şamaxı', 5],
                    ['Samux', 5],
                    ['Şəki', 5],
                    ['Şəmkir', 5],
                    ['Şirvan', 5],
                    ['Siyəzən', 5],
                    ['Sumqayıt', 5],
                    ['Tərtər', 5],
                    ['Tovuz', 5],
                    ['Ucar', 5],
                    ['Xaçmaz', 5],
                    ['Xırdalan', 5],
                    ['Xızı', 5],
                    ['Xudat', 5],
                    ['Yardımlı', 5],
                    ['Yevlax', 5],
                    ['Zaqatala', 5],
                    ['Zərdab', 5],
                ]
            )
            ->execute();
        $az_central_cities_ids = $this->db->getLastInsertID('location_id_seq');

        $ins = [];
        for ($i = 0; $i < $count; $i++) {
            $id = $az_central_cities_ids - $i;
            array_push($ins, [$az_c['id'], $id]);
            array_push($ins, [$az_gr['id'], $id]);
            array_push($ins, [$az_r['id'], $id]);
        }
        $this->batchInsert('{{location_links}}', ['upper_id', 'lower_id'], $ins);

        // Insert Azerbaidjan currency
        $this->insert('public.{{ref_curr}}', [
            'name' => 'm.',
            'full_name' => 'Monat',
            'fmt' => '%s m',
            'iso_code' => '944',
        ]);

        $az_curr = $this->db->getLastInsertID('ref_curr_id_seq');

        /* Cвязь между страной и валютой */
        $this->insert(
            '{{location_currencies}}',
            [
                'location_id' => $az_c['id'],
                'curr_id' => $az_curr
            ]
        );

        /* Связь между локацией и временной зоной */
        $this->insert(
            '{{location_timezones}}',
            [
                'location_id' => $az_gr['id'],
                'timezone' => 'Asia/Baku'
            ]
        );
    }

    public function safeDown()
    {
        $this->delete(
            'public.{{ref_curr}}',
            [
                'and',
                ['name' => 'm.'],
                ['full_name' => 'Monat'],
                ['fmt' => '%s m'],
                ['iso_code' => '944'],
            ]
        );

        $az_objects_id = (new \yii\db\Query())
            ->select('id')
            ->from('location')
            ->where([
                'or',
                ['name' => 'Azərbaycan'],
                ['name' => 'Əsas hissə'],
                ['name' => 'Naxçıvan Muxtar Respublikası'],
                ['name' => 'Culfa'],
                ['name' => 'Naxçıvan'],
                ['name' => 'Ordubad'],
                ['name' => 'Şahbuz'],
                ['name' => 'Şərur'],
                ['name' => 'Babək'],
                ['name' => 'Qıvraq'],
                ['name' => 'Sədərək'],
                ['name' => 'Ağcabədi'],
                ['name' => 'Ağdaş'],
                ['name' => 'Ağstafa'],
                ['name' => 'Ağsu'],
                ['name' => 'Astara'],
                ['name' => 'Bakı'],
                ['name' => 'Balakən'],
                ['name' => 'Beyləqan'],
                ['name' => 'Bərdə'],
                ['name' => 'Biləsuvar'],
                ['name' => 'Cəlilabad'],
                ['name' => 'Daşkəsən'],
                ['name' => 'Dəliməmmədli'],
                ['name' => 'Gədəbəy'],
                ['name' => 'Gəncə'],
                ['name' => 'Goranboy'],
                ['name' => 'Göyçay'],
                ['name' => 'Göygöl'],
                ['name' => 'Göytəpə'],
                ['name' => 'Hacıqabul'],
                ['name' => 'Horadiz'],
                ['name' => 'İmişli'],
                ['name' => 'İsmayıllı'],
                ['name' => 'Kürdəmir'],
                ['name' => 'Lerik'],
                ['name' => 'Lənkəran'],
                ['name' => 'Liman'],
                ['name' => 'Masallı'],
                ['name' => 'Mingəçevir'],
                ['name' => 'Naftalan'],
                ['name' => 'Neftçala'],
                ['name' => 'Oğuz'],
                ['name' => 'Qax'],
                ['name' => 'Qazax'],
                ['name' => 'Qəbələ'],
                ['name' => 'Qobustan'],
                ['name' => 'Qovlar'],
                ['name' => 'Quba'],
                ['name' => 'Qusar'],
                ['name' => 'Saatlı'],
                ['name' => 'Sabirabad'],
                ['name' => 'Şabran'],
                ['name' => 'Salyan'],
                ['name' => 'Şamaxı'],
                ['name' => 'Samux'],
                ['name' => 'Şəki'],
                ['name' => 'Şəmkir'],
                ['name' => 'Şirvan'],
                ['name' => 'Siyəzən'],
                ['name' => 'Sumqayıt'],
                ['name' => 'Tərtər'],
                ['name' => 'Tovuz'],
                ['name' => 'Ucar'],
                ['name' => 'Xaçmaz'],
                ['name' => 'Xırdalan'],
                ['name' => 'Xızı'],
                ['name' => 'Xudat'],
                ['name' => 'Yardımlı'],
                ['name' => 'Yevlax'],
                ['name' => 'Zaqatala'],
                ['name' => 'Zərdab'],
            ])
            ->all();

        $names = \yii\helpers\ArrayHelper::getColumn($az_objects_id, 'id');

        $this->delete(
            '{{location}}',
            [
                'in',
                'id',
                $names
            ]
        );
    }
}
