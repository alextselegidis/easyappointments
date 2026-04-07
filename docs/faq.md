# FAQ

## How do I check that my server has Apache, PHP, and MySQL installed?

Easy!Appointments needs three things on your server: **Apache** (the web server), **PHP** (the programming language), and **MySQL** (the database). You also need the PHP **curl** extension and the Apache **mod_rewrite** module enabled.

**Two ways to check:**

1. **Ask your hosting company** — they can confirm what's installed.
2. **Create a test file** — make a file called `phpinfo.php` in your website's root folder with this content:
   ```php
   <?php phpinfo(); ?>
   ```
   Then open `http://your-domain.com/phpinfo.php` in your browser. It will show everything installed on your server. **Delete this file when you're done** for security reasons.

## How do I create a Google Calendar API key?

See the [Google Calendar Sync](google-calendar-sync.md) guide for detailed step-by-step instructions.

## The Installation Page Is Not Working

This usually happens for one of two reasons:

**1. Wrong config settings**

Open `config.php` and double-check:

- `BASE_URL` is set to your exact installation URL (e.g. `http://your-domain.com/easyappointments`)
- Your database name, username, and password are correct

**2. Server is blocking requests**

Some hosting providers need an `.htaccess` fix. Create or edit the `.htaccess` file in your installation folder and add:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?/$1 [L]
```

If that doesn't work, check the **Apache error log** and the **browser console** (press F12 → Console tab) for error messages. Contact your hosting company with those details.

## The Booking Page Won't Show Any Available Hours

**If no hours appear at all:**

This is usually a server issue. Check the Apache error log and browser console (F12 → Console) for errors. Contact your hosting company with the details you find.

**If you see "There are no available appointment hours for the selected date":**

This often happens because the **default working plan includes breaks** that don't leave enough room for your service. For example, if your service lasts 3–4 hours but there are lunch breaks in between, no slot is long enough.

**Fix:** Go to **Users** → **Providers**, select the provider, and adjust their working plan. Remove or shorten breaks so there's enough continuous time for your services.

## Installing on a Subdomain Doesn't Show Available Hours

If Easy!Appointments is on a subdomain like `http://book.mysite.com`, make sure `BASE_URL` in `config.php` uses the **subdomain URL** — not the folder path.

**Correct:** `BASE_URL = 'http://book.mysite.com'`

**Wrong:** `BASE_URL = 'http://mysite.com/book'`

Using the wrong URL causes a browser security error that blocks the booking page from loading appointment hours.

## How Do I Change the Time Slot Interval?

By default, available appointment times are shown every **15 minutes** (e.g. 9:00, 9:15, 9:30…). You can change this from the backend settings of each service by adjusting the availabilities type and duration.

## I'm Getting a Timezone Warning

If you see an error like `DateTime::__construct(): It is not safe to rely on the system's timezone settings...`, it means PHP doesn't have a timezone set.

**Fix:** Open your `php.ini` file and set the timezone:

```ini
date.timezone = America/New_York
```

Use your own timezone from the [PHP timezone list](https://www.php.net/manual/en/timezones.php).

If you can't edit `php.ini`, add this line to the top of `index.php`:

```php
date_default_timezone_set('America/New_York'); // Use your own timezone
```

## How Do I Use Caddy Instead of Apache?

If you prefer [Caddy](https://caddyserver.com/) as your web server:

1. Install Caddy
2. Install PHP-FPM: `sudo apt install php-fpm`
3. Set up Easy!Appointments in a folder (e.g. `/var/www/html/easyappointments`)
4. Add this to `/etc/caddy/Caddyfile`:

```Caddyfile
easyappointments.example.com {
        root * /var/www/html/easyappointments
        encode gzip zstd
        php_fastcgi unix//run/php/php-fpm.sock
        file_server
}
```

5. Restart Caddy: `sudo systemctl restart caddy.service`

*This document applies to Easy!Appointments v1.6.0.*

[Back](readme.md)
