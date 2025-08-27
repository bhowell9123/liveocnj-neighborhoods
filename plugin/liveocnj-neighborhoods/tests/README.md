# ACF Integration Tests for LiveOCNJ Neighborhoods

This directory contains test files for verifying the Advanced Custom Fields (ACF) integration in the LiveOCNJ Neighborhoods WordPress plugin.

## Available Test Files

### 1. Test Loader (`load-tests.php`)

This file provides a simple way to load all ACF integration tests. It includes:

- Loading of all test files
- Admin notices indicating that tests are active
- A dashboard widget showing test status
- Links to the full test page

### 2. ACF Test File (`acf-test.php`)

This file provides a comprehensive test suite for verifying the ACF integration. It includes:

- A test page accessible from the WordPress admin under Tools > ACF Integration Test
- Tests for ACF activation status
- Verification of field group registration
- Tests for field access and data retrieval
- Detailed output for administrators

### 3. ACF Admin Notice (`acf-admin-notice.php`)

This file provides a simple admin notice that can be temporarily added to the plugin to verify the ACF integration. It includes:

- An admin notice displaying the ACF integration status
- A dashboard widget showing the ACF integration status
- Links to the full test page

### 4. Example Usage (`example-usage.php`)

This file demonstrates various ways to include the ACF integration tests in the main plugin file. It includes examples for:

- Using the test loader (recommended)
- Using individual test files
- Conditional loading based on environment
- Conditional loading based on user role
- Loading tests via a query parameter

## How to Use

### Using the Test Loader (Recommended)

To enable all tests at once:

1. Open the main plugin file (`liveocnj-neighborhoods.php`)
2. Add the following line near the top of the file, after the plugin header:

```php
// Include ACF integration tests (remove in production)
require_once __DIR__ . '/tests/load-tests.php';
```

3. All tests will now be available, including the test page, admin notices, and dashboard widget.
4. Remove this line before deploying to production.

### Using the Test Page

1. The test page is automatically available in the WordPress admin under Tools > ACF Integration Test when the plugin is active.
2. Visit this page to run all tests and see detailed results.

### Using the Admin Notice

To temporarily add the admin notice to the plugin:

1. Open the main plugin file (`liveocnj-neighborhoods.php`)
2. Add the following line near the top of the file, after the plugin header:

```php
// Include ACF integration test notice (remove in production)
require_once __DIR__ . '/tests/acf-admin-notice.php';
```

3. The admin notice will now appear on the WordPress dashboard and relevant admin pages.
4. Remove this line before deploying to production.

### Advanced Usage Examples

For more advanced usage examples, see the `example-usage.php` file. This file demonstrates:

- Using the test loader (recommended)
- Using individual test files
- Conditional loading based on environment
- Conditional loading based on user role
- Loading tests via a query parameter

These examples can be adapted to your specific needs and integrated into the main plugin file as needed.

## Test Results Interpretation

### Success Indicators

- ✅ Green text indicates successful tests
- All tests passing indicates that the ACF integration is working correctly

### Warning Indicators

- ⚠️ Yellow text indicates warnings that may need attention
- Warnings typically indicate missing data rather than integration issues

### Error Indicators

- ❌ Red text indicates errors that need to be fixed
- Errors typically indicate that ACF is not active or the field group is not registered

## Troubleshooting

If tests fail, check the following:

1. Ensure ACF plugin is installed and activated
2. Verify that the ACF JSON directory exists at `plugin/liveocnj-neighborhoods/acf-json/`
3. Confirm that the field group JSON file exists at `plugin/liveocnj-neighborhoods/acf-json/group_neighborhood_locnj.json`
4. Check that the ACF_Fields.php file is being loaded correctly
5. Verify that the field group is being registered either via JSON or PHP

## Notes for Production

These test files are intended for development and testing purposes only. They should not be included or activated in a production environment.