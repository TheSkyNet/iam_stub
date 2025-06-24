# API Framework

The API framework provides a foundation for building RESTful APIs in the Phalcon stub project. It includes base classes and utilities for handling JSON responses, data serialization, and API controller functionality.

## Overview

This folder contains the core components for API development:

- **aAPI.php** - Abstract base class for API controllers
- **Entity.php** - Abstract model class with JSON serialization
- **iAPI.php** - API interface (placeholder for future extensions)
- **Rest.php** - REST utilities class (work in progress)

## Components

### aAPI (Abstract API Controller)

**File:** `aAPI.php`  
**Namespace:** `IamLab\Core\API`

The `aAPI` class is an abstract base class that extends Phalcon's `Injectable` and provides common functionality for API controllers.

#### Key Features

- JSON response handling
- Data retrieval from request body
- Parameter extraction with type casting
- Error response management
- CRUD operation helpers

#### Methods

##### `dispatch($data): void`
Sends a JSON response and terminates execution.

```php
// Example usage in a controller
class UserApi extends aAPI 
{
    public function getUserAction()
    {
        $user = User::findFirst();
        $this->dispatch([
            'success' => true,
            'data' => $user
        ]);
    }
}
```

##### `dispatchError($data): void`
Sends an error response in JSON format.

```php
// Example error handling
if (!$user) {
    $this->dispatchError([
        'success' => false,
        'message' => 'User not found'
    ]);
}
```

##### `save($data): void`
Saves a model and handles validation errors automatically.

```php
// Example saving with automatic error handling
public function createUserAction()
{
    $user = new User();
    $user->setName($this->getParam('name'));
    $user->setEmail($this->getParam('email'));
    
    $this->save($user); // Automatically handles validation errors
}
```

##### `delete($data): void`
Deletes a model and handles errors automatically.

```php
// Example deletion
public function deleteUserAction()
{
    $user = User::findFirst($this->getParam('id'));
    $this->delete($user); // Automatically handles deletion errors
}
```

##### `getData()`
Retrieves and decodes JSON data from the request body.

```php
// Get all request data
$requestData = $this->getData();
```

##### `getParam($name, $default = null, $cast = null): mixed`
Extracts a specific parameter from the request data with optional type casting.

```php
// Get parameters with defaults and casting
$userId = $this->getParam('user_id', 0, 'int');
$email = $this->getParam('email', '');
$isActive = $this->getParam('active', false, 'bool');
```

##### `hasParam(string $name): bool`
Checks if a parameter exists in the request data.

```php
// Check if parameter exists
if ($this->hasParam('optional_field')) {
    // Handle optional field
}
```

### Entity (Abstract Model)

**File:** `Entity.php`  
**Namespace:** `IamLab\Core\API`

The `Entity` class extends Phalcon's `Model` and implements `JsonSerializable` to provide enhanced JSON serialization capabilities.

#### Key Features

- Automatic JSON serialization
- Data type casting
- Field amendments (computed properties)
- Flexible data transformation

#### Properties

##### `$casts`
Array defining how fields should be cast during serialization.

```php
class User extends Entity
{
    protected $casts = [
        'created_at' => 'datetime',
        'is_active' => 'boolean',
        'metadata' => 'json'
    ];
}
```

##### `$amends`
Array defining additional computed fields to include in JSON output.

```php
class User extends Entity
{
    protected $amends = [
        'full_name',
        'avatar_url'
    ];
    
    protected function amendFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    
    protected function amendAvatarUrl()
    {
        return '/avatars/' . $this->id . '.jpg';
    }
}
```

#### Methods

##### `cast()`
Applies type casting to specified fields.

##### `amend()`
Adds computed properties to the model output.

##### `jsonSerialize(): array`
Implements JsonSerializable interface for automatic JSON conversion.

```php
// Usage example
$user = User::findFirst();
echo json_encode($user); // Automatically includes casts and amendments
```

## Usage Examples

### Creating an API Controller

