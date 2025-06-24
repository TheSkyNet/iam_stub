<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class PackageMigration_100
 */
class PackageMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('package', [
            'columns' => [
                new Column(
                    'id',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'autoIncrement' => true,
                        'size' => 1,
                        'first' => true,
                        'primary' => true
                    ]
                ),
                new Column(
                    'name',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 1000,
                        'after' => 'id'
                    ]
                ),
                new Column(
                    'description',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 1000,
                        'after' => 'name'
                    ]
                ),
                new Column(
                    'lang',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 500,
                        'after' => 'description'
                    ]
                ),
                new Column(
                    'link',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 500,
                        'after' => 'lang'
                    ]
                ),
                new Column(
                    'slug',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 100,
                        'after' => 'link'
                    ]
                ),
                new Column(
                    'type',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'default' => "package",
                        'notNull' => false,
                        'size' => 15,
                        'after' => 'slug'
                    ]
                ),
            ],
            'indexes' => [
                new Index('package_id_uindex', ['id'], ''),
                new Index('package_pkey', ['id'], ''),
            ],
            'options' => [
                'TABLE_TYPE' => 'BASE TABLE',
                'AUTO_INCREMENT' => '',
                'ENGINE' => 'InnoDB',
                'TABLE_COLLATION' => 'utf8mb4_0900_ai_ci',
            ],
        ]);
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up(): void
    {
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
    }
}
