# Estate Planning Manager - Testing Guide

This document provides comprehensive information about testing the Estate Planning Manager WordPress plugin.

## Overview

The Estate Planning Manager plugin includes a comprehensive test suite covering:

- Database operations and data integrity
- Security and access control
- PDF generation functionality
- Audit logging and compliance
- SuiteCRM API integration
- User permissions and role management
- Modular UI functionality

## Test Structure

### Test Files

- `tests/test-epm-database.php` - Database operations, CRUD operations, data validation
- `tests/test-epm-security.php` - Security features, access control, data sanitization
- `tests/test-epm-pdf-generator.php` - PDF generation, templates, security features
- `tests/test-epm-audit-logger.php` - Audit logging, compliance tracking, data integrity
- `tests/test-epm-suitecrm-api.php` - CRM integration, data synchronization, API handling
- `tests/test-epm-modular-ui.php` - Modular UI functionality, view class rendering, AJAX integration

### Supporting Files

- `tests/class-epm-test-case.php` - Base test case with common functionality
- `tests/class-epm-test-factory.php` - Test data factory for creating test objects
- `tests/bootstrap.php` - Test environment bootstrap
- `phpunit.xml` - PHPUnit configuration
- `run-tests.php` - Simple test runner for basic functionality

## Quick Start

### 1. Basic Test Runner

For a quick test of core functionality without full WordPress setup:

```bash
php run-tests.php
```

This will run basic tests to verify the testing environment is working.

### 2. Full Test Suite

For comprehensive testing with PHPUnit:

```bash
# Install dependencies
composer install

# Run all tests
composer test

# Run tests with coverage report
composer test-coverage

# Run code quality checks
composer quality
```

## Test Categories

### Database Tests (`test-epm-database.php`)

Tests database operations including:

- **Table Creation**: Verifies all required tables are created with proper structure
- **Client Management**: Tests client creation, retrieval, and management
- **Data Operations**: CRUD operations for all data sections
- **Data Validation**: Input validation and sanitization
- **Foreign Key Constraints**: Referential integrity
- **Performance**: Query optimization and bulk operations

Key test methods:
- `test_database_tables_created()` - Verifies table structure
- `test_create_client()` - Client creation functionality
- `test_save_basic_personal_data()` - Data saving operations
- `test_data_sanitization()` - Input sanitization

### Security Tests (`test-epm-security.php`)

Tests security features including:

- **Access Control**: User permissions and role-based access
- **Data Sharing**: Selective data sharing permissions
- **Input Sanitization**: XSS and injection prevention
- **Session Security**: Session management and validation
- **Rate Limiting**: API rate limiting and abuse prevention
- **Audit Logging**: Security event logging

Key test methods:
- `test_user_can_access_own_data()` - Basic access control
- `test_sharing_permissions()` - Data sharing functionality
- `test_data_sanitization()` - Input sanitization
- `test_rate_limiting()` - Rate limiting functionality

### PDF Generation Tests (`test-epm-pdf-generator.php`)

Tests PDF generation including:

- **Basic Generation**: PDF creation from client data
- **Template System**: Custom templates and formatting
- **Security Features**: Password protection and restrictions
- **Performance**: Generation speed and memory usage
- **Error Handling**: Invalid data and error conditions

Key test methods:
- `test_generate_pdf_with_basic_data()` - Basic PDF generation
- `test_pdf_security_features()` - PDF security options
- `test_pdf_generation_performance()` - Performance testing

### Audit Logging Tests (`test-epm-audit-logger.php`)

Tests audit and compliance features:

- **Action Logging**: User action tracking
- **Security Events**: Security incident logging
- **Data Changes**: Change tracking and history
- **Log Retrieval**: Query and filtering capabilities
- **Compliance**: Data retention and cleanup

