<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class QrLoginSessionsUpdateMigration_101
 * Updates the qr_login_sessions table to support reverse QR login
 */
class QrLoginSessionsUpdateMigration_101 extends Migration
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
        if ($this->getConnection()->tableExists('qr_login_sessions')) {
            // Alter the status column to include new ENUM values for reverse QR login
            $this->getConnection()->modifyColumn(
                'qr_login_sessions',
                null,
                new Column(
                    'status',
                    [
                        'type' => Column::TYPE_ENUM,
                        'notNull' => true,
                        'size' => "'pending','authenticated','expired','pending_mobile_auth','mobile_authenticated'",
                        'default' => 'pending'
                    ]
                )
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
        // Execute the morph to update the table structure
        $this->morph();
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        // Revert the status column to original ENUM values
        if ($this->getConnection()->tableExists('qr_login_sessions')) {
            $this->getConnection()->modifyColumn(
                'qr_login_sessions',
                null,
                new Column(
                    'status',
                    [
                        'type' => Column::TYPE_ENUM,
                        'notNull' => true,
                        'size' => "'pending','authenticated','expired'",
                        'default' => 'pending'
                    ]
                )
            );
        }
    }
}