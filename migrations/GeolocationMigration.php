<?php

use yii\db\Migration;

/**
 * класс миграции для модуля кошельков
 */
class GeolocationMigration extends Migration
{

    /* DDL

        CREATE TABLE location (
            id integer NOT NULL,
            name character varying NOT NULL,
            code character varying,
            type_id integer
        );

        ALTER TABLE location OWNER TO simsells;

        CREATE TABLE location_currencies (
            location_id integer NOT NULL,
            curr_id integer NOT NULL
        );

        ALTER TABLE location_currencies OWNER TO simsells;

        COMMENT ON TABLE location_currencies IS 'Links Country-Currency';

        CREATE SEQUENCE location_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;

        ALTER TABLE location_id_seq OWNER TO simsells;
        ALTER SEQUENCE location_id_seq OWNED BY location.id;

        CREATE TABLE location_links (
            upper_id integer NOT NULL,
            lower_id integer NOT NULL,
            level integer
        );

        ALTER TABLE location_links OWNER TO simsells;

        CREATE TABLE location_timezones (
            location_id integer NOT NULL,
            timezone character varying NOT NULL
        );

        ALTER TABLE location_timezones OWNER TO simsells;

        ALTER TABLE ONLY location ALTER COLUMN id SET DEFAULT nextval('location_id_seq'::regclass);
         
        ALTER TABLE ONLY location_currencies
            ADD CONSTRAINT location_currencies_pkey PRIMARY KEY (location_id, curr_id);

        ALTER TABLE ONLY location_links
            ADD CONSTRAINT location_links_upper_id_lower_id_key UNIQUE (upper_id, lower_id);

        ALTER TABLE ONLY location
            ADD CONSTRAINT location_pkey PRIMARY KEY (id);

        ALTER TABLE ONLY location_timezones
            ADD CONSTRAINT location_timezones_pkey PRIMARY KEY (location_id);

        ALTER TABLE ONLY location_currencies
            ADD CONSTRAINT location_currencies_curr_id_fkey FOREIGN KEY (curr_id) REFERENCES ref_curr(id) ON UPDATE CASCADE ON DELETE CASCADE;

        ALTER TABLE ONLY location_currencies
            ADD CONSTRAINT location_currencies_location_id_fkey FOREIGN KEY (location_id) REFERENCES location(id) ON UPDATE CASCADE ON DELETE CASCADE;

        ALTER TABLE ONLY location_links
            ADD CONSTRAINT location_links_lower_id_fkey FOREIGN KEY (lower_id) REFERENCES location(id) ON UPDATE CASCADE ON DELETE CASCADE;

        ALTER TABLE ONLY location_links
            ADD CONSTRAINT location_links_upper_id_fkey FOREIGN KEY (upper_id) REFERENCES location(id) ON UPDATE CASCADE ON DELETE CASCADE;

        ALTER TABLE ONLY location_timezones
            ADD CONSTRAINT location_timezones_location_id_fkey FOREIGN KEY (location_id) REFERENCES location(id) ON UPDATE CASCADE ON DELETE CASCADE;

        CREATE SEQUENCE ref_curr_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;


        ALTER TABLE ref_curr_id_seq OWNER TO inettaxi;

        CREATE TABLE ref_curr (
            id integer DEFAULT nextval('ref_curr_id_seq'::regclass) NOT NULL,
            name character varying(5) NOT NULL,
            full_name character varying(50) NOT NULL,
            fmt character varying,
            countries character varying,
            iso_code integer NOT NULL
        );


        ALTER TABLE ref_curr OWNER TO inettaxi;

        COPY ref_curr (id, name, full_name, fmt, countries, iso_code) FROM stdin;
        1	р.	Рубли	%s руб.		810
        \.

        SELECT pg_catalog.setval('ref_curr_id_seq', 1, true);

        ALTER TABLE ONLY ref_curr
            ADD CONSTRAINT "REF_CURR_pkey" PRIMARY KEY (id);

    */
    
    // наименование для владельца таблиц
    public $dbOwner = 'user';
    
