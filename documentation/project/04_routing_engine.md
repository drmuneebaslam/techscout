# 04. Routing & HTTP Request Engine

## File Locations
- [`app/Core/Router.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Router.php)
- [`app/Core/Request.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Request.php)
- [`app/Core/Response.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Response.php)

## Key Capabilities
- **Regex Parametric Routes**: Converts parameters such as `/blog/{slug}` or `/admin/users/{id}/edit` into compiled regular expressions.
- **HTTP Method Matching**: Matches `GET`, `POST`, `PUT`, `DELETE` requests.
- **Request Object Isolation**: Sanitizes input parameters (`GET`, `POST`, JSON body) and determines AJAX request types.
- **Response Helper**: Provides methods for emitting JSON responses and performing HTTP redirects.
