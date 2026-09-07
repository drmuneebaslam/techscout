# 10. Exception Handling & Error Diagnostics (`ErrorLogger.php`)

## File Location
[`app/Core/ErrorLogger.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/ErrorLogger.php)

## Key Features
- **Global Handlers**: Registers custom error, exception, and shutdown handlers via `set_error_handler`, `set_exception_handler`, and `register_shutdown_function`.
- **Log Diagnostics**: Writes full stack traces, URI contexts, and request IP details to log files and the `system_logs` database table.
- **Graceful Error Rendering**: Renders user-friendly error views (`views/errors/500.php`) during production failures without leaking sensitive credentials.
