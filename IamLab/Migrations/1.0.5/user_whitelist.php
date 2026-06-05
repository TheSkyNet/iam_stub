<?php

use Phalcon\Db\Column;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class UserWhitelistMigration_105
 */
class UserWhitelistMigration_105 extends Migration
{
    /**
     * Run the migrations
     */
    public function up(): void
    {
        $this->getConnection()->addColumn(
            'user',
            null,
            new Column(
                'whitelist_domains',
                [
                    'type' => Column::TYPE_TEXT,
                    'notNull' => false,
                    'after' => 'key'
                ]
            )
        );
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        $this->getConnection()->dropColumn('user', null, 'whitelist_domains');
    }
}