```php
<?php

namespace IamLab\Service;

use IamLab\Core\API\aAPI;
use IamLab\Model\User;

class UserApi extends aAPI
{
    public function indexAction()
    {
        $users = User::find();
        $this->dispatch([
            'success' => true,
            'data' => $users,
            'count' => count($users)
        ]);
    }
    
    public function createAction()
    {
        $user = new User();
        $user->setName($this->getParam('name'));
        $user->setEmail($this->getParam('email'));
        $user->setPassword($this->getParam('password'));
        
        $this->save($user);
    }
    
    public function updateAction()
    {
        $id = $this->getParam('id', 0, 'int');
        $user = User::findFirst($id);
        
        if (!$user) {
            $this->dispatchError([
                'success' => false,
                'message' => 'User not found'
            ]);
            return;
        }
        
        if ($this->hasParam('name')) {
            $user->setName($this->getParam('name'));
        }
        
        if ($this->hasParam('email')) {
            $user->setEmail($this->getParam('email'));
        }
        
        $this->save($user);
    }
    
    public function deleteAction()
    {
        $id = $this->getParam('id', 0, 'int');
        $user = User::findFirst($id);
        
        if (!$user) {
            $this->dispatchError([
                'success' => false,
                'message' => 'User not found'
            ]);
            return;
        }
        
        $this->delete($user);
    }
}
```

### Creating an Enhanced Model

```php
<?php

namespace IamLab\Model;

use IamLab\Core\API\Entity;

class User extends Entity
{
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
        'settings' => 'json'
    ];
    
    protected $amends = [
        'full_name',
        'avatar_url',
        'posts_count'
    ];
    
    // Cast methods
    protected function castDatetime($value)
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }
    
    // Amendment methods
    protected function amendFullName()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
    
    protected function amendAvatarUrl()
    {
        return $this->avatar ? '/uploads/avatars/' . $this->avatar : '/images/default-avatar.png';
    }
    
    protected function amendPostsCount()
    {
        return $this->getRelated('posts')->count();
    }
}
```

### Registering API Routes

```php
// In app.php or routes file
$app->get('/api/users', [new UserApi(), 'indexAction']);
$app->post('/api/users', [new UserApi(), 'createAction']);
$app->put('/api/users', [new UserApi(), 'updateAction']);
$app->delete('/api/users', [new UserApi(), 'deleteAction']);
```

## Best Practices

### 1. Error Handling
Always use `dispatchError()` for consistent error responses:

```php
if (!$this->hasParam('required_field')) {
    $this->dispatchError([
        'success' => false,
        'message' => 'Required field missing',
        'field' => 'required_field'
    ]);
    return;
}
```

### 2. Parameter Validation
Use type casting and defaults for robust parameter handling:

```php
$page = $this->getParam('page', 1, 'int');
$limit = $this->getParam('limit', 10, 'int');
$search = $this->getParam('search', '');
```

### 3. Response Structure
Maintain consistent response structure:

```php
// Success response
$this->dispatch([
    'success' => true,
    'data' => $result,
    'message' => 'Operation completed successfully'
]);

// Error response
$this->dispatchError([
    'success' => false,
    'message' => 'Error description',
    'errors' => $validationErrors
]);
```

### 4. Model Enhancements
Use casts and amendments to create rich API responses:

```php
protected $casts = [
    'price' => 'decimal',
    'tags' => 'json',
    'published_at' => 'datetime'
];

protected $amends = [
    'formatted_price',
    'is_published',
    'reading_time'
];
```

## Security Considerations

1. **Input Validation**: Always validate and sanitize input parameters
2. **Authentication**: Implement proper authentication checks in controllers
3. **Authorization**: Verify user permissions before operations
4. **Rate Limiting**: Consider implementing rate limiting for API endpoints
5. **CORS**: Configure CORS headers appropriately for cross-origin requests

## Future Enhancements

The API framework can be extended with:

- Authentication middleware
- Rate limiting
- API versioning
- Request/response logging
- Swagger/OpenAPI documentation generation
- Caching mechanisms
- Pagination helpers
- Filtering and sorting utilities

## Dependencies

- Phalcon Framework
- Helper functions from `App\Core\Helpers`
- JsonSerializable interface (PHP built-in)

## Related Documentation

- [Helpers Documentation](../Helpers/README.md)
- [Main Project README](../../../README.md)
- [Phalcon Documentation](https://docs.phalcon.io/)