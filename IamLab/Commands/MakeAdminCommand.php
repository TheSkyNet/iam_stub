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
     */
    #[\Override]
    public function getSignature(): string
    {
        return 'user:make-admin [email] [-v|--verbose]';
    }

    /**
     * Get command help text
     */
    #[\Override]
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
    #[\Override]
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

        $this->verbose('User email: ' . $email);

        // Find the user
        $user = User::findFirstByEmail($email);
        if (!$user) {
            $this->error(sprintf("User with email '%s' not found", $email));
            return 1;
        }

        $this->verbose(sprintf('User found: %s (ID: %d)', $user->getName(), $user->getId()));

        $roleName = 'admin';

        try {
            $rolesService = new RolesService();

            if ($rolesService->hasRole($user, $roleName)) {
                $this->warn(sprintf("User '%s' already has the role '%s'", $email, $roleName));
                return 0;
            }

            // Add the role
            $this->info(sprintf("Adding role '%s' to user '%s'...", $roleName, $email));

            if ($rolesService->addRole($user, $roleName)) {
                $this->success(sprintf("Successfully made user '%s' an admin", $email));

                // Display current user roles
                if ($this->isVerbose()) {
                    $currentRoles = $rolesService->getUserRoles($user);
                    $this->info("Current user roles: " . implode(', ', $currentRoles));
                }

                return 0;
            }

            $this->error(sprintf("Failed to add role '%s' to user '%s'", $roleName, $email));
            return 1;
        } catch (Exception $exception) {
            $this->error("Exception occurred while adding role: " . $exception->getMessage());
            $this->verbose("Stack trace: " . $exception->getTraceAsString());
            return 1;
        }
    }

    /**
     * Get command description
     */
    #[\Override]
    public function getDescription(): string
    {
        return 'Make a user an administrator';
    }

    private function isVerbose()
    {
        return $this->option('verbose');
    }
}
