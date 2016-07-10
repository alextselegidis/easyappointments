> This page will guide you through the management of Easy!Appointments translations.

### Introduction 
Easy!Appointments supports the addition of custom translations in order to display the user interface into many languages and therefore be more user friendly. This page will guide you through the addition of a new translation and the configuration of the application. You can also modify the available translations or even set the default one for the application.

### Details 
Easy!Appointments is based upon CodeIgniter (PHP Framework) and it uses its build-in libraries in order to translate the content into many languages. Version 1.1 of the application comes with English, German, Greek, Hungarian, Portuguese, Spanish, Italian, Japanese, Dutch, French, Simplified Chinese, Polish, Danish, Luxembourgish, Slovak, Finnish, Russian, Romanian, Turkish and Hindi already included, but there is also the ability to add you own translation and change the user interface strings and captions. To add a new translation you must do the following:

  1. **CREATE A TRANSLATION FOLDER INSIDE "/APPLICATION/LANGUAGE/" DIRECTORY.** If you want for example to translate the application into French then you will need to create a new folder named "french" inside the `/application/language/` directory and copy the contents of the "english" folder. You must also copy the "migration_lang.php" file from another translation directory (e.g. "german") because CodeIgniter requires it when the version migration algorithm is executed.
  2. **TRANSLATE EACH STRING WITHIN YOUR "TRANSLATION_LANG.PHP" FILE INTO YOUR LANGUAGE.** You will just have to replace the English strings with your translation. Example >> `$lang['page_title'] = 'Write your translation here!';`
  3. **TELL EASY!APPOINTMENTS THAT YOU HAVE ADDED A NEW TRANSLATION.** When you are finished with the translation you will need to make some changes into the core config file of Easy!Appointments located at "/application/config/config.php" in order to tell the application that there is a new translation available. Find line 90 and add your language to the array. Example >> `$config['available_languages'] = array('english', 'german', 'greek', 'hungarian', 'portuguese', 'french');`. Then change the default language, though this is optional (e.g.` $config['language'] = 'english';`).

Follow these steps in order to add or adjust your translations and modify the message of the user interface of Easy!Appointments. If you want contribute to the translation process of Easy!Appointments please read the [Get Involved](https://github.com/alextselegidis/easyappointments/wiki/Get-Involved!) wiki page for more information. Please share your translations with the user community. 

### Available Translations
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/english.zip?dl=1" target="_blank">English (Alex Tselegidis)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/german.zip?dl=1" target="_blank">German (Stefan Tselegidis)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/greek.zip?dl=1" target="_blank">Greek (Alex Tselegidis)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/hungarian.zip?dl=1" target="_blank">Hungarian (Zsolt Zala)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/portuguese.zip?dl=1" target="_blank">Portuguese (Andre Tavares)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/spanish-hallar.zip?dl=1" target="_blank">Spanish (Karim Hallar)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/spanish-silva.zip?dl=1" target="_blank">Spanish (Cristian Silva)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/spanish-yoruba.zip?dl=1" target="_blank">Spanish (Obalogun Yoruba)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/dutch.zip?dl=1" target="_blank">Dutch (Marco Tielen)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/japanese.zip?dl=1" target="_blank">Japanese (Masashi Nakane)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/french.zip?dl=1" target="_blank">French (Bernard Sylvie)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/french%20-%20for%20beauty%20salons.zip?dl=1" target="_blank">French - For Beauty Salons (Bernard Sylvie)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/chinese.zip?dl=1" target="_blank">Simplified Chinese (Aaron Wong)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/polish.zip?dl=1" target="_blank">Polish (K.Januś)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/italian.zip?dl=1" target="_blank">Italian (Salvatore Cordiano)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/danish.zip?dl=1" target="_blank">Danish (Lars Juul)</a>
- <a href="https://www.dropbox.com/s/es6d16tv7hekiff/luxembourgish.zip?dl=1" target="_blank">Luxembourgish (Claudine Van Winckel-Weber)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/slovak.zip?dl=1" target="_blank">Slovak (Branislav Ďorď)</a>
- <a href="https://dl.dropboxusercontent.com/u/27545985/easyappointments/translations/finnish.zip?dl=1" target="_blank">Finnish (Joona Kannisto)</a>
- <a href="https://www.dropbox.com/s/4vms8y6h4y42ccg/russian.zip?dl=1" target="_blank">Russian (Roman Filippov)</a>
- <a href="https://www.dropbox.com/s/eg43v35ehuxmz5b/romanian.zip?dl=1" target="_blank">Romanian (Cosmin Raducanu)</a>
- <a href="https://www.dropbox.com/s/i6fsufcgrvoc945/turkish.zip?dl=1" target="_blank">Turkish (Burak Inal)</a>
- <a href="https://www.dropbox.com/s/fnd2ka43j26yg1c/hindi.zip?dl=1" target="_blank">Hindi (Sharon S.K. Nathaniel)</a>