Key test methods:
- `test_basic_audit_logging()` - Basic logging functionality
- `test_security_event_logging()` - Security event tracking
- `test_data_change_logging()` - Change tracking
- `test_audit_log_cleanup()` - Data retention

### SuiteCRM Integration Tests (`test-epm-suitecrm-api.php`)

Tests CRM integration including:

- **API Connection**: Connection and authentication
- **Data Synchronization**: Bi-directional data sync
- **Error Handling**: API errors and timeouts
- **Batch Operations**: Bulk data operations
- **Webhook Handling**: Real-time updates

Key test methods:
- `test_api_connection()` - API connectivity
- `test_sync_client_data()` - Data synchronization
- `test_error_handling()` - Error handling
- `test_batch_operations()` - Bulk operations

### Modular UI Tests (`test-epm-modular-ui.php`)

Tests modular UI functionality including:

- **View Class Rendering**: Each section (Personal, Banking, Investments, Insurance, Real Estate, Scheduled Payments, Autos, Personal Property, Emergency Contacts) is rendered only via its dedicated view class. The main shortcode handler (`EPM_Shortcodes`) does not perform inline rendering for any section.
- **Error Handling**: If a section view class is missing, an error is shown and no fallback rendering occurs.
- **AJAX and Frontend JS Integration**: All AJAX and frontend JS for section forms and modals are triggered via the modular view classes.
- **Unit Tests for View Classes**: Unit tests verify that the correct view class is called for each section, and that legacy rendering code is not used.

## Test Data

### Test Factory

The `EPM_Test_Factory` class provides methods to create test data:

```php
// Create test client data
EPM_Test_Factory::create_client_data($client_id, 'basic_personal');

// Create sharing permissions
EPM_Test_Factory::create_sharing_permission($client_id, $user_id, $section, $permission);

// Get default test data
$data = EPM_Test_Factory::get_default_data('basic_personal');
```

### Test Users

Tests automatically create users with different roles:
- Estate Client (`estate_client`)
- Financial Advisor (`financial_advisor`)
- Administrator (`administrator`)

## Running Specific Tests

### Individual Test Files

```bash
# Run database tests only
./vendor/bin/phpunit tests/test-epm-database.php

# Run security tests only
./vendor/bin/phpunit tests/test-epm-security.php
```

### Specific Test Methods

```bash
# Run specific test method
./vendor/bin/phpunit --filter test_create_client tests/test-epm-database.php
```

### Test Groups

Tests can be organized by groups:

```bash
# Run security-related tests
./vendor/bin/phpunit --group security

# Run integration tests
./vendor/bin/phpunit --group integration
```

## Test Environment Setup

### WordPress Test Environment

For full WordPress integration testing:

1. **Install WordPress Test Suite**:
   ```bash
   bash bin/install-wp-tests.sh wordpress_test root '' localhost latest
   ```

2. **Configure Database**:
   - Create test database
   - Update `phpunit.xml` with database credentials

3. **Set Environment Variables**:
   ```bash
   export WP_TESTS_DIR=/path/to/wordpress-tests-lib
   export WP_CORE_DIR=/path/to/wordpress
   ```

### Mock Environment

For testing without full WordPress:

1. **Use Simple Test Runner**:
   ```bash
   php run-tests.php
   ```

2. **Mock WordPress Functions**:
   The test runner includes basic WordPress function mocks for isolated testing.

## Continuous Integration

### GitHub Actions

Example `.github/workflows/tests.yml`:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:5.7
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: wordpress_test
        ports:
          - 3306:3306
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
          extensions: mysql
      
      - name: Install dependencies
        run: composer install
      
      - name: Run tests
        run: composer test
```

## Code Coverage

Generate code coverage reports:

```bash
# HTML coverage report
composer test-coverage