    // наименования таблиц
    public $tableNameLocation = 'location';
    public $tableNameLocationCurrencies = 'location_currencies';
    public $tableNameLocationLinks = 'location_links';
    public $tableNameLocationTimezones = 'location_timezones';
    
    public $tableNameCurrencies = 'ref_curr';
    
    /**
     * применение изменений в БД
     */
    public function safeUp()
    {
        $this->createTables( $schema );
        $this->createTableRelations( $schema );
    }
    
    /**
     * отмена изменений в БД
     */
    public function safeDown()
    {
        $this->deleteTableRelations( $schema );
        $this->deleteTables( $schema );
    }
    
    /**
     * создание таблиц
     */
    private function createTables()
    {
        $schema = $this->db->schema;
        $tableNames = $schema->getTableNames();
        
        // создаём таблицы, если их нет в БД
        if( !in_array($this->tableNameLocation, $tableNames) ) $this->createTableLocation();
        if( !in_array($this->tableNameLocationCurrencies, $tableNames) ) $this->createTableLocationCurrencies();
        if( !in_array($this->tableNameLocationLinks, $tableNames) ) $this->createTableLocationLinks();
        if( !in_array($this->tableNameLocationTimezones, $tableNames) ) $this->createTableLocationTimezones();
        if( !in_array($this->tableNameCurrencies, $tableNames) ) $this->createTableCurrencies();
    }
    
    /**
     * создать связи между таблицами
     */
    private function createTableRelations()
    {
        $this->createTableLocationRelations();
    }
    
    /**
     * удаление таблиц
     */
    private function deleteTables()
    {
        $schema = $this->db->schema;
        $tableNames = $schema->getTableNames();
        
        // удаляем таблицы, если они есть в БД
        if( in_array($this->tableNameLocation, $tableNames) ) $this->dropTable( $this->tableNameLocation );
        if( in_array($this->tableNameLocationCurrencies, $tableNames) ) $this->dropTable( $this->tableNameLocationCurrencies );
        if( in_array($this->tableNameLocationLinks, $tableNames) ) $this->dropTable( $this->tableNameLocationLinks );
        if( in_array($this->tableNameLocationTimezones, $tableNames) ) $this->dropTable( $this->tableNameLocationTimezones );
        if( in_array($this->tableNameCurrencies, $tableNames) ) $this->dropTable( $this->tableNameCurrencies );
    }
    
    /**
     * убрать связи между таблицами
     */
    private function deleteTableRelations()
    {
        $this->deleteTableLocationRelations();
    }
    
    /**
     * создание таблицы для локаций
     */
    private function createTableLocation()
    {
        $this->createTable( $this->tableNameLocation, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(), // name character varying NOT NULL,
            'code' => $this->string(), // code character varying,
            'type_id' => $this->integer(), // type_id integer
        ] );
        
