<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class JobsMigration_102
 */
class JobsMigration_102 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('jobs', [
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
                    'type',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'id'
                    ]
                ),
                new Column(
                    'payload',
                    [
                        'type' => Column::TYPE_TEXT,
                        'notNull' => false,
                        'after' => 'type'
                    ]
                ),
                new Column(
                    'status',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 50,
                        'default' => 'pending',
                        'after' => 'payload'
                    ]
                ),
                new Column(
                    'priority',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 1,
                        'default' => 5,
                        'after' => 'status'
                    ]
                ),
                new Column(
                    'attempts',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 1,
                        'default' => 0,
                        'after' => 'priority'
                    ]
                ),
                new Column(
                    'max_attempts',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 1,
                        'default' => 3,
                        'after' => 'attempts'
                    ]
                ),
                new Column(
                    'error_message',
                    [
                        'type' => Column::TYPE_TEXT,
                        'notNull' => false,
                        'after' => 'max_attempts'
                    ]
                ),
                new Column(
                    'scheduled_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => false,
                        'after' => 'error_message'
                    ]
                ),
                new Column(
                    'started_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => false,
                        'after' => 'scheduled_at'
                    ]
                ),
                new Column(
                    'completed_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => false,
                        'after' => 'started_at'
                    ]
                ),
                new Column(
                    'created_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => true,
                        'default' => 'CURRENT_TIMESTAMP',
                        'after' => 'completed_at'
                    ]
                ),
                new Column(
                    'updated_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => true,
                        'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
                        'after' => 'created_at'
                    ]
                ),
            ],
            'indexes' => [
                new Index('jobs_id_primary', ['id'], 'PRIMARY'),
                new Index('jobs_status_index', ['status'], ''),
                new Index('jobs_type_index', ['type'], ''),
                new Index('jobs_priority_index', ['priority'], ''),
                new Index('jobs_scheduled_at_index', ['scheduled_at'], ''),
                new Index('jobs_status_priority_created_index', ['status', 'priority', 'created_at'], ''),
            ],
            'options' => [
                'TABLE_TYPE' => 'BASE TABLE',
                'AUTO_INCREMENT' => '1',
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
        // All columns are created in morph() method
        // This method can be used for additional modifications if needed
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        $this->getConnection()->dropTable('jobs');
    }

    /**
     * This method is called after the table was created
     *
     * @return void
     */
    public function afterCreateTable(): void
    {
        // Insert initial data if needed
    }
}