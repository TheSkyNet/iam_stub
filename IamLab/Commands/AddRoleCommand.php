<?php

namespace IamLab\Commands;

use Exception;
use IamLab\Core\Command\BaseCommand;
use IamLab\Model\Role;
use IamLab\Model\User;
use IamLab\Service\RolesService;

use function IamLab\Core\Helpers\dd;

class AddRoleCommand extends BaseCommand
{
    /**
     * Get command signature/usage
     */
    #[\Override]
    public function getSignature(): string
    {
        return 'user:add-role [email] [role] [-v|--verbose]';
    }

    /**
     * Get command help text
     */
    #[\Override]
    public function getHelp(): string
    {
        return <<<HELP
Add a role to a user account

Usage:
  user:add-role [email] [role] [options]

Arguments:
  email                 Email address of the user (optional, will prompt if not provided)
  role                  Role name to assign (optional, will prompt if not provided)

Options:
  -v, --verbose        Enable verbose output

Available Roles:
  admin                Administrator with full system access
  editor               Editor with content management permissions
  member               Regular member with basic access
  guest                Guest user with limited access

Examples:
  ./phalcons command user:add-role user@example.com admin
  ./phalcons command user:add-role user@example.com editor -v
  ./phalcons command user:add-role
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
        $this->info("Starting role assignment...");

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

        // Get role name
        $roleName = $this->argument(1);
        if (!$roleName) {
            $this->displayAvailableRoles();
            $roleName = $this->ask("Enter role name to assign");
        }

        if (!$roleName) {
            $this->error("Role name is required");
            return 1;
        }

        $this->verbose('Role to assign: ' . $roleName);

        // Validate role exists
        $role = Role::findFirstByName($roleName);
        if (!$role) {
            $this->error(sprintf("Role '%s' does not exist", $roleName));
            $this->info("Available roles:");
            $this->displayAvailableRoles();
            return 1;
        }

        try {
            // Check if user already has this role
            $rolesService = new RolesService();
            $userRoles = $rolesService->listRoles($user);

            foreach ($userRoles as $userRole) {
                $this->info('User has role ' . $userRole);
            }

            if ($rolesService->hasRole($user, $roleName)) {
                $this->warn(sprintf("User '%s' already has the role '%s'", $email, $roleName));
                return 0;
            }

            // Add the role
            $this->info(sprintf("Adding role '%s' to user '%s'...", $roleName, $email));

            if ($rolesService->addRole($user, $roleName)) {
                $this->success(sprintf("Successfully added role '%s' to user '%s'", $roleName, $email));

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
     * Display available roles
     */
    private function displayAvailableRoles(): void
    {
        try {
            $roles = Role::find();
            if ($roles->count() > 0) {
                foreach ($roles as $role) {
                    $this->info(sprintf('  - %s: %s', $role->getName(), $role->getDescription()));
                }
            } else {
                $this->warn("No roles found in the system");
            }
        } catch (Exception $exception) {
            $this->error("Could not retrieve available roles: " . $exception->getMessage());
        }
    }

    /**
     * Get command description
     */
    #[\Override]
    public function getDescription(): string
    {
        return 'Add a role to a user account';
    }

    private function isVerbose()
    {
        return $this->option('verbose');
    }
}
