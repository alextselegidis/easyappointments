# Local Development Setup Guide

This guide will help you set up EasyAppointments with ScheduCal integration for local testing.

## Prerequisites

- PHP 7.4+ (with extensions: curl, json, mbstring, mysqli, openssl)
- MySQL 5.7+ or MariaDB 10.2+
- Composer
- Node.js and npm

## Quick Start

### 1. Create Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Edit .env with your actual ScheduCal credentials
```

Your `.env` file should look like:

```
SCHEDUCAL_ENABLED=true
SCHEDUCAL_API_KEY=<your-api-key>
SCHEDUCAL_API_SECRET=<your-api-secret>
SCHEDUCAL_API_URL=https://api.scheducal.com/api/v1/appointments
SCHEDUCAL_BASE_URL=http://localhost:8000
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Build Frontend Assets

```bash
# Build CSS and minified JS
npm run build

# Or use watch mode during development
npm start
```

### 4. Configure Database

Create a MySQL database and user:

```sql
CREATE DATABASE easyappointments;
CREATE USER 'ea_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON easyappointments.* TO 'ea_user'@'localhost';
FLUSH PRIVILEGES;
```

### 5. Create config.php

Copy `config-sample.php` to `config.php` and configure:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'easyappointments');
define('DB_USERNAME', 'ea_user');
define('DB_PASSWORD', 'your_password');
define('BASE_URL', 'http://localhost:8000');
```

### 6. Run Installation

Navigate to `http://localhost:8000` and follow the installation wizard.

### 7. Start Development Server

```bash
# Using PHP's built-in server
php -S localhost:8000 -t .
```

Or if you have a local web server (Apache/Nginx), point it to this directory.

## Testing ScheduCal Integration

### Enable ScheduCal

Make sure your `.env` file has:
- `SCHEDUCAL_ENABLED=true`
- Valid API credentials
- `SCHEDUCAL_BASE_URL` set to your local URL

### Test Flow

1. **Create an Appointment**:
   - Go to the booking page: `http://localhost:8000`
   - Book a new appointment
   - Check logs: `storage/logs/` for ScheduCal API calls

2. **Verify Sync**:
   - Check the database: The `id_google_calendar` field should contain the ScheduCal appointment ID
   - Check ScheduCal dashboard to see if the appointment was created

3. **Test Update**:
   - Reschedule the appointment
   - Check logs to verify UPDATE API call was made

4. **Test Delete**:
   - Cancel the appointment
   - Check logs to verify DELETE API call was made

### Debugging

Enable debug logging by editing `application/config/config.php`:

```php
$config['log_threshold'] = 2; // 0 = Disabled, 1 = Error, 2 = Debug, 3 = Info, 4 = All
```

View logs in `storage/logs/`.

## Common Issues

### ScheduCal Not Working

1. Check `.env` file exists and has correct credentials
2. Verify `application/config/scheducal.php` is loading environment variables
3. Check logs for API errors
4. Ensure `id_google_calendar` field exists in database

### Database Connection Failed

1. Verify MySQL service is running
2. Check database credentials in `config.php`
3. Ensure database exists and user has proper permissions

### Assets Not Loading

1. Run `npm run build` to compile assets
2. Check `assets/css/` and `assets/js/` folders for generated files
3. Clear browser cache

## Development Tips

### Watch Mode for Assets

During development, use watch mode to auto-rebuild on changes:

```bash
npm start
```

### Testing Without ScheduCal

Set `SCHEDUCAL_ENABLED=false` in `.env` to disable the integration.

### Inspect API Calls

Check `storage/logs/` for detailed ScheduCal API request/response logs.

### Database Reset

To start fresh:

```sql
DROP DATABASE easyappointments;
CREATE DATABASE easyappointments;
```

Then reinstall via web interface.

## Next Steps

Once local testing is working:

1. Test all appointment operations (create, update, delete)
2. Verify timezone handling is correct
3. Test with different timezones
4. Check that appointment links in ScheduCal work correctly
5. Verify error handling when ScheduCal is unavailable

## Additional Resources

- [EasyAppointments Documentation](https://easyappointments.org/docs.html)
- [ScheduCal Integration Guide](./SCHEDUCAL_INTEGRATION.md)
- [Contributing Guidelines](./CONTRIBUTING.md)
