# 16. Authentication Controller (`AuthController.php`)

## File Location
[`app/Controllers/AuthController.php`](file:///c:/xampp/htdocs/techscout.n/app/Controllers/AuthController.php)

## Handled User Workflows
- `login()`, `showLogin()`: User authentication with email/password validation, brute-force throttling, and session cookie setting.
- `register()`, `showRegister()`: Member account creation, email verification code generation, and initial welcome mail dispatch.
- `logout()`: Destroys user session and revokes database tokens.
- `sendResetLink()`, `resetPassword()`: Password reset link & OTP code verification.
- `verifyEmail()`, `resendVerifyEmail()`: Verification OTP PIN confirmation.
- `twoFactor()`: Two-Factor Authentication OTP code validation.
- `onboarding()`, `completeOnboarding()`: Initial account profile setup & topic interest selection.
