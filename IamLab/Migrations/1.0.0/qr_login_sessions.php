<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class QrLoginSessionsMigration_100
 */
class QrLoginSessionsMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('qr_login_sessions', [
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
                    'session_token',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 64,
                        'after' => 'id'
                    ]
                ),
                new Column(
                    'user_id',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => false,
                        'size' => 1,
                        'after' => 'session_token'
                    ]
                ),
                new Column(
                    'status',
                    [
                        'type' => Column::TYPE_ENUM,
                        'notNull' => true,
                        'size' => "'pending','authenticated','expired'",
                        'default' => 'pending',
                        'after' => 'user_id'
                    ]
                ),
                new Column(
                    'created_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => true,
                        'default' => 'CURRENT_TIMESTAMP',
                        'after' => 'status'
                    ]
                ),
                new Column(
                    'expires_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => true,
                        'after' => 'created_at'
                    ]
                ),
                new Column(
                    'authenticated_at',
                    [
                        'type' => Column::TYPE_TIMESTAMP,
                        'notNull' => false,
                        'after' => 'expires_at'
                    ]
                ),
            ],
            'indexes' => [
                new Index('qr_login_sessions_pkey', ['id'], ''),
                new Index('qr_login_sessions_session_token_uindex', ['session_token'], ''),
                new Index('qr_login_sessions_user_id_index', ['user_id'], ''),
                new Index('qr_login_sessions_status_index', ['status'], ''),
                new Index('qr_login_sessions_expires_at_index', ['expires_at'], ''),
            ],
            'references' => [
                new Reference(
                    'qr_login_sessions_user_id_fk',
                    [
                        'referencedTable' => 'user',
                        'referencedSchema' => null,
                        'columns' => ['user_id'],
                        'referencedColumns' => ['id'],
                        'onUpdate' => 'CASCADE',
                        'onDelete' => 'CASCADE'
                    ]
                ),
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
        // No initial data needed for QR login sessions
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the table if rolling back
        $this->getConnection()->dropTable('qr_login_sessions');
    }
}