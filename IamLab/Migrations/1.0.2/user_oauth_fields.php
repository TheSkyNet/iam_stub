<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class UserOauthFieldsMigration_102
 * Adds OAuth-related fields to the user table
 */
class UserOauthFieldsMigration_102 extends Migration
{
    /**
     * Define the table structure updates
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        // Check if table exists before attempting to modify
        if ($this->getConnection()->tableExists('user')) {
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
                        'after' => 'password'
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
                        'default' => false,
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
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up(): void
    {
        // Execute the morph to add the new columns
        $this->morph();

        // Update existing users to have created_at and updated_at timestamps
        $this->getConnection()->execute("
            UPDATE user 
            SET created_at = NOW(), updated_at = NOW() 
            WHERE created_at IS NULL OR updated_at IS NULL
        ");
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        // Remove the added columns if rolling back
        if ($this->getConnection()->tableExists('user')) {
            $this->getConnection()->dropColumn('user', null, 'updated_at');
            $this->getConnection()->dropColumn('user', null, 'created_at');
            $this->getConnection()->dropColumn('user', null, 'email_verified');
            $this->getConnection()->dropColumn('user', null, 'oauth_id');
            $this->getConnection()->dropColumn('user', null, 'oauth_provider');
            $this->getConnection()->dropColumn('user', null, 'avatar');
        }
    }
}