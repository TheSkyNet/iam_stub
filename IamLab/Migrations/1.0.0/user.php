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
                new Column(
                    'name',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 50,
                        'after' => 'id'
                    ]
                ),
                new Column(
                    'email',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 50,
                        'after' => 'name'
                    ]
                ),
                new Column(
                    'key',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => false,
                        'size' => 255,
                        'after' => 'password'
                    ]
                ),
                new Column(
                    'password',
                    [
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'email'
                    ]
                ),
            ],
            'indexes' => [
                new Index('user_email_uindex', ['email'], ''),
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
        $this->getConnection()->insert(
            'user',
            [
                'Admin',
                \App\Core\Helpers\env('PASSWORD_USERNAME', 'PASSWORD_USERNAME'),
                password_hash(
                    \App\Core\Helpers\env('PASSWORD_DEFAULT', 'PASSWORD_DEFAULT'),
                    PASSWORD_DEFAULT
                )
            ],
            [
                'name',
                'email',
                'password',
            ]
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
