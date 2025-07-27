<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class UserRolesMigration_103
 */
class UserRolesMigration_101 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('user_roles', [
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
                    'user_id',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 1,
                        'after' => 'id'
                    ]
                ),
                new Column(
                    'role_id',
                    [
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 1,
                        'after' => 'user_id'
                    ]
                ),
                new Column(
                    'created_at',
                    [
                        'type' => Column::TYPE_DATETIME,
                        'notNull' => false,
                        'after' => 'role_id'
                    ]
                ),
                new Column(
                    'updated_at',
                    [
                        'type' => Column::TYPE_DATETIME,
                        'notNull' => false,
                        'after' => 'created_at'
                    ]
                ),
            ],
            'indexes' => [
                new Index('user_roles_id_uindex', ['id'], ''),
                new Index('user_roles_user_id_index', ['user_id'], ''),
                new Index('user_roles_role_id_index', ['role_id'], ''),
                new Index('user_roles_user_role_unique', ['user_id', 'role_id'], 'UNIQUE'),
                new Index('user_roles_pkey', ['id'], ''),
            ],
            'references' => [
                new Reference(
                    'user_roles_user_id_fk',
                    [
                        'referencedTable' => 'user',
                        'referencedColumns' => ['id'],
                        'columns' => ['user_id'],
                        'onDelete' => 'CASCADE',
                        'onUpdate' => 'CASCADE'
                    ]
                ),
                new Reference(
                    'user_roles_role_id_fk',
                    [
                        'referencedTable' => 'roles',
                        'referencedColumns' => ['id'],
                        'columns' => ['role_id'],
                        'onDelete' => 'CASCADE',
                        'onUpdate' => 'CASCADE'
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
        // No default data needed for pivot table
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        $this->getConnection()->dropTable('user_roles');
    }
}