<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class UserMigration_100
 */
class FilepondMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('filepond', [
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
                    'filename',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 255,
                        'after' => 'id'
                    ]
                ),
                new Column(
                    'filepath',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 255,
                        'after' => 'filename'
                    ]
                ),
                new Column(
                    'extension',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 255,
                        'after' => 'filepath'
                    ]
                ),
                new Column(
                    'mimetypes',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 255,
                        'after' => 'extension'
                    ]
                ),
                new Column(
                    'expires_at',
                    [
                        'type' => Column::TYPE_DATETIME,
                        'notNull' => false,
                        'size' => 6,
                        'after' => 'mimetypes'
                    ]
                ),
            ],
            'indexes' => [
                new Index('filepond_pkey', ['id'], ''),
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
