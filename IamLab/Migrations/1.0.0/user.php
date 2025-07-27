<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class UserMigration_100
 */
class UserMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        // Create base table structure with minimal columns
        $this->morphTable('user', [
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
            ],
            'indexes' => [
                new Index('user_id_uindex', ['id'], ''),
                new Index('user_pkey', ['id'], ''),
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
        // Add name column
        $this->getConnection()->addColumn(
            'user',
            null,
            new Column(
                'name',
                [
                    'type' => Column::TYPE_VARCHAR,
                    'notNull' => true,
                    'size' => 50,
                    'after' => 'id'
                ]
            )
        );

        // Add email column
        $this->getConnection()->addColumn(
            'user',
            null,
            new Column(
                'email',
                [
                    'type' => Column::TYPE_VARCHAR,
                    'notNull' => true,
                    'size' => 50,
                    'after' => 'name'
                ]
            )
        );

        // Add password column
        $this->getConnection()->addColumn(
            'user',
            null,
            new Column(
                'password',
                [
                    'type' => Column::TYPE_VARCHAR,
                    'notNull' => false,
                    'size' => 255,
                    'after' => 'email'
                ]
            )
        );

        // Add key column
        $this->getConnection()->addColumn(
            'user',
            null,
            new Column(
                'key',
                [
                    'type' => Column::TYPE_VARCHAR,
                    'notNull' => false,
                    'size' => 255,
                    'after' => 'password'
                ]
            )
        );

        // Add avatar column
        $this->getConnection()->addColumn(
            'user',
            null,
            new Column(
                'avatar',
                [
                    'type' => Column::TYPE_VARCHAR,
                    'notNull' => false,
                    'size' => 255,
                    'after' => 'key'
                ]
            )
        );

        // Add oauth_provider column
        $this->getConnection()->addColumn(
            'user',
            null,
            new Column(
                'oauth_provider',
                [
                    'type' => Column::TYPE_VARCHAR,
                    'notNull' => false,
                    'size' => 50,
                    'after' => 'avatar'
                ]
            )
        );

        // Add oauth_id column
        $this->getConnection()->addColumn(
            'user',
            null,
            new Column(
                'oauth_id',
                [
                    'type' => Column::TYPE_VARCHAR,
                    'notNull' => false,
                    'size' => 100,
                    'after' => 'oauth_provider'
                ]
            )
        );

        // Add email_verified column
        $this->getConnection()->addColumn(
            'user',
            null,
            new Column(
                'email_verified',
                [
                    'type' => Column::TYPE_BOOLEAN,
                    'notNull' => true,
                    'default' => 0,
                    'after' => 'oauth_id'
                ]
            )
        );

        // Add created_at column
        $this->getConnection()->addColumn(
            'user',
            null,
            new Column(
                'created_at',
                [
                    'type' => Column::TYPE_TIMESTAMP,
                    'notNull' => true,
                    'default' => 'CURRENT_TIMESTAMP',
                    'after' => 'email_verified'
                ]
            )
        );

        // Add updated_at column
        $this->getConnection()->addColumn(
            'user',
            null,
            new Column(
                'updated_at',
                [
                    'type' => Column::TYPE_TIMESTAMP,
                    'notNull' => true,
                    'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
                    'after' => 'created_at'
                ]
            )
        );

        // Add email index
        $this->getConnection()->addIndex(
            'user',
            null,
            new Index('user_email_uindex', ['email'], '')
        );

        // Add indexes for OAuth fields
        $this->getConnection()->addIndex(
            'user',
            null,
            new Index('user_oauth_provider_index', ['oauth_provider'], '')
        );

        $this->getConnection()->addIndex(
            'user',
            null,
            new Index('user_oauth_id_index', ['oauth_id'], '')
        );

        $this->getConnection()->addIndex(
            'user',
            null,
            new Index('user_email_verified_index', ['email_verified'], '')
        );
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
