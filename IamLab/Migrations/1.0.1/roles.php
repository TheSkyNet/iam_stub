<?php

use Phalcon\Db\Column;
use Phalcon\Db\Exception;
use Phalcon\Db\Index;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class RolesMigration_103
 */
class RolesMigration_101 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws Exception
     */
    public function morph(): void
    {
        $this->morphTable('roles', [
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
                    'description',
                    [
                        'type' => Column::TYPE_TEXT,
                        'notNull' => false,
                        'after' => 'name'
                    ]
                ),
                new Column(
                    'created_at',
                    [
                        'type' => Column::TYPE_DATETIME,
                        'notNull' => false,
                        'after' => 'description'
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
                new Index('roles_id_uindex', ['id'], ''),
                new Index('roles_name_uindex', ['name'], 'UNIQUE'),
                new Index('roles_pkey', ['id'], ''),
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
        // Insert default roles
        $roles = [
            ['admin', 'Administrator with full system access'],
            ['editor', 'Editor with content management permissions'],
            ['member', 'Regular member with basic access'],
            ['guest', 'Guest user with limited access']
        ];

        foreach ($roles as $role) {
            // Check if role already exists
            $existingRole = $this->getConnection()->fetchOne(
                "SELECT COUNT(*) as count FROM roles WHERE name = '" . $role[0] . "'"
            );
            
            if ($existingRole['count'] == 0) {
                $this->getConnection()->insert(
                    'roles',
                    [
                        $role[0],
                        $role[1],
                        date('Y-m-d H:i:s'),
                        date('Y-m-d H:i:s')
                    ],
                    [
                        'name',
                        'description',
                        'created_at',
                        'updated_at'
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        $this->getConnection()->dropTable('roles');
    }
}