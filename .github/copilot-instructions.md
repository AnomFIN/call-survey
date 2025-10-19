# GitHub Copilot Instructions

## Project Overview
AnomFIN Call-Survey ULTRALIGHT is an enterprise-grade, lightweight call and message feedback tool. It integrates Twilio Voice IVR, SMS, WhatsApp messaging, CSV imports, and PIN-protected API endpoints into a seamless solution.

## Technology Stack
- **Backend**: PHP 8.3–8.4 (ULTRALIGHT - no Composer dependencies)
- **Database**: MySQL 8.0 with utf8mb4 charset
- **APIs**: Twilio Voice/SMS/WhatsApp
- **Server**: Apache with cPanel deployment
- **Frontend**: Vanilla JavaScript (no frameworks)

## Coding Standards

### PHP
- **Always use strict types**: Start every PHP file with `declare(strict_types=1);`
- **PHP version**: Target PHP 8.4, using modern features (typed properties, constructor promotion, etc.)
- **No Composer**: Use only standard PHP extensions (`mysqli`, `curl`, `mbstring`, `PDO`)
- **Error handling**: Use proper error handling with try-catch blocks for database and API operations
- **Type hints**: Always use type hints for function parameters and return types
- **Security**: Validate all external input; never trust user data

### Database
- **Charset**: All tables must use `utf8mb4` charset and `utf8mb4_unicode_ci` collation
- **Connection**: Use PDO with prepared statements to prevent SQL injection
- **Schema location**: Database schema definitions are in `sql/schema.sql`
- **Naming**: Use snake_case for table and column names

### Frontend
- **No frameworks**: Use vanilla JavaScript only
- **CSS prefix**: App-specific CSS classes must be prefixed with `app-`
- **Design sync**: UI follows anomfin-website design system
- **Theme files**: Never edit files in `public/css/theme/`, `public/js/theme/`, `public/fonts/`, or `public/img/brand/`
- **Custom styles**: All app-specific styles go in `public/css/style.css`
- **Custom scripts**: All app-specific JavaScript goes in `public/js/app.js`

## Security Requirements

### Twilio Webhook Validation
- **Always validate**: All Twilio webhooks must validate `X-Twilio-Signature` header
- **Implementation**: Use `hash_hmac('sha1', ...)` with base64 encoding and `hash_equals()` for comparison
- **Auth token**: Load from environment variable `TWILIO_AUTH_TOKEN`
- **Reference**: See `api/twilio-webhook.php` for the correct implementation pattern

### Environment Variables
- **Never commit**: `.env` files must never be committed to the repository
- **Location**: Production `.env` should be stored at `/home/anomfinf/.envs/anomfin-ultralight`
- **Example file**: Keep `.env.example` updated with all required variables (without values)
- **Access control**: `.htaccess` must block direct access to `.env*` files

### PIN Protection
- CSV import and sensitive API endpoints must be PIN-protected
- Validate PIN before processing any sensitive operations

### Installation Security
- The `install/` directory must be blocked by `.htaccess` unless `ANOMFIN_INSTALL_ACTIVE=1` is set
- After installation, the install directory should be disabled or removed

## Testing Requirements

### Before Deployment
- **PHP lint**: All PHP files must pass `php -l` syntax check
- **Local health check**: Test with `php -S 127.0.0.1:8080 -t public` and verify HTTP 200 response
- **Post-deploy check**: Verify `https://app.anomfin.fi/public/index.html` returns HTTP 200

### Regression Tests
- CSV import functionality
- PIN-protected API calls
- Twilio Voice DTMF routing (1/2 options, where 2 routes to agent via `AGENT_PSTN`)
- SMS and WhatsApp message sending
- Status updates

## Design System

### Synchronization
- Design assets are synced from the `anomfin-website` repository using `tools/sync-theme.sh`
- The script performs sparse checkout of specific paths:
  - `public/assets/css/*` → `public/css/theme/`
  - `public/assets/js/*` → `public/js/theme/`
  - `public/assets/fonts/*` → `public/fonts/`
  - `public/assets/img/brand/*` → `public/img/brand/`

### Asset Guidelines
- **PNG to SVG**: Prefer SVG icons over PNG to keep the repository binary-free
- **Theme files**: Auto-generated files like `public/css/theme/theme-index.css` should not be manually edited
- **Brand consistency**: UI must match anomfin-website color palette, typography, and logo

## Deployment

### Server Paths
- Main website: `/home/anomfinf/public_html/`
- Call-Survey app: `/home/anomfinf/public_html/app/`

### GitHub Actions
- Workflow: `.github/workflows/deploy.yml`
- Triggers: Push to `main` branch or manual `workflow_dispatch`
- Required secrets: `CPANEL_HOST`, `CPANEL_USER`, and either `CPANEL_PASS` or `CPANEL_SSH_KEY` + `CPANEL_SSH_PORT`

### Deployment Process
1. Checkout and sync design system
2. PHP lint and health check
3. Package `anomfin-ultralight.zip`
4. Upload to cPanel via SFTP/rsync
5. Extract to deployment path
6. Post-deploy health check
7. Save artifact for rollback

### Rollback
- Download `anomfin-ultralight.zip` from previous successful workflow run
- Redeploy using manual workflow dispatch

## File Organization

### Package Contents
When creating release packages, include:
- `public/` - Frontend files
- `api/` - API endpoints
- `install/` - Installation wizard
- `sql/` - Database schemas
- `worker/` - Background worker scripts
- `.env.example` - Environment template
- `README.md` - Documentation
- `.htaccess` - Apache configuration

### Excluded from Repository
- `.env` files (secrets)
- Theme files downloaded from anomfin-website (cached separately)
- Build artifacts
- `node_modules/` (if any Node tools are added)
- Temporary files

## Documentation
- Keep `README.md` updated with setup and deployment instructions
- Document any new environment variables in `.env.example`
- Update `copilot.md` for team-specific runbook changes (separate from these Copilot instructions)

## Best Practices
- **Minimal dependencies**: Keep the project lightweight; avoid adding npm/composer dependencies unless absolutely necessary
- **Apache compatibility**: Ensure `.htaccess` rules work with Apache and respect existing security configurations
- **Error logging**: Log errors appropriately for production debugging
- **Response codes**: Use proper HTTP status codes (200, 403, 500, etc.)
- **JSON responses**: API endpoints should return `Content-Type: application/json`
- **Graceful degradation**: Handle missing environment variables gracefully with clear error messages
