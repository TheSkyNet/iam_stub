<?php

namespace IamLab\Commands;

use Exception;
use IamLab\Core\Command\BaseCommand;
use IamLab\Model\User;
use IamLab\Service\RolesService;

class MakeAdminCommand extends BaseCommand
{
    /**
     * Get command signature/usage
     *
     * @return string
     */
    public function getSignature(): string
    {
        return 'user:make-admin [email] [-v|--verbose]';
    }

    /**
     * Get command help text
     *
     * @return string
     */
    public function getHelp(): string
    {
        return <<<HELP
Make a user an administrator

Usage:
  user:make-admin [email] [options]

Arguments:
  email                 Email address of the user (optional, will prompt if not provided)

Options:
  -v, --verbose        Enable verbose output

Examples:
  ./phalcons command user:make-admin user@example.com
  ./phalcons command user:make-admin
HELP;
    }

    /**
     * Handle the command execution
     *
     * @return int Exit code
     */
    protected function handle(): int
    {
        $this->info("Starting admin assignment...");

        // Get user email
        $email = $this->argument(0);
        if (!$email) {
            $email = $this->ask("Enter user email address");
        }

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error("Invalid email address provided");
            return 1;
        }

        $this->verbose("User email: {$email}");

        // Find the user
        $user = User::findFirstByEmail($email);
        if (!$user) {
            $this->error("User with email '{$email}' not found");
            return 1;
        }

        $this->verbose("User found: {$user->getName()} (ID: {$user->getId()})");

        $roleName = 'admin';

        try {
            $rolesService = new RolesService();

            if ($rolesService->hasRole($user, $roleName)) {
                $this->warn("User '{$email}' already has the role '{$roleName}'");
                return 0;
            }

            // Add the role
            $this->info("Adding role '{$roleName}' to user '{$email}'...");

            if ($rolesService->addRole($user, $roleName)) {
                $this->success("Successfully made user '{$email}' an admin");

                // Display current user roles
                if ($this->isVerbose()) {
                    $currentRoles = $rolesService->getUserRoles($user);
                    $this->info("Current user roles: " . implode(', ', $currentRoles));
                }

                return 0;
            } else {
                $this->error("Failed to add role '{$roleName}' to user '{$email}'");
                return 1;
            }
        } catch (Exception $e) {
            $this->error("Exception occurred while adding role: " . $e->getMessage());
            $this->verbose("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Get command description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Make a user an administrator';
    }

    private function isVerbose()
    {
        return $this->option('verbose');
    }
}
