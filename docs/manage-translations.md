# Manage Translations

Easy!Appointments supports multiple languages. You can modify existing translations or add your own.

## How Translations Work

Each language has its own folder inside `/application/language/` (e.g. `english`, `german`, `french`). Inside each folder, the `translations_lang.php` file contains all the text shown in the app.

## Adding a New Translation

### 1. Create the Language Folder

Inside `/application/language/`, create a new folder named after your language (e.g. `french`). Copy all files from the `english` folder into it.

Also copy the `migration_lang.php` file from another language folder (e.g. `german`) — the app needs this file to run database updates.

### 2. Translate the Strings

Open `translations_lang.php` in your new folder and replace the English text with your translation:

```php
$lang['page_title'] = 'Your translation here!';
```

Keep the key names (the part in square brackets) exactly the same — only change the text after the `=` sign.

### 3. Register Your Translation

Open `/application/config/config.php` and find the `available_languages` array (around line 90). Add your language to it:

```php
$config['available_languages'] = array('english', 'german', 'greek', 'french');
```

To change the default language for all users, update:

```php
$config['language'] = 'english'; // Change to your language
```

## Sharing Your Translation

If you'd like to contribute your translation to the project, you can submit a [pull request on GitHub](https://github.com/alextselegidis/easyappointments) or email it to [alextselegidis@gmail.com](mailto:alextselegidis@gmail.com).

*This document applies to Easy!Appointments v1.6.0.*

[Back](readme.md)
