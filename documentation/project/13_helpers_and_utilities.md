# 13. System Utilities & Helper Catalog

## File Locations
- [`app/Core/Config.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/Config.php)
- [`app/Core/FeatureFlag.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/FeatureFlag.php)
- [`app/Core/CredentialsManager.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/CredentialsManager.php)
- [`app/Core/SeoEngine.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SeoEngine.php)
- [`app/Core/DocxParser.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/DocxParser.php)
- [`app/Core/BannerHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/BannerHelper.php)
- [`app/Core/DateHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/DateHelper.php)
- [`app/Core/SocialHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SocialHelper.php)
- [`app/Core/SvgHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/SvgHelper.php)
- [`app/Core/TagHelper.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/TagHelper.php)
- [`app/Core/helpers.php`](file:///c:/xampp/htdocs/techscout.n/app/Core/helpers.php)

## Key Functions & Utilities
- `e($string)`: Safe HTML entity escaping.
- `url($path)`: Base URL generation.
- `asset($path)`: Cache-busting static asset URL.
- `auth()`: Current user session lookup.
- `session($key)`: Session getter wrapper.
- `old($key)`: Sticky form input restoration.
- `SeoEngine`: Meta tags & Schema.org JSON-LD structured data generator.
- `DocxParser`: Converts Word `.docx` documents into structured HTML.
- `SvgHelper`: Native inline SVG Lucide icon renderer.
