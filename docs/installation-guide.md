# Installation Guide

This guide walks you through installing and setting up Easy!Appointments on your server.

## What You Need

Easy!Appointments runs on a web server, just like a regular website. Before you begin, make sure you have:

- **Apache** (v2.4 or newer) — the web server software
- **PHP** (v7.2 or newer) — the programming language that runs the app
- **MySQL** (v5.7 or newer) — the database that stores your data

Most web hosting companies already include all three. If you're installing on your own computer, use a free bundle like [XAMPP](https://www.apachefriends.org/), [MAMP](https://www.mamp.info/), or [WAMP](https://www.wampserver.com/) — they install everything for you.

If you plan to use Google Calendar sync, you'll also need the **php_curl** PHP extension enabled.

## Installation Steps

### 1. Create a Database

You need a MySQL database to store appointment data. If your hosting plan includes one, use that. Otherwise, create a new one through your hosting control panel (e.g. cPanel or phpMyAdmin). Write down the **database name**, **username**, and **password** — you'll need them soon.

### 2. Upload the Files

Upload the Easy!Appointments files to your server using FTP or your hosting's file manager. Place them in a folder like `easyappointments`, `appointments`, or `book`.

Take note of the URL. For example, if you uploaded to a folder called `easyappointments`, the URL will look like: `http://your-domain.com/easyappointments`

### 3. Make the Storage Folder Writable

The `storage` folder is where the app saves logs and temporary files. It needs write permissions. If you're on Linux, you can run:

```
chmod -R 755 storage
```

Or set permissions through your hosting's file manager.

### 4. Edit the Config File

Open the `config.php` file in the root folder and fill in:

- **BASE_URL** — the URL to your installation (from Step 2)
- **DB_HOST** — usually `localhost`
- **DB_NAME** — your database name
- **DB_USERNAME** — your database username
- **DB_PASSWORD** — your database password

You can also add Google Calendar API keys here if you want to use that feature later.

### 5. Run the Installer

Open your browser and go to your Easy!Appointments URL (e.g. `http://your-domain.com/easyappointments`). You'll see a setup wizard. Fill in your admin account details and company info, then click **Install**.

That's it — Easy!Appointments is now installed!

## Setting Up Your Business

After installation, the app comes with some test data. Here's how to set things up for your business:

### 1. Set Your Working Hours

Go to the backend by visiting `http://your-url/index.php/backend` and log in. Click **Settings** → **Business Logic** and set your company's working days and hours. Click **Save**. This becomes the default schedule for any new providers you add.

### 2. Add Your Services

Click **Services** and add the services your customers can book (e.g. "Haircut", "Consultation", "Massage"). Fill in the name, duration, and price for each one. You can organize services into categories if you like.

### 3. Add Your Providers

Click **Users** → **Providers** and add the people who provide your services. For each provider, set their working hours and which services they offer.

**Tip:** If you run a one-person business, just create a single provider with your company name.

### 4. Add Secretaries (Optional)

If someone manages bookings for your team, click the **Secretaries** tab and add them. Choose which providers they can manage.

### 5. You're Ready!

Click **Go To Booking Page** at the bottom of the page to see your public booking page. Try creating a test appointment — it will show up in your **Calendar**.

Finally, add a link to your website (e.g. "Book an Appointment") that points to your Easy!Appointments URL so customers can find it.

Happy Bookin'!

*This document applies to Easy!Appointments v1.6.0.*

[Back](readme.md)
