# 09. Input Sanitization & Profanity Filtering

## File Locations
- [`app/Core/Sanitizer.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Sanitizer.php)
- [`app/Core/ContentFilter.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/ContentFilter.php)

## Key Features
- **XSS Prevention**: `Sanitizer::clean()` escapes HTML special characters.
- **Safe HTML Stripping**: `Sanitizer::cleanHtml()` allows safe formatting tags while removing malicious script blocks.
- **Slug Generator**: `Sanitizer::slug()` converts text into URL-safe lowercase slugs.
- **Profanity Masking**: `ContentFilter::maskProfanity()` replaces blacklisted words with asterisks.
