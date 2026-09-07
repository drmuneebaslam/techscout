# 17. Author Verification Application Controller

## File Location
[`app/Controllers/AuthorApplicationController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthorApplicationController.php)

## Handled Actions
- `showForm()`: Renders member author application submission form.
- `submit()`: Validates portfolio URLs, social profiles, and sample writing links, storing records in `author_applications` table.
- `status()`: Displays real-time editorial review status for pending applications.