# Text coverage report
./vendor/bin/phpunit --coverage-text
```

Coverage reports help identify untested code areas.

## Performance Testing

### Database Performance

Tests include performance benchmarks:

```php
public function test_bulk_data_operations() {
    $start_time = microtime(true);
    
    // Perform bulk operations
    for ($i = 0; $i < 1000; $i++) {
        EPM_Database::instance()->save_client_data($client_id, 'section', $data);
    }
    
    $end_time = microtime(true);
    $execution_time = $end_time - $start_time;
    
    $this->assertLessThan(10, $execution_time); // Should complete within 10 seconds
}
```

### Memory Usage

Monitor memory usage during tests:

```php
public function test_memory_usage() {
    $initial_memory = memory_get_usage();
    
    // Perform memory-intensive operations
    $this->generate_large_pdf();
    
    $final_memory = memory_get_usage();
    $memory_used = $final_memory - $initial_memory;
    
    $this->assertLessThan(50 * 1024 * 1024, $memory_used); // Less than 50MB
}
```

## Security Testing

### Input Validation

Tests verify all inputs are properly sanitized:

```php
public function test_xss_prevention() {
    $malicious_input = '<script>alert("xss")</script>';
    $sanitized = EPM_Security::instance()->sanitize_input($malicious_input);
    
    $this->assertStringNotContainsString('<script>', $sanitized);
}
```

### SQL Injection Prevention

Tests verify SQL injection protection:

```php
public function test_sql_injection_prevention() {
    $malicious_input = "'; DROP TABLE wp_users; --";
    $result = EPM_Database::instance()->get_client_data($malicious_input);
    
    $this->assertFalse($result);
}
```

## Troubleshooting

### Common Issues

1. **Database Connection Errors**:
   - Verify database credentials in `phpunit.xml`
   - Ensure test database exists and is accessible

2. **WordPress Function Errors**:
   - Check WordPress test environment setup
   - Verify `WP_TESTS_DIR` environment variable

3. **Memory Limit Errors**:
   - Increase PHP memory limit: `php -d memory_limit=512M vendor/bin/phpunit`

4. **Timeout Errors**:
   - Increase test timeout in `phpunit.xml`
   - Optimize slow tests

### Debug Mode

Enable debug mode for detailed error information:

```bash
# Run with debug output
./vendor/bin/phpunit --debug

# Run with verbose output
./vendor/bin/phpunit --verbose
```

## Best Practices

### Writing Tests

1. **Test One Thing**: Each test should verify one specific behavior
2. **Use Descriptive Names**: Test method names should clearly describe what is being tested
3. **Setup and Teardown**: Use `setUp()` and `tearDown()` methods for test preparation and cleanup
4. **Mock External Dependencies**: Use mocks for external APIs and services
5. **Test Edge Cases**: Include tests for error conditions and edge cases

### Test Data

1. **Use Factory Methods**: Create test data using factory methods for consistency
2. **Clean Up**: Always clean up test data after tests complete
3. **Realistic Data**: Use realistic test data that represents actual usage
4. **Isolation**: Tests should not depend on data from other tests

### Performance

1. **Fast Tests**: Keep tests fast to encourage frequent running
2. **Parallel Execution**: Use parallel test execution when possible
3. **Database Transactions**: Use database transactions for faster cleanup
4. **Selective Testing**: Run only relevant tests during development

## Contributing

When contributing to the test suite:

1. **Add Tests for New Features**: All new functionality should include tests
2. **Update Existing Tests**: Modify tests when changing existing functionality
3. **Follow Conventions**: Use existing test patterns and naming conventions
4. **Document Complex Tests**: Add comments for complex test logic
5. **Run Full Suite**: Ensure all tests pass before submitting changes

## Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [WordPress Plugin Testing](https://make.wordpress.org/cli/handbook/plugin-unit-tests/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [PIPEDA Compliance](https://www.priv.gc.ca/en/privacy-topics/privacy-laws-in-canada/the-personal-information-protection-and-electronic-documents-act-pipeda/)

For questions or issues with testing, please refer to the project documentation or create an issue in the project repository.
