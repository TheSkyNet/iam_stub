<?php

namespace IamLab\Commands;

use IamLab\Core\Command\BaseCommand;

class MakeJsCommand extends BaseCommand
{
    /**
     * Get command signature/usage
     *
     * @return string
     */
    public function getSignature(): string
    {
        return 'make:js <name> [--type=] [--api] [--controller] [-a|--all] [-v|--verbose]';
    }

    /**
     * Get command description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Generate Mithril.js views, services, and API controllers';
    }

    /**
     * Get command help text
     *
     * @return string
     */
    public function getHelp(): string
    {
        return <<<HELP
Generate Mithril.js views, services, and API controllers

Usage:
  make:js <name> [options]

Arguments:
  name                  Name of the component/service to generate (e.g., User, Product)

Options:
  --type=TYPE          Type of component to generate (view|service|both) [default: both]
  --api                Generate API controller along with JS components
  --controller         Generate API controller only
  -a, --all            Generate all components (view, service, and API controller)
  -v, --verbose        Enable verbose output

Examples:
  ./phalcons command make:js User                    # Generate User view and service
  ./phalcons command make:js Product --type=view     # Generate Product view only
  ./phalcons command make:js Order --api             # Generate Order view, service, and API controller
  ./phalcons command make:js Customer --controller   # Generate Customer API controller only
  ./phalcons command make:js Item -a                 # Generate all components (view, service, and API controller)
HELP;
    }

