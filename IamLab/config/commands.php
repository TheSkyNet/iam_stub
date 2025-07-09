<?php

/**
 * Command Registration
 * 
 * This file registers all available commands for the command runner.
 * Each command should have a unique name and specify the class that handles it.
 */

return [
    'test:mail' => [
        'class' => 'IamLab\\Commands\\TestMailCommand',
        'description' => 'Send a test email to verify email functionality'
    ],

    'test:pusher' => [
        'class' => 'IamLab\\Commands\\TestPusherCommand',
        'description' => 'Test Pusher real-time functionality'
    ],

    'make:command' => [
        'class' => 'IamLab\\Commands\\MakeCommandCommand',
        'description' => 'Generate a new command class'
    ],

    'cache:clear' => [
        'class' => 'IamLab\\Commands\\CacheClearCommand',
        'description' => 'Clear application cache'
    ],

    'user:create' => [
        'class' => 'IamLab\\Commands\\UserCreateCommand',
        'description' => 'Create a new user account'
    ],

    'make:js' => [
        'class' => 'IamLab\\Commands\\MakeJsCommand',
        'description' => 'Generate Mithril.js views, services, and API controllers'
    ],

    'make:zephir' => [
        'class' => 'IamLab\\Commands\\MakeZephirCommand',
        'description' => 'Generate a new Zephir extension with proper structure'
    ],

    // Add more commands here as needed
];
