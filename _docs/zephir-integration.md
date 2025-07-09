# Zephir Integration Guide

This document explains how to use Zephir PHP extensions in your Phalcon application.

## Overview

The build system has been enhanced to automatically compile Zephir extensions during the build process. When you run `./phalcons build`, the system will:

1. Automatically detect Zephir extensions in the `etc/` directory
2. Compile each extension using the Zephir compiler
3. Continue with the regular Docker build process

## Directory Structure

Zephir extensions should be placed in the following structure:

```
etc/
├── ExtensionName/
│   ├── config.json          # Zephir configuration file
│   └── ExtensionName/       # Namespace directory (matches config namespace)
│       ├── ClassName.zep
│       └── ...
└── AnotherExtension/
    ├── config.json
    └── AnotherExtension/
        └── ...
```

**Important**: The directory containing .zep files must match the `namespace` field in your `config.json` file.

## Creating a Zephir Extension

### 1. Create the Extension Directory

```bash
mkdir -p etc/YourExtension/YourExtension
```

**Note**: The inner directory name must match your extension's namespace.

### 2. Create the Configuration File

Create `etc/YourExtension/config.json`:

```json
{
    "namespace": "YourExtension",
    "name": "yourextension",
    "description": "Your extension description",
    "author": "Your Name",
    "version": "1.0.0",
    "verbose": false,
    "requires": {
        "php": ">=7.0"
    },
    "warnings": {
        "unused-variable": true,
        "nonexistent-function": true,
        "nonexistent-class": true
    },
    "optimizations": {
        "static-type-inference": true,
        "constant-folding": true
    }
}
```

### 3. Create Zephir Source Files

Create your Zephir classes in `etc/YourExtension/YourExtension/`. For example, `etc/YourExtension/YourExtension/Utils.zep`:

```zephir
namespace YourExtension;

class Utils
{
    public static function greet(string name) -> string
    {
        return "Hello, " . name . " from Zephir!";
    }

    public static function add(int a, int b) -> int
    {
        return a + b;
    }
}
```

## Building Extensions

### Docker-Based Automatic Build

Run the build command to compile all extensions and build the application:

```bash
./phalcons build
```

This will:
1. Build the Docker containers with Zephir and all dependencies pre-installed
2. Compile all Zephir extensions found in `etc/` inside the Docker container
3. Make extensions available in your PHP application

The compilation happens inside the Docker container, so you don't need to install Zephir on your host system.

### Manual Compilation

You can also compile Zephir extensions manually:

```bash
php bin/compile-zephir.php -v
```

Use the `-v` flag for verbose output.

## Using Extensions in Your Application

Once compiled and the application is built, you can use your Zephir extensions in PHP:

```php
<?php

// Using the TestExtension example
use TestExtension\Utils;

// Call static methods
echo Utils::greet("World");           // Output: Hello, World from Zephir extension!
echo Utils::add(5, 3);               // Output: 8
echo Utils::getCurrentTime();        // Output: current timestamp
var_dump(Utils::isEmpty(""));        // Output: bool(true)
```

## Test Extension

A test extension is included to demonstrate functionality:

- **Location**: `etc/TestExtension/`
- **Namespace**: `TestExtension`
- **Classes**: `TestExtension\Utils`

### Available Methods

- `Utils::greet(string $name)` - Returns a greeting message
- `Utils::add(int $a, int $b)` - Adds two numbers
- `Utils::getCurrentTime()` - Returns current timestamp
- `Utils::isEmpty(string $str)` - Checks if string is empty

## Prerequisites

### Docker Environment

This integration is designed to work with Docker. All Zephir dependencies are automatically installed in the Docker container during the build process, including:

- Zephir compiler
- php-zephir-parser extension
- PHP development headers
- Required build tools (gcc, make, autoconf, etc.)

No manual installation of Zephir is required on your host system.

## Troubleshooting

### Common Issues

1. **Docker build fails**: Ensure Docker is running and you have sufficient disk space
2. **Directory structure errors**: Make sure the namespace directory matches your config.json namespace field
3. **Compilation errors**: Check your Zephir syntax and configuration
4. **Extension not loading**: Rebuild the Docker containers with `./phalcons build --no-cache`

### Debugging

The build process automatically uses verbose mode for Zephir compilation. Check the build output for detailed compilation information.

You can also run the compilation manually inside the container:

```bash
./phalcons php bin/compile-zephir.php -v
```

### Logs

Check the compilation logs for detailed error information. The compiler will output:
- `[INFO]` messages for normal operations
- `[ERROR]` messages for compilation failures

## Best Practices

1. **Namespace**: Use descriptive namespaces for your extensions
2. **Documentation**: Document your Zephir classes and methods
3. **Testing**: Test your extensions thoroughly before deployment
4. **Version Control**: Include extension source code in version control, but exclude compiled binaries
5. **Dependencies**: Keep extension dependencies minimal

## File Structure Example

```
project/
├── etc/
│   ├── TestExtension/
│   │   ├── config.json
│   │   └── TestExtension/      # Namespace directory
│   │       └── Utils.zep
│   └── MyCustomExtension/
│       ├── config.json
│       └── MyCustomExtension/  # Namespace directory
│           ├── Helper.zep
│           └── Calculator.zep
├── bin/
│   └── compile-zephir.php
├── _docs/
│   └── zephir-integration.md
├── docker/
│   └── 8.1/
│       └── Dockerfile          # Modified with Zephir installation
└── phalcons                    # Modified build script
```

This integration allows you to seamlessly use high-performance Zephir extensions alongside your regular PHP code in your Phalcon application.
