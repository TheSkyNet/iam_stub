<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class ProjectMigration_100
 */
class ProjectMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('project', [
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
                    'img',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 1000,
                        'after' => 'id'
                    ]
                ),
                new Column(
                    'title',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 1000,
                        'after' => 'img'
                    ]
                ),
                new Column(
                    'body',
                    [
                        'type' => Column::TYPE_TEXT,
                        'notNull' => false,
                        'after' => 'title'
                    ]
                ),
                new Column(
                    'type',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'default' => "project",
                        'notNull' => false,
                        'size' => 15,
                        'after' => 'body'
                    ]
                ),
            ],
            'indexes' => [
                new Index('project_id_uindex', ['id'], ''),
                new Index('project_pkey', ['id'], ''),
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
