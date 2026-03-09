<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class PaymentMigration_104
 */
class PaymentMigration_104 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('payment', [
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
                    'transaction_id',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 255,
                        'after' => 'payment_method'
                    ]
                ),
                new Column(
                    'amount',
                    [
                        'type' => Column::TYPE_DECIMAL,
                        'notNull' => true,
                        'size' => 10,
                        'scale' => 2,
                        'after' => 'transaction_id'
                    ]
                ),
                new Column(
                    'currency',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 3,
                        'default' => 'USD',
                        'after' => 'amount'
                    ]
                ),
                new Column(
                    'status',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 20,
                        'default' => 'pending',
                        'after' => 'currency'
                    ]
                ),
                new Column(
                    'type',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 30,
                        'default' => 'single',
                        'after' => 'status'
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
                new Index('payment_pkey', ['id'], ''),
                new Index('payment_user_id_index', ['user_id'], ''),
                new Index('payment_transaction_id_index', ['transaction_id'], ''),
                new Index('payment_status_index', ['status'], ''),
            ],
            'references' => [
                new Reference(
                    'payment_user_id_fk',
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
        $this->getConnection()->dropTable('payment');
    }
}
