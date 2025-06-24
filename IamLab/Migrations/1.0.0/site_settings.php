<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Migrations\Mvc\Model\Migration;

class SiteSettingsMigration_100 extends Migration
{
    public function morph(): void
    {
        $this->morphTable('site_settings', [
            'columns' => [
                new Column(
                    'id',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'autoIncrement' => true,
                        'size' => 11,
                        'first' => true,
                        'primary' => true
                    ]
                ),
                new Column(
                    'key',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'id'
                    ]
                ),
                new Column(
                    'value',
                    [
                        'type' => Column::TYPE_TEXT,
                        'notNull' => true,
                        'after' => 'key'
                    ]
                ),
                new Column(
                    'type',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 50,
                        'after' => 'value'
                    ]
                ),
                new Column(
                    'description',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 1000,
                        'after' => 'type'
                    ]
                ),
                new Column(
                    'created_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => true,
                        'default' => 'CURRENT_TIMESTAMP',
                        'after' => 'description'
                    ]
                ),
                new Column(
                    'updated_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => true,
                        'default' => 'CURRENT_TIMESTAMP',
                        'after' => 'created_at'
                    ]
                )
            ],
            'indexes' => [
                new Index('site_settings_id_uindex', ['id'], ''),
                new Index('site_settings_pkey', ['id'], ''),
                new Index('site_settings_key_unique', ['key'], 'UNIQUE')
            ],
            'options' => [
                'TABLE_TYPE' => 'BASE TABLE',
                'AUTO_INCREMENT' => '',
                'ENGINE' => 'InnoDB',
                'TABLE_COLLATION' => 'utf8mb4_0900_ai_ci',
            ],
        ]);
    }

    public function up(): void
    {
    }

    public function down(): void
    {
    }
}