<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class SubscriptionMigration_104
 */
class SubscriptionMigration_104 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('subscription', [
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
                    'user_id',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'after' => 'id'
                    ]
                ),
                new Column(
                    'payment_method',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 50,
                        'after' => 'user_id'
                    ]
                ),
                new Column(
                    'subscription_id',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 255,
                        'after' => 'payment_method'
                    ]
                ),
                new Column(
                    'plan_id',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 100,
                        'after' => 'subscription_id'
                    ]
                ),
                new Column(
                    'status',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 20,
                        'default' => 'active',
                        'after' => 'plan_id'
                    ]
                ),
                new Column(
                    'starts_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => false,
                        'after' => 'status'
                    ]
                ),
                new Column(
                    'ends_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => false,
                        'after' => 'starts_at'
                    ]
                ),
                new Column(
                    'trial_ends_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => false,
                        'after' => 'ends_at'
                    ]
                ),
                new Column(
                    'canceled_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => false,
                        'after' => 'trial_ends_at'
                    ]
                ),
                new Column(
                    'payload',
                    [
                        'type' => Column::TYPE_TEXT,
                        'notNull' => false,
                        'after' => 'canceled_at'
                    ]
                ),
                new Column(
                    'created_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => true,
                        'default' => 'CURRENT_TIMESTAMP',
                        'after' => 'payload'
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
                new Index('subscription_pkey', ['id'], ''),
                new Index('subscription_user_id_index', ['user_id'], ''),
                new Index('subscription_id_index', ['subscription_id'], ''),
                new Index('subscription_status_index', ['status'], ''),
            ],
            'references' => [
                new Reference(
                    'subscription_user_id_fk',
                    [
                        'referencedTable' => 'user',
                        'columns' => ['user_id'],
                        'referencedColumns' => ['id'],
                        'onDelete' => 'CASCADE',
                        'onUpdate' => 'CASCADE'
                    ]
                )
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
        $this->getConnection()->dropTable('subscription');
    }
}
