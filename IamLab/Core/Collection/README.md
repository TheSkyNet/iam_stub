# Collection Utilities

The Collection utilities provide enhanced functionality for working with data collections in the Phalcon stub project. It extends Phalcon's built-in Collection class with additional methods for data manipulation and processing.

## Overview

This folder contains:

- **Collection.php** - Enhanced collection class with additional utility methods

## Components

### Collection Class

**File:** `Collection.php`  
**Namespace:** `IamLab\Core\Collection`

The `Collection` class extends Phalcon's `Support\Collection` and adds useful methods for data manipulation, iteration, and transformation.

#### Key Features

- Enhanced iteration with callbacks
- Data transformation methods
- Filtering capabilities
- First element access
- Chainable method calls

#### Methods

##### `each($callback)`
Iterates over each item in the collection and executes a callback function.

**Parameters:**
- `$callback` - Function to execute for each item

**Returns:** `$this` (for method chaining)

```php
use IamLab\Core\Collection\Collection;

$collection = new Collection(['apple', 'banana', 'cherry']);

$collection->each(function($item) {
    echo "Fruit: " . $item . "\n";
});
// Output:
// Fruit: apple
// Fruit: banana
// Fruit: cherry
```

##### `map($callback)`
Transforms each item in the collection using a callback function.

**Parameters:**
- `$callback` - Function to transform each item

**Returns:** `$this` (for method chaining)

```php
$collection = new Collection([1, 2, 3, 4, 5]);

$collection->map(function($item) {
    return $item * 2;
});

// Collection now contains: [2, 4, 6, 8, 10]
```

##### `filter($callback)`
Filters the collection based on a callback function.

**Parameters:**
- `$callback` - Function that returns true for items to keep

**Returns:** `$this` (for method chaining)

**Note:** There appears to be a bug in the current implementation. The filter method should only keep items where the callback returns true, but currently it transforms them.

```php
$collection = new Collection([1, 2, 3, 4, 5]);

$collection->filter(function($item) {
    return $item > 3; // Should keep only items greater than 3
});

// Expected result: [4, 5]
```

##### `first()`
Returns the first element in the collection.

**Returns:** Mixed - The first element

```php
$collection = new Collection(['apple', 'banana', 'cherry']);
$firstFruit = $collection->first(); // Returns 'apple'
```

## Usage Examples

### Basic Collection Operations

```php
<?php

use IamLab\Core\Collection\Collection;

// Create a collection
$numbers = new Collection([1, 2, 3, 4, 5]);

// Chain operations
$result = $numbers
    ->map(function($n) { return $n * 2; })    // Double each number
    ->filter(function($n) { return $n > 5; }) // Keep numbers > 5
    ->each(function($n) { echo $n . " "; });  // Print each number

// Working with objects
$users = new Collection([
    ['name' => 'John', 'age' => 25],
    ['name' => 'Jane', 'age' => 30],
    ['name' => 'Bob', 'age' => 35]
]);

// Transform user data
$users->map(function($user) {
    $user['display_name'] = strtoupper($user['name']);
    return $user;
});

// Get first user
$firstUser = $users->first();
```

### Working with Model Collections

```php
// Convert Phalcon model resultset to enhanced collection
$userModels = User::find();
$userCollection = new Collection($userModels->toArray());

// Transform model data
$userCollection
    ->map(function($user) {
        return [
            'id' => $user['id'],
            'full_name' => $user['first_name'] . ' ' . $user['last_name'],
            'email' => $user['email'],
            'created' => date('Y-m-d', strtotime($user['created_at']))
        ];
    })
    ->each(function($user) {
        // Process each transformed user
        echo "User: " . $user['full_name'] . "\n";
    });
```

### Data Processing Pipeline

```php
// Process API response data
$apiData = new Collection($responseData);

$processedData = $apiData
    ->filter(function($item) {
        return isset($item['status']) && $item['status'] === 'active';
    })
    ->map(function($item) {
        return [
            'id' => $item['id'],
            'title' => ucwords($item['title']),
            'description' => substr($item['description'], 0, 100) . '...',
            'processed_at' => date('Y-m-d H:i:s')
        ];
    });

// Get the first processed item
$firstItem = $processedData->first();
```