    /**
     * Handle the command execution
     *
     * @return int Exit code
     */
    protected function handle(): int
    {
        $name = $this->argument(0);
        if (!$name) {
            $this->error("Component name is required");
            return 1;
        }

        // Validate and format name
        $name = $this->formatName($name);
        $this->verbose("Formatted name: {$name}");

        $type = $this->option('type', 'both');
        $generateApi = $this->hasOption('api');
        $controllerOnly = $this->hasOption('controller');
        $generateAll = $this->hasOption('all') || $this->hasOption('a');

        // If --all flag is used, generate everything
        if ($generateAll) {
            $type = 'both';
            $generateApi = true;
        }

        $this->info("Generating components for: {$name}");

        try {
            if ($controllerOnly) {
                $this->generateController($name);
            } else {
                if ($type === 'view' || $type === 'both') {
                    $this->generateView($name);
                }

                if ($type === 'service' || $type === 'both') {
                    $this->generateService($name);
                }

                if ($generateApi) {
                    $this->generateController($name);
                }
            }

            $this->success("Successfully generated components for {$name}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error generating components: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Format component name
     */
    protected function formatName(string $name): string
    {
        return ucfirst(strtolower($name));
    }

    /**
     * Generate Mithril.js view component
     */
    private function generateView(string $name): void
    {
        $this->info("Generating view component for {$name}...");

        $viewContent = $this->getViewTemplate($name);
        $viewPath = "assets/js/components/{$name}.js";

        $this->createFile($viewPath, $viewContent);
        $this->verbose("Created view: {$viewPath}");
    }

    /**
     * Generate JavaScript service
     */
    private function generateService(string $name): void
    {
        $this->info("Generating service for {$name}...");

        $serviceContent = $this->getServiceTemplate($name);
        $servicePath = "assets/js/services/{$name}Service.js";

        // Create services directory if it doesn't exist
        $servicesDir = "assets/js/services";
        if (!is_dir($servicesDir)) {
            mkdir($servicesDir, 0755, true);
            $this->verbose("Created directory: {$servicesDir}");
        }

        $this->createFile($servicePath, $serviceContent);
        $this->verbose("Created service: {$servicePath}");
    }

    /**
     * Generate API controller
     */
    private function generateController(string $name): void
    {
        $this->info("Generating API controller for {$name}...");

        $controllerContent = $this->getControllerTemplate($name);
        $controllerPath = "IamLab/Service/{$name}Api.php";

        $this->createFile($controllerPath, $controllerContent);
        $this->verbose("Created controller: {$controllerPath}");
    }

    /**
     * Create file with content
     */
    private function createFile(string $path, string $content): void
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($path)) {
            if (!$this->confirm("File {$path} already exists. Overwrite?", false)) {
                $this->warn("Skipped: {$path}");
                return;
            }
        }

        file_put_contents($path, $content);
    }

    /**
     * Get view template content
     */
    protected function getViewTemplate(string $name): string
    {
        $lowerName = strtolower($name);

        return "const {$name} = {
    data: {
        items: [],
        loading: false,
        error: null
    },

    oninit: function(vnode) {
        this.load{$name}s();
    },

    load{$name}s: function() {
        this.data.loading = true;
        this.data.error = null;

        {$name}Service.getAll()
            .then(response => {
                this.data.items = response.data || [];
                this.data.loading = false;
                m.redraw();
            })
            .catch(error => {
                this.data.error = error.message || 'Failed to load {$lowerName}s';
                this.data.loading = false;
                m.redraw();
            });
    },

    view: function(vnode) {
        return m(\".container.mx-auto.p-6\", [
            m(\"h1.text-3xl.font-bold.text-base-content.mb-6\", \"{$name} Management\"),

            // Error display
            this.data.error ? m(\".alert.alert-error.mb-4\", [
                m(\"span\", this.data.error)
            ]) : null,

            // Loading state
            this.data.loading ? m(\".flex.justify-center.items-center.py-8\", [
                m(\".loading.loading-spinner.loading-lg\")
            ]) : null,

            // Content
            !this.data.loading ? m(\".card.bg-base-100.shadow-xl\", [
                m(\".card-body\", [
                    m(\".flex.justify-between.items-center.mb-4\", [
                        m(\"h2.card-title\", \"{$name}s\"),
                        m(\"button.btn.btn-primary\", {
                            onclick: () => this.create{$name}()
                        }, \"Add {$name}\")
                    ]),

                    // Items list
                    this.data.items.length > 0 ? 
                        m(\".overflow-x-auto\", [
                            m(\"table.table.table-zebra.w-full\", [
                                m(\"thead\", [
                                    m(\"tr\", [
                                        m(\"th\", \"ID\"),
                                        m(\"th\", \"Name\"),
                                        m(\"th\", \"Created\"),
                                        m(\"th\", \"Actions\")
                                    ])
                                ]),
                                m(\"tbody\", 
                                    this.data.items.map(item => 
                                        m(\"tr\", [
                                            m(\"td\", item.id),
                                            m(\"td\", item.name || 'N/A'),
                                            m(\"td\", item.created_at ? new Date(item.created_at).toLocaleDateString() : 'N/A'),
                                            m(\"td\", [
                                                m(\"button.btn.btn-sm.btn-outline.mr-2\", {
                                                    onclick: () => this.edit{$name}(item)
                                                }, \"Edit\"),
                                                m(\"button.btn.btn-sm.btn-error\", {
                                                    onclick: () => this.delete{$name}(item)
                                                }, \"Delete\")
                                            ])
                                        ])
                                    )
                                )
                            ])
                        ]) :
                        m(\".text-center.py-8\", [
                            m(\"p.text-base-content.opacity-70\", \"No {$lowerName}s found\"),
                            m(\"button.btn.btn-primary.mt-4\", {
                                onclick: () => this.create{$name}()
                            }, \"Create First {$name}\")
                        ])
                ])
            ]) : null
        ]);
    },

    create{$name}: function() {
        // TODO: Implement create functionality
        console.log('Create {$name}');
    },

    edit{$name}: function(item) {
        // TODO: Implement edit functionality
        console.log('Edit {$name}', item);
    },

    delete{$name}: function(item) {
        if (confirm(`Are you sure you want to delete this {$lowerName}?`)) {
            {$name}Service.delete(item.id)
                .then(() => {
                    this.load{$name}s();
                })
                .catch(error => {
                    this.data.error = error.message || 'Failed to delete {$lowerName}';
                    m.redraw();
                });
        }
    }
};

export {{$name}};";
    }

    /**
     * Get service template content
     */
    protected function getServiceTemplate(string $name): string
    {
        $lowerName = strtolower($name);

        return "const {$name}Service = {
    baseUrl: '/api/{$lowerName}',

    /**
     * Get all {$lowerName}s
     */
    getAll: function() {
        return m.request({
            method: 'GET',
            url: this.baseUrl
        });
    },

    /**
     * Get {$lowerName} by ID
     */
    getById: function(id) {
        return m.request({
            method: 'GET',
            url: `\${this.baseUrl}/\${id}`
        });
    },

    /**
     * Create new {$lowerName}
     */
    create: function(data) {
        return m.request({
            method: 'POST',
            url: this.baseUrl,
            body: data
        });
    },

    /**
     * Update {$lowerName}
     */
    update: function(id, data) {
        return m.request({
            method: 'PUT',
            url: `\${this.baseUrl}/\${id}`,
            body: data
        });
    },

    /**
     * Delete {$lowerName}
     */
    delete: function(id) {
        return m.request({
            method: 'DELETE',
            url: `\${this.baseUrl}/\${id}`
        });
    },

    /**
     * Search {$lowerName}s
     */
    search: function(query) {
        return m.request({
            method: 'GET',
            url: `\${this.baseUrl}/search`,
            params: { q: query }
        });
    }
};

