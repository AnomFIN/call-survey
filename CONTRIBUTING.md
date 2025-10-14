# Contributing to Call-Survey ULTRALIGHT

Thank you for considering contributing to Call-Survey ULTRALIGHT! This document provides guidelines for contributing to the project.

## Code of Conduct

- Be respectful and inclusive
- Welcome newcomers and help them learn
- Focus on constructive feedback
- Keep discussions professional and on-topic

## How to Contribute

### Reporting Bugs

Before creating bug reports, please check existing issues. When creating a bug report, include:

- **Clear title and description**
- **Steps to reproduce** the problem
- **Expected behavior** vs actual behavior
- **Environment details** (PHP version, OS, database)
- **Error messages** or logs
- **Screenshots** if applicable

### Suggesting Enhancements

Enhancement suggestions are welcome! Please include:

- **Clear title and description** of the feature
- **Use cases** and benefits
- **Possible implementation** approach
- **Examples** from other systems if applicable

### Pull Requests

1. **Fork the repository** and create your branch from `main`
2. **Follow the coding style** used in the project
3. **Write clear commit messages**
4. **Add tests** if applicable
5. **Update documentation** for any changed functionality
6. **Ensure all tests pass**

## Development Setup

```bash
# Clone your fork
git clone https://github.com/YOUR-USERNAME/call-survey.git
cd call-survey

# Install dependencies
composer install

# Copy and configure
cp config.example.php config.php
nano config.php

# Setup database
mysql -u root -p < database.sql

# Run tests
php test.php

# Start dev server
./start-dev.sh
```

## Coding Standards

### PHP Code Style

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard
- Use meaningful variable and function names
- Add docblocks for classes and methods
- Keep functions focused and small
- Avoid deep nesting (max 3 levels)

Example:
```php
<?php
/**
 * Brief description of what this class does
 */
class MyClass {
    /**
     * Brief description of method
     * 
     * @param string $param Description
     * @return bool Description
     */
    public function myMethod($param) {
        // Implementation
    }
}
```

### Database

- Use prepared statements (PDO)
- Never concatenate user input in SQL
- Use transactions for multi-step operations
- Index foreign keys and frequently queried columns

### Frontend

- Keep HTML semantic and accessible
- Use CSS for styling, not inline styles
- Make UI responsive (mobile-first)
- Keep JavaScript minimal and vanilla

### Security

- **Never** commit sensitive data (passwords, API keys)
- Validate and sanitize all user input
- Use parameterized queries
- Escape output with `htmlspecialchars()`
- Use HTTPS in production
- Keep dependencies updated

## Project Structure

```
call-survey/
├── src/                    # Core PHP classes
│   ├── Database.php       # Database connection
│   ├── Survey.php         # Survey management
│   └── TwilioService.php  # Twilio integration
├── public/                # Web accessible files
│   ├── index.php         # Main entry point
│   ├── dashboard.php     # Dashboard UI
│   ├── api.php          # API endpoints
│   ├── webhook.php      # Twilio webhooks
│   └── survey/          # Survey pages
├── database.sql          # Database schema
├── composer.json        # PHP dependencies
├── config.example.php   # Config template
└── README.md           # Documentation
```

## Testing

```bash
# Run validation tests
php test.php

# Test specific functionality
php -l src/Database.php  # Syntax check
php create-sample-survey.php  # Integration test
```

## Commit Message Guidelines

Use clear, descriptive commit messages:

```
Add voice webhook handler for survey responses

- Implement IVR logic for multi-question surveys
- Add session management for call state
- Handle DTMF input validation
```

Format:
- First line: Brief summary (50 chars or less)
- Blank line
- Detailed description (wrap at 72 chars)
- Reference issues: `Fixes #123`

## Documentation

- Update README.md for user-facing changes
- Update docblocks for code changes
- Add examples for new features
- Keep CHANGELOG.md updated

## Release Process

1. Update version in composer.json
2. Update CHANGELOG.md
3. Create git tag: `git tag v1.0.0`
4. Push tag: `git push origin v1.0.0`
5. Create GitHub release with notes

## Need Help?

- Check existing documentation
- Search closed issues
- Ask questions in discussions
- Join the community chat

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

## Recognition

Contributors will be acknowledged in:
- CHANGELOG.md
- GitHub contributors page
- Release notes

Thank you for contributing! 🎉
