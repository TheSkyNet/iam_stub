<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class ErrorLogsMigration_103
 */
class ErrorLogsMigration_103 extends Migration
{
    /**
     * Define the table structure
     */
    public function morph(): void
    {
        $this->morphTable('error_logs', [
            'columns' => [
                new Column(
                    'id',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'autoIncrement' => true,
                        'size' => 10,
                        'first' => true,
                        'primary' => true,
                    ]
                ),
                new Column(
                    'level',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 20,
                        'after' => 'id',
                        'default' => 'error',
                    ]
                ),
                new Column(
                    'message',
                    [
                        'type' => Column::TYPE_TEXT,
                        'notNull' => true,
                        'after' => 'level',
                    ]
                ),
                new Column(
                    'context',
                    [
                        'type' => Column::TYPE_TEXT,
                        'notNull' => false,
                        'after' => 'message',
                    ]
                ),
                new Column(
                    'url',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 1024,
                        'after' => 'context',
                    ]
                ),
                new Column(
                    'user_agent',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 1024,
                        'after' => 'url',
                    ]
                ),
                new Column(
                    'ip',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 64,
                        'after' => 'user_agent',
                    ]
                ),
                new Column(
                    'user_id',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => false,
                        'size' => 10,
                        'after' => 'ip',
                    ]
                ),
                new Column(
                    'created_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => true,
                        'default' => 'CURRENT_TIMESTAMP',
                        'after' => 'user_id',
                    ]
                ),
            ],
            'indexes' => [
                new Index('error_logs_level_index', ['level']),
                new Index('error_logs_created_at_index', ['created_at']),
            ],
            'options' => [
                'TABLE_TYPE' => 'BASE TABLE',
                'ENGINE' => 'InnoDB',
                'TABLE_COLLATION' => 'utf8mb4_0900_ai_ci',
            ],
        ]);
    }

    public function up(): void
    {
        // Managed by morph()
    }

    public function down(): void
    {
        $this->getConnection()->dropTable('error_logs');
    }
}