### Utility Functions

```php
// Count items after filtering
function countActiveUsers($users) {
    $collection = new Collection($users);
    $activeCount = 0;
    
    $collection
        ->filter(function($user) { return $user['is_active']; })
        ->each(function($user) use (&$activeCount) {
            $activeCount++;
        });
    
    return $activeCount;
}

// Transform and collect results
function getUserEmails($users) {
    $collection = new Collection($users);
    $emails = [];
    
    $collection
        ->filter(function($user) { return !empty($user['email']); })
        ->each(function($user) use (&$emails) {
            $emails[] = $user['email'];
        });
    
    return $emails;
}
```

## Best Practices

### 1. Method Chaining
Take advantage of the fluent interface for readable code:

```php
$result = $collection
    ->filter($filterCallback)
    ->map($transformCallback)
    ->each($processCallback);
```

### 2. Callback Functions
Use descriptive callback functions for better code readability:

```php
// Good: Descriptive callback
$collection->filter(function($user) {
    return $user['is_active'] && $user['email_verified'];
});

// Better: Named function
function isActiveVerifiedUser($user) {
    return $user['is_active'] && $user['email_verified'];
}

$collection->filter('isActiveVerifiedUser');
```

### 3. Error Handling
Always check for empty collections when using `first()`:

```php
$collection = new Collection($data);

if ($collection->count() > 0) {
    $firstItem = $collection->first();
    // Process first item
} else {
    // Handle empty collection
}
```

### 4. Memory Efficiency
For large datasets, consider processing in chunks:

```php
// For very large collections, process in batches
$largeCollection = new Collection($largeDataset);
$batchSize = 100;
$processed = 0;

$largeCollection->each(function($item) use (&$processed, $batchSize) {
    // Process item
    $processed++;
    
    if ($processed % $batchSize === 0) {
        // Perform batch operations (e.g., database saves)
        echo "Processed $processed items\n";
    }
});
```

## Known Issues

### Filter Method Bug
The current `filter()` method implementation has a bug where it transforms items instead of filtering them. The correct implementation should be:

```php
public function filter($callback)
{
    $filtered = [];
    foreach ($this->data as $key => $item) {
        if ($callback($item)) {
            $filtered[$key] = $item;
        }
    }
    $this->data = $filtered;
    return $this;
}
```

## Integration with Phalcon

### Converting Phalcon Collections

```php
// Convert Phalcon\Support\Collection to enhanced Collection
$phalconCollection = new \Phalcon\Support\Collection($data);
$enhancedCollection = new Collection($phalconCollection->toArray());

// Convert model resultsets
$models = User::find();
$collection = new Collection($models->toArray());
```

### Using in Controllers

```php
class UserController extends \Phalcon\Mvc\Controller
{
    public function processUsersAction()
    {
        $users = User::find();
        $userCollection = new Collection($users->toArray());
        
        $processedUsers = $userCollection
            ->filter(function($user) { return $user['is_active']; })
            ->map(function($user) {
                return [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ];
            });
        
        $this->view->users = $processedUsers->toArray();
    }
}
```

## Performance Considerations

1. **Large Datasets**: For very large collections, consider using generators or processing in chunks
2. **Memory Usage**: Each method creates a new iteration, so be mindful of memory usage with large datasets
3. **Callback Complexity**: Keep callback functions simple to maintain performance
4. **Early Returns**: Use early returns in filter callbacks to improve performance

## Future Enhancements

The Collection class could be enhanced with:

- `reduce()` method for aggregation operations
- `sort()` and `sortBy()` methods for ordering
- `groupBy()` method for grouping items
- `unique()` method for removing duplicates
- `chunk()` method for processing in batches
- `pluck()` method for extracting specific fields
- `where()` method for simple filtering
- `isEmpty()` and `isNotEmpty()` helper methods

## Dependencies

- Phalcon Framework (`\Phalcon\Support\Collection`)

## Related Documentation

- [API Documentation](../API/README.md)
- [Helpers Documentation](../Helpers/README.md)
- [Main Project README](../../../README.md)
- [Phalcon Collection Documentation](https://docs.phalcon.io/5.0/en/support-collection)