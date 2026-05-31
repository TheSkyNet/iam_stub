<?php

/**
 * Command Registration
 *
 * This file registers all available commands for the command runner.
 * Each command should have a unique name and specify the class that handles it.
 */

use IamLab\Commands\TestMailCommand;
use IamLab\Commands\TestPusherCommand;
use IamLab\Commands\MakeJsCommand;
use IamLab\Commands\OllamaCommand;
use IamLab\Commands\AddRoleCommand;
use IamLab\Commands\MakeAdminCommand;
use IamLab\Core\Command\WorkerCommand;
use IamLab\Commands\ProjectInitCommand;

return [
    'test:mail' => [
        'class' => TestMailCommand::class,
        'description' => 'Send a test email to verify email functionality'
    ],

    'test:pusher' => [
        'class' => TestPusherCommand::class,
        'description' => 'Test Pusher real-time functionality'
    ],

    'make:js' => [
        'class' => MakeJsCommand::class,
        'description' => 'Generate Mithril.js views, services, and API controllers'
    ],

    'ollama' => [
        'class' => OllamaCommand::class,
        'description' => 'Enable or disable Ollama Docker service'
    ],

    'user:add-role' => [
        'class' => AddRoleCommand::class,
        'description' => 'Add a role to a user account'
    ],

    'user:make-admin' => [
        'class' => MakeAdminCommand::class,
        'description' => 'Make a user an administrator'
    ],

    'worker:run' => [
        'class' => WorkerCommand::class,
        'description' => 'Run the job queue worker'
    ],

    'project:init' => [
        'class' => ProjectInitCommand::class,
        'description' => 'Initialize a new project from this stub'
    ],

    // Add more commands here as needed
];
