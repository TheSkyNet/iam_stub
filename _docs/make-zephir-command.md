# Make Zephir Command Documentation

## Overview

The `make:zephir` command is a powerful code generator for creating Zephir extensions in the Phalcon Stub framework. This command automatically generates the proper directory structure, configuration files, and basic class templates needed to create high-performance Zephir extensions.

## Command Signature

```bash
./phalcons command make:zephir <name> [--class=] [-v|--verbose]
```

## Arguments

- **name** (required): Name of the Zephir extension (e.g., MyExtension, Calculator, Helper)

## Options

- `--class=CLASS`: Name of the initial class to create (defaults to Utils)
- `-v, --verbose`: Enable verbose output to see detailed generation process

## Usage Examples

### Basic Usage

```bash
# Generate extension with default Utils class
./phalcons command make:zephir MyExtension

# Generate extension with custom class name
./phalcons command make:zephir Calculator --class=Math

# Generate with verbose output
./phalcons command make:zephir Helper -v
```

### Advanced Examples

```bash
# Create a string manipulation extension
./phalcons command make:zephir StringUtils --class=StringHelper

# Create a mathematical extension with verbose output
./phalcons command make:zephir MathExtension --class=Calculator -v

# Create a utility extension
./phalcons command make:zephir AppUtils --class=Tools
```

## Generated Files

The command creates the following file structure:

```
etc/
└── ExtensionName/
    ├── config.json                    # Zephir configuration
    ├── .gitignore                     # Git ignore for compiled files
    └── ExtensionName/                 # Namespace directory
        └── ClassName.zep              # Zephir class file
```

### 1. Configuration File (config.json)

**Location**: `etc/ExtensionName/config.json`

The generated configuration includes:
- Extension namespace and metadata
- PHP version requirements
- Zephir compiler warnings and optimizations
- API documentation settings
- IDE stub generation settings

**Key Features**:
- Proper namespace configuration
- Optimized compiler settings
- Warning configurations for better code quality
- Documentation generation setup

### 2. Zephir Class File

**Location**: `etc/ExtensionName/ExtensionName/ClassName.zep`

The generated class includes:
- Proper namespace declaration
- Comprehensive documentation
- Useful utility methods as examples
- Type-safe method signatures

**Default Methods**:
- `greet(string $name)`: Returns a greeting message
- `add(int $a, int $b)`: Adds two numbers
- `multiply(int $a, int $b)`: Multiplies two numbers
- `isEmpty(string $str)`: Checks if string is empty
- `getCurrentTime()`: Returns current timestamp
- `toUpper(string $str)`: Converts string to uppercase
- `toLower(string $str)`: Converts string to lowercase

### 3. Git Ignore File

**Location**: `etc/ExtensionName/.gitignore`

The generated .gitignore includes:
- Compiled extension files (`/ext`, `.zephir`)
- Compilation logs (`compile.log`, `compile-errors.log`)
- IDE files (`.vscode/`, `.idea/`)
- OS files (`.DS_Store`, `Thumbs.db`)

## Directory Structure Requirements

The command follows the Zephir directory structure requirements:

- **Extension Directory**: `etc/ExtensionName/`
- **Namespace Directory**: `etc/ExtensionName/ExtensionName/`
- **Source Files**: `etc/ExtensionName/ExtensionName/*.zep`

**Important**: The namespace directory name must match the namespace defined in `config.json`.

## Next Steps After Generation

After generating a Zephir extension, follow these steps:

### 1. Customize Your Extension

Edit the generated `.zep` file to add your custom methods:

```zephir
namespace MyExtension;

class Utils
{
    public static function customMethod(string input) -> string
    {
        // Your custom logic here
        return "Processed: " . input;
    }
}
```

### 2. Compile the Extension

Use the build system to compile your extension:

```bash
./phalcons build
```

This will:
- Compile all Zephir extensions in the `etc/` directory
- Build the Docker containers with the compiled extensions
- Make extensions available in your PHP application

### 3. Use in PHP Code

Once compiled, use your extension in PHP:

```php
<?php

use MyExtension\Utils;

// Call your extension methods
echo Utils::greet("World");
echo Utils::add(5, 3);
echo Utils::customMethod("test");
```

## Command Validation

The command performs several validations:

### Name Formatting
- Removes non-alphanumeric characters
- Capitalizes the first letter
- Ensures valid PHP class name format

### File Overwrite Protection
- Checks for existing files before creation
- Prompts for confirmation before overwriting
- Allows skipping existing files

### Directory Creation
- Automatically creates required directory structure
- Sets proper permissions (755)
- Creates nested directories as needed

## Integration with Build System

The generated extensions integrate seamlessly with the existing build system:

### Automatic Detection
- Extensions are automatically detected in `etc/` directory
- No manual registration required
- Works with existing Docker-based compilation

### Compilation Process
- Uses the same compilation process as existing extensions
- Supports verbose compilation output
- Handles compilation errors gracefully

## Best Practices

### Naming Conventions
- Use PascalCase for extension names (e.g., `MyExtension`)
- Use descriptive names that reflect functionality
- Avoid generic names like `Extension` or `Plugin`

### Class Organization
- Keep related functionality in the same class
- Use static methods for utility functions
- Document all public methods with proper annotations

### Development Workflow
1. Generate extension with `make:zephir`
2. Implement your methods in the `.zep` file
3. Test compilation with `./phalcons build`
4. Use in PHP code and test functionality
5. Iterate and refine as needed

## Troubleshooting

### Common Issues

1. **Invalid extension name**: Ensure name contains only alphanumeric characters
2. **File already exists**: Use confirmation prompts or choose different names
3. **Compilation errors**: Check Zephir syntax in generated `.zep` files
4. **Permission issues**: Ensure proper directory permissions

### Debugging

Use verbose mode for detailed output:

```bash
./phalcons command make:zephir MyExtension -v
```

This will show:
- Directory creation steps
- File generation process
- Any warnings or errors

## Examples

### String Utility Extension

```bash
./phalcons command make:zephir StringUtils --class=StringHelper
```

Generated class can be customized for string operations:

```zephir
namespace StringUtils;

class StringHelper
{
    public static function reverse(string str) -> string
    {
        return strrev(str);
    }
    
    public static function wordCount(string str) -> int
    {
        return str_word_count(str);
    }
}
```

### Mathematical Extension

```bash
./phalcons command make:zephir MathExtension --class=Calculator
```

Generated class can be customized for mathematical operations:

```zephir
namespace MathExtension;

class Calculator
{
    public static function factorial(int n) -> int
    {
        if (n <= 1) {
            return 1;
        }
        return n * self::factorial(n - 1);
    }
    
    public static function isPrime(int n) -> bool
    {
        if (n < 2) {
            return false;
        }
        
        int i = 2;
        while (i * i <= n) {
            if (n % i == 0) {
                return false;
            }
            let i++;
        }
        
        return true;
    }
}
```

## Related Documentation

- [Zephir Integration Guide](_docs/zephir-integration.md) - Complete guide to Zephir integration
- [Make JS Command Documentation](_docs/make-js-command.md) - Similar command for JavaScript components

## Command Implementation

The command is implemented in `IamLab/Commands/MakeZephirCommand.php` and registered in `IamLab/config/commands.php`. The implementation follows the same patterns as other make commands in the framework.

This command streamlines the process of creating Zephir extensions and ensures consistency across all generated extensions in your project.