# Make JS Command Documentation

## Overview

The `make:js` command is a powerful code generator for creating Mithril.js views, services, and API controllers in the Phalcon Stub framework. This command follows the existing JavaScript patterns in the project and generates fully functional components that work together seamlessly.

## Command Signature

```bash
./phalcons command make:js <name> [--type=] [--api] [--controller] [-a|--all] [-v|--verbose]
```

## Arguments

- **name** (required): Name of the component/service to generate (e.g., User, Product, Order)

## Options

- `--type=TYPE`: Type of component to generate
  - `view`: Generate only the Mithril.js view component
  - `service`: Generate only the JavaScript service
  - `both`: Generate both view and service (default)
- `--api`: Generate API controller along with JS components
- `--controller`: Generate API controller only
- `-a, --all`: Generate all components (view, service, and API controller)
- `-v, --verbose`: Enable verbose output

## Usage Examples

### Basic Usage

```bash
# Generate both view and service for User
./phalcons command make:js User

# Generate only a view component
./phalcons command make:js Product --type=view

# Generate only a service
./phalcons command make:js Order --type=service
```

### With API Controller

```bash
# Generate view, service, and API controller
./phalcons command make:js Customer --api

# Generate only API controller
./phalcons command make:js Invoice --controller
```

### Verbose Output

```bash
# Enable verbose output to see detailed generation process
./phalcons command make:js Product --api -v
```

### Generate All Components

```bash
# Generate all components (view, service, and API controller) with the -a flag
./phalcons command make:js Item -a

# Same as above but with verbose output
./phalcons command make:js Category --all -v

# The -a flag is equivalent to --type=both --api
./phalcons command make:js Product -a
# is the same as:
./phalcons command make:js Product --type=both --api
```

## Generated Files

### 1. Mithril.js View Component

**Location**: `assets/js/components/{Name}.js`

The generated view component includes:
- Complete CRUD interface with Tailwind CSS and DaisyUI styling
- Loading states and error handling
- Data table with pagination-ready structure
- Create, edit, and delete functionality placeholders
- Integration with the corresponding service

**Features**:
- Responsive design using Tailwind CSS
- DaisyUI components for consistent styling
- Error display and loading states
- CRUD operations with confirmation dialogs
- Automatic service integration

### 2. JavaScript Service

**Location**: `assets/js/services/{Name}Service.js`

The generated service includes:
- RESTful API methods (GET, POST, PUT, DELETE)
- Search functionality
- Proper error handling
- Mithril.js request integration

**API Methods**:
- `getAll()`: Retrieve all items
- `getById(id)`: Get item by ID
- `create(data)`: Create new item
- `update(id, data)`: Update existing item
- `delete(id)`: Delete item
- `search(query)`: Search items

### 3. PHP API Controller

**Location**: `IamLab/Service/{Name}Api.php`

The generated controller includes:
- Full RESTful API implementation
- Proper error handling and validation
- Integration with Phalcon models
- Search functionality
- Consistent JSON response format

**API Endpoints**:
- `GET /api/{name}`: List all items
- `GET /api/{name}/{id}`: Get specific item
- `POST /api/{name}`: Create new item
- `PUT /api/{name}/{id}`: Update item
- `DELETE /api/{name}/{id}`: Delete item
- `GET /api/{name}/search`: Search items

## File Structure

After running `./phalcons command make:js Product --api`, the following files will be created:

```
assets/js/
├── components/
│   └── Product.js              # Mithril.js view component
└── services/
    └── ProductService.js       # JavaScript service

IamLab/Service/
└── ProductApi.php              # PHP API controller
```

## Integration Guide

### 1. Using the Generated View

```javascript
// In your main app routing
import {Product} from './components/Product.js';

// Add route
m.route(document.body, "/", {
    "/products": Product,
    // ... other routes
});
```

### 2. Using the Generated Service

```javascript
// Import the service
import {ProductService} from './services/ProductService.js';

// Use in your components
ProductService.getAll()
    .then(response => {
        console.log('Products:', response.data);
    })
    .catch(error => {
        console.error('Error:', error);
    });
```

### 3. API Controller Setup

The generated API controller extends `aAPI` and is ready to use. Make sure you have:

1. **Model Class**: Create a corresponding model in `IamLab/Model/{Name}.php`
2. **Database Table**: Ensure the database table exists
3. **Routing**: Configure API routes in your routing configuration

### Example Model

```php
<?php
namespace IamLab\Model;

use Phalcon\Mvc\Model;

class Product extends Model
{
    public $id;
    public $name;
    public $description;
    public $price;
    public $created_at;
    public $updated_at;

    public function initialize()
    {
        $this->setSource('products');
    }

    // Add getters and setters as needed
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
```

## Customization

### View Customization

The generated view components use:
- **Mithril.js**: For reactive UI components
- **Tailwind CSS**: For utility-first styling
- **DaisyUI**: For pre-built component styles

You can customize:
- Table columns and data display
- Form fields and validation
- Styling and layout
- CRUD operation behavior

### Service Customization

The generated services can be extended with:
- Custom API endpoints
- Request/response transformations
- Caching mechanisms
- Authentication headers

### Controller Customization

The generated controllers can be enhanced with:
- Custom validation rules
- Authentication and authorization
- Custom search logic
- Additional endpoints
- Response formatting

## Best Practices

### 1. Naming Conventions

- Use PascalCase for component names: `User`, `ProductCategory`, `OrderItem`
- The command automatically formats names correctly
- Generated files follow consistent naming patterns

### 2. Model Requirements

Ensure your Phalcon models have:
- Proper getters and setters
- Database table configuration
- Validation rules
- Relationships (if needed)

### 3. API Security

- Implement authentication in controllers
- Add input validation
- Use CSRF protection
- Implement rate limiting

### 4. Frontend Integration

- Import generated components in your main app
- Configure routing for new views
- Handle loading states appropriately
- Implement proper error handling

## Troubleshooting

### Common Issues

1. **File Already Exists**: The command will prompt before overwriting existing files
2. **Missing Model**: Ensure the corresponding Phalcon model exists
3. **Database Connection**: Verify database configuration for API controllers
4. **Import Errors**: Check file paths and export statements

### Debug Mode

Use the verbose flag to see detailed generation process:

```bash
./phalcons command make:js Product --api --verbose
```

## Advanced Usage

### Batch Generation

Generate multiple components efficiently:

```bash
# Generate multiple components
./phalcons command make:js User --api
./phalcons command make:js Product --api
./phalcons command make:js Order --api
```

### Custom Templates

The command uses built-in templates, but you can modify the `MakeJsCommand.php` file to customize:
- Template structure
- Default styling
- API patterns
- Generated methods

## Framework Integration

This command integrates seamlessly with:
- **Phalcon PHP Framework**: For backend API controllers
- **Mithril.js**: For frontend components
- **Tailwind CSS**: For styling
- **DaisyUI**: For UI components
- **Existing Command System**: Follows the same patterns as other commands

## Support and Contribution

The `make:js` command is part of the Phalcon Stub framework's code generation tools. It follows the established patterns and conventions of the framework while providing modern JavaScript development capabilities.

For issues or enhancements, refer to the project's main documentation and contribution guidelines.
