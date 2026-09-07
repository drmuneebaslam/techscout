# 03. PSR-4 Class Autoloader (`app/autoload.php`)

## File Location
[`app/autoload.php`](file:///c:/xampp/htdocs/techscout.n/app/autoload.php)

## Implementation Details
The autoloader uses `spl_autoload_register()` to automatically resolve namespaces matching the prefix `App\`.

```php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
```
