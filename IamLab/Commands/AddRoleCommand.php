<?php

namespace IamLab\Commands;

use Exception;
use IamLab\Core\Command\BaseCommand;
use IamLab\Model\Role;
use IamLab\Model\User;
use IamLab\Service\RolesService;
use function App\Core\Helpers\dd;

class AddRoleCommand extends BaseCommand
{
    /**
     * Get command signature/usage
     *
     * @return string
     */
    public function getSignature(): string
    {
        return 'user:add-role [email] [role] [-v|--verbose]';
    }

    /**
     * Get command help text
     *
     * @return string
     */
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

        $this->verbose("User email: {$email}");

        // Find the user
        $user = User::findFirstByEmail($email);
        if (!$user) {
            $this->error("User with email '{$email}' not found");
            return 1;
        }

        $this->verbose("User found: {$user->getName()} (ID: {$user->getId()})");

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

        $this->verbose("Role to assign: {$roleName}");

        // Validate role exists
        $role = Role::findFirstByName($roleName);
        if (!$role) {
            $this->error("Role '{$roleName}' does not exist");
            $this->info("Available roles:");
            $this->displayAvailableRoles();
            return 1;
        }

        try {
            // Check if user already has this role
            $rolesService = new RolesService();
            $userRoles= $rolesService->listRoles($user);

            foreach ($userRoles as $userRole) {
                $this->info("User has role $userRole");
            }

            if ($rolesService->hasRole($user, $roleName)) {
                $this->warn("User '{$email}' already has the role '{$roleName}'");
                return 0;
            }

            // Add the role
            $this->info("Adding role '{$roleName}' to user '{$email}'...");

            if ($rolesService->addRole($user, $roleName)) {
                $this->success("Successfully added role '{$roleName}' to user '{$email}'");

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
     * Display available roles
     *
     * @return void
     */
    private function displayAvailableRoles(): void
    {
        try {
            $roles = Role::find();
            if ($roles->count() > 0) {
                foreach ($roles as $role) {
                    $this->info("  - {$role->getName()}: {$role->getDescription()}");
                }
            } else {
                $this->warn("No roles found in the system");
            }
        } catch (Exception $e) {
            $this->error("Could not retrieve available roles: " . $e->getMessage());
        }
    }

    /**
     * Get command description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Add a role to a user account';
    }

    private function isVerbose()
    {
        return $this->option('verbose');
    }
}