        // устанавливаем владельца
        $this->execute('ALTER TABLE '.$this->tableNameLocation.' OWNER TO '.$this->dbOwner);
    }
    
    /**
     * создание таблицы для связи локаций и валюты
     */
    private function createTableLocationCurrencies()
    {
        $this->createTable( $this->tableNameLocationCurrencies, [
            'location_id' => $this->integer()->notNull(), // integer NOT NULL,
            'curr_id' => $this->integer()->notNull(), // integer NOT NULL
        ] );
        
        // устанавливаем владельца
        $this->execute('ALTER TABLE '.$this->tableNameLocationCurrencies.' OWNER TO '.$this->dbOwner);
    }
    
    /**
     * создание таблицы для связи локаций друг с другом
     */
    private function createTableLocationLinks()
    {
        $this->createTable( $this->tableNameLocationLinks, [
            'upper_id' => $this->integer()->notNull(), // integer NOT NULL,
            'lower_id' => $this->integer()->notNull(), // integer NOT NULL,
            'level' => $this->integer(), // integer
        ] );
        
        // устанавливаем владельца
        $this->execute('ALTER TABLE '.$this->tableNameLocationLinks.' OWNER TO '.$this->dbOwner);
    }
    
    /**
     * создание таблицы для связи локаций друг с другом
     */
    private function createTableLocationTimezones()
    {
        $this->createTable( $this->tableNameLocationTimezones, [
            'location_id' => $this->integer()->notNull(), // integer NOT NULL,
            'timezone' => $this->string()->notNull(), // character varying NOT NULL
        ] );
        
        // устанавливаем владельца
        $this->execute('ALTER TABLE '.$this->tableNameLocationTimezones.' OWNER TO '.$this->dbOwner);
    }
    
    /**
     * создание таблицы для валют
     */
    private function createTableCurrencies()
    {
        $this->createTable( $this->tableNameCurrencies, [
            'id' => $this->primaryKey(),
            'name' => $this->string(5)->notNull(), // character varying(5) NOT NULL,
            'full_name' => $this->string(50)->notNull(), // character varying(50) NOT NULL,
            'fmt' => $this->string(), // character varying,
            'countries' => $this->string(), // character varying,
            'iso_code' => $this->integer()->notNull(), // integer NOT NULL
        ] );
        
        // устанавливаем владельца
        $this->execute('ALTER TABLE '.$this->tableNameCurrencies.' OWNER TO '.$this->dbOwner);
    }
    
    /**
     * создать индексы и связи для таблицы локаций
     */
    private function createTableRelations()
    {
        // таблица LocationCurrencies
        // поле "curr_id"
        $this->createForeign( $this->tableNameLocationCurrencies, 'curr_id', $this->tableNameCurrencies, 'id' );
        // поле "location_id"
        $this->createForeign( $this->tableNameLocationCurrencies, 'location_id', $this->tableNameLocation, 'id' );
        
        // таблица LocationLinks
        // поле "lower_id"
        $this->createForeign( $this->tableNameLocationLinks, 'lower_id', $this->tableNameLocation, 'id' );
        // поле "upper_id"
        $this->createForeign( $this->tableNameLocationLinks, 'upper_id', $this->tableNameLocation, 'id' );
        
        // таблица LocationTimezones
        // поле "location_id"
        $this->createForeign( $this->tableNameLocationTimezones, 'location_id', $this->tableNameLocation, 'id' );
    }
    
    /**
     * удалить индексы и связи для таблицы локаций
     */
    private function deleteTableRelations()
    {
        // таблица LocationCurrencies
        // поле "curr_id"
        $this->deleteForeign( $this->tableNameLocationCurrencies, 'curr_id', $this->tableNameCurrencies, 'id' );
        // поле "location_id"
        $this->deleteForeign( $this->tableNameLocationCurrencies, 'location_id', $this->tableNameLocation, 'id' );
        
        // таблица LocationLinks
        // поле "lower_id"
        $this->deleteForeign( $this->tableNameLocationLinks, 'lower_id', $this->tableNameLocation, 'id' );
        // поле "upper_id"
        $this->deleteForeign( $this->tableNameLocationLinks, 'upper_id', $this->tableNameLocation, 'id' );
        
        // таблица LocationTimezones
        // поле "location_id"
        $this->deleteForeign( $this->tableNameLocationTimezones, 'location_id', $this->tableNameLocation, 'id' );
    }
    
    /**
     * создать связь с полем другой таблицы
     */
    private function createForeign( $tableName, $field, $targetTableName, $targetField )
    {
        // поле "currency"
        $this->createIndex(
            $this->createIndexName( $tableName, $field ),
            $tableName,
            $field
        );
        $this->addForeignKey(
            $this->createForeignName( $tableName, $field ),
            $tableName,
            $field,
            $targetTableName,
            $targetField,
            'RESTRICT',
            'CASCADE'
        );
    }
    
    /**
     * убрать связь с полем другой таблицы
     */
    private function deleteForeign( $tableName, $field, $targetTableName, $targetField )
    {
        $this->dropForeignKey( $this->createForeignName( $tableName, $field ), $tableName);
        $this->dropIndex( $this->createIndexName( $tableName, $field ), $tableName);
    }
    
    /**
     * создать нименование для индекса
     */
    private function createIndexName( $tableName, $field )
    {
        return 'fki_'.$tableName.'_'.$field;
    }
     
    /**
     * создать наименование для связи
     */
    private function createForeignName( $tableName, $field )
    {
        return $tableName.'_'.$field;
    }
}
