# Manage Translations 

> This page will guide you through the management of Easy!Appointments translations.

### Introduction 
Easy!Appointments supports the addition of custom translations in order to display the user interface into many languages and therefore be more user friendly. This page will guide you through the addition of a new translation and the configuration of the application. You can also modify the available translations or even set the default one for the application.

### Details 
Easy!Appointments is based upon CodeIgniter (PHP Framework) and it uses its build-in libraries in order to translate the content into many languages. Version 1.1 of the application comes with English, German, Greek, Hungarian, Portuguese, Spanish, Italian, Japanese, Dutch, French, Simplified Chinese, Polish, Danish, Luxembourgish, Slovak, Finnish, Russian, Romanian, Turkish, Hindi and Bulgarian already included, but there is also the ability to add you own translation and change the user interface strings and captions. To add a new translation you must do the following:

  1. **CREATE A TRANSLATION FOLDER INSIDE "/APPLICATION/LANGUAGE/" DIRECTORY.** If you want for example to translate the application into French then you will need to create a new folder named "french" inside the `/application/language/` directory and copy the contents of the "english" folder. You must also copy the "migration_lang.php" file from another translation directory (e.g. "german") because CodeIgniter requires it when the version migration algorithm is executed.
  2. **TRANSLATE EACH STRING WITHIN YOUR "TRANSLATION_LANG.PHP" FILE INTO YOUR LANGUAGE.** You will just have to replace the English strings with your translation. Example >> `$lang['page_title'] = 'Write your translation here!';`
  3. **TELL EASY!APPOINTMENTS THAT YOU HAVE ADDED A NEW TRANSLATION.** When you are finished with the translation you will need to make some changes into the core config file of Easy!Appointments located at "/application/config/config.php" in order to tell the application that there is a new translation available. Find line 90 and add your language to the array. Example >> `$config['available_languages'] = array('english', 'german', 'greek', 'hungarian', 'portuguese', 'french');`. Then change the default language, though this is optional (e.g.` $config['language'] = 'english';`).

Follow these steps in order to add or adjust your translations and modify the message of the user interface of Easy!Appointments. If you want contribute to the translation process of Easy!Appointments please read the [Get Involved](https://github.com/alextselegidis/easyappointments/wiki/Get-Involved!) wiki page for more information. Please share your translations with the user community. 

*This document applies to Easy!Appointments v1.4.1.*

[Back](readme.md)