export {{$name}Service};";
    }

    /**
     * Get controller template content
     */
    protected function getControllerTemplate(string $name): string
    {
        $lowerName = strtolower($name);

        return <<<PHP
<?php

namespace IamLab\Service;

use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Model\\{$name};

class {$name}Api extends aAPI
{
    /**
     * Get all {$lowerName}s
     * GET /api/{$lowerName}
     */
    public function indexAction(): void
    {
        try {
            \${$lowerName}s = {$name}::find();

            \$this->dispatch([
                'success' => true,
                'data' => \${$lowerName}s->toArray()
            ]);
        } catch (Exception \$e) {
            \$this->dispatchError([
                'success' => false,
                'message' => 'Failed to retrieve {$lowerName}s',
                'error' => \$e->getMessage()
            ]);
        }
    }

    /**
     * Get {$lowerName} by ID
     * GET /api/{$lowerName}/:id
     */
    public function showAction(): void
    {
        try {
            \$id = \$this->getParam('id');
            if (!\$id) {
                \$this->dispatchError([
                    'success' => false,
                    'message' => 'ID parameter is required'
                ]);
                return;
            }

            \${$lowerName} = {$name}::findFirst(\$id);
            if (!\${$lowerName}) {
                \$this->dispatchError([
                    'success' => false,
                    'message' => '{$name} not found'
                ]);
                return;
            }

            \$this->dispatch([
                'success' => true,
                'data' => \${$lowerName}->toArray()
            ]);
        } catch (Exception \$e) {
            \$this->dispatchError([
                'success' => false,
                'message' => 'Failed to retrieve {$lowerName}',
                'error' => \$e->getMessage()
            ]);
        }
    }

    /**
     * Create new {$lowerName}
     * POST /api/{$lowerName}
     */
    public function createAction(): void
    {
        try {
            \${$lowerName} = new {$name}();

            // Set properties from request data
            \$name = \$this->getParam('name');
            if (\$name) {
                \${$lowerName}->setName(\$name);
            }

            // Add more property assignments as needed
            // \${$lowerName}->setDescription(\$this->getParam('description'));
            // \${$lowerName}->setStatus(\$this->getParam('status', 'active'));

            \$this->save(\${$lowerName});
        } catch (Exception \$e) {
            \$this->dispatchError([
                'success' => false,
                'message' => 'Failed to create {$lowerName}',
                'error' => \$e->getMessage()
            ]);
        }
    }

    /**
     * Update {$lowerName}
     * PUT /api/{$lowerName}/:id
     */
    public function updateAction(): void
    {
        try {
            \$id = \$this->getParam('id');
            if (!\$id) {
                \$this->dispatchError([
                    'success' => false,
                    'message' => 'ID parameter is required'
                ]);
                return;
            }

            \${$lowerName} = {$name}::findFirst(\$id);
            if (!\${$lowerName}) {
                \$this->dispatchError([
                    'success' => false,
                    'message' => '{$name} not found'
                ]);
                return;
            }

            // Update properties from request data
            \$name = \$this->getParam('name');
            if (\$name !== null) {
                \${$lowerName}->setName(\$name);
            }

            // Add more property updates as needed
            // if (\$this->hasParam('description')) {
            //     \${$lowerName}->setDescription(\$this->getParam('description'));
            // }

            \$this->save(\${$lowerName});
        } catch (Exception \$e) {
            \$this->dispatchError([
                'success' => false,
                'message' => 'Failed to update {$lowerName}',
                'error' => \$e->getMessage()
            ]);
        }
    }

    /**
     * Delete {$lowerName}
     * DELETE /api/{$lowerName}/:id
     */
    public function deleteAction(): void
    {
        try {
            \$id = \$this->getParam('id');
            if (!\$id) {
                \$this->dispatchError([
                    'success' => false,
                    'message' => 'ID parameter is required'
                ]);
                return;
            }

            \${$lowerName} = {$name}::findFirst(\$id);
            if (!\${$lowerName}) {
                \$this->dispatchError([
                    'success' => false,
                    'message' => '{$name} not found'
                ]);
                return;
            }

            \$this->delete(\${$lowerName});
        } catch (Exception \$e) {
            \$this->dispatchError([
                'success' => false,
                'message' => 'Failed to delete {$lowerName}',
                'error' => \$e->getMessage()
            ]);
        }
    }

    /**
     * Search {$lowerName}s
     * GET /api/{$lowerName}/search
     */
    public function searchAction(): void
    {
        try {
            \$query = \$this->getParam('q', '');
            if (empty(\$query)) {
                \$this->dispatchError([
                    'success' => false,
                    'message' => 'Search query is required'
                ]);
                return;
            }

            // Implement search logic based on your model structure
            \${$lowerName}s = {$name}::find([
                "name LIKE :query:",
                'bind' => ['query' => "%\$query%"]
            ]);

            \$this->dispatch([
                'success' => true,
                'data' => \${$lowerName}s->toArray(),
                'query' => \$query
            ]);
        } catch (Exception \$e) {
            \$this->dispatchError([
                'success' => false,
                'message' => 'Search failed',
                'error' => \$e->getMessage()
            ]);
        }
    }
}
PHP;
    }
}
